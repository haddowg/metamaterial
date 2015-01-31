<?php
namespace HaddowG\MetaMaterial;

use stdClass;

/**
 * @package     MetaMaterial
 * @author      Gregory Haddow
 * @copyright   Copyright (c) 2014, Gregory Haddow, http://www.greghaddow.co.uk/
 * @license     http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @version     0.1
 * @link        http://www.greghaddow.co.uk/MetaMaterial
 */

class MM_User extends MetaMaterial
{
    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONSTANTS
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONFIGURABLE OPTIONS/VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Used to hide the meta box title.
     * Note this will result in the html elements being removed from the document by javascript.
     * The "postbox" container for the metabox will have the "headless" class applied.
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		boolean Whether to hide the metabox's title
     * @see		$title
     */
    private $hide_title = FALSE;

    /**
     * Used to add additional classes to this metaboxes postbox.
     *
     * Config Option
     *
     * @since   0.1
     * @access  private
     * @var		array Array of additional classes to be added to this metaboxes postbox
     */
    private $postbox_classes;


    /**
     * Exclude any users belonging to one or more role(s).
     *
     * Value can be provided as:
     * a single string value  - 'administrator'
     * a string of comma separated values - 'administrator,editor'
     * an array of values - array('administrator','editor')
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		string|array Exclude any users belonging to these role(s)
     */
    private $exclude_role;

    /**
     * Exclude these user id(s).
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		string|array Exclude any user with these user id(s)
     */
    private $exclude_user_id;

    /**
     * Include any users belonging to one or more role(s).
     *
     * Value can be provided as:
     * a single string value  - 'administrator'
     * a string of comma separated values - 'administrator,editor'
     * an array of values - array('administrator','editor')
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		string|array Include any users belonging to these role(s)
     */
    private $include_role;

