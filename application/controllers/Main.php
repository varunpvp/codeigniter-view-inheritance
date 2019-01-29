<?php

require('Controller.php');

/**
 *
 */
class Main extends Controller
{
	public function index() {
		// $this->layout = "layouts/site";
        $this->load->view('index.php');
	}
}
