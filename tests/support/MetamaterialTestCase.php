<?php
use HaddowG\MetaMaterial\Metamaterial;

class MetaMaterialTestCase extends PHPUnit_Framework_TestCase {

    protected static $SUPPORT_DIR;
    static $MINIMAL_CONF = array();

    public function setUp(){

        WP_Mock::setUp();
        self::$MINIMAL_CONF = array(
            'template' => self::$SUPPORT_DIR. 'templates/empty.php'
        );

        $mm_classes = array(
            'HaddowG\MetaMaterial\MM_Minimal',
            'HaddowG\MetaMaterial\MM_Metabox',
            'HaddowG\MetaMaterial\MM_Taxonomy',
            'HaddowG\MetaMaterial\MM_Dashboard',
            'HaddowG\MetaMaterial\MM_User'
        );

        foreach($mm_classes as $classname){
            Metamaterial::registerDependancy($classname,function($id,$config) use($classname){
                $mm =  Mockery::mock($classname,array($id,$config));
                $mm->shouldReceive('initInstanceActions')->andReturn(true);
                return $mm;
            });
        }
    }

    public function tearDown() {
        WP_Mock::tearDown();
        Metamaterial::purgeInstances();
    }

    public static function setUpBeforeClass()
    {
        self::$SUPPORT_DIR = dirname(dirname(__FILE__)) . '/support/';
    }

}
