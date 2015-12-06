<?php
use HaddowG\MetaMaterial\Facades\MMM;
use HaddowG\MetaMaterial\MM_Minimal;

class Metamaterial_Test extends WPIntegratedTest {

    function test_admin_head_action_triggers_correct_actions() {

        $mock = MMM::shouldReceive('globalHead')->once();

        $test  = MM_Minimal::getInstance('test',array('template'=>dirname(dirname(__FILE__)).'/support/templates/empty.php'));

        ob_start();
            do_action('admin_head');
        ob_end_clean();
    }

	function test_current_screen_action_triggers_correct_actions() {

        $mock = MMM::shouldReceive('globalInit')->once();

        $test  = MM_Minimal::getInstance('test',array('template'=>dirname(dirname(__FILE__)).'/support/templates/empty.php'));

        do_action('current_screen');
	}
}

