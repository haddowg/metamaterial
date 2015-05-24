<?php


use HaddowG\MetaMaterial\Metamaterial;
class MetaMaterialTest extends MetaMaterialTestCase {


    public function setUp(){
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
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
        $mm2 = Metamaterial::getInstance('test',self::$MINIMAL_CONF,'HaddowG\MetaMaterial\MM_Minimal');

    }

    function test_getInstance_returnsCorrectInstance_forExistingID(){
        $mm = Metamaterial::getInstance('test',self::$MINIMAL_CONF,'HaddowG\MetaMaterial\MM_Minimal');
        $mm2 = Metamaterial::getInstance('test',array(),'HaddowG\MetaMaterial\MM_Minimal');
        $this->assertEquals($mm,$mm2);

    }

    function test_getInstance_initializesActions_forNewInstances(){

        Metamaterial::registerDependancy('HaddowG\MetaMaterial\MM_Minimal',function($id,$config){
            $mm =  Mockery::mock('HaddowG\MetaMaterial\MM_Minimal',array($id,$config));
            $mm->shouldReceive('initInstanceActions')->once()->andReturn(true);
            return $mm;
        });
        /** @noinspection PhpUnusedLocalVariableInspection */
        $mm = Metamaterial::getInstance('test',self::$MINIMAL_CONF,'HaddowG\MetaMaterial\MM_Minimal');

    }


}
