<?php

if(!class_exists( 'HaddowG\MetaMaterial\Metamaterial' )) {
	spl_autoload_register(
		function ( $pClassName ) {
			spl_autoload( '/src/' . strtolower( str_replace( "\\", "/", $pClassName ) ) );
		}
	);

	\HaddowG\MetaMaterial\Metamaterial::$default_assets_url = plugin_dir_url(__FILE__).'src/haddowg/metamaterial/';
	\HaddowG\MetaMaterial\Metamaterial::$default_assets_path = plugin_dir_path(__FILE__).'src/haddowg/metamaterial/';

	echo "\r\n" . \HaddowG\MetaMaterial\Metamaterial::$default_assets_url .  "\r\n";
	echo "\r\n" . \HaddowG\MetaMaterial\Metamaterial::$default_assets_path .  "\r\n";
}