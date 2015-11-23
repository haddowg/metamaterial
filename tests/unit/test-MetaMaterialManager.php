<?php

use HaddowG\MetaMaterial\MetaMaterialManager;
use HaddowG\MetaMaterial\Metamaterial;
use HaddowG\MetaMaterial\Facades\MMM;

class MetaMaterialManagerTest extends MetaMaterialTestCase {

    public function setUp(){
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }


    function test_registerAlias_removesAliasIfNullProvided(){

        $ref = new ReflectionClass('HaddowG\MetaMaterial\MetaMaterialManager');

        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));


        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);


        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertFalse(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));

    }

    function test_registerAlias_setsAliasIfNotAlreadyPresent(){

        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);

        $ref = new ReflectionClass('HaddowG\MetaMaterial\MetaMaterialManager');

        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertFalse(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));


        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal','foobar');


        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));
        $this->assertEquals('foobar',$aliases['HaddowG\MetaMaterial\MM_Minimal']);

    }

    function test_resolveAlias_resolvesExistingAlias(){

        $ref = new ReflectionClass('HaddowG\MetaMaterial\MetaMaterialManager');
        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];

        $this->assertTrue(array_key_exists('HaddowG\MetaMaterial\MM_Minimal',$aliases));

        $resolved = MMM::resolveAlias('HaddowG\MetaMaterial\MM_Minimal');

        $this->assertEquals($aliases['HaddowG\MetaMaterial\MM_Minimal'],$resolved);
        $this->assertTrue(is_callable($resolved));
        $mm = $resolved();
        $this->assertInstanceOf('HaddowG\MetaMaterial\MM_Minimal',$mm);
        $this->assertTrue($mm instanceof \Mockery\MockInterface);
    }

    function test_resolveAlias_resolvesClassnameAndSetsAsAliasIfNoExisitngAlias(){
        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',null);

        $resolved = MMM::resolveAlias('HaddowG\MetaMaterial\MM_Minimal');
        $this->assertFalse(is_callable($resolved));
        $this->assertSame('HaddowG\MetaMaterial\MM_Minimal',$resolved);
        $ref = new ReflectionClass('HaddowG\MetaMaterial\MetaMaterialManager');
        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $this->assertEquals('HaddowG\MetaMaterial\MM_Minimal',$aliases['HaddowG\MetaMaterial\MM_Minimal']);

    }


    function test_getInstance_throwsException_whenNoTypeProvided() {
        $this->setExpectedException('\HaddowG\MetaMaterial\MM_Exception',null ,500);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MMM::getInstance('test');

    }

    /**
     * @test
     * @group wip
     */
    function test_getInstance_returnsCorrectInstance_whenTypeProvided() {
        $mm = MMM::getInstance('test','HaddowG\MetaMaterial\MM_Minimal');
        $this->assertInstanceOf('HaddowG\MetaMaterial\MM_Minimal',$mm);
    }


    function test_getInstance_returnsCorrectInstance_forExistingID(){
        $mm = MMM::getInstance('test','HaddowG\MetaMaterial\MM_Minimal');
        $mm2 = MMM::getInstance('test','HaddowG\MetaMaterial\MM_Minimal');
        $this->assertEquals($mm,$mm2);

    }

    function test_hasInstance_returnsFalse_forNonExistentInstance(){
        $this->assertFalse(MMM::hasInstance('nothing','HaddowG\MetaMaterial\MM_Minimal'));
    }

    function test_hasInstance_returnsFalse_forInvalidClass(){
        $mm = MMM::getInstance('test', 'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertFalse(MMM::hasInstance('test','HaddowG\MetaMaterial\MM_Exception'));
    }

    function test_hasInstance_returnsTrue_forExistingInstance(){
        $mm = MMM::getInstance('test', 'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertTrue(MMM::hasInstance('test','HaddowG\MetaMaterial\MM_Minimal'));
        $this->assertFalse(MMM::hasInstance('nothing','HaddowG\MetaMaterial\MM_Minimal'));
    }


    public function testPurgeInstances_clearsInstancesAndAliases(){
        MMM::getInstance('test', 'HaddowG\MetaMaterial\MM_Minimal');

        $ref = new ReflectionClass('HaddowG\MetaMaterial\MetaMaterialManager');

        MMM::purgeInstances();

        $staticProps=$ref->getStaticProperties();
        $aliases = $staticProps['registeredAliases'];
        $instances = $staticProps['instances'];

        $this->assertEmpty($aliases);
        $this->assertEmpty($instances);
    }


    public function testGlobalInit_returnsFalse_ifNoShowingMetamaterials(){
        MMM::shouldReceive('getShowing')->andReturn([]);
        $this->assertFalse(MMM::globalInit());
    }

    public function testGlobalInit_callsAppropriateMethods(){


        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('initInstanceActions')->andReturn(true);

            $mm->shouldReceive('init')->once()->andReturn(true);
            $mm->shouldReceive('addDefaultFilters')->once()->andReturn(true);
            $mm->shouldReceive('addDefaultActions')->once()->andReturn(true);
            $mm->shouldReceive('doAction')->with('init')->once()->andReturn(true);
            $mm->shouldReceive('initOnce')->once()->andReturn(true);

            return $mm;
        });

        $conf = array_merge(self::$MINIMAL_CONF,['ajax_save'=>true]);
        $mm  = Metamaterial::getInstance('test',$conf,'HaddowG\MetaMaterial\MM_Minimal');

        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('initInstanceActions')->andReturn(true);

            $mm->shouldReceive('init')->once()->andReturn(true);
            $mm->shouldReceive('addDefaultFilters')->once()->andReturn(true);
            $mm->shouldReceive('addDefaultActions')->once()->andReturn(true);
            $mm->shouldReceive('doAction')->with('init')->once()->andReturn(true);
            $mm->shouldNotReceive('initOnce');

            return $mm;
        });

        $conf2 = array_merge(self::$MINIMAL_CONF,['ajax_save'=>false]);
        $mm2  = MetaMaterial::getInstance('test2',$conf2,'HaddowG\MetaMaterial\MM_Minimal');

        MMM::shouldReceive('getShowing')->andReturn([$mm,$mm2]);

        MMM::globalInit();
    }

}
