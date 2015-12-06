<?php
/**
 * MetamaterialManager Class.
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

namespace HaddowG\MetaMaterial;

use HaddowG\MetaMaterial\Facades\MMM;

/**
 * Class MetamaterialManager
 *
 * Manages registering of all metamaterial instances, organises the output of relevant instances,
 * and the output of appropriate global scripts.
 *
 * @package HaddowG\MetaMaterial
 */
class MetamaterialManager {

    /**
     * Storage array for instances of MetaMaterial
     * Instances are grouped into arrays by classname and then keyed by $id
     *
     * @var	array Array of arrays each of a type of  MetaMaterial instances
     */
    protected static $instances =array();

    /**
     * Provides an IOC mechanism to alter the classes returned by getInstance.
     * Array of classnames or factory methods keyed by target classname they will replace.
     * SHould only be used for testing purposes, this is not designed for production use though could have potential
     * use allow users to override the Metamaterial classes used in thirdparty code without having to modify or
     * duplicate the code.
     *
     * @var array
     */
    protected static $registeredAliases = array();

    /**
     * If FALSE only the first showing metabox's $hide_on_screen values will be considered.
     * When this is the default TRUE all showing metaboxes have their $hide_on_screen values
     * merged to determine what should be hidden.
     *
     * Config Option
     *
     * @var     boolean whether to compound metabox $hide_on_screen values
     */
    protected static $compoundHide = true;

    /**
     * Array of MetaMaterial instances showing on the current admin page.
     * Cached result of getShowing() to avoid re-execution.
     *
     * @var     array Array of MetaMaterial instances showing on the current admin page
     */
    protected static $allShowing = null;

    /**
     * Array of Classes of MetaMaterial for which there is a showing Metamaterial instance on the current admin page.
     * Cached result of getShowing() to avoid re-execution.
     *
     * @var     array Classes of MetaMaterial for which there is a showing Metamaterial instance on the current admin page.
     */
    protected static $allShowingTypes = null;

    /**
     * Return or create instance of MetaMaterial.
     * If Instance with this $id exists it will be returned.
     * If no existing instance exists with this id it will be constructed with the provided $config.
     *
     * If config was passed when an instance already exists for the provided $id an exception will be thrown.
     *
     * @param string $id Unique id for this MetaMaterial instance
     * @param string|null $class
     * @return Metamaterial new or existing instance of MetaMaterial
     * @throws MM_Exception
     */
    public function getInstance($id, $class=NULL)
    {
        if(empty($id) || is_numeric($id)){
            throw new MM_Exception('id value is required, and must be a non numeric string',500);
        }

        if(!empty($class)){
            $class = static::namespaceIt(static::unNamespaceIt($class));
        }

        if(is_subclass_of($class, 'HaddowG\MetaMaterial\Metamaterial')){
            if(is_object($class)){
                /** @var Object $class */
                $class = get_class($class);
            }
            /** @var string $class */
            $resolved = static::resolveAlias($class);

            // ensure class array exists
            if(!array_key_exists($class, static::$instances)){
                static::$instances[$class] = array();
            }
            // Check if an instance exists with this key already
            if (!array_key_exists($id, static::$instances[$class])) {

                // instance doesn't exist yet, so create it
                if(is_callable($resolved)){
                    static::$instances[$class][$id] = $resolved();
                }else {
                    static::$instances[$class][$id] = new $resolved();
                }

            }
            // Return the correct instance of this class
            return static::$instances[$class][$id];

        }else{
            throw new MM_Exception('Attempt to instantiate non Metamaterial Class.',500);

        }
    }

    /**
     * Resolve a classname to an alias if one is registered
     *
     * @param string $classname
     * @return callable
     */
    public function resolveAlias($classname){

        if(!in_array($classname,array_keys(static::$registeredAliases))){
            static::$registeredAliases[$classname] = $classname;
        }
        return static::$registeredAliases[$classname];

    }

    /**
     * Register an alias to a classname
     *
     * @param string $classname
     * @param callable $resolution classname or factory method
     */
    public function registerAlias($classname,$resolution){

        if(is_null($resolution) && in_array($classname,array_keys(static::$registeredAliases))) {
            unset(static::$registeredAliases[$classname]);
        }else{
            static::$registeredAliases[$classname] = $resolution;
        }

    }


