<?php namespace HaddowG\MetaMaterial;

use HaddowG\MetaMaterial\Facades\MMM;

class MetaMaterialManager {

    /**
     * Storage array for instances of MetaMaterial
     * Array is keyed by instance id's.
     *
     * @var		array Array of arrays each of a type of  MetaMaterial instances
     * @see		getInstance(), $id
     */
    private static $instances =array();


    private static $registeredAliases = array();


    /**
     * Array of MetaMaterial instances showing on the current admin page.
     * Cached result of get_showing() to avoid re-execution.
     *
     * @var     array Array of MetaMaterial instances showing on the current admin page
     * @see     getShowing()
     */
    protected static $allShowing = null;

    /**
     * Array of Classes of MetaMaterial for which there is a showing Metamaterial instance on the current admin page.
     * Cached result of get_showing() to avoid re-execution.
     *
     * @var     array Classes of MetaMaterial for which there is a showing Metamaterial instance on the current admin page.
     * @see     getShowing()
     */
    protected static $allShowingTypes = null;

    /**
     * Return or create instance of MetaMaterial.
     * If Instance with this $id exists it will be returned.
     * If no existing instance exists with this id it will be constructed with the provided $config.
     *
     * If config was passed when an instance already exists for the provided $id an exception will be thrown.
     *
     * @param   string $id Unique id for this MetaMaterial instance
     * @param null $class
     * @return Metamaterial new or existing instance of MetaMaterial
     * @throws MM_Exception
     */
    public function getInstance($id, $class=NULL)
    {
        if(empty($id) || is_numeric($id)){
            throw new MM_Exception('id value is required, and must be a non numeric string',500);
        }

        if(!empty($class)){
            $class = self::namespaceIt(self::unNamespaceIt($class));
        }

        if(is_subclass_of($class, 'HaddowG\MetaMaterial\Metamaterial')){
            if(is_object($class)){
                /** @var Object $class */
                $class = get_class($class);
            }
            /** @var string $class */
            $resolved = self::resolveAlias($class);

            // ensure class array exists
            if(!array_key_exists($class, self::$instances)){
                self::$instances[$class] = array();
            }
            // Check if an instance exists with this key already
            if (!array_key_exists($id, self::$instances[$class])) {

                // instance doesn't exist yet, so create it
                if(is_callable($resolved)){
                    self::$instances[$class][$id] = $resolved();
                }else {
                    self::$instances[$class][$id] = new $resolved();
                }

            }
            // Return the correct instance of this class
            return self::$instances[$class][$id];

        }else{
            throw new MM_Exception('Attempt to instantiate non Metamaterial Class or Abstract MetaMaterial Class.',500);

        }
    }

    public function resolveAlias($classname){

        if(!in_array($classname,array_keys(self::$registeredAliases))){
            self::$registeredAliases[$classname] = $classname;
        }
        return self::$registeredAliases[$classname];

    }

    public function registerAlias($classname,$resolution){

        if(is_null($resolution) && in_array($classname,array_keys(self::$registeredAliases))) {
            unset(self::$registeredAliases[$classname]);
        }else{
            self::$registeredAliases[$classname] = $resolution;
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
            return (array_key_exists($class, self::$instances)) && array_key_exists($id, self::$instances[$class]);
        }

        return false;
    }


    public function purgeInstances(){

        self::$instances = array();
        self::$registeredAliases = array();

    }

    /**
     * @param bool $return
     * @return null|string
     */
    public function listInstances($return=false){

        $str='';

        foreach(self::$instances as $k => $v){
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
     *
     * @param bool $sort
     *
     * @return Metamaterial[]
     */
    public static function getShowing( $sort = TRUE )
    {
        if(!is_null(self::$allShowing) && $sort){
            return self::$allShowing;
        }

        $showing = array();
        $priority=array();
        $title = array();
        foreach (self::$instances as $class => $objects) {
            /** @var $objects Metamaterial[] */
            if(reset($objects)->is_target_admin()){
                foreach ($objects as $id => $mm) {
                    if($mm->can_output()){

                        if(!in_array($class,self::$allShowingTypes)){
                            self::$allShowingTypes[]=$class;
                        }

                        $showing[$id] = $mm;

                        if($sort){
                            $context[$id] = $mm->getContext(TRUE) + 1;
                            $priority[$id] = $mm->getPriority(TRUE);
                            $title[$id] = $mm->getTitle();
                        }
                    }
                }
            }
        }
        if(count($showing)>0){
            if($sort){
                array_multisort( $context, SORT_ASC, SORT_NUMERIC, $priority, SORT_DESC, SORT_NUMERIC, $title, SORT_ASC, SORT_STRING, $showing);
            }
        }
        if($sort){
            self::$allShowing = $showing;
            return self::$allShowing;
        }else{
            return $showing;
        }
    }

    public static function getShowingTypes()
    {
        if(is_null(self::$allShowingTypes)) {
            self::getShowing(false);
        }

        return self::$allShowingTypes;

    }


    /**
     * Adds all appropriate metaboxes for the current page from any Instances of MetaMaterial.
     * Registers all necessary actions and filters for each box as appropriate.
     *
     * @since   0.1
     * @access  public
     * @see		is_target_admin(), getShowing(), add_action(), add_filter()
     */

    public static function globalInit(){
        /** @var $showing Metamaterial[] */
        $showing = MMM::getShowing();

        if(empty($showing)){
            return false;
        }

        foreach($showing as $mm){
            $mm->init();

            $mm->addDefaultFilters();

            $mm->addDefaultActions();

            $mm->doAction('init');
        }

        foreach (self::$instances as $inst) {
            /** @var $inst Metamaterial[] */
            foreach($inst as $mm){
                if(in_array($mm,$showing)){
                    $mm->initOnce();
                    break;
                }
            }
        }

        return true;
    }

    public static function namespaceIt($class){
        return 'HaddowG\MetaMaterial\\' . $class;
    }
    public static function unNamespaceIt($class){
        return str_replace('HaddowG\MetaMaterial\\','', $class);
    }
}