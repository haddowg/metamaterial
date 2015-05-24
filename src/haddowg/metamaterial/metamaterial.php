<?php
namespace HaddowG\MetaMaterial;

use stdClass;
use WP_Embed;

if (!defined('PHP_INT_MIN')) {

	/** Minimum possible value of an integer */
	define('PHP_INT_MIN', ~PHP_INT_MAX);

}


/**
 * Class Metamaterial
 *
 * @package     MetaMaterial
 * @author      Gregory Haddow
 * @copyright   Copyright (c) 2014, Gregory Haddow, http://www.greghaddow.co.uk/
 * @license     http://opensource.org/licenses/gpl-3.0.html The GPL-3 License with additional attribution clause as detailed below.
 * @version     0.1
 * @link        http://www.greghaddow.co.uk/MetaMaterial
 *
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
 */
abstract class Metamaterial
{
    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONSTANTS
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /** mode option */
    const STORAGE_MODE_ARRAY = 'array';

     /** mode option */
    const STORAGE_MODE_EXTRACT = 'extract';

    /** field type hint */
    const HINT_SELECT_MULTI = 'select_multi';

    /** field type hint */
    const HINT_CHECKBOX_MULTI = 'checkbox_multi';

    /** priority option numeric equivalent */
	const PRIORITY_TOP = PHP_INT_MAX;

    /** priority option numeric equivalent */
    const PRIORITY_HIGH = 150;

    /** priority option numeric equivalent */
    const PRIORITY_CORE = 100;

    /** priority option numeric equivalent */
    const PRIORITY_DEFAULT = 50;

    /** priority option numeric equivalent */
    const PRIORITY_LOW = 0;

    /** priority option numeric equivalent */
	const PRIORITY_BOTTOM = PHP_INT_MIN;

    /** default delete confirmation message */
	const DEFAULT_DELETE_CONFIRM = 'This action can not be undone, are you sure?';

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONFIGURABLE OPTIONS/VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

	/**
	 * User defined unique identifier for this instance.
	 * Required for instantiation.
     *
	 * @since	0.1
	 * @access	protected
	 * @var		string identifier for this instance, required.
	 */
	protected $id;

	/**
	 * Used to set the title of the metabox.
	 * Required for instantiation.
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		string the title of the metabox
	 * @see		$hide_title
	 */
    protected $title = 'Custom Meta';

	/**
	 * Used to set the metabox template file, the contents of your metabox should be
	 * defined within this file.
     * Required for instantiation.
	 *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		string metabox template file, required
	 */
    protected $template;


	/**
     * The part of the page where the metabox should be shown.
	 * 'before_title', 'after_title', 'after_editor', 'normal', 'advanced' or 'side'
     * Defaults to 'normal'
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		string metabox page context
	 */
	protected $context = 'normal';

	/**
     * The priority within the context where the boxes should show
	 * 'top', 'high', 'core', 'default', 'low' or 'bottom'
     * Defaults to 'high'
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		string metabox priority within page context
	 */
	protected $priority = 'high';

	/**
	 * Used to set how the class stores this metaboxes data.
     * The following class constants should be used to set this option:
     *
     * STORAGE_MODE_ARRAY (default) - Data will be stored as an associative array in a single meta entry in the wp_postmeta table.
     *
	 * STORAGE_MODE_EXTRACT - Data will be saved as individual entries in the wp_postmeta table,
     * an additional 'fields' meta will be saved to indicate the fields that are present and to speed retrieval.
	 *
     * Config Option
     *
     * @todo    maybe add a STORAGE_MODE_TABLE option to store/retrieve values from a dedicated table. would require type hinting and greater validation, how to handle nesting without serialize?
	 * @since	0.1
	 * @access	protected
	 * @var		string either STORAGE_MODE_ARRAY or STORAGE_MODE_EXTRACT to indicate desired storage mode.
     * @see     $meta_key, $prefix, STORAGE_MODE_ARRAY, STORAGE_MODE_EXTRACT
	 */
	protected $mode = self::STORAGE_MODE_ARRAY;

    /**
     * User defined meta key for use with STORAGE_MODE_ARRAY, or as an optional prefix for keys when using STORAGE_MODE_EXTRACT.
     * Prefix with an underscore to prevent fields(s) from showing up in the custom fields metabox.
     * Will default to underscore prefixed $id if not provided.
     *
     * Config Option
     *
     * @since   0.1
     * @access  protected
     * @var     string key to use for meta storage when using STORAGE_MODE_ARRAY, or as an optional prefix for keys when using STORAGE_MODE_EXTRACT
     * @see     $mode, $prefix, STORAGE_MODE_ARRAY, STORAGE_MODE_EXTRACT
     */
    protected $meta_key;

	/**
	 * When the mode option is set to STORAGE_MODE_EXTRACT, you have to take
	 * care to avoid name collisions with other meta entries. Use this option to
	 * automatically prefix your variables with the value of $meta_key.
	 * Defaults to TRUE to help prevent name collisions.
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		bool whether to prefix keys when using STORAGE_MODE_EXTRACT
     * @see     $mode, $meta_key, STORAGE_MODE_ARRAY, STORAGE_MODE_EXTRACT
	 */
    protected $prefix = FALSE;

    /**
	 * Used to hide the default elements on the page.
     * Array of named elements, possible values include:
     * permalink, the_content, excerpt, custom_fields, discussion, comments, slug, author,
     * format, featured_image, revisions, categories, tags, send-trackbacks
     *
	 * If $compound_hide is set to FALSE then only the first showing metaboxes values will be considered,
     * otherwise they are combined to determine what should be hidden.
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		array Array of named elements to be hidden, see description for possible values.
     * @see     $compound_hide, $hide_on_screen_styles
	 */
	protected $hide_on_screen;

    /**
     * If FALSE only the first showing metaboxes $hide_on_screen values will be considered.
     * When this is the default TRUE all showing metaboxes have their $hide_on_screen values
     * combined to determine what should be hidden.
     *
     * Config Option
     *
     * @since   0.1
     * @access  protected
     * @var     boolean whether to compound $hide_on_screen values
     * @see     $hide_on_screen
     */
    protected static $compound_hide = TRUE;

	/**
	 * Callback function triggered on the WordPress "current_screen" action
     * Callback is executed only when the metabox is present.
	 *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable
     * @see     global_init()
	 */
    protected $init_action;

	/**
	 * Output Filter function callback used to override when the meta box gets displayed.
     *
     * Filter function should return boolean to determine if the metabox should or should not be displayed.
     * Filter function should accept 3 arguments:
     *  - bool $can_output - the current can_output() return value, i.e. the result of any existing filtering.
     *  - int $post_id - the post id of post being displayed
     *  - MetaMaterial $MetaMaterial - this MetaMaterial Object.
	 *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable see description for provided arguments and expected return
	 * @see		prep(), can_output()
	 */
    protected $output_filter;