    /**
     * Assert if Metamaterial instance exists for a specified id and class combination
     *
     * @param $id string metamaterial id
     * @param $class string classname of concrete implementation of Metamaterial
     * @return bool whether instance exists for specified id and class combination
     */
    public function hasInstance($id,$class){

        if(is_subclass_of($class, 'HaddowG\MetaMaterial\Metamaterial')){
            return (array_key_exists($class, static::$instances)) && array_key_exists($id, static::$instances[$class]);
        }

        return false;
    }

    /**
     * Clear all registered metamaterial instances and aliases
     */
    public function purgeInstances(){

        static::$instances = array();
        static::$registeredAliases = array();

    }

    /**
     * Get all registered metamaterial instances.
     *
	 * Instances are grouped into arrays by classname and then keyed by $id
	 *
     * @return array
     */
    public function getInstances(){

        return static::$instances;

    }


    /**
     * Produce a list of the registered metamaterial instances
     * Useful for debugging purposes.
     *
     * @param bool $return pass true to return the list rather than echo
     * @return null|string
     */
    public function listInstances($return=false){

        $str='';

        foreach(static::$instances as $k => $v){
            $str .= "\r\n" . ($k) . "\r\n";
            foreach($v as $mm){
                $str .= ('     ' . get_class($mm)) . "\r\n";
            }
        }

        if($return){
            return $str;
        }else{
            echo($str);
            return null;
        }
    }


    /**
     * Get an array of all metamaterial instances that are showing on the current admin page
     *
     * @param bool $sort true if the list should be sorted to display order or false to return in the order registered.
     *
     * @return Metamaterial[]
     */
    public function getShowing( $sort = TRUE )
    {
        if(!is_null(static::$allShowing) && $sort){
            return static::$allShowing;
        }

		if(is_null(static::$allShowingTypes)){
			static::$allShowingTypes = array();
		}
        $showing = array();
        $priority=array();
        $title = array();
        foreach (static::$instances as $class => $objects) {
            /** @var $objects Metamaterial[] */
            if(reset($objects)->isTargetAdmin()){
                foreach ($objects as $id => $mm) {
                    if($mm->canOutput()){

                        if(!in_array($class,static::$allShowingTypes)){
                            static::$allShowingTypes[]=$class;
                        }

                        $showing[$id] = $mm;

                        if($sort){
                            $context[$id] = $mm->getContext(TRUE) + 1;
                            $priority[$id] = $mm->getPriority(TRUE);
                            $title[$id] = $mm->get_the_title();
                        }
                    }
                }
            }
        }

		if(empty(static::$allShowingTypes)){
			static::$allShowingTypes = null;
		}

        if(count($showing)>0){
            if($sort){
                array_multisort( $context, SORT_ASC, SORT_NUMERIC, $priority, SORT_DESC, SORT_NUMERIC, $title, SORT_ASC, SORT_STRING, $showing);
            }
        }
        if($sort){
            static::$allShowing = $showing;
            return static::$allShowing;
        }else{
            return $showing;
        }
    }

    /**
     * Get the classes of Metamaterial for which there are showing instances on the current admin page
     *
     * @return array
     */
    public function getShowingTypes()
    {
        if(is_null(static::$allShowingTypes)) {
            MMM::getShowing(false);
        }

        return is_null(static::$allShowingTypes)?array():static::$allShowingTypes;

    }


    /**
     * Adds all appropriate metaboxes for the current page from any Instances of MetaMaterial.
     * Registers all necessary actions and filters and initialises each box as appropriate.
     */
    public function globalInit(){

        /** @var $showing Metamaterial[] */
        $showing = MMM::getShowing();

        if(empty($showing)){
            return false;
        }

        foreach($showing as $mm){
            $mm->initWhenShowing();

            $mm->addDefaultFilters();

            $mm->addDefaultActions();

            $mm->doAction('init');
        }

        foreach (MMM::getShowingTypes() as $type) {
            reset(static::$instances[$type])->initOnce();
        }

        return true;
    }

    /**
     * Get global styles for enqueueing
     *
     * @return array style uri's keyed by unique handle ready for enqueueing
     */
    public function getGlobalStyles()
    {
        $styles = array();

        foreach(MMM::getShowingTypes() as $type){
            $styles = array_merge($styles,reset(static::$instances[$type])->getGlobalStyle());
        }

        return apply_filters('metamaterial_filter_global_styles',$styles);

    }

