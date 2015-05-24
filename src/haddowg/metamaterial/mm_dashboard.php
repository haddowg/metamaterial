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


class MM_Dashboard extends Metamaterial
{
    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONSTANTS
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /** view option */
    const VIEW_OPEN = 'open';

    /** view option */
    const VIEW_CLOSED = 'closed';

    /** view option */
    const VIEW_ALWAYS_OPEN = 'always_open';

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  CONFIGURABLE OPTIONS/VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    private $frontend;

    private $in_front = FALSE;

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
     * Used to prevent dragging of a metabox.
     * Note it is still possible to reorder metaboxes by dragging unlocked boxes above or below a locked metabox.
     * The "postbox" container for the metabox will have the "locked" class applied.
     *
     * Config Option
     *
     * @access	private
     * @var		boolean Whether to prevent
     */
    private $lock = FALSE;

    /**
     * Used to set the initial view state of the metabox.
     * possible values are:
     * VIEW_OPEN, VIEW_CLOSED, VIEW_ALWAYS_OPEN
     * If VIEW_ALWAYS_OPEN the "postbox" container for the metabox will have the "open" class applied.
     *
     * Config Option
     *
     * @since	0.1
     * @access	private
     * @var		string possible values are: VIEW_OPEN, VIEW_CLOSED, VIEW_ALWAYS_OPEN
     * @see     VIEW_OPEN, VIEW_CLOSED, VIEW_ALWAYS_OPEN
     */
    private $view;

