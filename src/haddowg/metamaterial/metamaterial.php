<?php
/**
 * Abstract Metamaterial Class.
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

use WP_Embed;

use HaddowG\MetaMaterial\Facades\MMM;

if (!defined('PHP_INT_MIN')) {

	/** Minimum possible value of an integer */
	define('PHP_INT_MIN', ~PHP_INT_MAX);

}

/**
 * Abstract Class Metamaterial.
 *
 * Provides base functionality and framework for all Metamaterial types.
 * Individual types must override the abstract methods and may extend default variables or method
 * implementations to customize their own behaviour.
 *
 * @package HaddowG\MetaMaterial
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
	 * @var	string identifier for this instance, required.
	 */
	protected $id;

	/**
	 * Used to set the title of the metabox.
	 * Required for instantiation.
     *
     * Config Option
     *
	 * @var	string the title of the metabox
	 */
    protected $title = 'Custom Meta';

	/**
	 * Used to set the metabox template file, the contents of your metabox should be
	 * defined within this file.
     * Required for instantiation.
	 *
     * Config Option
     *
	 * @var	string metabox template file, required
	 */
    protected $template;


	/**
     * The part of the page where the metabox should be shown.
	 * 'before_title', 'after_title', 'after_editor', 'normal', 'advanced' or 'side'
     * Defaults to 'normal'
     *
     * Config Option
     *
	 * @var	string metabox page context
	 */
	protected $context = 'normal';

	/**
     * The priority within the context where the boxes should show
	 * 'top', 'high', 'core', 'default', 'low' or 'bottom'
     * Defaults to 'high'
     *
     * Config Option
     *
	 * @var	string metabox priority within page context
	 */
	protected $priority = 'high';

	/**
	 * Used to set how the class stores this metaboxes data.
     * The following class constants should be used to set this option:
     *
     * STORAGE_MODE_ARRAY (default) - Data will be stored as an associative array in a single meta entry in the wp_postmeta table.
     *
	 * STORAGE_MODE_EXTRACT - each field in the data will be saved as an individual entry in the postmeta table,
     * an additional 'fields' meta will be saved to indicate the fields that are present and to speed mass retrieval.
     * NOTE: nested fields will not be separated but will appear with their owning top level field's entry.
	 *
     * Config Option
     *
     * @var string either STORAGE_MODE_ARRAY or STORAGE_MODE_EXTRACT to indicate desired storage mode.
	 */
	protected $mode = self::STORAGE_MODE_ARRAY;

    /**
     * User defined meta key for use with STORAGE_MODE_ARRAY, or as an optional prefix for keys when using STORAGE_MODE_EXTRACT.
     * Prefix with an underscore to prevent fields(s) from showing up in the custom fields metabox.
     * Will default to underscore prefixed $id if not provided.
     *
     * Config Option
     *
     * @var string key to use for meta storage when using STORAGE_MODE_ARRAY, or as an optional prefix for keys when using STORAGE_MODE_EXTRACT
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
	 * @var	bool whether to prefix keys when using STORAGE_MODE_EXTRACT
	 */
    protected $prefix = TRUE;

    /**
	 * Used to hide the default elements on the page.
     * Array of named elements, possible values include:
     * permalink, the_content, excerpt, custom_fields, discussion, comments, slug, author,
     * format, featured_image, revisions, categories, tags, send-trackbacks
     *
	 * If MMM::$compound_hide is set to FALSE then only the first showing metaboxes values will be considered,
     * otherwise they are combined to determine what should be hidden.
     *
     * Config Option
     *
	 * @var array Array of named elements to be hidden, see description for possible values.
	 */
	protected $hide_on_screen;

    /**
     * Whether ajax saving is enabled.
     * Enabled by default.
     *
     * Config Option
     *
     * @var bool whether ajax saving is enabled.
     */
    protected $ajax_save = true;

	/**
	 * Callback function triggered on the WordPress "current_screen" action
     * Callback is executed only when the metabox is present.
	 *
     * Config Option
     *
	 * @var callable callable for additional initialisation of this instance
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
	 * @var	callable see description for provided arguments and expected return
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
	 * @var	callable see description for provided arguments and expected return
	 */
    protected $save_filter;

    /**
     * Ajax Save Success Filter function callback used to modify ajax response on successful ajax save.
     *
     * Filter function should return modified array, containing at least 'message'.
     * Filter function should accept 2 arguments:
     *  - array $ajax_return array of data to be returned
     *	- int $object_id - the id of the object that was saved
     *  - boolean $is_ajax - if the save was via an ajax request
     *
     * Config Option
     *
     * @var	callable see description for provided arguments and expected return
     */
	protected $ajax_save_success_filter;

    /**
     * Ajax Save Fail Filter function callback used to modify ajax response on failed ajax save.
     *
     * Filter function should return modified array, containing at least 'message'.
     * Filter function should accept 2 arguments:
     *  - array $ajax_return array of data to be returned
     *	- int $object_id - the id of the object that was saved
     *  - boolean $is_ajax - if the save was via an ajax request
     *
     * Config Option
     *
     * @var	callable see description for provided arguments and expected return
     */
	protected $ajax_save_fail_filter;

	/**
	 * Callback function triggered after this metabox completes saving.
     *
     * Callback function should accept 3 arguments:
     * - array $meta metabox data that was saved
	 * - int $post_id - the post id of post metadata was saved to
     * - boolean $is_ajax - if the save was via an ajax request
	 *
     * Config Option
     *
	 * @var	callable save callback
	 */
    protected $save_action;

	/**
	 * Callback used to insert STYLE or SCRIPT tags into the head.
     *
     * Callback is executed only when the metabox is present.
	 * Called with lower than default priority so other script/style dependencies should be present if enqueued.
     *
     * Config Option
     *
	 * @var	callable head callback for script/style inclusion
	 */
    protected $head_action;

	/**
	 * Callback used to insert SCRIPT tags into the footer.
     *
     * Callback is executed only when the metabox is present.
	 * Called with lower than default priority so other script/style dependencies should be present if enqueued.
	 *
     * Config Option
     *
	 * @var	callable foot callback for script/style inclusion
	 */
    protected $foot_action;

    /**
     * Callback used to enqueue scripts.
     *
     * Callback is executed only when the metabox is present.
     *
     * Config Option
     *
     * @var	callable enqueue callback for script/style inclusion
     */
    protected $enqueue_action;

	/**
     * Defines the default asset url where assets for this metamaterial type can be located.
     *
	 * @var string default url for assets loaded by this Metamaterial class
	 */
	public static $default_assets_url = false;

    /**
     * Defines the default asset file path where assets for this metamaterial type can be located.
     *
	 * @var string default path for assets loaded by this Metamaterial class
	 */
	public static $default_assets_dir = false;

    /**
     * Defines the default template file path where assets for this metamaterial type can be located.
     *
     * @var string default path for templates loaded by this Metamaterial class
     */
    public static $default_templates_dir = false;


    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Cached path to this instances template file
     * @var bool|string
     */
    protected $template_path = false;

    /**
	 * Cached value of can_output(), to prevent re-execution.
	 * @var	string
	 */
    protected $will_show = null;

    /**
	 * Cached value of meta(), to prevent re-execution internally.
     * @var	array
	 */
	protected $meta  = array();

    /**
     * Stores current field name in template
     *
     * @var string current field name
     */
	protected $name;

	/**
	 * Used to provide field type hinting
	 *
	 * @var	string
     */
	protected $is_multi;

    /**
     * If we are currently in a template
     *
     * @var boolean If we are currently in a template
     */
	protected $in_template = FALSE;

    /**
     * Html group tag for current loop
     *
     * @var string Html group tag for current loop
     */
	protected $group_tag;

    /**
     * Html loop tag for current loop container
     *
     * @var string Html loop tag for current loop
     */
    protected $loop_tag;

    /**
	 * Used to store loop stack for
	 *
	 * @var array of MM_Loop objects indexed by group name/id
	 */
    protected $loop_stack = array();

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @var array Array of admin page targets on which this MetaMaterial Class is designed to display
     */
    protected static $admin_targets = array();


    /**
     * Array of priorities with numerical equivalents.
     * Used to order metaboxes within a page context.
     * Plugin/Theme developers should avoid default use of 'top' or 'high' to allow end users to more easily adjust as desired.
     *
     * @var array Array of priorities with numerical equivalents.
     */
	protected static $priorities = array(
        'top' => self::PRIORITY_TOP,
        'high' => self::PRIORITY_HIGH,
        'core' => self::PRIORITY_CORE,
        'default' => self::PRIORITY_DEFAULT,
        'low' => self::PRIORITY_LOW,
        'bottom' => self::PRIORITY_BOTTOM
    );

    /**
     * Array of contexts in order that they are displayed.
     * Used to order metaboxes within a page context.
     * Plugin/Theme developers should avoid default use of 'top' or 'high' to allow end users to more easily adjust as desired.
     *
     * @var     array Array of priorities with numerical equivalents.
     */
    protected static $contexts = array(
        'normal',
        'advanced',
        'side'
    );

	/**
     * An associative array of css styles that can used to hide components on the admin screen
     *
	 * @var array
	 */
	public static $hide_on_screen_styles = array();


    /**
     * Initializes generic action hooks for a Metamaterial instance
     * Some are global and only bound once for any number of metamaterial instances, others are per instance, in both
     * cases they are run regardless of whether an instance is active on the current page.
     *
     * @param $id
     * @param array $config
     * @return Metamaterial
     * @throws MM_Exception
     */
    public static function getInstance($id, $config = array())
    {
        $class = get_called_class();

        $is_new =!MMM::hasInstance($id,$class);

        /** @var $instance MetaMaterial */
        $instance = MMM::getInstance($id,$class);

        if($is_new){

            $config = $instance->applyFilters('before_config', $config, $id);

            $instance->applyBaseConfig($id,$config);

            $instance->applyConfig($config);

            $instance->initInstanceActions();
        }else{
            if(!empty($config)){
                throw new MM_Exception('Attempt to pass config to existing instance.',500);
            }
        }

        return $instance;

    }

    /**
     * Setup wordpress action hooks for this instance
     *
     * Sets up the wordpress hooks required to power any metamaterial instance, including several global hooks
     * bound once no matter how many instances or types of Metamaterial are added.
     */
    protected function initInstanceActions(){

        //these are added only once, the first time a MetaMaterial is constructed, therefore they run only once for all instances.
        $this->addAction('admin_enqueue_scripts', 'HaddowG\MetaMaterial\Facades\MMM::globalEnqueue', 10, 1, FALSE, FALSE);
        $this->addAction('admin_enqueue_scripts', 'HaddowG\MetaMaterial\Facades\MMM::enqueue', 10, 1, FALSE, FALSE);
        $this->addAction('admin_print_styles', 'HaddowG\MetaMaterial\Facades\MMM::printGlobalStyles', 10, 1, FALSE, FALSE);
        $this->addAction('admin_head', 'HaddowG\MetaMaterial\Facades\MMM::head', 11, 1, FALSE, FALSE);
        $this->addAction('admin_footer', 'HaddowG\MetaMaterial\Facades\MMM::foot', 11, 1, FALSE, FALSE);
        $this->addAction('current_screen', 'HaddowG\MetaMaterial\Facades\MMM::globalInit' ,10,1,FALSE,FALSE);


        //register output filters on 'admin_init' so they are available before globalInit() runs on the 'current_screen' hook.
        add_action('admin_init', array($this,'initAlways'));

        //if ajax save is enabled this needs to be available regardless of the admin page
        if($this->ajax_save){
            add_action('wp_ajax_' . $this->getActionTag('ajax_save'), array($this, 'ajax_save'));
        }
    }

    /**
     * Applies generic configuration options for all Metamaterial instances.
     * Throws exception if invalid array is provided.
     *
     * @param $id
     * @param $config
     * @throws MM_Exception
     */
    public function applyBaseConfig($id, &$config)
    {

        if (is_array($config)) {

            $this->id = $id;

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
                'enqueue_action' => $this->enqueue_action,
                'head_action' => $this->head_action,
                'foot_action' => $this->foot_action
            );

            //discard non config options and merge with defaults
            $config = array_merge($config_defaults, array_intersect_key($config, $config_defaults));

            $prep_arrays = array(
                'hide_on_screen'
            );

            foreach ($prep_arrays as $v)
            {
                $this->$v = static::ensureArray($this->$v);
            }

            //set instance config options
            foreach ($config as $n => $v) {
                $this->$n = $v;
            }

            //resolve and cache template path
            $this->template_path = $this->applyFilters('template_path',$this->getTemplatePath());


        }else{
            throw new MM_Exception('provided config was not a valid array',500);
        }
    }

    /**
     * Resolves provided template to an absolute path to an existing file and throws exception if it cant.
     * Attempts to resolve in the current order:
     *      - template option as provided (i.e check if already absolute path)
     *      - relative to the active theme directory
     *      - relative to the default_templates_dir if defined
     *
     * @return string the resolved absolute path to the template
     * @throws MM_Exception
     */
    public function getTemplatePath(){

        //return cached path if present
        if($this->template_path){
            return $this->template_path;
        }

        //abort if we dont have a template option set
        if(empty($this->template)){
            $this->template_path =false;
            throw new MM_Exception('template config value is required',500);
        }else{
            if(substr($this->template,strlen($this->template)-4)!=='.php'){
                $this->template = $this->template . '.php';
            }
        }

        //see if provided template option is an absolute path to existing file.
        if(file_exists($this->template)) {
            $this->template_path = $this->template;
            return $this->template_path;
        }

        //first try relative the the current theme directory
        if( file_exists(trailingslashit( get_stylesheet_directory()) . $this->template )){
            $this->template_path = trailingslashit( get_stylesheet_directory()) . $this->template;
            return $this->template_path;
        }

        //lastly try relative to the default templates directory
        if(static::$default_templates_dir) {
            if ( file_exists( trailingslashit(static::$default_templates_dir) . $this->template ) ) {
                $this->template_path = trailingslashit(static::$default_templates_dir)  . $this->template;
                return $this->template_path;
            }
        }

        //if we haven't found any match throw exception
        throw new MM_Exception('Unable to locate template file, please ensure path and filename are correct.',500);
    }

	/**
	 * print this metaboxes title
	 */
	public function the_title(){
		echo $this->title;
	}

    /**
     * Simple accessor for this metaboxes title
     *
     * @return	string the metaboxes title
     */
    public function get_the_title(){
        return $this->title;
    }


	/**
	 * Accessor for this metaboxes context.
	 * Optionally return the numeric equivalent for use in sorting by passing TRUE as a parameter
	 *
	 * @param	boolean	$numeric whether to return numeric equivalent rather than text value
	 * @return	string|int the metaboxes context as text or integer
	 */
	public function getContext($numeric = FALSE){
		$cntxt = array_search($this->context,static::$contexts);
        if($numeric){
            if ($cntxt!==FALSE) {
                return $cntxt;
            } else {
                return array_search('normal',static::$contexts);
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
     * @param   boolean $numeric whether to return numeric equivalent rather than text value
     * @param   boolean $sanitized if non-standard values need casting to nearest wordpress equivalents for wordpress internal use
     * @return  string|int the metaboxes priority as text or integer
     */
	public function getPriority($numeric = FALSE,$sanitized=TRUE){
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
				foreach(array_reverse(static::$priorities) as $k => $v){
					if ($this->priority >= $v) {
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
	 * @return	callable|null the metaboxes save_filter
	 */
    public function get_save_filter(){
        return $this->save_filter;
    }

	/**
     * Simple accessor for this metaboxes ajax_save_success_filter
     *
	 * @return callable|null the metaboxes ajax_save_success_filter
     */
	public function get_ajax_save_success_filter(){
        return $this->ajax_save_success_filter;
    }

	/**
     * Simple accessor for this metaboxes ajax_save_fail_filter
     *
     * @return callable|null the metaboxes ajax_save_fail_filter
	 */
	public function get_ajax_save_fail_filter(){
        return $this->ajax_save_fail_filter;
    }
	/**
	 * Simple accessor for this metaboxes save_action
	 *
	 * @return	callable|null the metaboxes save_action
	 */
    public function get_save_action(){
        return $this->save_action;
    }

	/**
	 * Simple accessor for this metaboxes head_action
	 *
	 * @return	callable|null the metaboxes head_action
	 */
    public function get_head_action(){
        return $this->head_action;
    }

	/**
	 * Simple accessor for this metaboxes foot_action
	 *
	 * @return	callable|null the metaboxes foot_action
	 */
    public function get_foot_action(){
        return $this->foot_action;
    }

	/**
	 * Simple accessor for this metaboxes init_action
	 *
	 * @return	callable|null the metaboxes init_action
	 */
    public function get_init_action(){
        return $this->init_action;
    }

	/**
	 * Simple accessor for this metaboxes enqueue_action
	 *
	 * @return	callable|null the metaboxes enqueue_action
	 */
	public function get_enqueue_action(){
		return $this->enqueue_action;
	}

    /**
     * Apply config options specific to the Metamaterial concrete class.
     *
     * @param $config array the config array (will contain merged default values at this point)
     */
    protected abstract function applyConfig($config);

	/**
     * Initialises this metamaterial instance when it is showing on the current admin page.
	 */
	public abstract function initWhenShowing();

	/**
	 * Initialises this metamaterial class once regardless of how many instances may exist.
	 */
	public static function initOnce(){
        // no default behaviour override in extending class to use.
    }

	/**
	 * Initialises this metamaterial instance regardless of whether it is showing on the current admin page
     * Used to initialize the metabox's output filter, runs on WordPress admin_init action.
	 * This runs before the globalInit() runs on the current_screen action to ensure we can
	 * correctly determine if a box should be showing or not.
	 */
	public function initAlways()
	{
        //add output filter if configured
		if ( !empty($this->output_filter) )
		{
			$this->addFilter('output', $this->output_filter,10,3);
		}

	}

	/**
	 * Used to insert STYLE or SCRIPT tags into the head.
	 * called on WordPress admin_head action.
	 */
	public function head()
	{
        $this->doAction('head');
	}

	/**
	 * Used to insert SCRIPT tags into the footer.
	 * called on WordPress admin_footer action.
	 */
	public function foot()
	{
        $this->doAction('foot');
	}

    /**
     * Used to enqueue scripts.
     * called on wordpress admin_enqueue_scripts action.
     */
    public function enqueue(){
        $this->doAction('enqueue');
    }

	/**
	 * Render a metabox from template file.
     * Exposes global post, Metamaterial instance and meta array to template.
	 * Appends nonce for verification.
	 */
	public abstract function render();

    /**
     * Add the default filters common to all metamaterial instances.
     *
     */
    public function addDefaultFilters(){

        $filters = array('save'=>3);

        if($this->ajax_save){
            $filters['ajax_save_success'] = 2;
            $filters['ajax_save_fail'] = 2;
        }

        foreach ($filters as $filter => $args)
        {
            $var = 'get_' . $filter . '_filter';
            $fltr = $this->$var();

            if (!empty($fltr))
            {
                $this->addFilter($filter, $fltr, 10, $args);
            }
        }

    }

	/**
	 * Used to properly prefix filter tags.
     * Ensures filter tags are unique to this metabox instance
	 *
	 * @param	string $tag name of the filter
	 * @return	string uniquely prefixed tag name
	 */
	protected function getFilterTag($tag)
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
     * @param   string $tag tag name for the filter
     * @param   Callable $function_to_add filter function
     * @param   int $priority filter priority
     * @param   int $accepted_args filter accepted arguments
     */
	public function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
	{
		$tag = $this->getFilterTag($tag);;
		add_filter($tag, $function_to_add, $priority, $accepted_args);
	}

    /**
     * Wrapper for WordPress has_filter() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress has_filter()
     *
     * @param   string $tag tag name for the filter to check for
     * @param   Callable|boolean $function_to_check optional function to check for existing filter for
     * @return  int|boolean priority of the filter if it exists for the given function, or boolean if any filter exists for the given tag if no $function_to check ias provided.
     */
	public function hasFilter($tag, $function_to_check = FALSE)
	{
		$tag = $this->getFilterTag($tag);
		return has_filter($tag, $function_to_check);
	}

    /**
     * Wrapper for WordPress apply_filters() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress apply_filters()
     *
     * @param $tag
     * @param $value
     * @return mixed
     */
	public function applyFilters($tag, $value)
	{
		$args = func_get_args();
		$args[0] = $this->getFilterTag($tag);
		return call_user_func_array('apply_filters', $args);
	}

    /**
     * Wrapper for WordPress remove_filter() function
     * Uniquely prefixes filter tag for this instance of Metamaterial
     * see WordPress remove_filter()
     *
     * @param $tag
     * @param $function_to_remove
     * @param int $priority
     * @return bool
     */
	public function removeFilter($tag, $function_to_remove, $priority = 10)
	{
		$tag = $this->getFilterTag($tag);
		return remove_filter($tag, $function_to_remove, $priority);
	}

    /**
     * Add the default actions common to all metamaterial instances.
     *
     */
    public function addDefaultActions(){

        $actions = array(
            'save'=>3,
            'enqueue'=>1,
            'head'=>1,
            'foot'=>1,
            'init'=>1
        );

        foreach ($actions as $action => $args)
        {
            $var = 'get_' . $action . '_action';
            $actn = $this->$var();

            if (!empty($actn))
            {
                $this->addAction($action, $actn, 10, $args);
            }
        }
    }

	/**
	 * Used to properly prefix an action tag, making the tag is unique to this metabox instance
	 *
	 * @param	string $tag name of the action
	 * @return	string uniquely prefixed tag name
	 */
	protected function getActionTag($tag)
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
    public function addAction($tag, $function_to_add, $priority = 10, $accepted_args = 1, $prefix = TRUE, $suffixes = FALSE, $once = TRUE)
	{
        if($suffixes && empty($suffixes)){
            $suffixes = static::$admin_targets;
        }

        if($prefix){
            $tag = $this->getActionTag($tag);
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
	 * @param $tag
	 * @param bool $function_to_check
	 * @return bool|int
	 */
	public function hasAction($tag, $function_to_check = FALSE)
	{
		$tag = $this->getActionTag($tag);
		return has_action($tag, $function_to_check);
	}

	/**
	 * Uses WordPress remove_action() function, see WordPress remove_action()
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param int $priority
	 * @return bool
	 */
	public function removeAction($tag, $function_to_remove, $priority = 10)
	{
		$tag = $this->getActionTag($tag);
		return remove_action($tag, $function_to_remove, $priority);
	}

	/**
	 * Uses WordPress do_action() function, see WordPress do_action()
     *
	 * @param $tag
	 * @param string $arg
	 * @return mixed
	 */
	public function doAction($tag, $arg = '')
	{
		$args = func_get_args();
		$args[0] = $this->getActionTag($tag);
		return call_user_func_array('do_action', $args);
	}

	/**
	 * Used to check if we are on a target admin page
	 *
	 * @return	bool if this is a target admin page
	 */
	public static function isTargetAdmin()
	{
        global $hook_suffix;

        if (in_array($hook_suffix, static::$admin_targets)) {
            return TRUE;
        }

        return FALSE;
	}

	/**
     * Determine if this instance should display on the current admin page
     *
	 * @return bool
	 */
	public abstract function canOutput();


	/**
     * Return this instance's hide_on_screen config value
     *
	 * @return array
	 */
	public function getHideOnScreen()
    {
        if (empty($this->hide_on_screen)) {
            $this->hide_on_screen = array();
        }
        return $this->hide_on_screen;
    }

    /**
     * Constructs a full asset url from a relative path or filename.
     *
     * Will search relative the the current theme directory (stylesheet directory),
     * followed by the default assets directory.
     *
     * @param string $asset a filename or file path
     * @return bool|string
     */
	public function getAssetUrl($asset){

        if( file_exists(trailingslashit( get_stylesheet_directory()) . $asset )){
            return trailingslashit( get_stylesheet_directory_uri()) . $asset;
        }

		if(static::$default_assets_url && static::$default_assets_dir) {
			if ( file_exists( trailingslashit(static::$default_assets_dir) . $asset ) ) {
				return trailingslashit(static::$default_assets_url)  . $asset;
			}
		}

		return false;
	}

	/**
     * Get any type specific styles
     *
	 * @return array returns associative array of style urls, keyed by a unique handle for enqueueing
	 */
	public function getGlobalStyle(){

        $script = $this->getGlobalStyleUri();

        return apply_filters(
            'metamaterial_filter_' . strtolower(MMM::unNamespaceIt(get_called_class())). '_global_style',
            array(MMM::unNamespaceIt(get_called_class()) => $script)
        );

    }

    /**
     * Get the Uri for this Metamaterial types global stylesheet
     *
     * @return bool|string
     */
    public function getGlobalStyleUri()
    {
        return $this->getAssetUrl('css/' . MMM::unNamespaceIt(get_called_class()) . '.css');
    }

    /**
     * Get any type specific scripts
     *
     * @return array returns associative array of script urls, keyed by a unique handle for enqueueing
     */
	public function getGlobalScript()
	{
        $script = $this->getGlobalScriptUri();

        return apply_filters(
            'metamaterial_filter_' . strtolower(MMM::unNamespaceIt(get_called_class())). '_global_script',
            array(MMM::unNamespaceIt(get_called_class()) => $script)
        );
	}

    /**
     * Get the Uri for this Metamaterial types global script
     *
     * @return bool|string
     */
	public function getGlobalScriptUri()
	{
		return $this->getAssetUrl('js/' . MMM::unNamespaceIt(get_called_class()) . '.js');
	}

	/**
	 * Gets the meta data for a meta box
	 *
	 * @param	int $post_id optional post ID for which to retrieve the meta data
	 * @return	array
	 */
	public function the_meta($post_id = NULL)
	{
		return $this->meta($post_id);
	}

	/**
	 * Gets the meta data for a meta box
	 *
	 * Internal method calls will typically bypass the data retrieval and will
	 * immediately return the current meta data
	 *
	 * @param	int $object_id optional post ID for which to retrieve the meta data
	 * @param	bool $internal optional boolean if internally calling
	 * @return	array
	 */
    public abstract function meta($object_id = NULL, $internal = FALSE);

	/**
	 * Prints this instances id.
     * can also use the_ID(), php functions are case-insensitive
	 */
	public function the_id()
	{
		echo $this->get_the_id();
	}

    /**
     * Simple accessor for this instances id.
     * can also use the_ID(), php functions are case-insensitive
     */
	function get_the_id()
	{
		return $this->id;
	}

	/**
	 * Set the current field focus
     *
	 * @param string $n field name to focus on
	 * @param bool $multi if this field can have multiple submitted values i.e checkboxes
	 */
	function the_field($n, $multi = false)
	{
		$this->name = $n;
		$this->is_multi = $multi;
	}

	/**
	 * Check if a field has a value
	 *
	 * @param null|string $n the name of a field to check, omit to check the current focused field
	 * @return bool if the current field has a value
	 */
	function have_value($n = NULL)
	{
		if ($this->get_the_value($n)) return TRUE;

		return FALSE;
	}

    /**
     * Print a field value
     *
     * @param null|string $n the name of a field to print, omit to print the current focused field value
     */
	function the_value($n = NULL)
	{
		echo $this->get_the_value($n);
	}

	/**
	 * Retrieve a field value
     *
	 * @param null|string $n the name of a field to retrieve, omit to retrieve the current focused field value
	 * @param bool $collection if the field is the member of a group return the whole group
	 * @return mixed
	 */
	function get_the_value($n = NULL, $collection = FALSE)
	{
		$this->meta(NULL, TRUE);

        $value = null;
        $n = is_null($n) ? $this->name : $n ;

		if ($this->isInLoop())
		{
            $keys = $this->getNestedMetaPath();

            if(is_null($n) && $collection){

                end($keys);
                $last   = key($keys);
                unset($keys[$last]);

            }elseif(!$collection){

                $keys[] = $n;

            }

			$value = $this->getNestedMetaValue($keys);
		}
		else
		{

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
                global /** @var $wp_embed WP_Embed */
                $wp_embed;

				return do_shortcode($wp_embed->run_shortcode($value));
			}
		}


        return $value;

	}

    /**
     * Print a field name
     *
     * @param null|string $n the name of a field name to print, omit to print the current focused field value
     */
	function the_name($n = NULL)
	{
		echo $this->get_the_name($n);
	}

	/**
	 * Retrieve a field name
     *
	 * @param null|string $n the name of a field name to print, omit to print the current focused field value
	 * @return string the fields name
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

        if ($this->isInLoop())
		{
			$n = is_null($n) ? $this->name : $n ;

			if (!is_null($n))
				$the_field = $this->getNestedMetaName() . '[' . $n . ']' ;
			else
				$the_field = $this->getNestedMetaName();
		}
		else
		{
			$n = is_null($n) ? $this->name : $n ;

			$the_field = $this->id . '[' . $n . ']';
		}

		if ($this->is_multi)
		{
			$the_field .= '[]';
		}

		return $the_field;
	}

	/**
     * Print the current field index
     *
     * Will print the current field index within any group, or 0 when not within a group.
	 */
	function the_index()
	{
		echo $this->get_the_index();
	}

    /**
     * Get the current field index
     *
     * @return int the current field index within any group, or 0 when not within a group.
     */
	function get_the_index()
	{
		return $this->isInLoop() ? $this->getCurrentLoop()->current : 0 ;
	}

	/**
	 * Check if the current field is the first in its group.
     *
     * @return bool if the current field is the first in its group.
	 */
	function is_first()
	{
		if ($this->isInLoop() && $this->getCurrentLoop()->is_first()){
            return TRUE;
        }

		return FALSE;
	}

    /**
     * Check if the current field is the last in its group.
     *
     * @return bool if the current field is the last in its group.
     */
	function is_last()
	{
        if ($this->isInLoop() && $this->getCurrentLoop()->is_last()){
            return TRUE;
        }

		return FALSE;
	}

	/**
	 * Used to check if a value is selected, useful when working with checkbox,
	 * radio and select values.
	 *
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	bool
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
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 */
	function the_checkbox_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a checkbox field, the returned string is
	 * suitable to be used inline within the INPUT tag.
	 *
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the INPUT tag
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
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 */
	function the_radio_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a radio field, the returned string is
	 * suitable to be used inline within the INPUT tag.
	 *
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the INPUT tag
	 */
	function get_the_radio_state($n, $v = NULL, $is_default = FALSE)
	{
		return $this->get_the_checkbox_state($n, $v, $is_default);
	}

	/**
	 * Prints the current state of a select field and should be used inline
	 * within the SELECT tag.
	 *
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 */
	function the_select_state($n, $v = NULL, $is_default = FALSE)
	{
		echo $this->get_the_select_state($n, $v, $is_default);
	}

	/**
	 * Returns the current state of a select field, the returned string is
	 * suitable to be used inline within the SELECT tag.
	 *
	 * @param	string $n the field name to check or the value to check for (if the_field() is used prior)
	 * @param	string $v optional the value to check for
     * @param   boolean $is_default if this is the default option
	 * @return	string suitable to be used inline within the SELECT tag
	 */
	function get_the_select_state($n, $v = NULL, $is_default = FALSE)
	{
		if ($this->is_selected($n, $v, $is_default)){
            return ' selected="selected"';
        }
        return '';
	}

    /**
     * Print the opening tag of a field group for use in a loop.
     *
     * @param string $t group element tag
     * @param string $w wrapper element tag
     * @param bool $sortable if the group should be sortable within its loop
     */
    function the_group_open($sortable=FALSE, $t='div', $w='div')
	{
		echo $this->get_the_group_open($sortable,$t,$w);
	}

    /**
     * Retrieve the opening tag of a field group for use in a loop.
     *
     * @param string $t group element tag
     * @param string $w wrapper element tag
     * @param bool $sortable if the group should be sortable within its loop
     * @return string group opening tag, preceded y a wrapping tag if first in loop.
     */
	function get_the_group_open($sortable=FALSE, $t='div', $w='div')
	{
		$this->group_tag = $t;
        $this->loop_tag = $w;

		$curr_loop = $this->getCurrentLoop();

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

			if ($curr_loop->type == 'multi')
			{
				array_push($css_class, 'mm_tocopy');
			}
		}

		return $loop_open . '<' . $t . ' class="'. implode(' ', $css_class) . '">';
	}

	/**
	 * Print the closing tag of a field group within a loop.
	 */
	function the_group_close()
	{
		echo $this->get_the_group_close();
	}

	/**
	 * Retrieve the closing tag of a field group within a loop.
     *
     * @return string the closing tag of the current group within a loop
	 */
	function get_the_group_close()
	{
		$loop_close = NULL;

		$curr_loop = $this->getCurrentLoop();

		if ($curr_loop->is_last())
		{
			$loop_close = '</' . $curr_loop->loop_tag . '>';
		}

		return '</' . $curr_loop->group_tag . '>' . $loop_close;
	}

	/**
	 * Create a repeatable field or field group.
	 *
	 * @param string $n a name for the field or field group
	 * @param int|null $length the initial number of items to display the form for, note this is a minimum
	 * 	but will be overridden if there is a larger number of saved values.
	 * @param null $limit limit the number of items that are allowed for this field or field group, leave as null for no limit.
	 * @return bool true if there are still values to iterate over within the current loop, false otherwise.
	 */
	function have_fields_and_multi($n, $length = NULL,$limit = NULL)
	{

		$this->meta(NULL, TRUE);
		$this->pushOrSetCurrentLoop($n, $length, 'multi', $limit);
		return $this->loop();
	}

	/**
	 * Create a fixed muber of a field or field group.
	 *
	 * @param string $n a name for the field or field group
	 * @param int|null $length the number of items to display the form for.
	 * @return bool true if there are still values to iterate over within the current loop, false otherwise.
	 */
	function have_fields($n,$length=NULL)
	{
		$this->meta(NULL, TRUE);
        $this->pushOrSetCurrentLoop($n, $length, 'normal');
		return $this->loop();
	}

	/**
	 * Iterate the current loop and determine if there are more values to show.
	 *
	 * @return bool true if there are still values to iterate over within the current loop, false otherwise.
	 */
    protected function loop()
	{
        $currentLoop = $this->getCurrentLoop();

		$cnt = $this->getCurrentLoopCount();

        $currentLoop->length = is_null($currentLoop->length) ? $cnt : $currentLoop->length;

		if ($currentLoop->type == 'multi' && $cnt > $currentLoop->length){
            $currentLoop->length = $cnt;
        }

        if($this->in_template && $currentLoop->and_one){
            $currentLoop->length++;
			$currentLoop->and_one = FALSE;
        }

		$currentLoop->current++;
		//error_log(print_r($currentLoop,true));
		if ($currentLoop->current < $currentLoop->length)
		{
			$this->name = NULL;

			return TRUE;
		}
		else if ($currentLoop->current == $currentLoop->length)
		{
			$this->name = NULL;
            $currentLoop->current = -1;
			$currentLoop->length = $currentLoop->initLength;
			$currentLoop->and_one = ($currentLoop->type=='multi');
			$this->popLoop();
		}

		return FALSE;
	}

	/**
	 * Save the posted meta data for the provided object ID.
	 *
	 * Classes of Metamaterial should implement this method to persist data appropriately.
	 *
	 * @param int $object_id the objects id to save data for.
	 * @param bool $is_ajax if the save is part of an ajax request.
	 * @return integer the $object_id
	 */
	public abstract function save($object_id,$is_ajax=FALSE);


    /**
     * Trigger a save from an ajax request.
	 *
	 * Verifies nonce and that an object if was provided before proceeding.
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
	 * Cleans an array, removing blank ('') values and arrays.
     *
     * Will reindex numerically keyed arrays
	 *
	 * @param array $arr the array to clean (passed by reference)
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
     * Push a new loop onto the loop stack
     *
	 * @param $name
	 * @param $length
	 * @param $type
	 * @param null $limit
	 * @return MM_Loop
	 */
	function pushLoop($name, $length, $type, $limit = NULL)
	{
		$loop         = new MM_Loop($name, $length, $type, $limit);
		$parent       = $this->getCurrentLoop();
		if($parent)
			$loop->parent = $parent->name;
		else
			$loop->parent = FALSE;
		$this->loop_stack[$name] = $loop;
		return $loop;
	}

	/**
     * Set the current loop within the loop stack pushing onto the stack if a loop fo this $name is not already present.
     *
	 * @param $name
	 * @param $length
	 * @param $type
	 * @param null $limit
	 */
	function pushOrSetCurrentLoop($name, $length, $type, $limit = NULL)
	{
		if( !array_key_exists( $name, $this->loop_stack ) )
		{
			$this->pushLoop($name, $length, $type, $limit);
		}

		$this->setCurrentLoop($name);
	}

	/**
     * Set the current loop within the loop stack.
     *
	 * @param $name string the name of the loop to set as current
	 */
	function setCurrentLoop($name)
	{
		reset($this->loop_stack);
		if(!array_key_exists($name, $this->loop_stack)){
			return;
		}
		while(key($this->loop_stack) !== $name){
			next($this->loop_stack);
        }
	}

	/**
     * Pop the last loop off of the loop stack returning to its parent if there is one.
     *
	 * @return bool return true if we are still in a loop after popping.
	 */
	function popLoop()
	{
		$parent = $this->getCurrentLoop()->parent;
		if($parent)
		{
			$this->setCurrentLoop($parent);
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
     * Get the stack of loop objects up to the current loop in an ordered array.
     *
     * @return MM_Loop[]
     */
    protected function getCurrentLoopStack()
	{
		$collection   = array();

        $curr = $this->getCurrentLoop();
        if($curr)
        {
            $loop_stack   = $this->loop_stack;
            $loop         = $loop_stack[$curr->name];
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

		return $collection;
	}

	/**
	 * Get the field name for the current focused nested meta value
	 *
	 * @return string
	 */
	protected function getNestedMetaName()
	{
		$loop_name  = $this->id;
		$curr       = $this->getCurrentLoop();

		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->getCurrentLoopStack();

		foreach ($loop_stack as $loop)
		{
			$loop_name .= '[' . $loop->name . '][' . $loop->current . ']';

			if($loop->name === $curr->name)
				break;
		}
		return $loop_name;
	}

	/**
	 * Get an ordered array of the names and indexes needed to build the field name of the current loop field.
     *
	 * @return array
	 */
	protected function getNestedMetaPath()
	{
		$loop_name   = array();
		$curr        = $this->getCurrentLoop();

		// copy loop_stack to prevent internal pointer ruined
		$loop_stack = $this->getCurrentLoopStack();
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
     * Get the value of a nested meta field
     *
	 * @param $keys array an ordered array of the names and indexes to be used to traverse to the requested meta value
	 * @return mixed the value of the requested meta field
	 */
	protected function getNestedMetaValue($keys)
	{
		$meta = $this->meta;

		if(!is_array($keys) || !is_array($meta) || is_null($meta)) {
            return null;
        }

		$value = $this->meta;
		foreach ($keys as $key)
		{
			if(is_array($meta) and array_key_exists($key, $value))
			{
				$value = $value[$key];
			}
			else
			{
				return null;
			}
		}
		return $value;
	}

	/**
     * Get the count of the number of saved items within the current loop
     *
	 * @return int the count of the number of saved items within the current loop
	 */
	function getCurrentLoopCount()
	{
		$arr  = $this->getNestedMetaPath();
		end($arr);
		$last = key($arr);
		unset($arr[$last]);
		$meta = $this->getNestedMetaValue($arr);
		return count($meta);
	}

	/**
     * Get the current loop within the loop stack
	 * @return MM_Loop
	 */
	protected function getCurrentLoop()
	{
		return current($this->loop_stack);
	}

	/**
     * Check if we are currently in any loop within a loop stack
     *
	 * @return bool if we are currently in any loop within a loop stack
	 */
	protected function isInLoop()
	{
		if(current($this->loop_stack) === FALSE) {
            return FALSE;
        }

		return TRUE;
	}

    /**
     * Ensure the value is an array, wrapping it in one if necessary.
     * Will create an array from a string of comma separated values.
     *
     * @param $value array|string the value to check.
     * @return array the value wrapping in an array if necessary.
     */
    public static function ensureArray($value){
            if (!empty($value) AND !is_array($value))
            {
                $value = array_map('trim',explode(',',$value));
            }
        return $value;
    }

}