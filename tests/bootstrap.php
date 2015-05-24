<?php

    require dirname(dirname(__FILE__)).'/vendor/autoload.php';



    spl_autoload_register(
        function ( $pClassName ) {
            spl_autoload( dirname(dirname(__FILE__)).'/src/' . strtolower( str_replace( "\\", "/", $pClassName ) ));
        }
    );

    require 'support/mm_minimal.php';
    require 'support/MetamaterialTestCase.php';
