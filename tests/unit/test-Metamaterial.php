<?php


use HaddowG\MetaMaterial\Metamaterial;
class MetaMaterialTest extends MetaMaterialTestCase {


    public function setUp(){
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }


    function test_registerAlias_removesAliasIfNullProvided(){

        $ref = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');

        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));


        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);


        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertFalse(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));

    }

    function test_registerAlias_setsAliasIfNotAlreadyPresent(){

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);

        $ref = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');

        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertFalse(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));


        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal','foobar');


        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));
        $this->assertEquals('foobar',$aliases['HaddowG\MetaMaterial\MM_Minimal']);

    }

    function test_resolveAlias_resolvesExistingAlias(){

        $ref = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];

        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));

        $resolved = Metamaterial::resolveAlias('HaddowG\MetaMaterial\MM_Minimal');

        $this->assertEquals($aliases['HaddowG\MetaMaterial\MM_Minimal'],$resolved);
        $this->assertTrue(is_callable($resolved));
        $mm = $resolved();
        $this->assertInstanceOf('HaddowG\MetaMaterial\MM_Minimal',$mm);
        $this->assertTrue($mm instanceof \Mockery\MockInterface);
    }

    function test_resolveAlias_resolvesClassnameAndSetsAsAliasIfNoExisitngAlias(){
        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);

        $resolved = Metamaterial::resolveAlias('HaddowG\MetaMaterial\MM_Minimal');
        $this->assertFalse(is_callable($resolved));
        $this->assertSame('HaddowG\MetaMaterial\MM_Minimal',$resolved);
        $ref = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertEquals('HaddowG\MetaMaterial\MM_Minimal',$aliases['HaddowG\MetaMaterial\MM_Minimal']);

    }


    function test_getInstance_throwsException_whenNoTypeProvided() {
        $this->setExpectedException('\HaddowG\MetaMaterial\MM_Exception',null ,500);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test',self::$MINIMAL_CONF);

    }

    function test_getInstance_returnsCorrectInstance_whenTypeProvided() {
        $mm = Metamaterial::getInstance('test',self::$MINIMAL_CONF,'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertInstanceOf('HaddowG\MetaMaterial\MM_Minimal',$mm);
    }

    function test_getInstance_throwsWarning_whenPassingConfigToExistingInstance(){
        $this->setExpectedException('\HaddowG\MetaMaterial\MM_Exception',null ,500);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test',self::$MINIMAL_CONF,'HaddowG\MetaMaterial\MM_Minimal');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm2 = Metamaterial::getInstance('test',array('anything'=>true),'HaddowG\MetaMaterial\MM_Minimal');

    }

    function test_getInstance_returnsCorrectInstance_forExistingID(){
        $mm = Metamaterial::getInstance('test',array(),'HaddowG\MetaMaterial\MM_Minimal');
        $mm2 = Metamaterial::getInstance('test',array(),'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertEquals($mm,$mm2);

    }

    function test_getInstance_initializesActions_forNewInstances(){

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->once()->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');


    }

    function test_getInstance_appliesBaseConfig_forNewInstances(){

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->once()->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

    }

    function test_getInstance_appliesConfig_forNewInstances(){

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->once()->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

    }

    function test_hasInstance_returnsFalse_forNonExistentInstance(){
        $this->assertFalse(Metamaterial::hasInstance('nothing','HaddowG\MetaMaterial\MM_Minimal'));
    }

    function test_hasInstance_returnsFalse_forInvalidClass(){
        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertFalse(Metamaterial::hasInstance('test','HaddowG\MetaMaterial\MM_Exception'));
    }

    function test_hasInstance_returnsTrue_forExistingInstance(){
        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertTrue(Metamaterial::hasInstance('test','HaddowG\MetaMaterial\MM_Minimal'));
        $this->assertFalse(Metamaterial::hasInstance('nothing','HaddowG\MetaMaterial\MM_Minimal'));
    }


    function test_initInstanceActions_callsAddActionAppropriately(){
        WP_Mock::wpFunction('has_action');

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[applyBaseConfig,applyConfig,addAction]')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('addAction')->with('admin_head', 'HaddowG\MetaMaterial\Metamaterial::globalHead', 10, 1, FALSE, FALSE)->times(1);
            $mm->shouldReceive('addAction')->with('admin_footer', 'HaddowG\MetaMaterial\Metamaterial::globalFoot', 10, 1, FALSE, FALSE)->times(1);
            $mm->shouldReceive('addAction')->andReturn(true);
            return $mm;
        });

        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

    }

    function test_initInstanceActions_addsAdminInitAction()
    {

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[applyBaseConfig,applyConfig,addAction]')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('addAction')->andReturn(true);
            return $mm;
        });

        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

        WP_Mock::expectActionAdded('admin_init', array($mm, 'prep'));

        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

    }

    function test_initInstanceActions_addsAjaxSaveAction_whenAjaxSaveEnabled()
    {
        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[applyBaseConfig,applyConfig,addAction]')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('addAction')->andReturn(true);
            return $mm;
        });

        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

        $this->setPrivateProperties($mm,array('id'=>'test','ajax_save'=>true));

        WP_Mock::expectActionAdded('wp_ajax_metamaterial_action_test_ajax_save', array($mm, 'ajax_save'));

        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

    }

    function test_initInstanceActions_doesNotAddAjaxSaveAction_whenAjaxSaveDisabled()
    {

        Metamaterial::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[applyBaseConfig,applyConfig,addAction]')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('addAction')->andReturn(true);
            return $mm;
        });


        $mm = Metamaterial::getInstance('test', array(), 'HaddowG\MetaMaterial\MM_Minimal');

        $this->setPrivateProperties($mm,array('id'=>'test','ajax_save'=>false));

        WP_Mock::expectActionAdded('wp_ajax_metamaterial_action_test_ajax_save', array($mm, 'ajax_save'));

        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

        $exceptionTriggered = false;
        try {
            Mockery::close();
        }catch (\Mockery\Exception\InvalidCountException $x){
            if($x->getMethodName()==='intercepted()') {
                $exceptionTriggered = true;
            }
        }

        $this->assertTrue($exceptionTriggered);
    }
}
