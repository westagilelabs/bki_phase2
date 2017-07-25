<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	
	public function __construct(){
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('session');
	}
	
	public function check(){
		$this->load->model("testmodel");
		$data = array('BRIAN BATCHELORE','TECHWYN D ANDERSON');
		foreach($data as $d){
			$data = $this->testmodel->getEmpIdByName($d);
		}
	}
}