    /**
     * Include these user id(s).
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		string|array Include these user id(s)
     */
    private $include_user_id;

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * current post id being used for meta retrieval
     *
     * @since   0.1
     * @access  private
     * @var     string current post id being used for meta retrieval
     * @see     meta()
     */
    private $meta_user_id;

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of admin page targets on which this MetaMaterial Class is designed to display
     * @see     is_target_admin()
     */
    protected static $admin_targets = array('user-edit.php','user-new.php','profile.php','wp-login.php');


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
        'before_personal_table',
        'personal_table_top',
        'personal_table_bottom',
        'before_name_table',
        'name_table_top',
        'name_table_bottom',
        'before_contact_table',
        'contact_table_top',
        'contact_table_bottom',
        'before_about_table',
        'about_table_top',
        'about_table_bottom',
        'normal'
    );

    /**
     * Array of CSS rules used to hide default screen elements.
     * Used to dynamically build a style tag in get_global_style().
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of CSS rules used to hide default screen elements.
     * @see     get_global_style(), $hide_on_screen, $compound_hide
     */
    protected static $hide_on_screen_styles = array();

    /**
     * Private MetaMaterial Constructor
     *
     * @since   0.1
     * @access  private
     * @param   string  $id Unique id for this MetaMaterial instance
     * @param   array   $config Configuration options for this instance, see individual option documentation
     */
    protected function __construct($id, $config = array())
    {
        if (is_array($config))
        {

            $config_defaults = array
            (
                'title'                 =>  $this->title,
                'template'              =>  $this->template,
                'context'               =>  $this->context,
                'priority'              =>  $this->priority,
                'ajax_save'             =>  $this->ajax_save,
                'mode'                  =>  $this->mode,
                'meta_key'              =>  '_' . $id,
                'prefix'                =>  $this->prefix,
                'hide_on_screen'        =>  $this->hide_on_screen,
                'hide_title'            =>  $this->hide_title,
                'postbox_classes'		=>	$this->postbox_classes,
                'exclude_role'      =>  $this->exclude_role,
                'exclude_user_id'   =>  $this->exclude_user_id,
                'include_role'      =>  $this->include_role,
                'include_user_id'   =>  $this->include_user_id,
                'init_action'           =>  $this->init_action,
                'output_filter'         =>  $this->output_filter,
                'save_filter'           =>  $this->save_filter,
                'save_action'           =>  $this->save_action,
                'head_action'           =>  $this->head_action,
                'foot_action'           =>  $this->foot_action
            );


            //discard non config options and merge with defaults
            $conf = array_merge($config_defaults, array_intersect_key($config,$config_defaults));

            //set instance config options
            foreach ($conf as $n => $v) {
                $this->$n = $v;
            }

            // convert non-array values
            $prep_arrays = array
            (
                'exclude_role',
                'exclude_user_id',

                'include_role',
                'include_user_id',

                'postbox_classes'
            );

            foreach ($prep_arrays as $v)
            {
                // ideally these values should be in array form, convert to array otherwise
                if (!empty($this->$v) AND !is_array($this->$v))
                {
                    $this->$v = array_map('trim',explode(',',$this->$v));
                }
            }

        }
        else
        {
            die('Config array not provided, at minimum template parameter must be provided.');
        }
    }

    /**
     * Filters this metaboxes postbox classes.
     * Runs of the 'postbox_classes_{page}_{id}' wordpress hook to affect this metabox only.
     * Classes are added  or removed that are used to trigger javascript and target CSS for the behaviour of
     * metabox options $view, $lock, $hide_title and $hide-screen-option.
     * Additional classes can be added as desired using the $postbox_classes option.
     *
     * @since   0.1
     * @access  public
     * @param	array $classes current classes array
     * @return	array modified classes array
     * @see		global_init()
     */
    public function add_postbox_classes($classes){

		$classes[] = 'mm_userbox';

        if($this->postbox_classes && is_array($this->postbox_classes) && !empty($this->postbox_classes)){
            $classes = array_merge($classes, $this->postbox_classes);
        }
        return $classes;

    }

    /**
     *
     */
    protected function init(){

        global $typenow;

        // must be a targeted admin page
        if (!static::is_target_admin()) {
            return;
        }

        //add the metabox
        add_action( 'show_user_profile', array($this, 'render') );
        add_action( 'edit_user_profile', array($this, 'render') );

        //make it save
        add_action('personal_options_update', array($this,'save'));
        add_action('edit_user_profile_update', array($this,'save'));
    }

    protected function init_once(){
        add_filter( 'admin_body_class', 'HaddowG\MetaMaterial\MM_User::add_user_body_classes' );
    }

    public static function add_user_body_classes($classes){
        $classes = explode(' ',$classes);

        $classes[] = 'has_metamaterial';

        return implode(' ',$classes);
    }

    /**
     * Render a metabox from template file.
     * Exposes global post, MetaMaterial instance and meta array to template.
     * Appends nonce for verification.
     *
     * @since	0.1
     * @access	protected
     * @see		global_init()
     */
    public function render()
    {
        $this->in_template = TRUE;

        // also make current post data available
        global $post;

        // shortcuts
        $mb =& $this;
        $metabox =& $this;
        $mm =& $this;
        $id = $this->id;
        $meta = $this->meta(NULL, TRUE);

        // use include because users may want to use one template for multiple meta boxes
        include $this->template;

        // create a nonce for verification
        echo '<input type="hidden" name="'. $this->id .'_nonce" value="' . wp_create_nonce($this->id) . '" />';

        $this->in_template = FALSE;
    }

    /**
     * Used to get the current post id.
     *
     * @static
     * @since	0.1
     * @return	int post ID
     */
    static function get_user_id()
    {
        $p_user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null ;

        $g_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null ;

        $user_id = $g_user_id ? $g_user_id : $p_user_id;


        if (isset($user_id))
        {
            return (integer) $post_id;
        }

        return null;
    }

    /**
     * @since	0.1
     */
    function can_output()
    {
        if(!is_null($this->will_show)){
            return $this->will_show;
        }


        if(is_null(self::$current_post) || (isset(self::$current_post->id) && self::$current_post->id !== self::get_post_id())){
            self::$current_post = new stdClass;
            self::$current_post->id = self::get_post_id();
            self::$current_post->type = self::get_current_post_type();
        }

        $post_id = self::$current_post->id;

        if (
            (!isset(self::$current_post->template_file)) &&
            (
                !empty($this->exclude_template) OR
                !empty($this->include_template)
            )
        ) {

            self::$current_post->template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
        }

        if (
            (!isset(self::$current_post->format)) &&
            (
                !empty($this->exclude_post_format) OR
                !empty($this->include_post_format)
            )
        ) {

            self::$current_post->format =  get_post_format($post_id);
        }

        if (
            (!isset(self::$current_post->categories)) &&
            (
                !empty($this->exclude_category) OR
                !empty($this->exclude_category_id) OR
                !empty($this->include_category) OR
                !empty($this->include_category_id)
            )
        ) {
            self::$current_post->categories = wp_get_post_categories($post_id,'fields=all');
        }

        if (
            (!isset(self::$current_post->tags)) &&
            (
                !empty($this->exclude_tag) OR
                !empty($this->exclude_tag_id) OR
                !empty($this->include_tag) OR
                !empty($this->include_tag_id)
            )
        ) {
            self::$current_post->tags = wp_get_post_tags($post_id);
        }

        if (
            (!isset(self::$current_post->taxonomies)) &&
            (
                !empty($this->exclude_taxonomy) OR
                !empty($this->exclude_taxonomy_id) OR
                !empty($this->include_taxonomy) OR
                !empty($this->include_taxonomy_id)
            )
        ) {
            self::$current_post->taxonomies = wp_get_post_terms($post_id,'fields=all');
        }

        // processing order: "exclude" then "include"
        // processing order: "template" then "category" then "post"

        $can_output = TRUE; // include all

        if (
            !empty($this->exclude_template) OR
            !empty($this->exclude_post_format) OR
            !empty($this->exclude_category_id) OR
            !empty($this->exclude_category) OR
            !empty($this->exclude_tag_id) OR
            !empty($this->exclude_tag) OR
            !empty($this->exclude_taxonomy_id) OR
            !empty($this->exclude_taxonomy) OR
            !empty($this->exclude_post_id) OR

            !empty($this->include_template) OR
            !empty($this->include_post_format) OR
            !empty($this->include_category_id) OR
            !empty($this->include_category) OR
            !empty($this->include_tag_id) OR
            !empty($this->include_tag) OR
            !empty($this->include_taxonomy_id) OR
            !empty($this->include_taxonomy) OR
            !empty($this->include_post_id)
        ) {

            if (!empty($this->exclude_template))
            {
                if (in_array(self::$current_post->template_file,$this->exclude_template))
                {
                    $can_output = FALSE;
                }
            }

            if (!empty($this->exclude_post_format))
            {
                if (in_array(self::$current_post->format,$this->exclude_post_format))
                {
                    $can_output = FALSE;
                }
            }

            if (!empty($this->exclude_category_id))
            {
                foreach (self::$current_post->categories as $cat)
                {
                    if (in_array($cat->term_id,$this->exclude_category_id))
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_category))
            {
                foreach (self::$current_post->categories as $cat)
                {
                    if
                    (
                        in_array($cat->slug,$this->exclude_category) OR
                        in_array($cat->name,$this->exclude_category)
                    )
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_tag_id))
            {
                foreach (self::$current_post->tags as $tag)
                {
                    if (in_array($tag->term_id,$this->exclude_tag_id))
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_tag))
            {
                foreach (self::$current_post->tags as $tag)
                {
                    if
                    (
                        in_array($tag->slug,$this->exclude_tag) OR
                        in_array($tag->name,$this->exclude_tag)
                    )
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_taxonomy_id))
            {
                foreach (self::$current_post->taxonomies as $tax)
                {
                    if (in_array($tax->term_id,$this->exclude_taxonomy_id))
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_taxonomy))
            {
                foreach (self::$current_post->taxonomies as $tax)
                {
                    if
                    (
                        in_array($tax->slug,$this->exclude_taxonomy) OR
                        in_array($tax->name,$this->exclude_taxonomy)
                    )
                    {
                        $can_output = FALSE;
                        break;
                    }
                }
            }

            if (!empty($this->exclude_post_id))
            {
                if (in_array($post_id,$this->exclude_post_id))
                {
                    $can_output = FALSE;
                }
            }

            // excludes are not set use "include only" mode

            if
            (
                empty($this->exclude_template) AND
                empty($this->exclude_post_format) AND
                empty($this->exclude_category_id) AND
                empty($this->exclude_category) AND
                empty($this->exclude_tag_id) AND
                empty($this->exclude_tag) AND
                empty($this->exclude_taxonomy_id) AND
                empty($this->exclude_taxonomy) AND
                empty($this->exclude_post_id)
            )
            {
                $can_output = FALSE;
            }

            if (!empty($this->include_template))
            {
                if (in_array(self::$current_post->template_file,$this->include_template))
                {
                    $can_output = TRUE;
                }
            }

            if (!empty($this->include_post_format))
            {
                if (in_array(self::$current_post->format,$this->include_post_format))
                {
                    $can_output = TRUE;
                }
            }

            if (!empty($this->include_category_id))
            {
                foreach (self::$current_post->categories as $cat)
                {
                    if (in_array($cat->term_id,$this->include_category_id))
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_category))
            {
                foreach (self::$current_post->categories as $cat)
                {
                    if
                    (
                        in_array($cat->slug,$this->include_category) OR
                        in_array($cat->name,$this->include_category)
                    )
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_tag_id))
            {
                foreach (self::$current_post->tags as $tag)
                {
                    if (in_array($tag->term_id,$this->include_tag_id))
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_tag))
            {
                foreach (self::$current_post->tags as $tag)
                {
                    if
                    (
                        in_array($tag->slug,$this->include_tag) OR
                        in_array($tag->name,$this->include_tag)
                    )
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_taxonomy_id))
            {
                foreach (self::$current_post->taxonomies as $tax)
                {
                    if (in_array($tax->term_id,$this->include_taxonomy_id))
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_taxonomy))
            {
                foreach (self::$current_post->taxonomies as $tax)
                {
                    if
                    (
                        in_array($tax->slug,$this->include_taxonomy) OR
                        in_array($tax->name,$this->include_taxonomy)
                    )
                    {
                        $can_output = TRUE;
                        break;
                    }
                }
            }

            if (!empty($this->include_post_id))
            {
                if (in_array($post_id,$this->include_post_id))
                {
                    $can_output = TRUE;
                }
            }
        }

        if (isset(self::$current_post->type) AND ! in_array(self::$current_post->type, $this->types))
        {
            $can_output = FALSE;
        }

        // filter: output (can_output)
        if ($this->has_filter('output'))
        {
            $can_output = $this->apply_filters('output', $can_output, $post_id, $this);
        }

        $this->will_show = $can_output;
        return $can_output;
    }


    public function get_global_style()
    {
        if(self::is_target_admin()){
        $out='';
        $out .= static::build_global_style();

        //locked header style
        $out .= ' .postbox.locked > h3 { cursor:pointer; }';
        //open locked header style
        $out .= ' .postbox.locked.open > h3 { cursor:inherit; }';
        //hide tocopy's
        $out .= ' .mm_group.mm_tocopy { display:none; } ';

        return $out;
        }else{
            return '';
        }
    }

    /**
     *
     */
    public function get_global_script()
    {

        // must be a targeted admin page
        if (!self::is_target_admin()) {
            return;
        }

        ob_start();

        ?>
        <script type="text/javascript">
        /* <![CDATA[ */

        //runs after all document loads, i.e after standard wordpress js initialisation.
        jQuery(window).load(function()
        {
            //prevent collapse toggle for ALWAYS_OPEN metaboxes
            jQuery('.postbox.open > h3, .postbox.open .hndle').unbind('click.postboxes');

        });

        jQuery(function($)
        {
            //hide screen options
            jQuery('.postbox.hide-screen-option').each(function(){
                jQuery('.metabox-prefs label[for='+ jQuery(this).attr('id') +'-hide]').remove();
            });

            //remove titles for headless metaboxes
            jQuery('.postbox.headless > h3, .postbox.headless > .handlediv').remove();

            //remove toggle div for ALWAYS_OPEN metaboxes
            jQuery('.postbox.open .handlediv').remove();

            //prevent dragging of locked metaboxes
            jQuery('.postbox.locked > h3').removeClass('hndle');

            //move before_title metaboxes
            jQuery('#before_title-sortables').prependTo('#post-body-content');

            $('.mm_loop[data-mm_sortable="true"]').sortable();

            $(document).on('click', '[class*=mm_dodelete]', function(e){

                e.preventDefault();

                var $this = $(this).first();
                var $p = $this.closest('.mm_group, .postbox');

                var $the_name = $this.attr('class').match(/mm_dodelete-([a-zA-Z0-9_-]*)/i);

                $the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

                if(!$the_name && $p.hasClass('postbox')){
                    return false;
                }

                var $conf = ($this.attr('data-mm_delete_confirm')==='false')?$this.data('mm_delete_confirm'):$this.data('mm_delete_confirm')||'<?php echo __(self::DEFAULT_DELETE_CONFIRM); ?>';

                var $proceed = ($conf!==false)?confirm($conf):true;

                if ($proceed)
                {
                    var $context;

                    if ($the_name)
                    {
                        $context = $p;
                        $('.mm_group-'+ $the_name, $p).not('.mm_tocopy').remove();
                    }
                    else
                    {
                        $context = $p.parents('.mm_group, .postbox').first();
                        $p.remove();
                    }

                    if(!$the_name)
                    {
                        var $the_group = $this.closest('.mm_group');
                        if($the_group && $the_group.attr('class'))
                        {
                            $the_name = $the_group.attr('class').match(/mm_group-([a-zA-Z0-9_-]*)/i);
                            $the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;
                        }
                    }
                    checkLoopLimit($the_name,$context);
                    $context.trigger('mm_delete.'+$the_name, $the_name);
                    return true;
                }
                return false;
            });

            $(document).on('click', '[class*=mm_docopy-]',function(e)
            {
                e.preventDefault();

                var $this = $(this).first();

                var $p = $this.closest('.mm_group, .postbox');

                var $the_name = $this.attr('class').match(/mm_docopy-([a-zA-Z0-9_-]*)/i)[1];

                var $the_group = $('.mm_group-'+ $the_name +'.mm_tocopy', $p).first();

                var $the_clone = $the_group.clone().removeClass('mm_tocopy last');

                incrementIndex($the_group, $the_name);

                if ($this.hasClass('ontop'))
                {
                    $('.mm_group-'+ $the_name, $p).first().before($the_clone);
                }
                else
                {
                    $the_group.before($the_clone);
                }

                checkLoopLimit($the_name);

                $the_clone.trigger('mm_copy.'+$the_name, $the_name);
            });

            $(document).on('click', '[class*=mm_dodupe]',function(e)
            {
                e.preventDefault();

                var $this = $(this).first();

                var $p = $this.closest('.mm_group');

                if($p.length < 1){
                    return false;
                }

                var $the_clone = $p.clone().removeClass('first');

                var $the_name = $p.attr('class').match(/mm_group-([a-zA-Z0-9_-]*)/i);
                $the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

                if(!$the_name){
                    return false;
                }

                var $the_group = $('.mm_group-'+ $the_name +'.mm_tocopy', $p.parent()).first();

                var $index = incrementIndex($the_group, $the_name);
                incrementIndex($the_clone, $the_name, $index);

                if ($this.hasClass('ontop'))
                {
                    $('.mm_group-'+ $the_name, $p).first().before($the_clone);
                }
                else
                {
                    $the_group.before($the_clone);
                }

                checkLoopLimit($the_name);

                $the_clone.trigger('mm_dupe.'+$the_name, $the_name);
                return true;
            });

            $(document).on('click', '[class*=mm_doajax]',function(e)
            {
                e.preventDefault();

                var $metabox = $(this).closest('.postbox');
                $metabox.find('.mm_ajax_notice').html('').hide();
                var $mm_id = $metabox.attr('id').match(/([a-zA-Z0-9_-]*?)_metamaterial/i);
                $mm_id = ($mm_id && $mm_id[1]) ? $mm_id[1] : null ;
                if(!$mm_id){
                    return false;
                }
                var $pt = $('#post_type').eq(0).val();
                var $pid = $('#post_ID').eq(0).val();
                var $fields = $('[name^="' + $mm_id + '"]' ,$metabox);
                var $action = $(this).data('mm_ajax_action') || 'ajax_save'
                var $on_success = $(this).data('mm_on_success');
                var $on_error = $(this).data('mm_on_error');
                var $data = {
                    action: 'metamaterial_action_'+ $mm_id + '_' + $action,
                    mm_object_id: $pid,
                    mm_nonce: $('[name="'+ $mm_id + '_nonce"]',$metabox).eq(0).val(),
                    post_type: $pt
                    };
                $data = $.param($data) + '&' + $fields.serialize();
                $.post(ajaxurl,$data, function($response){ return process_ajax($response, $(this), $metabox, $on_success,$on_error);});
            });

            function process_ajax($response, $trigger, $metabox, $on_success, $on_error){
                //error
                if( !$response.success )
                {
                    if($on_error){
                        var $error_handler = window[$on_error];
                        if(typeof $error_handler === 'function') {
                            if(!$error_handler($response, $trigger, $metabox)){
                                return true;
                            }
                        }

                    }

                    return display_alert( $response.data.error ,'error', $metabox);
                }

                //success
                if($on_success){
                    var $success_handler = window[$on_success];
                    if(typeof $success_handler === 'function') {
                        if(!$success_handler($response,$trigger, $metabox)){
                            return true;
                        }
                    }
                }

                return display_alert( $response.data.message ,'success', $metabox);

            }

            function display_alert($message, $type, $metabox){
                var $class= $type=='error'?'error':($type=='success'?'updated':'update-nag');
                $metabox.find('.mm_ajax_notice').show().html('<div class="' + $class + '"><p>' + $message + '</p></div>').delay(4000).fadeOut();
            }

            function incrementIndex($container, $the_name, $index){
                var $the_props = ['name', 'id', 'for', 'class'];
                var $reg = new RegExp('\\['+$the_name+'\\]\\[(\\d+)\\]', 'i');
                var $reg2 =/-n(\d+)/gi;
                var $firstmatch =false;
                $container.find('.mm_loop *').addClass('mm_ignore');
                $container.find('*').each(function(i, elem)
                {
                    var $elem = $(elem);

                    for (var j = 0; j < $the_props.length; j++)
                    {
                        var $the_prop = $elem.attr($the_props[j]);

                        if ($the_prop)
                        {

                            var the_match = $the_prop.match($reg);

                            if (the_match)
                            {
                                if(!$firstmatch){ $firstmatch = the_match[1];}
                                var $newindex  = typeof $index !== 'undefined' ? $index : (+the_match[1]+1);

                                $the_prop = $the_prop.replace(the_match[0], '['+ $the_name + ']' + '['+ (+$newindex) +']');

                                $elem.attr($the_props[j], $the_prop);
                            }

                            the_match = null;
                            if(!$elem.hasClass('mm_ignore')){

                                $the_prop = $the_prop.replace($reg2,function(match, contents) {
                                    var $newindex  = typeof $index !== 'undefined' ? $index : (+contents+1);
                                    return '-n' + (+$newindex);
                                });
                                $elem.attr($the_props[j], $the_prop);
                            }else{
                                $elem.removeClass('mm_ignore');
                            }

                        }
                    }
                });
                return $firstmatch;
            }

            function checkLoopLimit(name,$context)
            {
                var elems = $('.mm_docopy-' + name, $context);

                $.each(elems, function(){

                    var p = $(this).parents('.mm_group:first');

                    if(p.length <= 0)
                        p = $(this).parents('.postbox');


                    var the_limit = $('.mm_loop-' + name, p).data('mm_loop_limit');
                    if(the_limit){
                        if ($('.mm_group-' + name, p).not('.mm_group.mm_tocopy').length >= the_limit)
                        {
                            $(this).hide();
                        }
                        else
                        {
                            $(this).show();
                        }
                    }

                });
            }

            /* do an initial limit check, show or hide buttons */
            $('[class*=mm_docopy-]').each(function()
            {
                var $the_name = $(this).attr('class').match(/mm_docopy-([a-zA-Z0-9_-]*)/i)[1];

                checkLoopLimit($the_name);
            });
        });
        /* ]]> */
        </script>
    <?php
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }

    /**
     * Gets the meta data for a meta box
     *
     * Internal method calls will typically bypass the data retrieval and will
     * immediately return the current meta data
     *
     * @since	0.1
     * @access	private
     * @param	int $post_id optional post ID for which to retrieve the meta data
     * @param	bool $internal optional boolean if internally calling
     * @return	array
     * @see		the_meta()
     */
    function meta($post_id = NULL, $internal = FALSE)
    {
        if ( ! is_numeric($post_id))
        {
            if ($internal AND $this->meta_post_id)
            {
                $post_id = $this->meta_post_id;
            }
            else
            {
                global $post;

                $post_id = $post->ID;
            }
        }

        // this allows multiple internal calls to meta() without having to fetch data everytime
        if ($internal AND !empty($this->meta) AND $this->meta_post_id == $post_id) return $this->meta;

        $this->meta_post_id = $post_id;

        // self::STORAGE_MODE_ARRAY

        $meta = get_post_meta($post_id, $this->meta_key, TRUE);

        // self::STORAGE_MODE_EXTRACT

        $fields = get_post_meta($post_id, $this->meta_key . '_fields', TRUE);

        if ( ! empty($fields) AND is_array($fields))
        {
            $meta = array();

            foreach ($fields as $field)
            {
                $field_noprefix = ($this->prefix)?preg_replace('/^' . $this->meta_key . '_/i', '', $field):$field;
                $meta[$field_noprefix] = get_post_meta($post_id, $field, TRUE);
            }
        }

        $this->meta = $meta;
        return $this->meta;
    }

    /**
     * @since	0.1
     * @access	public
     */
    public function save($post_id, $is_ajax=FALSE)
    {
        /**
         * note: the "save_post" action fires for saving revisions and post/pages,
         * when saving a post this function fires twice, once for a revision save,
         * and again for the post/page save ... the $post_id is different for the
         * revision save, this means that "get_post_meta()" will not work if trying
         * to get values for a revision (as it has no post meta data)
         * see http://alexking.org/blog/2008/09/06/wordpress-26x-duplicate-custom-field-issue
         *
         * why let the code run twice? wordpress does not currently save post meta
         * data per revisions (I think it should, so users can do a complete revert),
         * so in the case that this functionality changes, let it run twice
         */

        if(!$is_ajax){

            $real_post_id = isset($_POST['post_ID']) ? $_POST['post_ID'] : NULL ;

            // check autosave
            if (defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE AND !$this->autosave) return $post_id;


            // make sure data came from our meta box, verify nonce
            $nonce = isset($_POST[$this->id .'_nonce']) ? $_POST[$this->id .'_nonce'] : NULL ;
            if (!wp_verify_nonce($nonce, $this->id)) return $post_id;

        }else{
            $real_post_id = $_POST['mm_object_id'];
        }

        // check user permissions
        $pt = ($_POST['post_type'] == 'page')?'page':'post';

        if (!current_user_can('edit_'.$pt, $post_id)){
            if(!$is_ajax){
                return $post_id;
            }else{
                $ajax_return = $this->apply_filters('ajax_save_fail',array(
                    'error' => __( 'You do not have permission to edit this ') . $_POST['post_type']
                ));
                wp_send_json_error($ajax_return);
            }
        }


        // authentication passed, save data

        $new_data = isset( $_POST[$this->id] ) ? $_POST[$this->id] : NULL ;

        self::clean($new_data);

        if (empty($new_data))
        {
            $new_data = NULL;
        }
        // filter: save
        if ($this->has_filter('save'))
        {
            $new_data = $this->apply_filters('save', $new_data, $real_post_id, $is_ajax);

            /**
             * halt saving
             * @since 1.3.4
             */
            if (FALSE === $new_data){
                if(!$is_ajax){
                    return $post_id;
                }else{
                    $ajax_return = $this->apply_filters('ajax_save_fail',array(
                        'error' => __( 'Save Aborted') . $_POST['post_type']
                    ));
                    wp_send_json_error($ajax_return);
                }
            }

            self::clean($new_data);
        }

        // get current fields, use $real_post_id (checked for in both modes)
        $current_fields = get_post_meta($real_post_id, $this->id . '_fields', TRUE);

        if ($this->mode == self::STORAGE_MODE_EXTRACT)
        {
            $new_fields = array();

            if (is_array($new_data))
            {
                foreach ($new_data as $k => $v)
                {

                    $field = ($this->prefix)?$this->meta_key . '_' . $k : $k;

                    array_push($new_fields,$field);

                    $new_value = $new_data[$k];

                    if (is_null($new_value))
                    {
                        delete_post_meta($post_id, $field);
                    }
                    else
                    {
                        update_post_meta($post_id, $field, $new_value);
                    }
                }
            }

            $diff_fields = array_diff((array)$current_fields,$new_fields);

            if (is_array($diff_fields))
            {
                foreach ($diff_fields as $field)
                {
                    delete_post_meta($post_id,$field);
                }
            }

            delete_post_meta($post_id, $this->meta_key . '_fields');

            if ( ! empty($new_fields))
            {
                add_post_meta($post_id,$this->meta_key . '_fields', $new_fields, TRUE);
            }

            // keep data tidy, delete values if previously using self::STORAGE_MODE_ARRAY
            if(!array_key_exists($this->meta_key,$new_fields)){
                delete_post_meta($post_id, $this->meta_key );
            }
        }
        else
        {
            if (is_null($new_data))
            {
                delete_post_meta($post_id, $this->meta_key );
            }
            else
            {
                update_post_meta($post_id, $this->meta_key , $new_data);
            }

            // keep data tidy, delete values if previously using self::STORAGE_MODE_EXTRACT
            if (is_array($current_fields))
            {
                foreach ($current_fields as $field)
                {
                    if($field !== $this->meta_key){
                        delete_post_meta($post_id, $field);
                        delete_post_meta($post_id, $this->meta_key . $field);
                    }
                }

                delete_post_meta($post_id, $this->meta_key  . '_fields');
            }
        }

        // action: save
        if ($this->has_action('save'))
        {
            $this->do_action('save', $new_data, $real_post_id, $is_ajax);
        }

        if($is_ajax){
            $ajax_return = array(
                'message'=> __('Save Successful.'),
                'fields' => $new_data
            );
            $ajax_return = $this->apply_filters('ajax_save_success',$ajax_return);
            wp_send_json_success($ajax_return);
        }

        return $post_id;
    }

}

