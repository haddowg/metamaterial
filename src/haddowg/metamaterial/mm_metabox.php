<?php
/**
 * MM_Metabox Class.
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

use stdClass;

/**
 * Class MM_Metabox
 *
 * @package HaddowG\MetaMaterial
 */
class MM_Metabox extends Metamaterial
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
     *  TYPE SPECIFIC VARIABLE OVERRIDES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Array of admin page targets on which this MetaMaterial Class is designed to display.
     *
     * @var     array Array of admin page targets on which this MetaMaterial Class is designed to display
     */
    protected static $admin_targets = array('post.php','post-new.php');


    /**
     * Array of priorities with numerical equivalents.
     * Used to order metaboxes within a page context.
     * Plugin/Theme developers should avoid default use of 'top' or 'bottom' to allow end users to more easily adjust as desired.
     *
     * @var     array Array of priorities with numerical equivalents.
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
     *
     * @var     array Array of priorities with numerical equivalents.
     */
    protected static $contexts = array(
        'before_title',
        'after_title',
        'after_editor',
        'normal',
        'advanced',
        'side'
    );

    /**
     * @var     array Array of CSS rules used to hide default screen elements.
     */
    public static $hide_on_screen_styles = array(
        'permalink'     =>  '#edit-slug-box {display: none;}',
        'the_content'   =>  '#postdivrich {display: none;}',
        'editor'        =>  '#postdivrich {display: none;}',
        'excerpt'       =>  '#postexcerpt, #screen-meta label[for="postexcerpt-hide"] {display: none;}',
        'custom_fields' =>  '#postcustom, #screen-meta label[for="postcustom-hide"] { display: none; }',
        'discussion'    =>  '#commentstatusdiv, #screen-meta label[for="commentstatusdiv-hide"] {display: none;}',
        'comments'      =>  '#commentsdiv, #screen-meta label[for="commentsdiv-hide"] {display: none;}',
        'slug'          =>  '#slugdiv, #screen-meta label[for="slugdiv-hide"] {display: none;}',
        'author'        =>  '#authordiv, #screen-meta label[for="authordiv-hide"] {display: none;}',
        'format'        =>  '#formatdiv, #screen-meta label[for="formatdiv-hide"] {display: none;}',
        'featured_image'=>  '#postimagediv, #screen-meta label[for="postimagediv-hide"] {display: none;}',
        'revisions'     =>  '#revisionsdiv, #screen-meta label[for="revisionsdiv-hide"] {display: none;}',
        'categories'    =>  '#categorydiv, #screen-meta label[for="categorydiv-hide"] {display: none;}',
        'tags'          =>  '#tagsdiv-post_tag, #screen-meta label[for="tagsdiv-post_tag-hide"] {display: none;}',
        'send-trackbacks'=> '#trackbacksdiv, #screen-meta label[for="trackbacksdiv-hide"] {display: none;}' ,
        'page_attributes'=> '#pageparentdiv, #screen-meta label[for="pageparentdiv-hide"] {display: none;}'
    );


    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  TYPE SPECIFIC CONFIGURABLE OPTIONS/VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Used to set the post types that this metabox can appear in.
     * Defaults to 'post' and 'page' types, to add your metabox to custom post types you must define the types option.
     *
     * Config Option
     *
     * @var		array an array of post types that this metabox can appear on.
     */
    private $types = array('post', 'page');

    /**
     * Whether to save this metaboxes data on autosave.
     * Defaults to TRUE.
     *
     * Config Option
     *
     * @var		bool Whether to save this metaboxes data on autosave.
     */
    private $autosave = TRUE;

    /**
     * Used to hide the meta box title.
     * Note this will result in the html elements being removed from the document by javascript.
     * The "postbox" container for the metabox will have the "headless" class applied.
     *
     * Config Option
     *
     * @var		boolean Whether to hide the metabox's title
     */
    private $hide_title = FALSE;

    /**
     * Used to prevent dragging of a metabox.
     * Note it is still possible to reorder metaboxes by dragging unlocked boxes above or below a locked metabox.
     * The "postbox" container for the metabox will have the "locked" class applied.
     *
     * Config Option
     *
     * @var		boolean Whether to prevent
     */
    private $lock = FALSE;

    /**
     * Used to set the initial view state of the metabox.
     * possible values are:
     * VIEW_OPEN, VIEW_CLOSED, VIEW_ALWAYS_OPEN
     * If VIEW_ALWAYS_OPEN the "postbox" container for the metabox will have the "open" class applied.
     * If VIEW_CLOSED the "postbox" container for the metabox will have the "closed" class applied.
     *
     * Config Option
     *
     * @var		string possible values are: VIEW_OPEN, VIEW_CLOSED, VIEW_ALWAYS_OPEN
     */
    private $view;

    /**
     * Used to hide the show/hide checkbox option from the screen options area.
     * The "postbox" container for the metabox will have the "hide-screen-option" class applied.
     *
     * Config Option
     *
     * @var		boolean
     */
    private $hide_screen_option = FALSE;

    /**
     * Used to add additional classes to this metaboxes postbox.
     *
     * Config Option
     *
     * @var		array Array of additional classes to be added to this metaboxes postbox
     */
    private $postbox_classes;

    /**
     * Exclude any post object that uses any of these page templates.
     *
     * Value can be provided as:
     * a single string value  - 'my_template.php'
     * a string of comma separated values - 'my_template.php,my_other_template.php'
     * an array of values - array('my_template.php','my_other_template.php')
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that uses any of these page templates
     */
    private $exclude_template;

    /**
     * Exclude any post object that is one of these post formats.
     *
     * Value can be provided as:
     * a single string value  - 'gallery'
     * a string of comma separated values - 'gallery,image'
     * an array of values - array('gallery','image')
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that is one of these post formats
     */
    private $exclude_post_format;

    /**
     * Exclude any post object that belongs to any of these categories.
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that belongs to one of these categories
     */
    private $exclude_category_id;

    /**
     * Exclude any post object that belongs to any of these categories.
     * This will match slugs or category names
     *
     * Value can be provided as:
     * a single string value  - 'blog'
     * a string of comma separated values - 'blog,portfolio'
     * an array of values - array('Blog','My Portfolio')
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that belongs to one of these categories
     */
    private $exclude_category;

    /**
     * Exclude any post object that belongs to any of these post tags.
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that belongs to one of these post tags
     */
    private $exclude_tag_id;

    /**
     * Exclude any post object that belongs to any of these post tags.
     * This will match slugs or category names
     *
     * Value can be provided as:
     * a single string value  - 'boring'
     * a string of comma separated values - 'boring,interesting'
     * an array of values - array('Boring','Interesting Stuff')
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that belongs to one of these post tags
     */
    private $exclude_tag;

    /**
     * Exclude any post object that belongs to any of these taxonomy terms.
     * This is a lot broader than $exclude_category_id or $exclude_tag_id and will consider all taxonomies.
     * Note: this uses term id's and not taxonomy term id's
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Exclude any post object that belongs to one of these taxonomy terms
     */
    private $exclude_taxonomy_id;

    /**
     * Exclude any post object that belongs to any of these taxonomy terms.
     * This is a lot broader than $exclude_category or $exclude_tag and will consider all taxonomies.
     *
     * Value can be provided as:
     * a single string value  - 'boring'
     * a string of comma separated values - 'boring,interesting'
     * an array of values - array('Boring','Interesting Stuff')
     *
     * @var		string|array Exclude any post object that belongs to one of these taxonomy terms
     */
    private $exclude_taxonomy;

    /**
     * Exclude these post id(s).
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Exclude any post object wth these id(s)
     */
    private $exclude_post_id;

    /**
     * Include any post object that uses any of these page templates.
     *
     * Value can be provided as:
     * a single string value  - 'my_template.php'
     * a string of comma separated values - 'my_template.php,my_other_template.php'
     * an array of values - array('my_template.php','my_other_template.php')
     *
     * Config Option
     *
     * @var		string|array Include any post object that uses any of these page templates
     */
    private $include_template;

    /**
     * Include any post object that is one of these post formats.
     *
     * Value can be provided as:
     * a single string value  - 'gallery'
     * a string of comma separated values - 'gallery,image'
     * an array of values - array('gallery','image')
     *
     * Config Option
     *
     * @var		string|array Include any post object that is one of these post formats
     */
    private $include_post_format;

    /**
     * Include any post object that belongs to any of these categories.
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these categories
     */
    private $include_category_id;

    /**
     * Include any post object that belongs to any of these categories.
     * This will match slugs or category names
     *
     * Value can be provided as:
     * a single string value  - 'blog'
     * a string of comma separated values - 'blog,portfolio'
     * an array of values - array('Blog','My Portfolio')
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these categories
     */
    private $include_category;

    /**
     * Include any post object that belongs to any of these post tags.
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these post tags
     */
    private $include_tag_id;

    /**
     * Include any post object that belongs to any of these post tags.
     * This will match slugs or category names
     *
     * Value can be provided as:
     * a single string value  - 'boring'
     * a string of comma separated values - 'boring,interesting'
     * an array of values - array('Boring','Interesting Stuff')
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these post tags
     */
    private $include_tag;

    /**
     * Include any post object that belongs to any of these taxonomy terms.
     * This is a lot broader than $exclude_category_id or $exclude_tag_id and will consider all taxonomies.
     * Note: this uses term id's and not taxonomy term id's
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these taxonomy terms
     */
    private $include_taxonomy_id;

    /**
     * Include any post object that belongs to any of these taxonomy terms.
     * This is a lot broader than $exclude_category or $exclude_tag and will consider all taxonomies.
     *
     * Value can be provided as:
     * a single string value  - 'boring'
     * a string of comma separated values - 'boring,interesting'
     * an array of values - array('Boring','Interesting Stuff')
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these taxonomy terms
     */
    private $include_taxonomy;

    /**
     * Include these post id's.
     *
     * Value can be provided as:
     * a single string value  - '12'
     * a string of comma separated values - '12,34'
     * an array of values - array(12,34)
     *
     * Config Option
     *
     * @var		string|array Include any post object that belongs to one of these taxonomy terms
     */
    private $include_post_id;

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  INTERNAL USE VALUES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * current post id being used for meta retrieval
     *
     * @var     string current post id being used for meta retrieval
     */
    protected $meta_post_id;

    /**
     * Current post data cache for can_output() use.
     * Could be used in templates to avoid re-retrieval.
     *
     * @var     stdClass Current post data cache
     */
    protected static $current_post = null;




    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  TYPE SPECIFIC OVERRIDES
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Apply config options specific to the Metamaterial concrete class.
     *
     * @param $config array the config array (will contain merged default values at this point)
     */
    protected function applyConfig($config)
    {

            $config_defaults = array
            (
                'types'                 =>  $this->types,
                'autosave'              =>  $this->autosave,
                'hide_title'            =>  $this->hide_title,
                'lock'                  =>  $this->lock,
                'view'                  =>  $this->view,
                'hide_screen_option'    =>  $this->hide_screen_option,
                'postbox_classes'		=>	$this->postbox_classes,

                'exclude_template'      =>  $this->exclude_template,
                'exclude_post_format'   =>  $this->exclude_post_format,
                'exclude_category_id'   =>  $this->exclude_category_id,
                'exclude_category'      =>  $this->exclude_category,
                'exclude_tag_id'        =>  $this->exclude_tag_id,
                'exclude_tag'           =>  $this->exclude_tag,
                'exclude_taxonomy_id'   =>  $this->exclude_taxonomy_id,
                'exclude_taxonomy'      =>  $this->exclude_taxonomy,
                'exclude_post_id'       =>  $this->exclude_post_id,

                'include_template'      =>  $this->include_template,
                'include_post_format'   =>  $this->include_post_format,
                'include_category_id'   =>  $this->include_category_id,
                'include_category'      =>  $this->include_category,
                'include_tag_id'        =>  $this->include_tag_id,
                'include_tag'           =>  $this->include_tag,
                'include_taxonomy_id'   =>  $this->include_taxonomy_id,
                'include_taxonomy'      =>  $this->include_taxonomy,
                'include_post_id'       =>  $this->include_post_id,

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
                'types',
                'postbox_classes',

                'exclude_template',
                'exclude_post_format',
                'exclude_category_id',
                'exclude_category',
                'exclude_tag_id',
                'exclude_tag',
                'exclude_taxonomy_id',
                'exclude_taxonomy',
                'exclude_post_id',

                'include_template',
                'include_post_format',
                'include_category_id',
                'include_category',
                'include_tag_id',
                'include_tag',
                'include_taxonomy_id',
                'include_taxonomy',
                'include_post_id',
            );

            foreach ($prep_arrays as $v)
            {
                $this->$v = static::ensureArray($this->$v);
            }
    }

    /**
     * Initialises this metamaterial instance when it is showing on the current admin page.
     */
    public function initWhenShowing(){

        global $typenow;

        //add the metabox
        add_meta_box($this->id . '_metamaterial', $this->get_the_title(), array($this, 'render'), $typenow, $this->getContext(), $this->getPriority(FALSE,TRUE));

        //add postbox classes
        add_filter('postbox_classes_' . $typenow . '_' . $this->id . '_metamaterial', array($this,'getPostboxClasses'));

        //make it save
        add_action('save_post', array($this,'save'));
    }

    /**
     * Initialises this metamaterial class once regardless of how many instances may exist.
     */
    public static function initOnce(){

        global $wp_version;

        if(version_compare($wp_version,'4.1.0') >= 0 ){
            static::$contexts = array(
                'before_title',
                'before_permalink',
                'after_title',
                'after_editor',
                'normal',
                'advanced',
                'side');

            // Allow 'before_permalink' context
            add_action('edit_form_before_permalink', 'HaddowG\MetaMaterial\MM_Metabox::editFormBeforePermalink');

        }
        // Allow 'before_title' and 'after_title' contexts
        add_action('edit_form_after_title', 'HaddowG\MetaMaterial\MM_Metabox::editFormAfterTitle');
        // Allow 'after_editor' context
        add_action('edit_form_after_editor', 'HaddowG\MetaMaterial\MM_Metabox::editFormAfterEditor');
    }

    /**
     * Render a metabox from template file.
     * Exposes global post, MetaMaterial instance and meta array to template.
     * Appends nonce for verification.
     */
    public function render()
    {
        $this->in_template = TRUE;

        // also make current post data available
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $post;

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

        // use include because users may want to use one template for multiple meta boxes
        /** @noinspection PhpIncludeInspection */
        include $this->template_path;

        // create a nonce for verification
        echo '<input type="hidden" name="'. $this->id .'_nonce" value="' . wp_create_nonce($this->id) . '" />';

        $this->in_template = FALSE;
    }

    /**
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     *  TYPE SPECIFIC FUNCTIONS
     *~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*
     */

    /**
     * Creates the before_title and after_title context sections and outputs the respective metaboxes.
     * The before_title section is moved by javascript on page load as there is no way of inserting above the title natively.
     * Runs of the edit_form_after_title wordpress hook.
     */
    public static function editFormAfterTitle()
    {
        global $post, $typenow, $wp_meta_boxes;

        do_meta_boxes( $typenow, 'before_title', $post);
        do_meta_boxes( $typenow, 'after_title', $post);

        unset( $wp_meta_boxes[$typenow]['before_title'] );
        unset( $wp_meta_boxes[$typenow]['after_title'] );
    }

    /**
     * Creates the before_permalink context section and outputs the respective metaboxes.
     * Runs of the edit_form_before_permalink wordpress hook.
     *
     * @throws MM_Exception
     */
    public static function editFormBeforePermalink()
    {
        global $post, $typenow, $wp_meta_boxes, $wp_version;

        if(version_compare($wp_version,'4.1.0') >= 0 ) {

            do_meta_boxes($typenow, 'before_permalink', $post);

            unset($wp_meta_boxes[$typenow]['before_permalink']);

        }else{

            throw new MM_Exception('This function is unsupported in your Wordpress version',500);

        }
    }


    /**
     * Creates the after_editor context section and outputs the respective metaboxes.
     * Runs of the edit_form_after_editor wordpress hook.
     */
    public static function editFormAfterEditor()
    {
        global $post, $typenow, $wp_meta_boxes;

        do_meta_boxes( $typenow, 'after_editor', $post);

        unset( $wp_meta_boxes[$typenow]['after_editor'] );
    }

    /**
     * Filters this metaboxes postbox classes.
     * Runs of the 'postbox_classes_{page}_{id}' wordpress hook to affect this metabox only.
     * Classes are added  or removed that are used to trigger javascript and target CSS for the behaviour of
     * metabox options $view, $lock, $hide_title and $hide-screen-option.
     * Additional classes can be added as desired using the $postbox_classes option.
     *
     * @param	array $classes current classes array
     * @return	array modified classes array
     */
    public function getPostboxClasses($classes){

		$classes[] = 'mm_postbox';

        if($this->view == static::VIEW_ALWAYS_OPEN || $this->view == static::VIEW_OPEN){

            if(($key = array_search('closed', $classes)) !== FALSE) {
                unset($classes[$key]);
            }

        }elseif($this->view == static::VIEW_CLOSED){
            $classes[] = 'closed';
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


        if($this->postbox_classes && is_array($this->postbox_classes) && !empty($this->postbox_classes)){
            $classes = array_merge($classes, $this->postbox_classes);
        }

        return $classes;

    }




    /**
     * Used to check for the current post type, works when creating or editing a
     * new post, page or custom post type.
     *
     * @return	string the post_type
     */
    static function get_current_post_type()
    {
        global $typenow;

        // when editing pages, $typenow isn't set until later!
        if (empty($typenow)) {
            // try to pick it up from the query string
            if (!empty($_GET['post'])) {
                $post = get_post($_GET['post']);
                $typenow = $post->post_type;
            }
            // try to pick it up from the quick edit AJAX post
            elseif (!empty($_POST['post_ID'])) {
                $post = get_post($_POST['post_ID']);
                $typenow = $post->post_type;
            }
        }

        return $typenow;
    }

    /**
     * Used to get the current post id.
     */
    static function get_post_id()
    {
        global $post;

        $p_post_id = isset($_POST['post_ID']) ? $_POST['post_ID'] : null ;

        $g_post_id = isset($_GET['post']) ? $_GET['post'] : null ;

        $post_id = $g_post_id ? $g_post_id : $p_post_id ;

        $post_id = isset($post->ID) ? $post->ID : $post_id ;

        if (isset($post_id))
        {
            return (integer) $post_id;
        }

        return null;
    }

	/**
	 * Determine if this instance should display on the current admin page
	 *
	 * @return bool
	 */
    function canOutput()
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
        if ($this->hasFilter('output'))
        {
            $can_output = $this->applyFilters('output', $can_output, $post_id, $this);
        }

        $this->will_show = $can_output;
        return $can_output;
    }

    /**
     * Gets the meta data for a meta box
     *
     * Internal method calls will typically bypass the data retrieval and will
     * immediately return the current meta data
     *
     * @param	int $post_id optional post ID for which to retrieve the meta data
     * @param	bool $internal optional boolean if internally calling
     * @return	array
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
        }else{

            // self::STORAGE_MODE_ARRAY
            $meta = get_post_meta($post_id, $this->meta_key, TRUE);

        }

        $this->meta = $meta;
        return $this->meta;
    }

	/**
	 * Save the posted meta data for the provided post ID.
	 *
	 * Saves data as metadata in the post_meta table.
	 *
	 * @param int $post_id the posts id to save data for.
	 * @param bool $is_ajax if the save is part of an ajax request.
	 * @return integer the $object_id
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
                $ajax_return = $this->applyFilters('ajax_save_fail',array(
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
        if ($this->hasFilter('save'))
        {
            $new_data = $this->applyFilters('save', $new_data, $real_post_id, $is_ajax);

            /**
             * halt saving if filter returned false
             */
            if (FALSE === $new_data){
                if(!$is_ajax){
                    return $post_id;
                }else{
                    $ajax_return = $this->applyFilters('ajax_save_fail',array(
                        'error' => __('Save Aborted') . ' ' . $_POST['post_type']
                    ));
                    wp_send_json_error($ajax_return);
                }
            }

            self::clean($new_data);
        }
		error_log(print_r($new_data,true));
        // get current fields, use $real_post_id (checked for in both modes)
        $current_fields = get_post_meta($real_post_id, $this->meta_key . '_fields', TRUE);

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
                    $field = ($this->prefix)?$this->meta_key . '_' . $field : $field;
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
        if ($this->hasAction('save'))
        {
            $this->doAction('save', $new_data, $real_post_id, $is_ajax);
        }

        if($is_ajax){
            $ajax_return = array(
                'message'=> __('Save Successful.'),
                'fields' => $new_data,
                'id'     => $real_post_id
            );
            $ajax_return = $this->applyFilters('ajax_save_success',$ajax_return, $real_post_id);
            wp_send_json_success($ajax_return);
        }

        return $post_id;
    }

}

