<?php


use HaddowG\MetaMaterial\MM_Minimal;
use HaddowG\MetaMaterial\Facades\MMM;


class MetaMaterialTest extends MetaMaterialTestCase {


    function test_getInstance_throwsException_whenPassingConfigToExistingInstance(){
        $this->setExpectedException('\HaddowG\MetaMaterial\MM_Exception',null ,500);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MM_Minimal::getInstance('test',self::$MINIMAL_CONF);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm2 = MM_Minimal::getInstance('test',array('anything'=>true));

    }

    function test_getInstance_returnsCorrectInstance_forExistingID(){
        $mm = MM_Minimal::getInstance('test',array());
        $mm2 = MM_Minimal::getInstance('test',array());
        $this->assertEquals($mm,$mm2);

    }

    function test_getInstance_appliesFilterToConfig(){


        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->with('test',array('foobar'=>'boofar'))->once()->andReturn(true);
            return $mm;
        });

        WP_Mock::onFilter( 'metamaterial_filter_test_before_config' )->with(array(),'test')->reply(array('foobar'=>'boofar'));

        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MM_Minimal::getInstance('test', array());


    }

    function test_getInstance_initializesActions_forNewInstances(){

        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->once()->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MM_Minimal::getInstance('test', array());


    }

    function test_getInstance_appliesBaseConfig_forNewInstances(){

        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->andReturn(true);
            $mm->shouldReceive('applyConfig')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->once()->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MM_Minimal::getInstance('test', array());

    }

    function test_getInstance_appliesConfig_forNewInstances(){

        MMM::registerAlias('HaddowG\MetaMaterial\MM_Minimal',function(){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal')->shouldAllowMockingProtectedMethods();
            $mm->shouldReceive('initInstanceActions')->andReturn(true);
            $mm->shouldReceive('applyBaseConfig')->andReturn(true);
            $mm->shouldReceive('applyConfig')->once()->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = MM_Minimal::getInstance('test', array());

    }


    function test_initInstanceActions_callsAddActionAppropriately(){
        WP_Mock::wpFunction('has_action');

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[addAction]')->shouldAllowMockingProtectedMethods();
        $mm->shouldReceive('addAction')->with('admin_enqueue_scripts', 'HaddowG\MetaMaterial\Facades\MMM::globalEnqueue', 10, 1, FALSE, FALSE)->times(1);
        $mm->shouldReceive('addAction')->with('admin_enqueue_scripts', 'HaddowG\MetaMaterial\Facades\MMM::enqueue', 10, 1, FALSE, FALSE)->times(1);

        $mm->shouldReceive('addAction')->with('admin_print_styles', 'HaddowG\MetaMaterial\Facades\MMM::printGlobalStyles', 10, 1, FALSE, FALSE)->times(1);


        $mm->shouldReceive('addAction')->with('admin_head', 'HaddowG\MetaMaterial\Facades\MMM::head', 11, 1, FALSE, FALSE)->times(1);
        $mm->shouldReceive('addAction')->with('admin_footer', 'HaddowG\MetaMaterial\Facades\MMM::foot', 11, 1, FALSE, FALSE)->times(1);

        $mm->shouldReceive('addAction')->with('current_screen','HaddowG\MetaMaterial\Facades\MMM::globalInit', 10, 1, FALSE, FALSE)->times(1);


        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

    }

    function test_initInstanceActions_addsAdminInitAction()
    {

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[addAction]')->shouldAllowMockingProtectedMethods();
        $mm->shouldReceive('addAction')->andReturn(true);

        WP_Mock::expectActionAdded('admin_init', array($mm, 'initAlways'));

        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

    }

    function test_initInstanceActions_addsAjaxSaveAction_whenAjaxSaveEnabled()
    {
        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[addAction]')->shouldAllowMockingProtectedMethods();
        $mm->shouldReceive('addAction')->andReturn(true);

        $this->setPrivateProperties($mm,array('id'=>'test','ajax_save'=>true));

        WP_Mock::expectActionAdded('wp_ajax_metamaterial_action_test_ajax_save', array($mm, 'ajax_save'));

        $method = new ReflectionMethod('HaddowG\MetaMaterial\MM_Minimal', 'initInstanceActions');
        $method->setAccessible(true);

        $method->invoke($mm);

    }

    function test_initInstanceActions_doesNotAddAjaxSaveAction_whenAjaxSaveDisabled()
    {

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('addAction')->andReturn(true);

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


    public function testApplyBaseConfig_throwsException_whenPassedNonArray(){

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->andReturn(true);

        $this->setExpectedException('\HaddowG\MetaMaterial\MM_Exception',null ,500);

        $notConf='foobar';
        $mm->applyBaseConfig('test', $notConf);
    }

    public function testApplyBaseConfig_setsIDAndDefaultsMetaKey_whenNoConfigProvided(){

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->andReturn(true);

        $conf = array();
        $mm->applyBaseConfig('test', $conf);
        $this->assertEquals('test',$mm->get_the_id());

        $meta_key = new ReflectionProperty('HaddowG\MetaMaterial\MM_Minimal','meta_key');
        $meta_key->setAccessible(true);
        $this->assertEquals($meta_key->getValue($mm),'_test');

    }

    public function testApplyBaseConfig_mergesDefaultsWithConfig(){

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->andReturn(true);

        $conf = array();
        $mm->applyBaseConfig('test', $conf);

        $expected = array(
            'title',
            'template',
            'context',
            'priority',
            'ajax_save',
            'mode',
            'meta_key',
            'prefix',
            'hide_on_screen',
            'init_action',
            'output_filter',
            'save_filter',
            'save_action',
            'head_action',
            'foot_action'
        );

        foreach($expected as $k){
            $this->assertArrayHasKey($k,$conf);
        }

    }

    public function testApplyBaseConfig_setsAllBaseConfigOptionsProvided(){

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->andReturn(true);

        $conf = array(
            'title' => 'foobar_title',
            'template' => 'foobar_template',
            'context' => 'foobar_context',
            'priority' => 'foobar_priority',
            'ajax_save' => 'foobar_ajax_save',
            'mode' => 'foobar_mode',
            'meta_key' => 'foobar_meta_key',
            'prefix' => 'foobar_prefix',
            'hide_on_screen' => 'foobar_hide_on_screen',
            'init_action' => 'foobar_init_action',
            'output_filter' => 'foobar_output_filter',
            'save_filter' => 'foobar_save_filter',
            'save_action' => 'foobar_save_action',
            'head_action' => 'foobar_head_action',
            'foot_action' => 'foobar_foot_action'
        );

        $mm->applyBaseConfig('test',$conf);

        foreach($conf as $k=>$v){
            $prop = new ReflectionProperty('HaddowG\MetaMaterial\MM_Minimal',$k);
            $prop->setAccessible(true);
            $this->assertEquals($prop->getValue($mm),$v);
        }


    }

    public function testApplyBaseConfig_ignoresNonBaseConfigOptionsProvided(){

        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->andReturn(true);

        $conf = array(
            'not_allowed'=>'foobar',
            'another_thing'=>'boofar'
        );
        $not_expected = array(
            'not_allowed',
            'another_thing'
        );

        $mm->applyBaseConfig('test',$conf);

        foreach($not_expected as $k){
            $this->assertObjectNotHasAttribute($k,$mm);
        }


    }

    public function testApplyBaseConfig_callsGetTemplatePath(){
        $mm = Mockery::mock('HaddowG\MetaMaterial\MM_Minimal[getTemplatePath]');
        $mm->shouldReceive('getTemplatePath')->once()->andReturn(true);
        $conf= array();
        $mm->applyBaseConfig('test',$conf);

    }

    public function testGetTemplatePath_throwsExceptionIfNoTemplateOptionProvided(){

        $this->setExpectedException('HaddowG\MetaMaterial\MM_Exception');
        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $mm->getTemplatePath();

    }

    public function testGetTemplatePath_throwsExceptionIfUnableToLocateTemplate(){

        WP_Mock::wpFunction('trailingslashit');
        WP_Mock::wpFunction('get_stylesheet_directory');

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $template = $class->getProperty('template');
        $template->setAccessible(true);

        $this->setExpectedException('HaddowG\MetaMaterial\MM_Exception');
        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $template->setValue($mm,'foobar');

        $mm->getTemplatePath();

    }

    public function testGetTemplatePath_addsPHPExtensionToTemplateIfNeeded(){
        WP_Mock::wpFunction('trailingslashit');
        WP_Mock::wpFunction('get_stylesheet_directory');

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $template = $class->getProperty('template');
        $template->setAccessible(true);


        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $template->setValue($mm,dirname(dirname(__FILE__)). '/support/templates/empty');

        $mm->getTemplatePath();

        $this->assertEquals('.php',substr($template->getValue($mm),strlen($template->getValue($mm))-4));

    }

    public function testGetTemplatePath_returnsPathIfFileExists(){
        WP_Mock::wpFunction('trailingslashit');
        WP_Mock::wpFunction('get_stylesheet_directory');

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $template = $class->getProperty('template');
        $template->setAccessible(true);


        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $template->setValue($mm,dirname(dirname(__FILE__)). '/support/templates/empty.php');

        $this->assertEquals(dirname(dirname(__FILE__)). '/support/templates/empty.php',$mm->getTemplatePath());

    }

    public function testGetTemplatePath_checksDefaultTemplatesDirectoryIfProvided(){
        WP_Mock::wpFunction('trailingslashit', array('return_arg' => 0));
        WP_Mock::wpFunction('get_stylesheet_directory');

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $template = $class->getProperty('template');
        $template->setAccessible(true);
        $templates_dir = $class->getProperty('default_templates_dir');
        $templates_dir->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $template->setValue($mm,'empty.php');
        $templates_dir->setValue($mm, dirname(dirname(__FILE__)) . '/support/templates/');

        $this->assertEquals(dirname(dirname(__FILE__)). '/support/templates/empty.php',$mm->getTemplatePath());

    }

    public function testGetTemplatePath_checksThemeDirectory(){
        WP_Mock::wpFunction('trailingslashit', array('return_arg' => 0));
        WP_Mock::wpFunction('get_stylesheet_directory', array('return'=>dirname(dirname(__FILE__)) . '/support/templates/'));

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $template = $class->getProperty('template');
        $template->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $template->setValue($mm,'empty.php');

        $this->assertEquals(dirname(dirname(__FILE__)). '/support/templates/empty.php',$mm->getTemplatePath());

    }

    public function testGetContext_returnsDefaultValues_ifInvalidOptionSet(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $context = $class->getProperty('context');
        $context->setAccessible(true);
        $contexts = $class->getProperty('contexts');
        $contexts->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $context->setValue($mm,'foobar');
        $default_contexts = $contexts->getValue($mm);

        $method = $class->getMethod('getContext');
        $method->setAccessible(true);

        $this->assertEquals('normal',$method->invoke($mm));

        $this->assertEquals(array_search('normal',$default_contexts),$method->invoke($mm,true));

    }

    public function testGetContext_returnsCorrectValue_ifValidOptionSet(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $context = $class->getProperty('context');
        $context->setAccessible(true);
        $contexts = $class->getProperty('contexts');
        $contexts->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $context->setValue($mm,'advanced');
        $default_contexts = $contexts->getValue($mm);

        $method = $class->getMethod('getContext');
        $method->setAccessible(true);

        $this->assertEquals('advanced',$method->invoke($mm));

        $this->assertEquals(array_search('advanced',$default_contexts),$method->invoke($mm,true));

    }

    public function testGetPriority_returnsDefaultValues_ifInvalidOptionSet(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $priority = $class->getProperty('priority');
        $priority->setAccessible(true);
        $priorities = $class->getProperty('priorities');
        $priorities->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $priority->setValue($mm,'foobar');
        $default_priorities = $priorities->getValue($mm);

        $method = $class->getMethod('getPriority');
        $method->setAccessible(true);

        $this->assertEquals('bottom',$method->invoke($mm,false,false));

        $this->assertEquals($default_priorities['bottom'],$method->invoke($mm,true,false));

    }

    public function testGetPriority_returnsCorrectValue_ifValidOptionSet(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $priority = $class->getProperty('priority');
        $priority->setAccessible(true);
        $priorities = $class->getProperty('priorities');
        $priorities->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $priority->setValue($mm,'high');
        $default_priorities = $priorities->getValue($mm);

        $method = $class->getMethod('getPriority');
        $method->setAccessible(true);

        $this->assertEquals('high',$method->invoke($mm,false,false));

        $this->assertEquals($default_priorities['high'],$method->invoke($mm,true,false));

    }

    public function testGetPriority_castsUndefinedValuesToNearestStandardValue_forTextReturn(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $priority = $class->getProperty('priority');
        $priority->setAccessible(true);
        $priorities = $class->getProperty('priorities');
        $priorities->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $priority->setValue($mm,'top');
        $default_priorities = $priorities->getValue($mm);

        $method = $class->getMethod('getPriority');
        $method->setAccessible(true);

        $this->assertEquals('high',$method->invoke($mm,false,true));
        $this->assertEquals($default_priorities['top'],$method->invoke($mm,true,true));

        $priority->setValue($mm,'bottom');
        $this->assertEquals('low',$method->invoke($mm,false,true));
        $this->assertEquals($default_priorities['bottom'],$method->invoke($mm,true,true));

    }

    public function testGetPriority_convertsNumericOptionToNearestMatch_forTextReturn(){

        $class = new ReflectionClass('HaddowG\MetaMaterial\Metamaterial');
        $priority = $class->getProperty('priority');
        $priority->setAccessible(true);

        $mm = new HaddowG\MetaMaterial\MM_Minimal();
        $priority->setValue($mm,151);

        $method = $class->getMethod('getPriority');
        $method->setAccessible(true);

        $this->assertEquals('high',$method->invoke($mm,false,false));
        $this->assertEquals(151,$method->invoke($mm,true,false));

        $priority->setValue($mm,1);
        $this->assertEquals('low',$method->invoke($mm,false,false));
        $this->assertEquals(1,$method->invoke($mm,true,false));

    }

}
