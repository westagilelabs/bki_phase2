<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Author: Venkatesh Vemula
 * Description: Login model class
 */
class Login_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}

	public function validate($flag=0){
		// grab user input
		$username = $this->security->xss_clean($this->input->post('login_name'));
		$password = $this->security->xss_clean($this->input->post('login_password'));

			// Prep the query
			$this->db->where('email', $username);
			$this->db->where('pwd', md5($password));

			// Run the query
			$query = $this->db->get('users');
//var_dump($query->num_rows());exit;
		// Let's check if there are any results
		if($query->num_rows() > 0)
		{
			// If there is a user, then create session data
			$row = $query->row();

			$data = array(
						'uid' => $row->uid,
						'email' => $row->email,
						'name'=> $row->name,
						'empid' => $row->empid,
						'first_login' => $row->first_login,
						'role' =>$row->role,
						'validated' => true);
			
			$this->load->library('session');
			$this->session->set_userdata($data);
			$_SESSION['user'] = $data;
			setcookie("user", json_encode($data), time() + (7200), "/");
			//echo "In login module :<br/>";
			//var_dump($_SESSION);exit;
			//echo "SET >>";
			//var_dump($_SESSION);exit;
			return true;
		}
		// If the previous process did not validate
		// then return false.
		return false;
	}
}
?>