	/**
	 * Save Filter function callback used to override or insert meta data before saving.
     *
     * Filter function should return modified array of metabox data, or you can abort saving by returning FALSE.
	 * Filter function should accept 2 arguments:
     *  - array $meta metabox data
	 *	- int $post_id - the post id of post being displayed
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable see description for provided arguments and expected return
	 * @see		$save_action, add_filter(), global_init()
	 */
    protected $save_filter;

	/**
	 * @var
	 */
	protected $ajax_save_success_filter;

	/**
	 * @var
	 */
	protected $ajax_save_fail_filter;

	/**
	 * Callback function triggered after this metabox completes saving.
     *
     * Callback function should accept 2 arguments:
     * - array $meta metabox data that was saved
	 * - int $post_id - the post id of post metadata was saved to
	 *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable save callback
	 * @see		$save_filter, add_filter(), global_init()
	 */
    protected $save_action;

	/**
	 * Callback used to insert STYLE or SCRIPT tags into the head.
     * Callback is executed only when the metabox is present.
	 * Called with lower than default priority so other script/style dependencies should be present if enqueued.
     *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable head callback for script/style inclusion
	 * @see		head(), add_action(), global_init()
	 */
    protected $head_action;

	/**
	 * Callback used to insert SCRIPT tags into the footer.
     * Callback is executed only when the metabox is present.
	 * Called with lower than default priority so other script/style dependencies should be present if enqueued.
	 *
     * Config Option
     *
	 * @since	0.1
	 * @access	protected
	 * @var		callable foot callback for script/style inclusion
	 * @see		foot(), add_action(), global_init()
	 */
    protected $foot_action;


	/**
	 * @var string url for assets loaded by this Metamaterial instance
	 */
	protected $assets_url = false;

	/**
	 * @var string default url for assets loaded by this Metamaterial instance
	 */
	public static $default_assets_url = false;

	/**
	 * @var string path for assets loaded by this Metamaterial instance
	 */
	protected $assets_path = false;

	/**
	 * @var string default path for assets loaded by this Metamaterial instance
	 */
	public static $default_assets_path = false;

	/**
	 * @var bool
	 */
	protected $ajax_save = true;

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
	 * Storage array for instances of MetaMaterial
	 * Array is keyed by instance id's.
     *
	 * @since	0.1
	 * @access	private
	 * @var		array Array of arrays each of a type of  MetaMaterial instances
	 * @see		getInstance(), $id, __construct()
	 */
    private static $instances =array();


    private static $registeredDependencies = array();

    /**
	 * Cached value of can_output(), to prevent re-execution.
	 *
	 * @since	0.1
	 * @access	private
	 * @var		string
	 */
    protected $will_show = null;

    /**
	 * Cached value of meta(), to prevent re-execution internally.
	 *
	 * @since	0.1
	 * @access	private
	 * @var		array
     * @see     meta()
	 */
	protected $meta  = array();

    /**
     * Stores current field name in template
     *
     * @since   0.1
     * @access  private
     * @var     string current field name
     * @see     the_name(), get_the_name()
     */
	private $name;

	/**
	 * Used to provide field type hinting
	 *
	 * @since	0.1
	 * @access	private
	 * @var		string
	 * @see		the_field(), HINT_CHECKBOX_MULTI, HINT_SELECT_MULTI
	 */
	private $hint;

    /**
     * Stores length of current loop in template
     *
     * @since   0.1
     * @access  private
     * @var     int length of current loop
     */
	private $length = 0;

    /**
     * Stores current index within current loop in template
     *
     * @since   0.1
     * @access  private
     * @var     int current index within current loop
     */
	private $current = -1;

    /**
     * If we are currently in a loop in the template
     *
     * @since   0.1
     * @access  private
     * @var     boolean If we are currently in a loop in the template
     */
	private $in_loop = FALSE;

    /**
     * If we are currently in a template
     *
     * @since   0.1
     * @access  private
     * @var     boolean If we are currently in a template
     */
	protected $in_template = FALSE;

    /**
     * Html group tag for current loop
     *
     * @since   0.1
     * @access  private
     * @var     string Html group tag for current loop
     */
	private $group_tag;

    /**
     * Html loop tag for current loop container
     *
     * @since   0.1
     * @access  private
     * @var     string Html loop tag for current loop
     */
    private $loop_tag;

	/**
	 * Used to store current loop details, cleared after loop ends
	 *
	 * @since	0.1
	 * @access	private
	 * @var		stdClass
	 * @see		have_fields_and_multi(), have_fields()
	 */
	private $loop_data;

    /**
	 * Used to store loop stack for
	 *
	 * @since	0.1
	 * @access	private
	 * @var		array of MM_Loop objects indexed by group name/id
	 * @see		MM_Loop
	 */
    private $loop_stack = array();

    /**
     * Array of MetaMaterial instances showing on the current admin page.
     * Cached result of get_showing() to avoid re-execution.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of MetaMaterial instances showing on the current admin page
     * @see     get_showing()
     */
    protected static $allshowing = null;

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of admin page targets on which this MetaMaterial Class is designed to display
     * @see     is_target_admin()
     */
    protected static $admin_targets = array();


    /**
     * Array of priorities with numerical equivalents.
     * Used to order metaboxes within a page context.
     * Plugin/Theme developers should avoid default use of 'top' or 'high' to allow end users to more easily adjust as desired.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of priorities with numerical equivalents.
     * @see     is_target_admin()
     */
	protected static $priorities = array(
									'top' => self::PRIORITY_TOP,
									'high' => self::PRIORITY_HIGH,
									'core' => self::PRIORITY_CORE,
									'default' => self::PRIORITY_DEFAULT,
									'low' => self::PRIORITY_LOW,
									'bottom' => self::PRIORITY_BOTTOM );

    /**
     * Array of contexts in order that they are displayed.
     * Used to order metaboxes within a page context.
     * Plugin/Theme developers should avoid default use of 'top' or 'high' to allow end users to more easily adjust as desired.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of priorities with numerical equivalents.
     * @see     is_target_admin()
     */
    protected static $contexts = array(
									'normal',
                                    'advanced',
                                    'side');

	/**
	 * @var array
	 */
	protected static $hide_on_screen_styles = array();


    protected function construct($id, $config = array())
    {
        if(get_called_class()==='HaddowG\Metamaterial\Metamaterial'){
            throw new MM_Exception('Attempt to instantiate Abstract MetaMaterial Class',500);
        }

        if (is_array($config)) {

            $config_defaults = array
            (
                'title' => $this->title,
                'template' => $this->template,
                'context' => $this->context,
                'priority' => $this->priority,
                'ajax_save' => $this->ajax_save,
                'mode' => $this->mode,
                'meta_key' => '_' . $id,
                'prefix' => $this->prefix,
                'hide_on_screen' => $this->hide_on_screen,
                'init_action' => $this->init_action,
                'output_filter' => $this->output_filter,
                'save_filter' => $this->save_filter,
                'save_action' => $this->save_action,
                'head_action' => $this->head_action,
                'foot_action' => $this->foot_action
            );


            //discard non config options and merge with defaults
            $conf = array_merge($config_defaults, array_intersect_key($config, $config_defaults));

            //set instance config options
            foreach ($conf as $n => $v) {
                $this->$n = $v;
            }

            if (isset($config['compound_hide'])) {
                self::$compound_hide = $config['compound_hide'];
            }
        }else{
            throw new MM_Exception('provided config was not a valid array',500);
        }
    }