	/**
	 * Gather relevant hide_on_screen_styles for displayed metamaterial instances.
	 */
    public function buildGlobalStyle()
    {
        /** @var $showing MetaMaterial[] */
        $showing = MMM::getShowing();

        if(empty($showing)){
            return '';
        }

        $hide_on_screen = array();
        if (!apply_filters('metamaterial_filter_compound_hide',static::$compoundHide)) {
            $showing = array_shift($showing);
        }

        foreach($showing as $mm){
            foreach($mm->getHideOnScreen() as $k){
                if(!array_key_exists($k, $hide_on_screen)){
                    $styles = $mm::$hide_on_screen_styles;
                    $hide_on_screen[$k]=$styles[$k];
                }
            }
        }

        return implode("\r\n",$hide_on_screen) . "\r\n";
    }

    /**
	 * Check whether instance hide_on_screen values will be combined.
     * @return boolean
     */
    public function isCompoundHide()
    {
        return static::$compoundHide;
    }

    /**
	 * Set whether instance hide_on_screen values will be combined.
	 *
	 * Defaults to TRUE.
	 * If false only the first registered instance's hide_on_screen value will be considered.
	 *
     * @param boolean $compoundHide
     */
    public function setCompoundHide($compoundHide)
    {
        static::$compoundHide = $compoundHide;
    }

	/**
	 * Print the global css style to hide desired on screen elements.
	 *
	 * Combines appropriate styles for all relevant hide_on_screen_styles
	 */
    public function printGlobalStyles(){

        $styles = MMM::buildGlobalStyle();
        $styles = trim($styles);

        $styles = apply_filters('metamaterial_filter_print_global_styles',$styles);

        if(!empty($styles)){
            ?>
            <style type="text/css" id="metamaterial_global_style">
            <?php
                echo $styles;
            ?>
            </style>
            <?php
        }
    }

    /**
     * Get global scripts for enqueueing
     *
     * @return array script uri's keyed by unique handle ready for enqueueing
     */
    public function getGlobalScripts()
    {
        $scripts = array();

        foreach(MMM::getShowingTypes() as $type){
            $scripts = array_merge($scripts,reset(static::$instances[$type])->getGlobalScript());
        }

        return apply_filters('metamaterial_filter_global_scripts',$scripts);
    }

    /**
     * Enqueues global scripts.
	 *
     * Triggered on Wordpress admin_enqueue_scripts hook
     */
    public function globalEnqueue()
    {

        foreach(MMM::getGlobalStyles() as $handle=>$style){
            wp_enqueue_style($handle,$style);
        }
		wp_enqueue_script('mm_utils',Metamaterial::$default_assets_url . 'js/utility.js',array('jquery','jquery-ui-sortable'),null,true);
        foreach(MMM::getGlobalScripts() as $handle=>$script){
            wp_enqueue_script($handle,$script,array('jquery','jquery-ui-sortable','mm_utils'),null,true);
        }

    }

    /**
     * Enqueues scripts for each showing metamaterial instance.
	 *
     * Triggered on Wordpress admin_enqueue_scripts hook
     */
    public function enqueue()
    {
        /** @var $showing MetaMaterial[] */
        $showing = MMM::getShowing();
        foreach($showing as $mm){
            $mm->enqueue();
        }

    }

    /**
     * Used to insert STYLE or SCRIPT tags into the head for each showing metamaterial instance.
	 *
     * Triggered on WordPress admin_head action.
     */
    public function head()
    {
        /** @var $showing MetaMaterial[] */
        $showing = MMM::getShowing();
        foreach($showing as $mm){
            $mm->head();
        }

    }

    /**
     * Used to insert STYLE or SCRIPT tags into the footer for each showing metamaterial instance.
     *
	 * Triggered on WordPress admin_foot action.
     */
    public function foot()
    {
        /** @var $showing MetaMaterial[] */
        $showing = MMM::getShowing();
        foreach($showing as $mm){
            $mm->foot();
        }

    }

	/**
	 * Apply a namespace the provided classname within this package.
	 *
	 * @param $class
	 * @return string
	 */
    public function namespaceIt($class){
        return "HaddowG\\MetaMaterial\\" . $class;
    }

	/**
	 * Remove the namespace from the provided fully qualified classname.
	 *
	 * @param $class
	 * @return mixed
	 */
    public function unNamespaceIt($class){
		$split = explode("\\",$class);
        return array_pop($split);
    }
}