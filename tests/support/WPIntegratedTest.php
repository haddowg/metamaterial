<?php
use HaddowG\MetaMaterial\MetamaterialManager;
use HaddowG\MetaMaterial\Facades\MMM;

class WPIntegratedTest extends WP_UnitTestCase {

    protected static $SUPPORT_DIR;
    static $MINIMAL_CONF = array();


    public function setUp(){
        self::$MINIMAL_CONF = array(
            'template' => self::$SUPPORT_DIR. 'templates/empty.php'
        );
    }

    public function tearDown() {
        \Mockery::close();
        MMM::swap(new MetamaterialManager());
        MMM::purgeInstances();
    }

    public static function setUpBeforeClass()
    {
        self::$SUPPORT_DIR = dirname(dirname(__FILE__)) . '/support/';
    }

}
