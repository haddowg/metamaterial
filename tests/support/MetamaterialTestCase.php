<?php
use HaddowG\MetaMaterial\Facades\MMM;
use HaddowG\MetaMaterial\MetaMaterialManager;

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
            self::registerMockAlias($classname);
        }
    }

    public function tearDown() {
        WP_Mock::tearDown();
        MMM::swap(new MetaMaterialManager());
        MMM::purgeInstances();
    }

    public static function setUpBeforeClass()
    {
        self::$SUPPORT_DIR = dirname(dirname(__FILE__)) . '/support/';
    }

    public function registerMockAlias($classname, $stubs=[], $base=true){
        MMM::registerAlias($classname,function() use($classname, $stubs, $base){

            $methods = ($base)?['applyBaseConfig','applyConfig','initInstanceActions']:[];
            foreach(array_keys($stubs) as $method){
                $methods[] = $method;
            }

            $mm =  Mockery::mock($classname . '[' . implode(',',$methods) . ']')->shouldAllowMockingProtectedMethods();

            if($base) {
                $mm->shouldReceive('applyBaseConfig')->andReturn(true);
                $mm->shouldReceive('applyConfig')->andReturn(true);
                $mm->shouldReceive('initInstanceActions')->andReturn(true);
            }
            foreach($stubs as $method=>$return){
                $mm->shouldReceive($method)->andReturn($return);
            }

            return $mm;
        });
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
