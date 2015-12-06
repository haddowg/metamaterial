<?php
/**
 * MMM Class
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program has the following attribution requirement (GPL Section 7):
 *     - you agree to retain in MetaMaterial and any modifications to MetaMaterial the copyright, author attribution and
 *       URL information as provided in this notice and repeated in the licence.txt document provided with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Gregory Haddow <greg@greghaddow.co.uk>
 * @copyright   Gregory Haddow, http://www.greghaddow.co.uk/
 * @license     http://opensource.org/licenses/gpl-3.0.html The GPL-3 License with additional attribution clause as detailed below.
 * @link        http://www.greghaddow.co.uk/MetaMaterial
 */

namespace HaddowG\MetaMaterial\Facades;


use HaddowG\MetaMaterial\MetamaterialManager;
use Mockery;

/**
 * Class MMM
 *
 * Facade class provides static access to methods on a singleton instance of MetamaterialManager.
 *
 * Use of the Facade facilitates mocking during testing.
 *
 * @package HaddowG\MetaMaterial\Facades
 */
class MMM {

	/**
	 * @var MetamaterialManager singleton instance of MetamaterialManager
	 */
    private static $instance = null;

	/**
	 * Get the Metamaterial Manager instance
	 *
	 * Default to creating a MetamaterialManager but can be passed another object
	 * that extends MetamaterialManager prior to first use to use a different Class.
	 *
	 * @param MetamaterialManager|null $mmm
	 * @return MetamaterialManager
	 */
    public static function instance(MetamaterialManager $mmm = null){
        if(is_null(static::$instance)) {
            if(!is_null($mmm)) {
                static::$instance = $mmm;
            }else{
                static::$instance = new MetamaterialManager();
            }
        }
        return static::$instance;
    }

	/**
	 * Swap the singleton instance of MetamaterialManager with another instance.
	 *
	 * Useful for mocking the manager during testing.
	 *
	 * @param MetamaterialManager $mmm
	 */
    public static function swap(MetamaterialManager $mmm){
        static::$instance=$mmm;
    }

    /**
     * Initiate a mock expectation on the facade.
     *
     * @param  mixed
     * @return \Mockery\Expectation
     */
    public static function shouldReceive()
    {
        static::instance();

        if (static::isMock())
        {
            $mock = static::$instance;
        }
        else
        {
            static::$instance = Mockery::mock(static::$instance);
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
        return isset(static::$instance) && static::$instance instanceof Mockery\MockInterface;
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
                return static::instance()->$method();
            case 1:
                return static::instance()->$method($args[0]);
            case 2:
                return static::instance()->$method($args[0], $args[1]);
            case 3:
                return static::instance()->$method($args[0], $args[1], $args[2]);
            case 4:
                return static::instance()->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array(static::instance(), $method), $args);
        }
    }

}