    /**
     * Return or create instance of MetaMaterial.
     * If Instance with this $id exists it will be returned.
     * If no existing instance exists with this id it will be constructed with the provided $config.
     *
     * If config was passed when an instance already exists for the provided $id an exception will be thrown.
     *
     * @since   0.1
     * @access  public
     *
     * @param   string $id Unique id for this MetaMaterial instance
     * @param   array $config Configuration options for this instance, see individual option documentation
     * @param   Metamaterial|string $type optional class name or object of class extending Metamaterial.
     * @return  Metamaterial new or existing instance of MetaMaterial
     * @throws  MM_Exception
     * @see     __construct()
     */
	final public static function getInstance($id, $config = array(),$class=NULL)
    {

        if(empty($class) || (!is_subclass_of($class, 'HaddowG\MetaMaterial\Metamaterial'))){
            $class = get_called_class();
        }

        if(is_subclass_of($class, 'HaddowG\MetaMaterial\Metamaterial')){
            if(is_object($class)){
                $class = get_class($class);
            }

            $resolved = self::resolveDependancy($class);

            // ensure type array exists
            if(!array_key_exists($class, self::$instances)){
                self::$instances[$class] = array();
            }
            // Check if an instance exists with this key already
            if (!array_key_exists($id, self::$instances[$class])) {

                // instance doesn't exist yet, so create it
                if(is_callable($resolved)){
                    self::$instances[$class][$id] = $resolved($id, $config);
                }else {
                    self::$instances[$class][$id] = new $resolved($id, $config);
                }
                $newInst = self::$instances[$class][$id];
                /** @var $newInst Metamaterial */
                //init data
                $newInst->id = $id;
                $newInst->loop_data = new stdClass();

                //check minimum requirements
                //check valid id
                if (empty($newInst->id)) throw new MM_Exception('Metabox id required',500);
                if (is_numeric($newInst->id)) throw new MM_Exception('Metabox id must be a non numeric string',500);

                //check valid template
                if (empty($newInst->template)) throw new MM_Exception('Metabox template file required',500);
                //if the template is not found
                if(!file_exists($newInst->template)){
                    //try relative to the default /metabox/ directory in the theme folder
                    if(file_exists(get_stylesheet_directory() . '/metaboxes/' . $newInst->template)){
                        $newInst->template = get_stylesheet_directory() . '/metaboxes/' . $newInst->template;
                    }else{
                        throw new MM_Exception('Unable to locate Metabox template',500);
                    }
                }

                $newInst->initInstanceActions();

            } else {
                if (!empty($config)) {
                    throw new MM_Exception('Attempted to pass config to existing instance of MetaMaterial',500);
                }
            }
            // Return the correct instance of this class
            return self::$instances[$class][$id];

        }else{
            throw new MM_Exception('Attempt to instantiate non Metamaterial Class or Abstract MetaMaterial Class.',500);

        }
    }

    final public static function hasInstance($id,$type){
        // ensure type array exists
        return (array_key_exists($type, self::$instances)) && array_key_exists($id, self::$instances[$type]);
    }


    final public static function resolveDependancy($classname){

        if(!in_array($classname,array_keys(self::$registeredDependencies))){
            $registeredTypes[$classname] = $classname;
        }
        return self::$registeredDependencies[$classname];

    }

    public static function registerDependancy($classname,$resolution){
        self::$registeredDependencies[$classname] = $resolution;
    }

    public function initInstanceActions(){

        //these are added only once, the first time a MetaMaterial is constructed, therefore they run only once for all instances.
        $this->add_action('admin_head', 'HaddowG\MetaMaterial\MM_Base::global_head', 10, 1, FALSE, FALSE);
        $this->add_action('admin_footer', 'HaddowG\MetaMaterial\MM_Base::global_foot', 10, 1, FALSE, FALSE);

        //register output filters on 'admin_init' so they are available before global_init() runs on the 'current_screen' hook.
        add_action('admin_init', array($this,'prep'));

        //this is added only once the first time a MetaMaterial is constructed, therefore runs only once for all instances.
        $this->add_action('current_screen', 'HaddowG\MetaMaterial\MM_Base::global_init',10,1,FALSE,FALSE);

        //header and footer actions will be fired only for target admin pages
        $this->add_action('admin_head', array($this, 'head'), 11, 1, FALSE, TRUE);
        $this->add_action('admin_footer', array($this,'foot'), 11, 1, FALSE, TRUE);
        if($this->ajax_save){
            add_action('wp_ajax_' . $this->get_action_tag('ajax_save'), array($this, 'ajax_save'));
        }
    }

	/**
	 * Simple accessor for this metaboxes title
	 *
	 * @since   0.1
     * @access  public
	 * @return	string the metaboxes title
	 */
	public function get_title(){
		return $this->title;
	}

	/**
	 * Accessor for this metaboxes context.
	 * Optionally return the numeric equivalent for use in sorting by passing TRUE as a parameter
	 *
	 * @since   0.1
     * @access  public
	 * @param	boolean	$numeric whether to return numeric equivalent rather than text value
	 * @return	string|int the metaboxes context as text or integer
	 */
	public function get_context($numeric = FALSE){
		$cntxt = array_search($this->context,static::$contexts);
        if($numeric){
            if ($cntxt!==FALSE) {
                return $cntxt;
            } else {
                return PHP_INT_MAX;
            }
        }else{
			if ($cntxt!==FALSE) {
				return $this->context;
			}else{
				return 'normal';
			}
        }
	}

    /**
     * Accessor for this metaboxes priority.
     * Optionally return the numeric equivalent for use in sorting by passing TRUE as a parameter
     *
     * @since   0.1
     * @access  public
     * @param   boolean $numeric whether to return numeric equivalent rather than text value
     * @param   boolean $sanitized if the textual version needs sanitizing for wordpress internal use
     * @return    string|int the metaboxes priority as text or integer
     */
	public function get_priority($numeric = FALSE,$sanitized=TRUE){
        $p=FALSE;
		if($numeric){
			if(is_numeric($this->priority)){
				return $this->priority;
			} elseif (array_key_exists($this->priority,static::$priorities)) {
				return static::$priorities[$this->priority];
			}

			return self::PRIORITY_BOTTOM;
		}else{
			if (is_numeric($this->priority)) {
				foreach(static::$priorities as $k => $v){
					if ($this->priority > $v) {
						$p= $k;
					}
				}
			} elseif (array_key_exists($this->priority,static::$priorities)){
				$p=  $this->priority;
			}else{
			    $p= 'bottom';
            }
            if($sanitized){
                if($p=='top'){
                    $p='high';
                }elseif($p=='bottom'){
                    $p='low';
                }
            }
            return $p;
		}
	}

