<?php namespace HaddowG\MetaMaterial\Facades;


use HaddowG\MetaMaterial\MetaMaterialManager;
use Mockery;
use Mockery\MockInterface;

class MMM {

    private static $instance = null;

    private static function instance(MetaMaterialManager $mmm = null){
        if(!is_null($mmm)){
            self::$instance = $mmm;
        }elseif(is_null(self::$instance)){
            self::$instance = new MetaMaterialManager();
        }
        return self::$instance;
    }

    public static function swap(MetaMaterialManager $mmm){
        self::$instance=$mmm;
    }
    /**
     * Initiate a mock expectation on the facade.
     *
     * @param  mixed
     * @return \Mockery\Expectation
     */
    public static function shouldReceive()
    {
        if (static::isMock())
        {
            $mock = static::$instance;
        }
        else
        {
            static::$instance = Mockery::mock(self::$instance);
            $mock = static::$instance;
        }
        return call_user_func_array(array($mock, 'shouldReceive'), func_get_args());
    }


    /**
     * Determines whether a mock is set as the instance of the facade.
     *
     * @return bool
     */
    protected static function isMock()
    {
        return isset(self::$instance) && self::$instance instanceof MockInterface;
    }


    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        switch (count($args))
        {
            case 0:
                return self::instance()->$method();
            case 1:
                return self::instance()->$method($args[0]);
            case 2:
                return self::instance()->$method($args[0], $args[1]);
            case 3:
                return self::instance()->$method($args[0], $args[1], $args[2]);
            case 4:
                return self::instance()->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array(self::instance(), $method), $args);
        }
    }


}