<?php
use HaddowG\MetaMaterial\Metamaterial;

class MetaMaterialTestCase extends PHPUnit_Framework_TestCase {

    protected static $SUPPORT_DIR;
    static $MINIMAL_CONF = array();

    public function setUp(){
        WP_Mock::setUsePatchwork(true);
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
            Metamaterial::registerAlias($classname,function() use($classname){
                $mm =  Mockery::mock($classname)->shouldAllowMockingProtectedMethods();
                $mm->shouldReceive('applyBaseConfig')->andReturn(true);
                $mm->shouldReceive('applyConfig')->andReturn(true);
                $mm->shouldReceive('initInstanceActions')->andReturn(true);
                return $mm;
            });
        }
    }

    public function tearDown() {
        Metamaterial::purgeInstances();
        WP_Mock::tearDown();
    }

    public static function setUpBeforeClass()
    {
        self::$SUPPORT_DIR = dirname(dirname(__FILE__)) . '/support/';
    }

    public function setPrivateProperties($obj,$properties){
        $classname = get_class($obj);
        $reflector = new ReflectionClass($classname);
        foreach($properties as $k=>$v){
            $prop = $reflector->getProperty($k);
            $prop->setAccessible(true);
            $prop->setValue($obj,$v);
        }
    }

}
