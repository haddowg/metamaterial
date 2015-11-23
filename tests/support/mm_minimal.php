<?php
namespace HaddowG\MetaMaterial;
use Mockery;

class MM_Minimal extends Metamaterial{

    const MM_TYPE = 'minimal';

    /**
     * @return mixed
     */
    public function init()
    {
        // TODO: Implement init() method.
    }

    /**
     * @return mixed
     */
    public static function initOnce()
    {
        // TODO: Implement init_once() method.
    }

    /**
     * Render a metabox from template file.
     * Exposes global post, Metamaterial instance and meta array to template.
     * Appends nonce for verification.
     *
     * @since    0.1
     * @access    protected
     * @see        global_init()
     */
    public function render()
    {
        // TODO: Implement render() method.
    }

    /**
     * @return mixed
     */
    public function can_output()
    {
        // TODO: Implement can_output() method.
    }

    /**
     * @return mixed
     */
    public function get_global_style()
    {
        // TODO: Implement get_global_style() method.
    }

    /**
     * Gets the meta data for a meta box
     *
     * Internal method calls will typically bypass the data retrieval and will
     * immediately return the current meta data
     *
     * @since    0.1
     * @access    private
     * @param    int $object_id optional post ID for which to retrieve the meta data
     * @param    bool $internal optional boolean if internally calling
     * @return    array
     * @see        the_meta()
     */
    public function meta($object_id = NULL, $internal = FALSE)
    {
        // TODO: Implement meta() method.
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
    public function save($object_id, $is_ajax = FALSE)
    {
        // TODO: Implement save() method.
    }

    protected function applyConfig($id, &$config)
    {
        // TODO: Implement applyConfig() method.
    }
}