	/**
	 * Simple accessor for this metaboxes save_filter
	 *
	 * @since   0.1
     * @access  public
	 * @return	callable|null the metaboxes save_filter
	 * @see		save(), add_filter()
	 */
    public function get_save_filter(){
        return $this->save_filter;
    }

	/**
	 * @return mixed
	 */
	public function get_ajax_save_success_filter(){
        return $this->ajax_save_success_filter;
    }

	/**
	 * @return mixed
	 */
	public function get_ajax_save_fail_filter(){
        return $this->ajax_save_fail_filter;
    }
	/**
	 * Simple accessor for this metaboxes save_action
	 *
	 * @since   0.1
     * @access  public
	 * @return	callable|null the metaboxes save_action
	 * @see		$save_action, save(), add_action()
	 */
    public function get_save_action(){
        return $this->save_action;
    }

	/**
	 * Simple accessor for this metaboxes head_action
	 *
	 * @since   0.1
     * @access  public
	 * @return	callable|null the metaboxes head_action
	 * @see		$head_action, head(), add_action()
	 */
    public function get_head_action(){
        return $this->head_action;
    }

	/**
	 * Simple accessor for this metaboxes foot_action
	 *
	 * @since   0.1
     * @access  public
	 * @return	callable|null the metaboxes foot_action
	 * @see		$foot_action, foot(), add_action()
	 */
    public function get_foot_action(){
        return $this->foot_action;
    }

	/**
	 * Simple accessor for this metaboxes init_action
	 *
	 * @since   0.1
     * @access  public
	 * @return	callable|null the metaboxes init_action
	 * @see		$init_action, global_init(), add_action()
	 */
    public function get_init_action(){
        return $this->init_action;
    }


	/**
	 * @return mixed
	 */
	protected abstract function init();

	/**
	 * @return mixed
	 */
	protected abstract function init_once();

	/**
	 * Adds all appropriate metaboxes for the current page from any Instances of MetaMaterial.
	 * Registers all necessary actions and filters for each box as appropriate.
	 *
	 * @since   0.1
     * @access  public
	 * @see		is_target_admin(), get_showing(), add_action(), add_filter()
	 */

	public static function global_init(){

		$showing = self::get_showing();

		if(empty($showing)){
			return;
		}

		foreach($showing as $mm){
            $mm->init();

            $filters = array('save');

            if($mm->ajax_save){
                $filters[]='ajax_save_success';
                $filters[]='ajax_save_fail';
            }

            foreach ($filters as $filter)
            {
                $var = 'get_' . $filter . '_filter';
                $fltr = $mm->$var();

                if (!empty($fltr))
                {
                    if ('save' == $filter)
                    {
                        $mm->add_filter($filter, $fltr, 10, 3);
                    }
                    else
                    {
                        $mm->add_filter($filter, $fltr);
                    }
                }
            }

            $actions = array('save', 'head', 'foot', 'init');

            foreach ($actions as $action)
            {
                $var = 'get_' . $action . '_action';
                $actn = $mm->$var();

                if (!empty($actn))
                {
                    if ('save' == $action)
                    {
                        $mm->add_action($action, $actn, 10, 3);
                    }
                    else
                    {
                        $mm->add_action($action, $actn);
                    }
                }
            }



            if ($mm->has_action('init'))
            {
                $mm->do_action('init');
            }
		}
        foreach (self::$instances as $inst) {
            /** @var $inst Metamaterial[] */
            if(reset($inst)->is_target_admin()){
                reset($inst)->init_once();
            }
        }
	}

	/**
	 * Used to initialize the metabox's output filter, runs on WordPress admin_init action.
	 * This runs before the global_init() runs on the current_screen action to ensure we can
	 * correctly determine if a box should be showing or not.
	 *
	 * @since	0.1
	 * @access	public
	 */
	public function prep()
	{

		if ( ! empty($this->output_filter))
		{
			$this->add_filter('output', $this->output_filter,10,3);
		}

	}

	/**
	 * Used to insert STYLE or SCRIPT tags into the head.
	 * called on WordPress admin_head action.
	 *
	 * @since	0.1
	 * @access	public
	 * @see		$head_action, foot()
	 */
	public function head()
	{
		// action: head
		if ($this->has_action('head'))
		{
			$this->do_action('head');
		}
	}

	/**
	 * Used to insert SCRIPT tags into the footer.
	 * called on WordPress admin_footer action.
	 *
	 * @since	0.1
	 * @access	public
	 * @see	    $foot_action, head()
	 */
	public function foot()
	{
		// action: foot
		if ($this->has_action('foot'))
		{
			$this->do_action('foot');
		}
	}

	/**
	 * Render a metabox from template file.
     * Exposes global post, Metamaterial instance and meta array to template.
	 * Appends nonce for verification.
     *
	 * @since	0.1
	 * @access	protected
	 * @see		global_init()
	 */
	public abstract function render();

	/**
	 * Used to properly prefix filter tags.
     * Ensures filter tags are unique to this metabox instance
	 *
	 * @since	0.1
	 * @access	protected
	 * @param	string $tag name of the filter
	 * @return	string uniquely prefixed tag name
	 */
	protected function get_filter_tag($tag)
	{
		$prefix = 'metamaterial_filter_' . $this->id . '_';
		$prefix = preg_replace('/_+/', '_', $prefix);

		$tag = preg_replace('/^'. $prefix .'/i', '', $tag);
		return $prefix . $tag;
	}

    /**
     * Wrapper for WordPress add_filter() function.
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress add_filter()
     *
     * @since   0.1
     * @access  public
     * @param   string $tag tag name for the filter
     * @param   Callable $function_to_add filter function
     * @param   int $priority filter priority
     * @param   int $accepted_args filter accepted arguments
     */
	public function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
	{
		$tag = $this->get_filter_tag($tag);;
		add_filter($tag, $function_to_add, $priority, $accepted_args);
	}

    /**
     * Wrapper for WordPress has_filter() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress has_filter()
     *
     * @since   0.1
     * @access  public
     * @param   string $tag tag name for the filter to check for
     * @param   Callable|boolean $function_to_check optional function to check for existing filter for
     * @return  int|boolean priority of the filter if it exists for the given function, or boolean if any filter exists for the given tag if no $function_to check ias provided.
     */
	public function has_filter($tag, $function_to_check = FALSE)
	{
		$tag = $this->get_filter_tag($tag);
		return has_filter($tag, $function_to_check);
	}

    /**
     * Wrapper for WordPress apply_filters() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress apply_filters()
     *
     * @since   0.1
     * @access  public
     * @param    $tag
     * @param    $value
     * @return mixed
     */
	public function apply_filters($tag, $value)
	{
		$args = func_get_args();
		$args[0] = $this->get_filter_tag($tag);
		return call_user_func_array('apply_filters', $args);
	}

