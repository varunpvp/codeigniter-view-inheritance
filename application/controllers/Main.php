<?php

require('Controller.php');

/**
 *
 */
class Main extends Controller
{
	public function index() {
        ini_set('display_errors', 'On');
		// $this->layout = "layouts/site";
        $this->load->view('index.php');
	}
}
