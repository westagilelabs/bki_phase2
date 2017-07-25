<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class TestModel extends CI_Model
{
  function __construct(){
    parent::__construct();
    $this->load->helper('url');
  }
  public function getEmpIdByName($name){

	$this->db->where(" name LIKE  '%$name%' ");
	$query =  $this->db->get('users');
	//echo $this->db->last_query();
	//var_dump($query->num_rows());exit;
	if($query->num_rows() > 0)
	{
		foreach($query->result() as $row)
		{			
			var_dump($row->empid);
		}	  
	}
  }
}