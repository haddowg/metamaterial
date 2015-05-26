<?php
namespace HaddowG\MetaMaterial;

/**
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

/**
 * Class MM_Taxonomy
 *
 * @package HaddowG\MetaMaterial
 */
class MM_Taxonomy extends Metamaterial
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
     * Used to set the taxonomies that this metabox can appear in.
     * Defaults to 'category' and 'post_tag' taxonomies, to add your metabox to custom taxonomies you must define the taxonomies option.
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		array an array of post types that this metabox can appear on.
     */
     protected $taxonomies = array('category', 'post_tag');

    /**
     * Used to set the post types that this metabox can appear in the taxonomy screens of.
     * Defaults to empty (all types), to add your metabox to specific types only you must define the types option.
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		array an array of post types that this metabox can appear on.
     */
    private $types = array();

    /**
     * Used to hide the metabox title.
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
     * Exclude these terms.
     * Note: this uses term id's and not taxonomy term id's
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
     * @var		string|array Exclude any post object that belongs to one of these taxonomy terms
     */
    private $exclude_term_id;

    /**
     * Include these terms.
     * Note: this uses term id's and not taxonomy term id's
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
     * @var		string|array Exclude any post object that belongs to one of these taxonomy terms
     */
    private $include_term_id;

	/**
	 * @var bool
	 */
	private $show_on_new= TRUE;
    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */
    private $meta_term_id;

	/**
	 * @var bool
	 */
	private $in_new = FALSE;

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of admin page targets on which this MetaMaterial Class is designed to display
     * @see     is_target_admin()
     */
    protected static $admin_targets = array('edit-tags.php');


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
    protected static $hide_on_screen_styles = array(
        'tagcloud' => ' div.tagcloud{display:none;} ',
        'slug' => ' #addtag > .form-field:nth-of-type(2){display:none;} ',
        'parent' => ' body.hierarchical #addtag > .form-field:nth-of-type(3){display:none;} ',
        'description' => ' body.non-hierarchical #addtag >.form-field:nth-of-type(3){display:none;} body.hierarchical  #addtag >.form-field:nth-of-type(4){display:none;}'
    );

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
                'taxonomies'            =>  $this->taxonomies,
                'types'                 =>  $this->types,
                'show_on_new'           =>  $this->show_on_new,
                'mode'                  =>  $this->mode,
                'meta_key'              =>  $id,
                'context'               =>  $this->context,
                'priority'              =>  $this->priority,
                'hide_title'            =>  $this->hide_title,
                'hide_on_screen'        =>  $this->hide_on_screen,
                'exclude_term_id'       =>  $this->exclude_term_id,
                'include_term_id'       =>  $this->include_term_id,
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

            if(isset($config['compound_hide'])){
               self::$compound_hide = $config['compound_hide'];
            }

            $this->prefix = TRUE;

            // convert non-array values
            $prep_arrays = array
            (
                'taxonomies',
                'types',

                'exclude_term_id',
                'include_term_id',

                'hide_on_screen'
            );

            foreach ($prep_arrays as $v)
            {
                // ideally these values should be in array form, convert to array otherwise
                if (!empty($this->$v) AND !is_array($this->$v))
                {
                    $this->$v = array_map('trim',explode(',',$this->$v));
                }
            }
            add_filter('get_term', array($this, 'get_term'));
        }
        else
        {
            die('Config array not provided, at minimum template parameter must be provided.');
        }
    }

    /**
     *
     */
    protected function init(){
        // must be a targeted admin page
        if (!static::is_target_admin()) {
            return;
        }
        $screen = get_current_screen();
        $tax = $screen->taxonomy;

        add_action($tax . '_edit_form_fields', array($this,'render'));
        add_action('edit_' . $tax, array($this, 'save'));

        if($this->show_on_new){
            add_action($tax . '_add_form_fields', array($this,'render'));
            add_action('create_' . $tax, array($this, 'save'));
        }



    }

	/**
	 *
	 */
	public function prep(){
        parent::prep();
        foreach($this->taxonomies as $tax){
            add_action('create_' . $tax, array($this, 'save'));
        }
    }

	/**
	 *
	 */
	protected static function init_once(){
        add_filter( 'admin_body_class', 'HaddowG\MetaMaterial\MM_Taxonomy::add_taxonomy_body_classes' );
    }

	/**
	 * @param $classes
	 *
	 * @return string
	 */
	public static function add_taxonomy_body_classes($classes){
       global $taxnow;
       $classes = explode(' ',$classes);

       if($taxnow){
           $tax = get_taxonomy($taxnow);
           if($tax){
               if($tax->hierarchical){
                   $classes[] = 'hierarchical';
               }else{
                   $classes[] = 'non-hierarchical';
               }
           }
       }

       $classes[] = 'has_metamaterial';
       return implode(' ',$classes);
    }

	/**
	 *
	 */
	public function render()
    {
        $this->in_template = TRUE;
        $this->in_new = FALSE;
	    /** @noinspection PhpUnusedLocalVariableInspection */
	    $term = FALSE;
        $tid = $_REQUEST['tag_ID'];
        
        if(!empty($tid)){
	        /** @noinspection PhpUnusedLocalVariableInspection */
            $term = get_term($tid ,get_current_screen()->taxonomy);
        }else{
            $this->in_new = TRUE;
        }
        // shortcuts
	    /** @noinspection PhpUnusedLocalVariableInspection */
        $mb =& $this;
	    /** @noinspection PhpUnusedLocalVariableInspection */
        $metabox =& $this;
	    /** @noinspection PhpUnusedLocalVariableInspection */
        $mm =& $this;
	    /** @noinspection PhpUnusedLocalVariableInspection */
        $id = $this->id;
	    /** @noinspection PhpUnusedLocalVariableInspection */
        $meta = $this->meta(NULL, TRUE);

        echo '<' . ($this->is_new()?'div':'tr') . ' id="' . $this->id . '_metamaterial" class="mm_taxonomybox">' . ($this->is_new()?'':'<td colspan="2">');
        if(!$this->hide_title){
            echo '<h3 class="mm_taxonomybox_title">' . $this->title . '</h3>';
        }
        echo '<div class="mm_taxonomybox_content">';
        // use include because users may want to use one template for multiple meta boxes
	    /** @noinspection PhpIncludeInspection */
	    include $this->template;
        // create a nonce for verification
        echo '<input type="hidden" name="'. $this->id .'_nonce" value="' . wp_create_nonce($this->id) . '" />';
        echo '</div>';
        echo $this->is_new()?'</div>':'</td></tr>';
        $this->in_new = FALSE;
        $this->in_template = FALSE;

    }

	/**
	 * @param $term
	 *
	 * @return mixed
	 */
	function get_term($term)
    {
        if (in_array($term->taxonomy, $this->taxonomies))
        {
           $meta = self::meta($term->term_id);
            if(!empty($meta)){
            foreach ($meta as $n => $v)
            {
                // do not overwrite default values
                if (isset($term->$n))
                {
                    $term->{'_' . $n} = $v;
                }
                else
                {
                    $term->{$n} = $v;
                }
            }
            }

        }

        return $term;
    }

    /**
     *
     * @return bool
     */
    public function is_new(){
        return $this->in_new;
    }

    /**
     * @since	0.1
     */
    function can_output()
    {
        if(!is_null($this->will_show)){
            return $this->will_show;
        }

        $screen = get_current_screen();
        $tax = $screen->taxonomy;
        $tag_ID = $_REQUEST['tag_ID'];
        $type = get_query_var('post_type');
        $type = ($type=='')?'post':$type;

        $can_output = true;
        if(!empty($tag_ID)){
            if (
                !empty($this->exclude_term_id) OR

                !empty($this->include_term_id)
            ) {

                if (!empty($this->exclude_term_id))
                {
                    if(in_array($tag_ID,$this->exclude_term_id)){
                            $can_output = FALSE;
                    }
                }


                // excludes are not set use "include only" mode

                if
                (
                    empty($this->exclude_term_id)
                )
                {
                    $can_output = FALSE;
                }

                if (!empty($this->include_term_id))
                {
                    if (in_array($tag_ID,$this->include_term_id))
                    {
                        $can_output = TRUE;
                    }
                }
            }
        }

        if(!empty($this->types) && !in_array($type,$this->types)){
            $can_output= FALSE;
        }

        if (!in_array($tax, $this->taxonomies))
        {
            $can_output = FALSE;
        }
        // filter: output (can_output)
        if ($this->has_filter('output'))
        {
            $can_output = $this->apply_filters('output', $can_output, null, $this);
        }

        $this->will_show = $can_output;

        return $can_output;
    }


	/**
	 * @return string
	 */
	public function get_global_style()
    {
        if(self::is_target_admin()){
            $out='';
            $out .= static::build_global_style();
            $out .= ' div.mm_taxonomybox{ margin: 0 0 10px; padding: 8px 0;} ';
            $out .= ' .mm_taxonomybox td{ line-height: 1; margin-bottom: 0; padding: 15px 0 0; vertical-align: bottom;} ';
            $out .= ' .mm_taxonomybox_title{font-size: 20px; } #edittag .mm_taxonomybox_title{ font-weight: 400;}';
            $out .= ' .mm_taxonomybox_content{margin-bottom: 9px; padding: 0 10px 15px 0;} ';
            //hide tocopy's
            $out .= ' .mm_group.mm_tocopy { display:none; } ';

            return $out;
        }else{
            return '';
        }
    }


	/**
	 * @return bool|string
	 */
	public function get_global_script()
    {

        // must be a targeted admin page
        if (!self::is_target_admin()) {
            return FALSE;
        }

        ob_start();

        ?>
        <script type="text/javascript">
        /* <![CDATA[ */

        jQuery(function($)
        {
            $(document).ajaxComplete(function(event, xhr, settings) {
                if (xhr && xhr.readyState === 4 && xhr.status === 200
                    && settings.data && settings.data.indexOf('action=add-tag') >= 0) {

                    var res = wpAjax.parseAjaxResponse(xhr.responseXML, 'ajax-response');
                    if (!res || res.errors) {
                        return;
                    }
                    jQuery('img[data-placeholder]').each(function(){
                        jQuery(this).attr('src',jQuery(this).data('placeholder'));
                    });
                    jQuery('#addtag').trigger("reset");
                }
            });

            $('.mm_loop[data-mm_sortable="true"]').sortable();

            $(document).on('click', '[class*=mm_dodelete]', function(e){

                e.preventDefault();

                var $this = $(this).first();
                var $p = $this.closest('.mm_group, .mm_taxonomybox_content');

                var $the_name = $this.attr('class').match(/mm_dodelete-([a-zA-Z0-9_-]*)/i);

                $the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

                if(!$the_name && $p.hasClass('mm_taxonomybox_content')){
                    return false;
                }

                var $conf = ($this.attr('data-mm_delete_confirm')==='false')?$this.data('mm_delete_confirm'):$this.data('mm_delete_confirm')||'<?php echo self::DEFAULT_DELETE_CONFIRM; ?>';

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
                        $context = $p.parents('.mm_group, .mm_taxonomybox_content').first();
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

                var $p = $this.closest('.mm_group, .mm_taxonomybox_content');

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
                        p = $(this).parents('.mm_taxonomybox_content');

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
     * @param	int $term_id IGNORED
     * @param	bool $internal optional boolean if internally calling
     * @return	array
     * @see		the_meta()
     */
    function meta($term_id = NULL, $internal = FALSE)
    {
        if ( ! is_numeric($term_id))
        {
            if ($internal AND $this->meta_term_id)
            {
                $term_id = $this->meta_term_id;
            }
            else
            {
                $tid = $_REQUEST['tag_ID'];
                if($tid!==''){
                    $term_id = $tid;
                }else{
                    $term = get_query_var('term');
                    $tax = get_query_var('taxonomy');
                    if(!(empty($tax) || empty($term))){
                        $term_id = get_term_by( 'slug', $term, $tax )->term_id;
                    }else{
                        return false;
                    }
                }
            }
        }

        // this allows multiple internal calls to meta() without having to fetch data everytime
        if ($internal AND !empty($this->meta) AND $this->meta_term_id == $term_id) return $this->meta;

        $this->meta_term_id = $term_id;

        // self::STORAGE_MODE_ARRAY

        $meta = get_option( $this->meta_key . '_' . $this->meta_term_id );
		
        // self::STORAGE_MODE_EXTRACT

        $fields = get_option($this->meta_key . '_' . $this->meta_term_id . '_fields');

        if ( ! empty($fields) AND is_array($fields))
        {
            $meta = array();

            foreach ($fields as $field)
            {
                $field_noprefix = preg_replace('/^' . $this->meta_key . '_' . $this->meta_term_id . '_/i', '', $field);
                $meta[$field_noprefix] = get_option($field);
            }
        }

        $this->meta = $meta;
        return $this->meta;
    }

	/**
	 * @since    0.1
	 * @access    public
	 *
	 * @param null $term_id
	 * @param bool $is_ajax
	 *
	 * @return bool
	 */
    public function save($term_id=NULL, $is_ajax = false)
    {
        global $taxnow;

        if ( ! $taxnow )
            $taxnow = $_POST['taxonomy'];

        if ( ! $taxnow )
            wp_die( __( 'Invalid taxonomy' ) );

        $tax = get_taxonomy( $taxnow );
        if ( ! $tax )
            wp_die( __( 'Invalid taxonomy' ) );

        // make sure data came from our meta box, verify nonce, and check permissions
        $nonce = isset($_POST[$this->id .'_nonce']) ? $_POST[$this->id .'_nonce'] : NULL ;
        if (!(wp_verify_nonce($nonce, $this->id) && current_user_can($tax->cap->edit_terms))) return FALSE;

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
            $new_data = $this->apply_filters('save', $new_data, $term_id);


            if (FALSE === $new_data) return FALSE;

            self::clean($new_data);
        }

        // get current fields, use $real_post_id (checked for in both modes)
        $current_fields = get_option($this->meta_key . '_' . $term_id . '_fields');

        if ($this->mode == self::STORAGE_MODE_EXTRACT)
        {
            $new_fields = array();

            if (is_array($new_data))
            {
                
                foreach ($new_data as $k => $v)
                {

                    $field = $this->meta_key . '_' . $term_id . '_' . $k;

                    array_push($new_fields,$field);

                    $new_value = $new_data[$k];

                    if (is_null($new_value))
                    {
                        delete_option($field);
                    }
                    else
                    {
                        update_option($field, $new_value);
                    }
                }
            }

            $diff_fields = array_diff((array)$current_fields,$new_fields);

            if (is_array($diff_fields))
            {
                foreach ($diff_fields as $field)
                {
                    delete_option($field);
                }
            }

            delete_option($this->meta_key . '_' . $term_id . '_fields');

            if ( ! empty($new_fields))
            {
                add_option($this->meta_key . '_' . $term_id . '_fields', $new_fields);
            }

            if(!array_key_exists($this->meta_key,$new_fields)){
                delete_option($this->meta_key );
            }
        }
        else
        {
            if (is_null($new_data))
            {
                delete_option($this->meta_key . '_' . $term_id);
            }
            else
            {
                update_option($this->meta_key . '_' . $term_id, $new_data);
            }

            // keep data tidy, delete values if previously using self::STORAGE_MODE_EXTRACT
            if (is_array($current_fields))
            {
                foreach ($current_fields as $field)
                {
                    if($field !== $this->meta_key. '_' . $term_id){
                        delete_option($field);
                    }
                }

                delete_option($this->meta_key . '_fields');
            }
        }

        // action: save
        if ($this->has_action('save'))
        {
            $this->do_action('save', $new_data, NULL);
        }
        return TRUE;
    }

}