    /**
     * Used to hide the show/hide checkbox option from the screen options area.
     * The "postbox" container for the metabox will have the "hide-screen-option" class applied.
     *
     * Config Option
     *
     * @since   0.1
     * @access  private
     * @var		boolean
     */
    private $hide_screen_option = FALSE;

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
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @since   0.1
     * @access  protected
     * @var     array Array of admin page targets on which this MetaMaterial Class is designed to display
     * @see     is_target_admin()
     */
    protected static $admin_targets = array('index.php');


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
        'side',
        'column3',
        'column4');

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


    protected $configure_button = 'Configure';

    protected $cancel_button    = 'Cancel';

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
                'frontend'              =>  $this->frontend,
                'mode'                  =>  $this->mode,
                'meta_key'              =>  $id,
                'prefix'                =>  TRUE,
                'context'               =>  $this->context,
                'priority'              =>  $this->priority,
                'hide_on_screen'        =>  $this->hide_on_screen,
                'hide_title'            =>  $this->hide_title,
                'lock'                  =>  $this->lock,
                'view'                  =>  $this->view,
                'hide_screen_option'    =>  $this->hide_screen_option,
                'postbox_classes'		=>	$this->postbox_classes,
                'init_action'           =>  $this->init_action,
                'output_filter'         =>  $this->output_filter,
                'save_filter'           =>  $this->save_filter,
                'save_action'           =>  $this->save_action,
                'head_action'           =>  $this->head_action,
                'foot_action'           =>  $this->foot_action,
                'configure_button'      =>  $this->configure_button,
                'cancel_button'         =>  $this->cancel_button
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

            if($this->context=='column1'){
                $this->context= 'normal';
            }
            if($this->context=='column2'){
                $this->context= 'side';
            }

            // convert non-array values
            $prep_arrays = array
            (
                'hide_on_screen',
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
            if (empty($this->template) && empty($this->frontend)) die('Metabox template or frontend file required');

            //check valid template
            if (empty($this->frontend)) {$this->frontend = $this->template;};
            //if the template is not found
            if(!file_exists($this->frontend)){
                //try relative to the default /metabox/ directory in the theme folder
                if(file_exists(get_stylesheet_directory() . '/metaboxes/' . $this->frontend)){
                    $this->frontend = get_stylesheet_directory() . '/metaboxes/' . $this->frontend;
                }else{
                    die('Unable to locate Dashboard frontend template');
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


        if($this->view == self::VIEW_ALWAYS_OPEN || $this->view == self::VIEW_OPEN){
            if(($key = array_search('closed', $classes)) !== FALSE) {
                unset($classes[$key]);
            }
        }

        if($this->hide_screen_option){
            $classes[] = 'hide-screen-option';
        }

        if($this->view == self::VIEW_ALWAYS_OPEN){
            $classes[] = 'open';
        }

        if($this->lock){
            $classes[] = 'locked';
        }

        if($this->hide_title){
            $classes[] = 'headless';
        }

        if($this->ajax_save){
            $classes[] = 'ajax-save';
        }


        if($this->postbox_classes && is_array($this->postbox_classes) && !empty($this->postbox_classes)){
            $classes = array_merge($classes, $this->postbox_classes);
        }
        return $classes;

    }

    /**
     *
     */
    protected function init(){
        // must be a targeted admin page
        if (!static::is_target_admin()) {
            return;
        }
        $id = $this->id . '_metamaterial';
        $title = $this->get_title();
        $callback = array($this, 'render');
        if ( $this->frontend && $this->frontend!==$this->template && current_user_can( 'edit_dashboard' )  ) {
            if ( isset( $_GET['edit'] ) && $id == $_GET['edit'] ) {
                list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
                $title .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . $this->cancel_button . '</a></span>';
                $callback = array($this,'render_config');
            } else {
                list($url) = explode( '#', add_query_arg( 'edit', $id ), 2 );
                $title .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$id" ) . '" class="edit-box open-box">' . $this->configure_button . '</a></span>';
            }
        }

        //add the metabox
        add_meta_box($id, $title, $callback, 'dashboard', $this->get_context(), $this->get_priority(FALSE,TRUE));

        //add postbox classes
        add_filter('postbox_classes_dashboard_' . $id, array($this,'add_postbox_classes'));

        add_action('wp_dashboard_setup',array($this,'register_callback'));

    }

    public function prep(){
        parent::prep();
        add_action('wp_ajax_' . $this->get_action_tag('ajax_get_front'), array($this, 'ajax_get_front'));
        add_action('wp_ajax_' . $this->get_action_tag('ajax_get_config'), array($this, 'ajax_get_config'));
    }

    protected function init_once(){

    }

    public function render()
    {
        $this->in_front = TRUE;

        // shortcuts
        $mb =& $this;
        $metabox =& $this;
        $mm =& $this;
        $id = $this->id;
        $meta = $this->meta(NULL, TRUE);

        // use include because users may want to use one template for multiple meta boxes
        include $this->frontend;
        echo '<input type="hidden" name="'. $this->id .'_nonce" value="' . wp_create_nonce($this->id) . '" />';
        $this->in_front = FALSE;
    }

    public function render_config()
    {
        $this->in_template = TRUE;
        $full_id = $this->id . '_metamaterial';
        // shortcuts
        $mb =& $this;
        $metabox =& $this;
        $mm =& $this;
        $id = $this->id;
        $meta = $this->meta(NULL, TRUE);
        echo '<form action="" method="post" class="dashboard-widget-control-form metamaterial-dashboard-control-form">';
        // use include because users may want to use one template for multiple meta boxes
        include $this->template;

        wp_nonce_field( 'edit-dashboard-widget_' . $full_id, 'dashboard-widget-nonce' );
        // create a nonce for verification
        echo '<input type="hidden" name="'. $this->id .'_nonce" value="' . wp_create_nonce($this->id) . '" />';
        echo '<input type="hidden" name="widget_id" value="' . esc_attr($full_id) . '" />';
        submit_button( __('Submit') );
        echo '</form>';
        $this->in_template = FALSE;
    }

    public function register_callback(){
        global $wp_dashboard_control_callbacks;

        if ( $this->frontend && $this->frontend!==$this->template && current_user_can( 'edit_dashboard' )  ) {
            $wp_dashboard_control_callbacks[$this->id . '_metamaterial'] = array($this,'save');
        }
    }
    /**
     *
     * @return bool
     */
    public function is_in_front(){
        return $this->in_front;
    }

    public function state_toggle_button($classes='button'){
        echo $this->get_state_toggle_button($classes);
    }

    public function get_state_toggle_button($classes='',$force=FALSE){
        $id = $this->id . '_metamaterial';
        if ( $this->frontend && $this->frontend!==$this->template && current_user_can( 'edit_dashboard' )  ) {
            if ((isset( $_GET['edit'] ) && $id == $_GET['edit']) || $force=='cancel' ) {
                list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
                return '<a class="' . $classes . '" href="' . esc_url( $url ) . '">' . $this->cancel_button . '</a>';
            } else {
                list($url) = explode( '#', add_query_arg( 'edit', $id ), 2 );
                return '<a class="' . $classes . '" href="' . esc_url( "$url#$id" ) . '" class="edit-box open-box">' . $this->configure_button . '</a></span>';
            }
        }else{
            return false;
        }
    }
    /**
     * @since	0.1
     */
    function can_output()
    {
        if(!is_null($this->will_show)){
            return $this->will_show;
        }
        $can_output = true;
        // filter: output (can_output)
        if ($this->has_filter('output'))
        {
            $can_output = $this->apply_filters('output', $can_output, null, $this);
        }

        $this->will_show = $can_output;
        return $can_output;
    }


    public function get_global_style()
    {
        if(self::is_target_admin()){
            $out='';
            $out .= static::build_global_style();
            //open header action style
            $out .='.js #dashboard-widgets .postbox.open > h3 .postbox-title-action { right:10px; }';
            //locked header style
            $out .= ' .postbox.locked > h3 { cursor:pointer; }';
            $out .= ' .postbox.locked > h3 a { font-size: 11px; font-weight: 400;}';

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
            return FALSE;
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
            $('.postbox.hide-screen-option').each(function(){
                $('.metabox-prefs label[for='+ $(this).attr('id') +'-hide]').remove();
            });

            //remove titles for headless metaboxes
            $('.postbox.headless > h3, .postbox.headless > .handlediv').remove();

            //remove toggle div for ALWAYS_OPEN metaboxes
            $('.postbox.open .handlediv').remove();

            //prevent dragging of locked metaboxes
            $('.postbox.locked > h3').removeClass('hndle');

            $('.mm_loop[data-mm_sortable="true"]').sortable();


            $(document).on('click','.postbox.ajax-save .postbox-title-action a, .postbox.ajax-save .mm-dashboard-toggle a',function(e){
                e.preventDefault();
                var $metabox = $(this).closest('.postbox');

                var $mm_id = $metabox.attr('id').match(/([a-zA-Z0-9_-]*?)_metamaterial/i);
                $mm_id = ($mm_id && $mm_id[1]) ? $mm_id[1] : null ;
                if(!$mm_id){
                    return false;
                }


                var $data = {
                    action: 'metamaterial_action_'+ $mm_id + '_',
                    mm_object_id: -1,
                    mm_nonce: $('[name="'+ $mm_id + '_nonce"]',$metabox).eq(0).val()
                };
                if($metabox.hasClass('in-config')){
                    $data.action = $data.action + 'ajax_get_front';
                    $metabox.removeClass('in-config');
                }else{
                    $data.action = $data.action + 'ajax_get_config';
                    $metabox.addClass('in-config');
                }
                $.post(ajaxurl,$data, function($response){
                    $metabox.children('.inside').eq(0).html($response.data.content);
                    $metabox.find('.postbox-title-action, .mm-dashboard-toggle').html($response.data.button);
                });
            });

            $(document).on('click', '[class*=mm_dodelete]', function(e){

                e.preventDefault();

                var $this = $(this).first();
                var $p = $this.closest('.mm_group, .postbox');

                var $the_name = $this.attr('class').match(/mm_dodelete-([a-zA-Z0-9_-]*)/i);

                $the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

                if(!$the_name && $p.hasClass('postbox')){
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

            $(document).on('click', '.postbox.ajax-save #submit, [class*=mm_doajax]',function(e)
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

                $metabox.children('.inside').eq(0).html($response.data.front);
                $metabox.find('.postbox-title-action, .mm-dashboard-toggle').html($response.data.button);
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
     * @since	0.1
     * @access	public
     */
    public function ajax_get_front(){
        $this->ajax_get(TRUE);
    }
    /**
     * @since	0.1
     * @access	public
     */
    public function ajax_get_config(){
        $this->ajax_get(FALSE);
    }
    /**
     * @since	0.1
     * @access	public
     */
    public function ajax_get($front=FALSE){
        error_log('HIT: ' . print_r($_POST,true));
        check_ajax_referer($this->id,'mm_nonce');
        if(isset($_POST['mm_object_id'])){
            if($front){
                $html = '';
                $button = $this->get_state_toggle_button();
                ob_start();
                $this->render();
                $html = ob_get_contents();
                ob_end_clean();
            }else{
                $html = '';
                $button = $this->get_state_toggle_button('',TRUE);
                ob_start();
                $this->render_config();
                $html = ob_get_contents();
                ob_end_clean();
            }
            $ajax_return = array(
                'content' => $html,
                'button' => $button
            );
            wp_send_json_success($ajax_return);
        }else{
            wp_send_json_error( array(
                'error' => __( 'Object ID not set, no data was saved.' )
            ));
        }

    }

    /**
     * Gets the meta data for a meta box
     *
     * Internal method calls will typically bypass the data retrieval and will
     * immediately return the current meta data
     *
     * @since	0.1
     * @access	private
     * @param	int $object_id IGNORED
     * @param	bool $internal optional boolean if internally calling
     * @return	array
     * @see		the_meta()
     */
    function meta($object_id = NULL, $internal = FALSE)
    {

        // this allows multiple internal calls to meta() without having to fetch data everytime
        if ($internal AND !empty($this->meta)) return $this->meta;

        // self::STORAGE_MODE_ARRAY

        $meta = get_option( $this->meta_key );

        // self::STORAGE_MODE_EXTRACT

        $fields = get_option($this->meta_key . '_fields');

        if ( ! empty($fields) AND is_array($fields))
        {
            $meta = array();

            foreach ($fields as $field)
            {
                $field_noprefix = ($this->prefix)?preg_replace('/^' . $this->meta_key . '_/i', '', $field):$field;
                $meta[$field_noprefix] = get_option($field);
            }
        }

        $this->meta = $meta;

        return $this->meta;
    }

    /**
     * @since	0.1
     * @access	public
     */
    public function save($object_id=NULL, $is_ajax = FALSE)
    {
        if(!$is_ajax){
        // make sure data came from our meta box, verify nonce
        $nonce = isset($_POST[$this->id .'_nonce']) ? $_POST[$this->id .'_nonce'] : NULL ;
        if (!(wp_verify_nonce($nonce, $this->id))) return FALSE;

        }

        //check user permissions
        if (!current_user_can( 'edit_dashboard' )){
           if(!$is_ajax){
                return FALSE;
           }else{
               $ajax_return = $this->apply_filters('ajax_save_fail',array(
                   'error' => __( 'You do not have permission to edit this dashboard widget')
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
            $new_data = $this->apply_filters('save', $new_data, NULL, $is_ajax);


            if (FALSE === $new_data){
                if(!$is_ajax){
                    return FALSE;
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
        $current_fields = get_option($this->meta_key . '_fields');

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

            delete_option($this->meta_key . '_fields');

            if ( ! empty($new_fields))
            {
                add_option($this->meta_key . '_fields', $new_fields);
            }

            if(!array_key_exists($this->meta_key,$new_fields)){
                delete_option($this->meta_key );
            }
        }
        else
        {
            if (is_null($new_data))
            {
                delete_option($this->meta_key );
            }
            else
            {
                update_option($this->meta_key, $new_data);
            }

            // keep data tidy, delete values if previously using self::STORAGE_MODE_EXTRACT
            if (is_array($current_fields))
            {
                foreach ($current_fields as $field)
                {
                    if($field !== $this->meta_key){
                        delete_option($field);
                        delete_option($this->meta_key . $field);
                    }
                }

                delete_option($this->meta_key . '_fields');
            }
        }

        // action: save
        if ($this->has_action('save'))
        {
            $this->do_action('save', $new_data, NULL, $is_ajax);
        }

        if($is_ajax){
            $front = '';
            ob_start();
                $this->render();
                $front = ob_get_contents();
            ob_end_clean();
            $ajax_return = array(
                'message'=> __('Save Successful.'),
                'fields' => $new_data,
                'front' => $front,
                'button' => $this->get_state_toggle_button()
            );
            $ajax_return = $this->apply_filters('ajax_save_success',$ajax_return);
            wp_send_json_success($ajax_return);
        }

        return TRUE;
    }

}