    /**
     * Wrapper for WordPress remove_filter() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress remove_filter()
     *
     * @since   0.1
     * @access  public
     * @param    $tag
     * @param    $function_to_remove
     * @param int $priority
     * @param int $accepted_args
     * @return bool
     */
	public function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1)
	{
		$tag = $this->get_filter_tag($tag);
		return remove_filter($tag, $function_to_remove, $priority, $accepted_args);
	}

	/**
	 * Used to properly prefix an action tag, making the tag is unique to this metabox instance
	 *
	 * @since	0.1
	 * @access	protected
	 * @param	string $tag name of the action
	 * @return	string uniquely prefixed tag name
	 */
	protected function get_action_tag($tag)
	{
		$prefix = 'metamaterial_action_' . $this->id . '_';
		$prefix = preg_replace('/_+/', '_', $prefix);
		$tag = preg_replace('/^'. $prefix .'/i', '', $tag);

		return $prefix . $tag;
	}

    /**
     * Hooks a function to a specific action
     * By default actions are automatically prefixed to make them unique to this metabox, and are suffixed
     *
     * @param $tag
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @param bool $prefix
     * @param bool $suffixes
     * @param bool $once
     */
    protected function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1, $prefix = TRUE, $suffixes = FALSE, $once = TRUE)
	{
        if($suffixes && empty($suffixes)){
            $suffixes = static::$admin_targets;
        }

        if($prefix){
            $tag = $this->get_action_tag($tag);
        }

        if (!empty($suffixes) && is_array($suffixes)) {
            foreach ($suffixes as $sfx) {
                if ($once && !has_action($tag.'-'.$sfx, $function_to_add)) {
                    add_action($tag.'-'.$sfx, $function_to_add, $priority, $accepted_args);
                }
            }
        } else {
            if ($once && !has_action($tag, $function_to_add)) {
               add_action($tag, $function_to_add, $priority, $accepted_args);
            }
        }
    }

	/**
	 * Uses WordPress has_action() function, see WordPress has_action()
	 *
	 * @since    0.1
	 * @access    public
	 * @link    http://core.trac.wordpress.org/browser/trunk/wp-includes/plugin.php#L492
	 *
	 * @param $tag
	 * @param bool $function_to_check
	 *
	 * @return bool|int
	 */
	public function has_action($tag, $function_to_check = FALSE)
	{
		$tag = $this->get_action_tag($tag);
		return has_action($tag, $function_to_check);
	}

	/**
	 * Uses WordPress remove_action() function, see WordPress remove_action()
	 *
	 * @since    0.1
	 * @access    public
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param int $priority
	 * @param int $accepted_args
	 *
	 * @return bool
	 */
	public function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1)
	{
		$tag = $this->get_action_tag($tag);
		return remove_action($tag, $function_to_remove, $priority, $accepted_args);
	}

	/**
	 * Uses WordPress do_action() function, see WordPress do_action()
	 *
	 * @since    0.1
	 * @access    public
	 *
	 * @param $tag
	 * @param string $arg
	 *
	 * @return mixed
	 */
	public function do_action($tag, $arg = '')
	{
		$args = func_get_args();
		$args[0] = $this->get_action_tag($tag);
		return call_user_func_array('do_action', $args);
	}

	/**
	 * Used to check if we are on a target admin page
	 *
	 * @static
	 * @since	0.1
	 * @access	private
	 * @return	bool if this is a target admin page
	 */
	protected static function is_target_admin()
	{
        global $hook_suffix;

        if (in_array($hook_suffix, static::$admin_targets)) {
            return TRUE;
        }

        return FALSE;
	}

	/**
	 * @return mixed
	 */
	public abstract function can_output();

    /**
     *
     * @param bool $sort
     *
	 * @return Metamaterial[]
     */
    final public static function get_showing( $sort = TRUE )
    {
        if(!is_null(self::$allshowing) && $sort){
            return self::$allshowing;
        }

        $showing = array();
        $priority=array();
        $title = array();
        foreach (self::$instances as $inst) {
            /** @var $inst Metamaterial[] */
            if(reset($inst)->is_target_admin()){
                foreach ($inst as $id => $mm) {
                    if($mm->can_output()){
                        $showing[$id] = $mm;
                        if($sort){
                            $context[$id] = $mm->get_context(TRUE) + 1;
                            $priority[$id] = $mm->get_priority(TRUE);
                            $title[$id] = $mm->get_title();
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
            self::$allshowing = $showing;
            return self::$allshowing;
        }else{
            return $showing;
        }
    }

	/**
	 * @return array
	 */
	public function get_hide_on_screen()
    {
        if (empty($this->hide_on_screen)) {
            $this->hide_on_screen = array();
        }
        return $this->hide_on_screen;
    }

	/**
	 * @return string
	 */
	private static function get_global_styles()
    {
        $styles = array();
        foreach(self::$instances as $inst){
            /** @var $inst Metamaterial[] */
            if(!empty($inst)){
                $styles= array_merge($styles, reset($inst)->get_global_style());
            }
        }
        return apply_filters('metamaterial_filter_global_styles',$styles);

    }

    private static function print_global_styles(){
        $styles = self::get_global_styles();
        if(!empty($styles) && is_array($styles)){
        ?>
            <style type="text/css" id="metamaterial_global_style"><?php echo implode("\r\n",$styles); ?></style>
        <?php
        }
    }


	public function get_asset_url($asset){

		if( $this->assets_url && $this->assets_path ){
			if(file_exists($this->assets_path . $asset)) {
				return $this->assets_url . $asset;
			}
		}

		if(static::$default_assets_url && static::$default_assets_path) {
			if ( file_exists( static::$default_assets_path . $asset ) ) {
				return static::$default_assets_url  . $asset;
			}
		}

		if( file_exists(trailingslashit( get_stylesheet_directory_uri()) . $asset )){
			return trailingslashit( get_stylesheet_directory()) . $asset;
		}

		return false;
	}

	/**
	 *
	 */
	private static function print_global_scripts()
	{
		?>
		<script type="text/javascript">
			var METAMATERIAL = {};
		</script>
		<?php
        $scripts = self::get_global_scripts();
        if(!empty($scripts) && is_array($scripts)) {
            foreach ($scripts as $script) {
            ?>
            <script type="text/javascript" src="<?php echo $script ?>"></script>
            <?php
            }
        }
	}

	/**
	 * Gathers the global scripts for each active Metamaterial instance
	 * @return array
	 */
	private static function get_global_scripts()
    {
        $scripts = array();
	    //foreach Metamaterial class
        foreach(self::$instances as $inst){
            /** @var $inst Metamaterial[] */
	        //if there is an instance
            if(!empty($inst)){
	            //get the global script for the first instance only as they should all be the same.
                $script = reset($inst)->get_global_script();
	            if($script){
		            //add to the global scripts collection
		            $scripts[] = $script;
	            }
            }
        }
		//return through filter to allow possible modification
        return apply_filters('metamaterial_filter_global_scripts',$scripts);
    }

	/**
	 * @return mixed
	 */
	public abstract function get_global_style();

	/**
	 * @return bool|string
	 */
	public function get_global_script()
	{

		// must be a targeted admin page
		if (!self::is_target_admin()) {
			return false;
		}
		$script = $this->get_asset_url($this->get_global_script_name());
		if(file_exists($script)){
			return $script;
		}else{
			return false;
		}
	}

	public function get_global_script_name()
	{
		return 'js/' . get_called_class() . '/global.js';
	}


	/**
	 * @return string
	 */
	public static function build_global_style()
    {
        $style='';

        $showing = self::get_showing();

        if(empty($showing)){
			return '';
		}

        $hide_on_screen = array();
        if (static::$compound_hide) {
            foreach($showing as $mm){
                foreach($mm->get_hide_on_screen() as $k){
                    if(!in_array($k, $hide_on_screen)){
                        $hide_on_screen[]=$k;
                    }
                }
            }
        } else {
            $hide_on_screen = reset($showing)->get_hide_on_screen();
        }

        foreach ($hide_on_screen as $hide) {
            if (array_key_exists($hide,static::$hide_on_screen_styles)){
                $style.= static::$hide_on_screen_styles[$hide];
            }
        }

        return $style;
    }


	/**
	 * Used to insert global STYLE or SCRIPT tags into the head, called on
	 * WordPress admin_head action.
	 *
	 * @static
	 * @since	0.1
	 * @access	private
	 * @see		global_foot()
	 */
	static function global_head()
	{
        self::print_global_styles();
		self::print_global_scripts();
	}

	/**
	 * Used to insert global SCRIPT tags into the footer, called on WordPress
	 * admin_footer action.
	 *
	 * @static
	 * @since	0.1
	 * @access	public
	 * @see		global_head()
	 */
	public static function global_foot()
	{

	}

	/**
	 * Gets the meta data for a meta box
	 *
	 * @since	0.1
	 * @access	public
	 * @param	int $post_id optional post ID for which to retrieve the meta data
	 * @return	array
	 * @see		meta
	 */
	function the_meta($post_id = NULL)
	{
		return $this->meta($post_id);
	}

	/**
	 * Gets the meta data for a meta box
	 *
	 * Internal method calls will typically bypass the data retrieval and will
	 * immediately return the current meta data
	 *
	 * @since	0.1
	 * @access	private
	 * @param	int $object_id optional post ID for which to retrieve the meta data
	 * @param	bool $internal optional boolean if internally calling
	 * @return	array
	 * @see		the_meta()
	 */
    public abstract function meta($object_id = NULL, $internal = FALSE);


	// user can also use the_ID(), php functions are case-insensitive
	/**
	 * @since	0.1
	 * @access	public
	 */
	function the_id()
	{
		echo $this->get_the_id();
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function get_the_id()
	{
		return $this->id;
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param $n
	 * @param null $hint
	 */
	function the_field($n, $hint = NULL)
	{
		$this->name = $n;
		$this->hint = $hint;
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $n
	 *
	 * @return bool
	 */
	function have_value($n = NULL)
	{
		if ($this->get_the_value($n)) return TRUE;

		return FALSE;
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $n
	 */
	function the_value($n = NULL)
	{
		echo $this->get_the_value($n);
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $n
	 * @param bool $collection
	 *
	 * @return array|null|string
	 */
	function get_the_value($n = NULL, $collection = FALSE)
	{
		$this->meta(NULL, TRUE);

        $value = null;

		if ($this->is_in_loop())
		{
			$n = is_null($n) ? $this->name : $n ;

			if(!is_null($n))
			{
				if ($collection)
				{
					$keys   = $this->get_the_loop_group_name_array();
				}
				else
				{
					$keys   = $this->get_the_loop_group_name_array();
					$keys[] = $n;
				}
			}
			else
			{
				if ($collection)
				{
					$keys   = $this->get_the_loop_group_name_array();
					end($keys);
					$last   = key($keys);
					unset($keys[$last]);
				}
				else
				{
					$keys   = $this->get_the_loop_group_name_array();
				}
			}
			$value = $this->get_meta_by_array($keys);
		}
		else
		{
			$n = is_null($n) ? $this->name : $n ;

			if(isset($this->meta[$n]))
			{
				$value = $this->meta[$n];
			}
		}

		if (is_string($value) || is_numeric($value))
		{
			if ($this->in_template)
			{
				return htmlentities($value, ENT_QUOTES, 'UTF-8');
			}
			else
			{
				// http://wordpress.org/support/topic/call-function-called-by-embed-shortcode-direct
				// http://phpdoc.wordpress.org/trunk/WordPress/Embed/WP_Embed.html#run_shortcode

                global /** @var $wp_embed WP_Embed */
                $wp_embed;

				return do_shortcode($wp_embed->run_shortcode($value));
			}
		}
		else
		{
			// value can sometimes be an array
			return $value;
		}
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $n
	 */
	function the_name($n = NULL)
	{
		echo $this->get_the_name($n);
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $n
	 *
	 * @return string
	 */
	function get_the_name($n = NULL)
	{
		if (!$this->in_template AND $this->mode == self::STORAGE_MODE_EXTRACT)
		{
            if($this->prefix){
                return $this->meta_key . str_replace($this->meta_key, '', is_null($n) ? $this->name : $n);
            }else{
                return  is_null($n) ? $this->name : $n;
            }
		}

        if ($this->is_in_loop())
		{
			$n = is_null($n) ? $this->name : $n ;

			if (!is_null($n))
				$the_field = $this->get_the_loop_group_name(TRUE) . '[' . $n . ']' ;
			else
				$the_field = $this->get_the_loop_group_name(TRUE);
		}
		else
		{
			$n = is_null($n) ? $this->name : $n ;

			$the_field = $this->id . '[' . $n . ']';
		}

		$hints = array
		(
			self::HINT_CHECKBOX_MULTI,
			self::HINT_SELECT_MULTI
		);

		if (in_array($this->hint, $hints))
		{
			$the_field .= '[]';
		}

		return $the_field;
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function the_index()
	{
		echo $this->get_the_index();
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function get_the_index()
	{
		return $this->in_loop ? $this->current : 0 ;
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function is_first()
	{
		if ($this->in_loop AND $this->current == 0) return TRUE;

		return FALSE;
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function is_last()
	{
		if ($this->in_loop AND ($this->current+1) == $this->length) return TRUE;

		return FALSE;
	}

	/**
	 * Used to check if a value is a match
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
	 * @return	bool
	 * @see		is_value()
	 */
	function is_value($n, $v = NULL)
	{
		if (is_null($v))
		{
			$the_value = $this->get_the_value();

			$v = $n;
		}
		else
		{
			$the_value = $this->get_the_value($n);
		}

		if($v == $the_value) return TRUE;

		return FALSE;
	}

	/**
	 * Used to check if a value is selected, useful when working with checkbox,
	 * radio and select values.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	bool
	 * @see		is_value()
	 */
	public function is_selected($n, $v = NULL, $is_default = FALSE)
	{
		if (is_null($v))
		{
			$the_value = $this->get_the_value(NULL);

			$v = $n;
		}
		else
		{
			$the_value = $this->get_the_value($n);
		}

		if (is_array($the_value))
		{
			if (in_array($v, $the_value)) return TRUE;
		}
		elseif($v == $the_value)
		{
			return TRUE;
		}

		if( empty( $the_value ) && $is_default )
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Prints the current state of a checkbox field and should be used inline
	 * within the INPUT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @see		get_the_checkbox_state()
	 */
	function the_checkbox_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a checkbox field, the returned string is
	 * suitable to be used inline within the INPUT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the INPUT tag
	 * @see		the_checkbox_state()
	 */
	function get_the_checkbox_state($n, $v = NULL, $is_default = FALSE)
	{
		if ($this->is_selected($n, $v, $is_default)){
            return ' checked="checked"';
        }
        return '';
	}

	/**
	 * Prints the current state of a radio field and should be used inline
	 * within the INPUT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @see		get_the_radio_state()
	 */
	function the_radio_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a radio field, the returned string is
	 * suitable to be used inline within the INPUT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the INPUT tag
	 * @see		the_radio_state()
	 */
	function get_the_radio_state($n, $v = NULL, $is_default = FALSE)
	{
		return $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Prints the current state of a select field and should be used inline
	 * within the SELECT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @see		get_the_select_state()
	 */
	function the_select_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_select_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a select field, the returned string is
	 * suitable to be used inline within the SELECT tag.
	 *
	 * @since	0.1
	 * @access	public
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the SELECT tag
	 * @see		the_select_state()
	 */
	function get_the_select_state($n, $v = NULL, $is_default = FALSE)
	{
		if ($this->is_selected($n, $v, $is_default)){
            return ' selected="selected"';
        }
        return '';
	}

    /**
     * Open a field group for use in a loop.
     *
     * @param string $t group element tag
     * @param string $w wrapper element tag
     * @param bool $sortable if the group should be sortable within its loop
     */
    function the_group_open($t = 'div',$w='div',$sortable = FALSE)
	{
		echo $this->get_the_group_open($t,$w,$sortable);
	}

    /**
     * Open a field group for use in a loop.
     *
     * @param string $t group element tag
     * @param string $w wrapper element tag
     * @param bool $sortable if the group should be sortable within its loop
     * @return string group opening tag, preceded y a wrapping tag if first in loop.
     */
	function get_the_group_open($t = 'div', $w='div', $sortable = FALSE)
	{
		$this->group_tag = $t;
        $this->loop_tag = $w;

		$curr_loop = $this->get_the_current_loop();

        $curr_loop->group_tag = $t;
        $curr_loop->loop_tag = $w;

		$the_name  = $curr_loop->name;

		$loop_open = NULL;

		$loop_open_classes = array('mm_loop', 'mm_loop-' . $the_name);

		$css_class = array('mm_group', 'mm_group-'. $the_name);


		if ($curr_loop->is_first())
		{
			array_push($css_class, 'first');

            $data=array();
			if ($curr_loop->limit >0 )
			{
				$data['mm_loop_limit'] =$curr_loop->limit;
			}
            if($sortable){
                $data['mm_sortable']='true';
            }
            $dataattrs = '';
            foreach($data as $k=>$v){
                $dataattrs .=' data-' . $k . '="' . $v . '"';
            }
			$loop_open = '<' . $w . ' class="' . implode(' ', $loop_open_classes) . '"' . $dataattrs . '>';
		}

		if ($curr_loop->is_last())
		{
			array_push($css_class, 'last');

			if ($this->in_loop == 'multi')
			{
				array_push($css_class, 'mm_tocopy');
			}
		}

		return $loop_open . '<' . $t . ' class="'. implode(' ', $css_class) . '">';
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function the_group_close()
	{
		echo $this->get_the_group_close();
	}

	/**
	 * @since	0.1
	 * @access	public
	 */
	function get_the_group_close()
	{
		$loop_close = NULL;

		$curr_loop = $this->get_the_current_loop();

		if ($curr_loop->is_last())
		{
			$loop_close = '</' . $curr_loop->loop_tag . '>';
		}

		return '</' . $curr_loop->group_tag . '>' . $loop_close;
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param $n
	 * @param null $length
	 * @param null $limit
	 *
	 * @return bool
	 */
	function have_fields_and_multi($n, $length = NULL,$limit = NULL)
	{

		$this->meta(NULL, TRUE);

		$this->in_loop = 'multi';

        // push new loop or set loop to current name
		$this->push_or_set_current_loop($n, $length, $this->in_loop,$limit);

		return $this->loop($length, TRUE);
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param $n
	 * @param null $length
	 *
	 * @return bool
	 */
	function have_fields($n,$length=NULL)
	{
		$this->meta(NULL, TRUE);
		$this->in_loop = 'normal';
        $this->push_or_set_current_loop($n, $length, $this->in_loop);
		return $this->loop($length);
	}

	/**
	 * @since    0.1
	 * @access    private
	 *
	 * @param null $length
	 * @param bool $and_one
	 *
	 * @return bool
	 */
    function loop($length=NULL,$and_one=FALSE)
	{
		if (!$this->in_loop)
		{
			$this->in_loop = TRUE;
		}

		$cnt = $this->get_the_current_group_count();

		$length = is_null($length) ? $cnt : $length ;

		if ($this->in_loop == 'multi' AND $cnt > $length) $length = $cnt;

		$this->length = $length;

		if ($this->in_template )
		{
			if ($length == 0 AND $and_one)
			{
				$this->length = 1;
			}
			else
			{
				$this->length = $length+1;
			}
		}

		$this->set_the_current_group_length($this->length);
		$this->increment_current_loop();
		$this->current++;

		if ($this->get_the_current_group_current() < $this->get_the_current_group_length())
		{
			$this->name      = NULL;

			return TRUE;
		}
		else if ($this->get_the_current_group_current() == $this->get_the_current_group_length())
		{
			$this->name      = NULL;
			$this->set_the_current_group_current(-1);
			$this->prev_loop();
		}

		$this->in_loop = FALSE;
        $this->current =-1;
		$this->loop_data = new stdClass;

		return FALSE;
	}

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param $object_id
	 * @param bool $is_ajax
	 *
	 * @return
	 */
	public abstract function save($object_id,$is_ajax=FALSE);


    /**
     * @since	0.1
     * @access	public
     */
    public function ajax_save(){
        check_ajax_referer($this->id,'mm_nonce');
        if(isset($_POST['mm_object_id'])){
            $this->save($_POST['mm_object_id'],true);
        }else{
            wp_send_json_error( array(
                'error' => __( 'Object ID not set, no data was saved.' )
            ));
        }

    }

	/**
	 * Cleans an array, removing blank ('') values
	 *
	 * @static
	 * @since	0.1
	 * @access	public
	 * @param	array $arr the array to clean (passed by reference)
	 */
	static function clean(&$arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $i => $v)
			{
				if (is_array($arr[$i]))
				{
					self::clean($arr[$i]);

					if (!count($arr[$i]))
					{
						unset($arr[$i]);
					}
				}
				else
				{
					if ('' == trim($arr[$i]) OR is_null($arr[$i]))
					{
						unset($arr[$i]);
					}
				}
			}

			if (!count($arr))
			{
				$arr = array();
			}
			else
			{
				$keys = array_keys($arr);

				$is_numeric = TRUE;

				foreach ($keys as $key)
				{
					if (!is_numeric($key))
					{
						$is_numeric = FALSE;
						break;
					}
				}

				if ($is_numeric)
				{
					$arr = array_values($arr);
				}
			}
		}
	}

	/**
	 * @param $name
	 * @param $length
	 * @param $type
	 * @param null $limit
	 *
	 * @return MM_Loop
	 */
	function push_loop($name, $length, $type, $limit = NULL)
	{
		$loop         = new MM_Loop($name, $length, $type, $limit);
		$parent       = $this->get_the_current_loop();
		if($parent)
			$loop->parent = $parent->name;
		else
			$loop->parent = FALSE;
		$this->loop_stack[$name] = $loop;
		return $loop;
	}

	/**
	 * @param $name
	 * @param $length
	 * @param $type
	 * @param null $limit
	 */
	function push_or_set_current_loop($name, $length, $type,$limit = NULL)
	{
		if( !array_key_exists( $name, $this->loop_stack ) )
		{
			$this->push_loop($name, $length, $type, $limit);
		}

		$this->set_current_loop($name);
	}

	/**
	 * @param $name
	 */
	function set_current_loop($name)
	{
		reset($this->loop_stack);
		if(!array_key_exists($name, $this->loop_stack)){
			return;
		}
		while(key($this->loop_stack) !== $name){
			next($this->loop_stack);
        }
        $this->current = current($this->loop_stack)->current;
	}

	/**
	 * @return mixed
	 */
	function next_loop()
	{
		return next($this->loop_stack);
	}

	/**
	 * @return bool
	 */
	function prev_loop()
	{
		$parent = $this->get_the_current_loop()->parent;
		if($parent)
		{
			$this->set_current_loop($parent);
            return TRUE;
		}
		else
		{
            $this->name = reset($this->loop_stack)->name;
			$this->loop_stack = array();
			return FALSE;
		}
	}

	/**
	 * @return mixed
	 */
	function get_the_current_group_length()
	{
		return current($this->loop_stack)->length;
	}

	/**
	 * @return mixed
	 */
	function get_the_current_group_current()
	{
		return current($this->loop_stack)->current;
	}

	/**
	 * @param $length
	 */
	function set_the_current_group_length($length)
	{
		current($this->loop_stack)->length = $length;
	}

	/**
	 * @param $current
	 */
	function set_the_current_group_current($current)
	{
		current($this->loop_stack)->current = $current;
	}

    /**
     * @param string|null $name
     * @return MM_Loop[]
     */
    function get_the_loop_collection($name = null)
	{
		$collection   = array();

		if(is_null($name))
		{
			$curr = $this->get_the_current_loop();
			if($curr)
			{
				$name         = $curr->name;
				$loop_stack   = $this->loop_stack;
				$loop         = $loop_stack[$name];
				$collection[] = $loop;
				while ($loop)
				{
					$collection[] = $loop;
					if($loop->parent)
						$loop = $loop_stack[$loop->parent];
					else
						$loop = FALSE;
				}
				$collection = array_reverse($collection);
			}
		}

		return $collection;
	}

	/**
	 * @param bool $with_id
	 *
	 * @return string
	 */
	function get_the_loop_group_name($with_id = FALSE)
	{
		$loop_name  = $with_id ? $this->id : '';
		$curr       = $this->get_the_current_loop();

		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->get_the_loop_collection();
		// print_r($loop_stack);
		foreach ($loop_stack as $loop)
		{
			$loop_name .= '[' . $loop->name . '][' . $loop->current . ']';

			if($loop->name === $curr->name)
				break;
		}
		return $loop_name;
	}

	/**
	 * @return int
	 */
	function get_the_loop_level()
	{
		$curr  = $this->get_the_current_loop();
		$depth = 0;

		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->get_the_loop_collection();
		foreach ($loop_stack as $loop)
		{
			if($loop->name === $curr->name)
				break;
			$depth++;
		}
		return $depth;
	}

	/**
	 * @param bool $with_id
	 *
	 * @return array
	 */
	function get_the_loop_group_name_array($with_id = FALSE)
	{
		$loop_name   = array();
		$curr        = $this->get_the_current_loop();

		if($with_id)
		{
			$loop_name[] = $this->id;
		}

		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->get_the_loop_collection();
		foreach ($loop_stack as $loop)
		{
			$loop_name[] = $loop->name;
			$loop_name[] = $loop->current;

			if($loop->name === $curr->name)
				break;
		}
		return $loop_name;
	}

	/**
	 * @param $arr
	 *
	 * @return array|null
	 */
	function get_meta_by_array($arr)
	{
		$meta = $this->meta;

		if(!is_array($arr) || !is_array($meta) || is_null($meta))
			return null;

		foreach ($arr as $key)
		{
			if(is_array($meta) and array_key_exists($key, $meta))
			{
				$meta = $meta[$key];
			}
			else
			{
				return null;
			}
		}
		return $meta;
	}

	/**
	 * @return int
	 */
	function get_the_current_group_count()
	{
		$arr  = $this->get_the_loop_group_name_array();
		end($arr);
		$last = key($arr);
		unset($arr[$last]);
		$meta = $this->get_meta_by_array($arr);
		return count($meta);
	}

	/**
	 *
	 */
	function increment_current_loop()
	{
		current($this->loop_stack)->current++;
	}

	/**
	 * @return mixed
	 */
	function get_the_current_loop()
	{
		return current($this->loop_stack);
	}

	/**
	 * @return bool
	 */
	function is_in_multi_last()
	{
		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->get_the_loop_collection();
		foreach ($loop_stack as $loop)
		{
			if($loop->type === 'multi' and $loop->is_last())
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * @return bool
	 */
	function is_in_loop()
	{
		if(current($this->loop_stack) === FALSE)
			return FALSE;
		return TRUE;
	}

	/**
	 * @param bool $after
	 *
	 * @return string
	 */
	function the_copy_button_class($after=FALSE)
	{
        $loop_stack = $this->loop_stack;
        $a = next($loop_stack);
        if($after){
            $name = ($a)?$a->name:$this->name;
            return 'mm_docopy-' . $name;
        }else{
            return 'mm_docopy-' . current($this->loop_stack)->name;
        }
	}

	/**
	 *
	 */
	public static function logInstances(){

        foreach(self::$instances as $k => $v){
            error_log($k);
            foreach($v as $mm){
                error_log('     ' . get_class($mm));
            }
        }

    }

    /**
     *
     */
    final public static function purgeInstances(){

        self::$instances = array();

    }
}