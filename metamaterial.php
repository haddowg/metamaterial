<?php
/*
Plugin Name: Metamaterial
*/

if(!class_exists( 'HaddowG\MetaMaterial\Metamaterial' )) {

	spl_autoload_register(
		function ( $pClassName ) {
			spl_autoload( 'src/' . strtolower( str_replace( "\\", "/", $pClassName ) ));
		}
	);


	add_action('init',function(){
		\HaddowG\MetaMaterial\Metamaterial::$default_assets_url = plugins_url('src/haddowg/metamaterial/',__FILE__);
		\HaddowG\MetaMaterial\Metamaterial::$default_assets_dir = plugin_dir_path(__FILE__).'src/haddowg/metamaterial/';
	});


}