<?php
//S3 Library - https://github.com/psugand/CodeIgniter-S3
ini_set('set_time_limit', 0);
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	private $bucket = "bbatchelor-uploads";
	private $archive_bucket = "batchelor-archives";	
	private $s3URL = "//s3.amazonaws.com/bbatchelor-uploads/";


	public function __construct(){
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('session');
		$this->session->_sess_run();
	}
	public function is_logged_in()
	{
		  $user = $this->session->userdata('uid');
		  return isset($user);
	}

	public function is_role_sa()
	{
		$role = $this->session->userdata('role');
		if($role == 'SA'){
			return true;
		}
		return false;
	}

	public function is_role_a()
	{
		$role = $this->session->userdata('role');
		if($role == 'A'){
			return true;
		}
		return false;
	}

	public function is_role_e()
	{
		$role = $this->session->userdata('role');
		if($role == 'E'){
			return true;
		}
		return false;
	}
	public function logout()
	{
		$data = array(
					'uid' => "",
					'uname' => "",
					'email' =>"",
					'role' => '',
					'validated' => ''
					);
		$this->session->unset_userdata('uid');
		redirect('/user/index', 'refresh');
	}
	public function index($output = null){
		$flag=$output;
		if(!$output)
		{
			$flag=(object) array("u"=>"","msg"=>"");
		}
		
		$this->load->view('users/html/login.php' ,$flag);
	}

	//checkLogin starts
	public function checkLogin(){
		$this->load->model('login_model');
		$result = $this->login_model->validate(0);
		//var_dump($result);exit;
		if(!$result){
			// If user did not validate, then show them login page again
			$msg = 'Invalid username or password<br />';
			//$this->index($msg);
			$flag=(object) array("msg"=>$msg);
			$this->index($flag);
		}else{			
			redirect("user/routeDashboard");
		}
	}
	//checkLogin ends

	//routeDashboard starts
	public function routeDashboard()
	{
		$flogin = $this->session->userdata('first_login');
		/*
		//var_dump($this->session->userdata(''));
		var_dump($this->is_role_sa());
		echo "SESSION UID :<br/>";
		var_dump($_SESSION['uid']);
		echo "Flogin :<br/>";
		var_dump($flogin);
		echo "uid :<br/>";
		var_dump($this->session->userdata('uid'));
		exit;*/
		if($flogin == 1){
			redirect("user/profile/newuser");
		}
		if($this->is_logged_in() && $this->is_role_sa())
		{				
				redirect("user/dashboard");
		}
		else if($this->is_logged_in() && $this->is_role_a()){
			
			 $this->config->set_item('role', "A");
			redirect("user/manage");
		}
		else{
			redirect("user/files");
		}

	}
	//routeDashboard ends

	public function dashboard()
	{
		if(!$this->is_logged_in() || !$this->is_role_sa())
		{
			redirect("user/index",'refresh');
		}
		$this->load->model("usermodel");
		$data = $this->usermodel->getList('A');
		$data["data"] = $data;
		if($this->uri->segment(3) == "nh")
			$this->load->view("users/html/partials/admin-list.php",$data);
		else
			$this->load->view("users/html/dashboard.php",$data);
	}

	public function saveListItem()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}

		$this->load->model("usermodel");
		$flag = "insert";
		$error = false;
		$error1 = false;
		//new_employee_empid1


		$em = $this->security->xss_clean($this->input->post("admin_email"));
		if($this->uri->segment(3) == 'E'){
			$em = $this->security->xss_clean($this->input->post("new_employee_email"));
		}

		if(!$this->usermodel->CheckEmpId($this->security->xss_clean($this->input->post("new_employee_empid"))))
		{
			if(!$this->usermodel->CheckEmailID($em))
			{
				$pwd = $this->getRandomPassword();
				$flag = $this->usermodel->saveListItem($flag,$file = array(""),$pwd);
			}
			else
			{
				$error1 = true;
			}
		}
		else
		{
			$error = true;
		}



		$redirect = "";
		switch ($this->uri->segment(3)) {
			case 'A':
				$redirect = "/user/dashboard";
				break;
			case 'E':
				$redirect = "/user/manage";
				break;
		}
		if($flag && !$error && !$error1){

				
				$this->sendMail($em,$pwd);
				//$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Successfully created</h4>");
				echo "<h4 class='success-message' id='user-message'>Successfully created</h4>";
		}else {
				if($error)
				{
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>");
					echo "<h4 class='error-message' id='user-message'>Duplicate Employee ID.  Failed to save.</h4>";
				}
				else if($error1)
				{
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>");
					echo "<h4 class='error-message' id='user-message'>Duplicate Email ID.  Failed to save.</h4>";
				}
				else
				{
					echo "<h4 class='error-message' id='user-message' >Database Error Occured. Try again later</h4>";
				}
		}
		//redirect($redirect);
		//$post_data = $this->input->post("admin_name");
	}

	public function editListItem()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}

		$this->load->model("usermodel");
		$flag = "update";
		$error =false;
		$error1 = false;
		$pwd = $this->getRandomPassword();
		if($this->input->post("new_employee_empid") != $this->input->post("new_employee_empid1"))
		{
			if(!$this->usermodel->CheckEmpId($this->security->xss_clean($this->input->post("new_employee_empid"))))
			{
				if($this->input->post("edit_employee_id1") !=$this->input->post("new_employee_email") )
				{
					if(!$this->usermodel->CheckEmailID($this->input->post("new_employee_email")))
					{

						$flag = $this->usermodel->saveListItem($flag,$file = array(""),$pwd);
					}
					else
					{
						$error1 = true;
					}
				}
				else
					$flag = $this->usermodel->saveListItem($flag,$file = array(""),$pwd);
			}
			else
			{
				$error = true;
			}
		}
		else{
			if($this->input->post("edit_employee_id1") !=$this->input->post("new_employee_email") )
			{
				if(!$this->usermodel->CheckEmailID($this->input->post("new_employee_email")))
				{

					$flag = $this->usermodel->saveListItem($flag);
				}
				else
				{
					$error1 = true;
				}
			}
			else
				$flag = $this->usermodel->saveListItem($flag);
		}

		$redirect = "";
		switch ($this->uri->segment(3)) {
			case 'A':
				$redirect = "/user/dashboard";
				break;
			case 'E':
				$redirect = "/user/manage";
				break;
		}

		if($error)
		{
			echo "<h4 class='error-message' id='user-message'>Duplicate Employee ID. Failed to save</h4>";
		}
		else if($error1){
			echo "<h4 class='error-message' id='user-message'>Duplicate Email ID. Failed to save</h4>";
		}
		else{

			if($flag){
					//$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Updated Successfully</h4>");
					echo "<h4 class='success-message' id='user-message'>Updated Successfully</h4>";
			}else {
					echo "<h4 class='error-message' id='user-message'>Database error occured. Try again later.</h4>";
			}
		}
		//redirect($redirect);
		//$post_data = $this->input->post("admin_name");
	}

	public function deleteListItem()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		
		
		$this->load->model("usermodel");
		$flag = $this->usermodel->deleteListItem();
		//var_dump($flag);exit;
		
		//var_dump($flag);exit;
		switch ($this->uri->segment(3)) {
			case 'A':
				$redirect = "/user/dashboard";
				break;
			case 'E':
				$redirect = "/user/manage";
				break;
		}
		
		if($this->security->xss_clean($this->input->post("uid"))){
			$this->deleteFile("folder",$this->input->post("uid"),$redirect,$this->input->post("empid"));
		}

		if($flag){
				$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Employee account is deleted successfully</h4>");
		}else {
				$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to delete. Database error occured</h4>");
		}

		//var_dump($redirect);exit;
		redirect($redirect,'refresh');
	}

	public function deleteFile( $m = "",$f = "", $r = "", $e = ""){
		//var_dump($m);exit;

		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		$file = "";
		$uri = "";
		$folder = $this->security->xss_clean($this->input->post("empid"));
		$uri = $folder;
		$mode = "folder";
		if($this->security->xss_clean($this->input->post("action")) =="file"){
			$file = $this->security->xss_clean($this->input->post("file_name"));
			$e = $this->security->xss_clean($this->input->post("empid"));
			$ftype = $this->security->xss_clean($this->input->post("file_type"));
			$uri = $uri."/".$file;
			$mode = "file";
		}
		else if($this->security->xss_clean($this->input->post("action")) =="all"){
			//echo "in all";exit;
			$mode = "all";
		}



		$this->load->library("S3");
		$s3 = new S3();

		$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Failed to delete. There is an error with s3 server</h4>");

		if($mode == "folder" || $m == "folder")
		{
			
			if(!empty($f)){
				$folder = $f;
				
				$this->movetoArchive($folder);
			}
			else{
				//Direct folder deletes move to archive
				/*$this->load->model("usermodel");
				if($this->usermodel->setArchive($folder))
				{
					$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully</h4>");
					
					if($m == "folder")
						redirect($r);
					else
						redirect("user/manage#manage_files");
					
				}*/
			
			}
			
			
			
			$this->deleteCommonFile($folder);

			//exit;
			if($this->delete_s3_items($this->bucket,$folder))
			{
				$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully</h4>");

				if($m == "folder")
				{
					$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Employee account deleted successfully</h4>");
					redirect($r);
				}					
				else
					redirect("user/manage#manage_files");
			}
		}
		else if($mode == "file")
		{
			//var_dump($file);var_dump($uri);var_dump($e);var_dump($folder);exit;
			if($ftype == "common"){
				$this->deleteCommonFile($folder,$file);
				
				if($e != ""){
					$this->session->set_flashdata("message","<h4 class='success-message' id='files-message2'>Deleted Successfully</h4>");
				}
				else{
					$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully</h4>");
				}
					if($e != "")
						redirect("user/manage#manage_files#$e");
					else
						redirect("user/manage#manage_files");
			}
			else{
				if($s3->deleteObject($this->bucket,$uri))
				{
					if($e != ""){
					$this->session->set_flashdata("message","<h4 class='success-message' id='files-message2'>Deleted Successfully</h4>");
					}
					else{
						$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully</h4>");
					}
					if($e != "")
						redirect("user/manage#manage_files#$e");
					else
						redirect("user/manage#manage_files");
				}
			}
		}
		else if($mode == "all")
		{
			$this->load->model('usermodel');
			$this->usermodel->deleteCommonFolder();
			if($this->delete_s3_folders($this->bucket, "/"))
			{
				$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully</h4>");
				redirect("user/manage#manage_files");
			}
		}
		
		//var_dump($m);exit;
		if($m == "folder"){
			redirect("user/manage");
		}
		redirect("user/manage#manage_files");

	}
	public function manage()
	{
		if(!$this->is_logged_in() || $this->is_role_e())
		{
			redirect("user/index",'refresh');
		}
		$empid = $this->session->userdata('empid');
		$this->load->model("usermodel");

		$excludes = $this->usermodel->getCommonFilesCount();
		$data1 = $this->usermodel->getCommonFiles();

		if($this->is_role_sa())
		{
			$data2 = $this->getSpecificFiles();
			//$data = $this->usermodel->getList('NSA');
			$data = $this->usermodel->getEmployeesBySorted();
		}
		else if($this->is_role_a())
		{
			//$data = $this->usermodel->getList('E');
			$data2 = $this->getSpecificFiles();
			//$data = $this->usermodel->getEmployeesBySorted('E');
			$data = $this->usermodel->getEmployeesBySorted();
		}
		else
		{

			$data2 = $this->getSpecificFiles($empid);
		}

		foreach($data1 as $d){
			$c[] = (array)$d;
		}

		//echo "<pre>";var_dump($data2);exit;
		$data["data"] = $data;
		$data["data1"] = $data1;
		$data['excludes'] = $excludes;
		$data["data2"] = $data2;
		$data['error'] = '';

		$cnt = 0;
		$formatted = array();
		foreach($data as $dd)
		{
			foreach($data1 as $d1)
			{
				//checking for count of files
			  if($d1->unallowed != "null" && $d1->unallowed != null)
			  {
				  if(isset($dd['empid']))
				  {
					  if(!in_array($dd['empid'],json_decode($d1->unallowed)))
					  {
						  //echo "Inside ".$dd['empid']."<br/>";
						 /* $formatted['common_files']['file_name'] = $d1->file_name;
						  $formatted['common_files']['date'] = $d1->date;
						  $formatted['common_files']['size'] = $d1->size;*/
						  $cnt++;
					  }
				  }

			  }
			  else{
				  if(isset($dd['empid']))
					  $cnt++;
				}
			}

			if(isset($dd['empid']))
			{
				if(isset($data2[$dd['empid']]))
				{
					foreach($data2[$dd['empid']] as $d2){

						/*$formatted[$dd['empid']]['file_name'] = $d2['file_name'];
					    $formatted[$dd['empid']]['date'] = $d2['created_on'];
					    $formatted[$dd['empid']]['size'] = $d2['file_size'];*/

						$cnt++;
					}
				}
			}

		}

		$data['cnt'] =  $cnt;

		if($this->uri->segment(3) == "nh")
			$this->load->view("users/html/partials/employee-list.php",$data);
		else
			$this->load->view('users/html/manageEmployees.php',$data);


	}

	public function resendConfirmation(){
			
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		//$this->load->model("usermodel");
		
	
		$role = $this->session->userdata('role');
		$uid = $this->session->userdata('uid');
		$this->load->model("usermodel");
		
		
			$e = $this->input->post("email");
			$d = $this->usermodel->getEmpIdByEmail($e);
			//var_dump($e);exit;
			if(!empty($d))
			{
				$p = $this->getRandomPassword();
				$pwd = md5($p);
				$this->usermodel->updateNewPassword($e,$pwd);
				$this->sendMail($e,$p);
				$msg = "Instructions mail sent successfully";
				$this->session->set_flashdata("message1","<h4 class='success-message' id='files-message'>$msg</h4>");
			
			}
			else{
				$msg = "User not found in the application";
				$this->session->set_flashdata("message1","<h4 class='error-message' id='files-message'>$msg</h4>");
			}
		
		
		if($this->usermodel->checkFirstLogin($uid) && $this->uri->segment(3) != "newuser"){
				redirect("user/profile/newuser");
		}
		if($this->uri->segment(3) == "newuser"){
			if(!$this->usermodel->checkFirstLogin($uid)){
				redirect("user/profile");
			}
		}
		$data = $this->usermodel->getList($role,$uid);
		$address = $this->usermodel->getAddress($uid);

		$empid = $this->session->userdata('empid');
		//$data = $this->usermodel->getList('E');
		$data1 = $this->usermodel->getCommonFiles($empid);
		$data2 = $this->getSpecificFiles($empid);
		$data['data1'] = $data1;
		$data['data2'] = $data2;

		$data["data"] = $data;
		$data["address"] = $address;

		$this->load->view('users/html/profile.php',$data);
		
	}
	public function profile()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		//$this->load->model("usermodel");
		
	
		$role = $this->session->userdata('role');
		$uid = $this->session->userdata('uid');
		$this->load->model("usermodel");
		
	
		if($this->usermodel->checkFirstLogin($uid) && $this->uri->segment(3) != "newuser"){
				redirect("user/profile/newuser");
		}
		if($this->uri->segment(3) == "newuser"){
			if(!$this->usermodel->checkFirstLogin($uid)){
				redirect("user/profile");
			}
		}
		$data = $this->usermodel->getList($role,$uid);
		$address = $this->usermodel->getAddress($uid);

		$empid = $this->session->userdata('empid');
		//$data = $this->usermodel->getList('E');
		$data1 = $this->usermodel->getCommonFiles($empid);
		$data2 = $this->getSpecificFiles($empid);
		$data['data1'] = $data1;
		$data['data2'] = $data2;


		$data["data"] = $data;
		$data["address"] = $address;

		$this->load->view('users/html/profile.php',$data);
	}
	
	
	public function editProfile()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
			$admin = false;
			$this->load->model("usermodel");
			if($this->input->post("updateby") == "SA")
			{
				$admin = true;
				$uid = $this->input->post("uid");
				$flag = $this->usermodel->editProfile($uid);
			}				
			else 
				$flag = $this->usermodel->editProfile();

			if($flag){
				//if($this->is_role_e)
					$this->sendAddressUpdate();
				
					$this->session->set_flashdata("message","<h4 class='success-message'>Updated Successfully </h4>");
			}else {
					$this->session->set_flashdata("message","<h4 class='error-message'>Failed to update. Email ID already exists</h4>");
			}
	//	}
		if($admin)
			redirect("/user/emp_profile/$uid");
		else
			redirect("/user/profile");
		//$post_data = $this->input->post("admin_name");
	}
	
	public function updateImage()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
			$admin = false;
			$this->load->model("usermodel");
			
			$flag = $this->usermodel->editProfile("",true);

			if($flag){		
					$this->session->set_flashdata("message","<h4 class='success-message'>Updated Successfully </h4>");
			}else {
					$this->session->set_flashdata("message","<h4 class='error-message'>Failed to update. There is a database error </h4>");
			}
		if($admin)
			redirect("/user/emp_profile/$uid");
		else
			redirect("/user/profile");
	}

	public function updatePassword()
	{
		/*if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}*/
		$this->load->model("usermodel");
		$flag = $this->usermodel->updatePassword();
		if($flag){
				echo "<h4 class='success-message'>Updated Successfully</h4>";
		}else {
				echo "<h4 class='error-message'>Current password is wrong</h4>";
		}
		//redirect("/user/profile");
	}

	public function files()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		$this->load->model("usermodel");
		$uid = $this->session->userdata('uid');
		if($this->usermodel->checkFirstLogin($uid)){
				redirect("user/profile/newuser");
		}

		$empid = $this->session->userdata('empid');
		
		//$data = $this->usermodel->getList('E');
		$data1 = $this->usermodel->getCommonFiles($empid);
		$data2 = $this->getSpecificFiles($empid);
		$data['data1'] = $data1;
		$data['data2'] = $data2;
		//echo "<pre>";var_dump($this->getSpecificFiles());exit;
		$this->load->view("users/html/files.php",$data);

	}
	public function do_upload()
	{
		//var_dump($_FILES);exit;
		$tt =false;
		$it = false;
		$missing_data = [];
		if($this->uri->segment(3) == 'retry'){
			$tt = true;
			$it = true;
			$retry = true;
		}if($this->uri->segment(4)){
			$fpath = $this->uri->segment(4);
		}
		$allowed = array('pdf');
		$msg = "Invalid file format. Unable to process request.";
		//ob_start();
		$this->load->library('s3');
		$s3 = new S3();

		$this->load->model("usermodel");
		$users = $this->usermodel->getList();

		$empid = "";
		if(!empty($this->security->xss_clean($this->input->post("empid")))){
			$empid = $this->security->xss_clean($this->input->post("empid"));
		}
		$common_upload = false;
		$specific_upload = false;
		//var_dump($_FILES);exit;
		if ( (!$_FILES["upload_new_common_file"]["size"][0] > 100000000) || (!$_FILES["upload_new_employee_file"]["size"][0] > 100000000)) {
			
			if($empid !="")
				$msg = "<h4 class='error-message' id='files-message2'>Maximum file size exceeded. File failed to upload.</h4>";
			else
				$msg = "<h4 class='error-message' id='files-message'>Maximum file size exceeded. File failed to upload.</h4>";
			$this->session->set_flashdata("message",$msg);
			echo("<script>location.href = '/index.php/user/manage#manage_files';</script>");
			
			//return false;
		}

		if(!empty($_FILES['upload_new_common_file']['name'][0] ))
		{
			$common_upload = true;
			$files = $_FILES['upload_new_common_file'];
		}
		else if(!empty($_FILES['upload_new_employee_file']['name'][0] ) || $tt)
		{
			$specific_upload = true;

			if($tt){
				$files = "temp_uploads/".$fpath;
			}
			else{
				$files = $_FILES['upload_new_employee_file'];
			}
			
			if(!$retry){
				$newFilePath = "./uploads/" . $files['name'][0];
				//$fname = explode(".",$files['name'][0]);
				$ftype = pathinfo($files['name'][0], PATHINFO_EXTENSION);
			}else{
				$ftype = pathinfo($files, PATHINFO_EXTENSION);
			}
			
			//var_dump($fname[1]);
			//var_dump(in_array(strtolower($fname[1]),$allowed));exit;
			//do validation first

			if(in_array(strtolower($ftype),$allowed) || $retry)
			{
				
				$rrt = $this->uploadSpecificFile($files,$empid,$it);
			}
			else{
				
				$rrt = false;
				if($empid !="")
					$msg = "<h4 class='error-message' id='files-message2'>Unsupported file type</h4>";
				else
					$msg = "<h4 class='error-message' id='files-message'>Unsupported file type</h4>";
			}
			
			if(is_bool($rrt) && $rrt ){
				if($empid !="")
					$ret = "<h4 class='success-message' id='files-message2'>Uploaded successfully </h4>";
				else
					$ret = "<h4 class='success-message' id='files-message'>Uploaded successfully </h4>";
			}
			else if(!$rrt){
				$rrt = false;
			}
			else{
				$ret = json_decode($rrt);

				if($ret->value){
					$rrt = $ret->value;
				}
				else if($ret->value == false && $ret->retry == 1){
					$fpath = $ret->fpath;
					$missing_data= $ret->data;
					$missing_names= $ret->unames;
					//do_upload/retry/$fpath
					$ret = "<h4 class='error-message' id='files-message'>Some employee details are not found within the system.<a href='do_upload/retry/$fpath'> Continue</a>?</h4>";
				}
				else if($ret->value == false && $ret->retry == 0){
					$msg = $ret->message;
					if($empid !="")
						$ret = "<h4 class='error-message' id='files-message2'>$msg</h4>";
					else
						$ret = "<h4 class='error-message' id='files-message'>$msg</h4>";
				}
			}


			if($rrt)
			{
				$this->session->set_flashdata("message",$ret);
				//var_dump($ret);exit;
				$this->session->set_flashdata("data",$missing_data);
				$this->session->set_flashdata("names",$missing_names);
				//ob_end_flush();
				//redirect("user/manage");
				if($empid !="")
					echo("<script>location.href = '/index.php/user/manage#manage_files#$empid';</script>");
				else
					echo("<script>location.href = '/index.php/user/manage#manage_files';</script>");
			}
			else{
				$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>$msg</h4>");
				
				if($empid !="")
					echo("<script>location.href = '/index.php/user/manage#manage_files#$empid';</script>");
				else
					echo("<script>location.href = '/index.php/user/manage#manage_files';</script>");
		
			}

		}

		if($common_upload )
		{
			//echo "into common";exit;
			$total = count($files['name']);
			//$allowed = array('pdf');
			// Loop through each fil1e
			for($i=0; $i<$total; $i++) {
				//Get the temp file path
				$tmpFilePath = $files['tmp_name'][$i];
				//Make sure we have a filepath
				if ($tmpFilePath != ""){
					//Setup our new file path
					$newFilePath = "./uploads/" . $files['name'][$i];
					$fname = explode(".",$files['name'][$i]);
					$ftype = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
					if(in_array(strtolower($ftype),$allowed))
					{
						$folderpath = "common_files";
						$date = "-".date('Y-m-d');
						//$folderName = "$folderpath/$fname[0]$date.$fname[1]";  // path on s3 bucket.
						$folderName = "$folderpath/$fname[0].$fname[1]";

						if($s3->putObjectFile($tmpFilePath ,$this->bucket ,$folderName ,S3::ACL_PUBLIC_READ )) {

							$fileinfo = $s3->getObjectInfo($this->bucket, $folderName);
							//var_dump($fileinfo);exit;
							//$file['file_name'] = $fname[0].$date.".".$fname[1];
							$file['file_name'] = $fname[0].".".$fname[1];
							$file['file_size'] = $fileinfo['size'];

							//save into DB
							if($this->usermodel->saveListItem('insert',$file))
							{
								$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Uploaded successfully </h4>");
							}
							else{
								$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Failed to upload.. File already exists.. </h4>");
							}

							redirect("user/manage#manage_files");

						}
						else
						{
							$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>File failed to upload. There is an error with the S3 server. Please try again. </h4>");
							redirect("user/manage#manage_files");
						}
					}
					else
					{
						//Wrong File type
						//$data = array('messae' => "Unsupported file type");
						$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Unsupported file type</h4>");
						redirect("user/manage#manage_files");
					}
				}
			}
		}//if common file ends
	}

	public function uploadSpecificFile($files,$empid,$it){
		//var_dump($empid);var_dump($it);exit;
		
		if(empty($empid))
		{
			$this->load->library('SplitPDF');
			$spdf = new SplitPDF();
			
			if($it){//retry

				$t1 = explode("/",$files);
					//var_dump($t1 );
				return $spdf->split_pdf($files, 'split/',$t1[1],$it);
			}
			else{
				//echo "Upoad";var_dump($files['name'][0] );exit;
				return $spdf->split_pdf($files['tmp_name'][0], 'split/',$files['name'][0],$it);
			}

		}
		else{
			$this->load->library('s3');
			$s3 = new S3();
			//var_dump($empid);exit;
			$folderName = $empid."/".$files['name'][0];
			//echo $folderName;
			//var_dump($files);exit;
			if($s3->putObjectFile($files['tmp_name'][0] ,$this->bucket ,$folderName ,S3::ACL_PUBLIC_READ )) {
				return true;
			}
			return false;
		}

	}

	public function getSpecificFiles($folder=''){
		//$folder = "1496";
		$this->load->library('s3');
		$s3 = new S3();
		//$files = $s3->getObject($this->bucket,$folder);
		$files = $this->list_s3_bucket($this->bucket);
		if(empty($folder))
			return $files;
		else
			return @$files[$folder];
	}

	public function list_s3_bucket($bucket_name,$folder = "")
	{
		$this->load->library('s3');
		$s3 = new S3();
		// initialize the data array
		$data = array();
		$bucket_content = $s3->getBucket($bucket_name);

		foreach ($bucket_content as $key => $value) {
			// ignore s3 "folders"
			if (preg_match("/\/$/", $key)) continue;

			// explode the path into an array
			$file_path = explode('/', $key);
			$file_name = end($file_path);
				$file_folder = substr($key, 0, (strlen($file_name) * -1)+1);
			$file_folder = prev($file_path);

			$s3_url = "https://s3.amazonaws.com/{$bucket_name}/{$key}";
			
			if(!empty($folder))
			{
				if($folder == $file_folder)
				{
					$data[$file_folder][] = array(
					'file_name' => $file_name,
							's3_key' => $key,
					'file_folder' => $file_folder,
					'file_size' => $value['size'],
					'created_on' => date('Y-m-d H:i:s', $value['time']),
					's3_link' => $s3_url,
					'md5_hash' => $value['hash']);
				}
			}
			else{
				$data[$file_folder][] = array(
				'file_name' => $file_name,
						's3_key' => $key,
				'file_folder' => $file_folder,
				'file_size' => $value['size'],
				'created_on' => date('Y-m-d H:i:s', $value['time']),
				's3_link' => $s3_url,
				'md5_hash' => $value['hash']);
			}
			
		}
		return $data;
	}

	public function delete_s3_folders($bucket_name, $folder){
		$this->load->library('s3');
		$s3 = new S3();
		// initialize the data array
		$data;
		$bucket_content = $s3->getBucket($bucket_name);

		foreach ($bucket_content as $key => $value) {
		// ignore s3 "folders"
			if (preg_match("/\/$/", $key)) continue;

			// explode the path into an array
			$file_path = explode('/', $key);
			$file_name = end($file_path);
				$file_folder = substr($key, 0, (strlen($file_name) * -1)+1);
			$file_folder = prev($file_path);
			if($file_folder)
			{
				$uri = $file_folder."/".$file_name;
				$s3->deleteObject($bucket_name,$uri);
			}
		}

		return true;
	}

	public function delete_s3_items($bucket_name, $folder){
		$this->load->library('s3');
		$s3 = new S3();
		// initialize the data array
		$data;
		$bucket_content = $s3->getBucket($bucket_name);

		foreach ($bucket_content as $key => $value) {
			// ignore s3 "folders"
			if (preg_match("/\/$/", $key)) continue;

			// explode the path into an array
			$file_path = explode('/', $key);
			$file_name = end($file_path);
				$file_folder = substr($key, 0, (strlen($file_name) * -1)+1);
			$file_folder = prev($file_path);
			//var_dump($file_folder);continue;
			if($file_folder == $folder)
			{
				$uri = $folder."/".$file_name;
				$s3->deleteObject($bucket_name,$uri);
			}
			$s3_url = "https://s3.amazonaws.com/{$bucket_name}/{$key}";

			$data[$file_folder][] = array(
				'file_name' => $file_name,
						's3_key' => $key,
				'file_folder' => $file_folder,
				'file_size' => $value['size'],
				'created_on' => date('Y-m-d H:i:s', $value['time']),
				's3_link' => $s3_url,
				'md5_hash' => $value['hash']);
		}
		return $data;
	}

	public function deleteCommonFile($empid,$file=""){

		if(!empty($empid)){
			$this->load->model('usermodel');
			$data = $this->usermodel->deleteCommonFile($empid,$file);
			return $data[0]['empid'];
		}
	}

	public function getEmpId($uid){

		if(!empty($uid)){
			$this->load->model('usermodel');
			$data = $this->usermodel->getEmpId($uid);
			return $data[0]['empid'];
		}
	}

	public function getCommonFilesCount(){
		$this->load->model('usermodel');
		return $data = $this->usermodel->getCommonFilesCount();
	}

	public function getRandomPassword(){
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		
		 $string = 'B5';
		 $max = strlen($characters) - 1;
		 for ($i = 0; $i < 6; $i++) {
			  $string .= $characters[mt_rand(0, $max)];
		 }
		 //$string.="91";
		 
		 return $string;
	}
	
	
	public function sendAddressUpdate(){
		$this->load->library('email');
		
		$this->email->initialize(array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'smtp.sendgrid.net',
		  'smtp_user' => 'bbatchelor',
		  'smtp_pass' => 'BKIWebsite1',
		  'smtp_port' => 587,
		  'crlf' => "\r\n",
		  'newline' => "\r\n",
		  'mailtype' => 'html'
		));
		
		 $this->load->library('session');
       $eid = $this->session->userdata('uid');
	   $name = $this->session->userdata('name');
	
		$email  = "brian@bkimechanical.com";
		//$email  = "venkivenki123@gmail.com";
		$oldpic = $this->input->post("old_image");
		$uploadedpic = $this->input->post("profile_pic_input");
		/*$profilemessage = "";
		if($this->areEqual($oldpic,$uploadedpic))
		{
			$profilemessage = "Profile picture has been updated";
		}
		var_dump($this->areEqual($oldpic,$uploadedpic));exit;*/
		$upemail = $this->input->post("profile_email");
		$address = $this->input->post("profile_address");
		$city = $this->input->post("profile_city");
		$state = $this->input->post("profile_state");
		$zip = $this->input->post("profile_zip");
		$phone = $this->input->post("profile_phone");
		
		$html = "The following information has been modified by employee: $name<br/><br/>
				<table>
				<tr><td>Email:</td> <td>$upemail</td></tr>
				<tr><td>Address:</td> <td> $address</td></tr>
				<tr><td>City:</td> <td> $city</td></tr>
				<tr><td>State:</td> <td> $state</td></tr>
				<tr><td>Zip:</td> <td> $zip</td></tr>
				<tr><td>Phone:</td> <td> $phone</td></tr>
				<tr colspan='2'>$profilemessage</tr>
				</table>
				<br/>
				
				Thank you.";

		$this->email->from('administrator@bkimechanical.com', 'Batchelor & Kimball, Inc. Administrator');
		$this->email->to($email);
		$this->email->cc('manideep@westagilelabs.com');
		//$this->email->bcc('them@their-example.com');
		$this->email->subject('Employee Address Info changed');
		$this->email->message($html);
		$this->email->send();
	}
	public function areEqual($firstPath, $secondPath, $chunkSize = 500){

		// First check if file are not the same size as the fastest method
		if(filesize($firstPath) !== filesize($secondPath)){
			return false;
		}

		// Compare the first ${chunkSize} bytes
		// This is fast and binary files will most likely be different 
		$fp1 = fopen($firstPath, 'r');
		$fp2 = fopen($secondPath, 'r');
		$chunksAreEqual = fread($fp1, $chunkSize) == fread($fp2, $chunkSize);
		fclose($fp1);
		fclose($fp2);

		if(!$chunksAreEqual){
			return false;
		}

		// Compare hashes
		// SHA1 calculates a bit faster than MD5
		$firstChecksum = sha1_file($firstPath);
		$secondChecksum = sha1_file($secondPath);
		if($firstChecksum != $secondChecksum){
			return false;
		}

		return true;
	}

	public function sendMail($email,$pwd){
		$this->load->library('email');
		
		$this->email->initialize(array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'smtp.sendgrid.net',
		  'smtp_user' => 'bbatchelor',
		  'smtp_pass' => 'BKIWebsite1',
		  'smtp_port' => 587,
		  'crlf' => "\r\n",
		  'newline' => "\r\n",
		  'mailtype' => 'html'
		));

		$html = "Welcome to the Batchelor & Kimball, Inc. Employee Portal. Your account has been created,<br/>  and your login information is below. Please change your password when you log in. <br/><br/>You can update your  email address, address or phone number at any time to notify us of changes to your account.<br/> <br/>
				Username: $email<br/> <br/>
				Password: $pwd<br/><br/>
				Please click <a href='http://ec2-54-85-211-219.compute-1.amazonaws.com/'>here</a> to log in. You can also go to www.bkimechanical.com/employee to log in.";

		$this->email->from('administrator@bkimechanical.com', 'Batchelor & Kimball, Inc. Administrator');
		$this->email->to($email);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');
		$this->email->subject('Account Created');
		$this->email->message($html);
		$this->email->send();
	}

	public function sendForgotMail($email,$pwd){
		$this->load->library('email');
		
		$this->email->initialize(array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'smtp.sendgrid.net',
		  'smtp_user' => 'bbatchelor',
		  'smtp_pass' => 'BKIWebsite1',
		  'smtp_port' => 587,
		  'crlf' => "\r\n",
		  'newline' => "\r\n",
		  'mailtype' => 'html'
		));

		$html = "Hello,<br/> <br/>
				You have requested to change your password. To change your password, Please find the below details.<br/><br/>
				Username: $email <br/>
				Password: $pwd <br/><br/>
				You can <a href='http://ec2-54-85-211-219.compute-1.amazonaws.com/'>login</a> Here";
		$this->email->from('administrator@bkimechanical.com', 'Batchelor & Kimball, Inc. Administrator');
		$this->email->to($email);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');
		$this->email->subject('Reset Password Instructions');
		$this->email->message($html);
		$this->email->send();
	}

	public function forgotPassword(){
		$this->load->model('usermodel');
		$pwd = $this->getRandomPassword();
		$t = $this->usermodel->forgotPassword($this->security->xss_clean($this->input->post("email")), $pwd);
		if($t == 1){
			$this->sendForgotMail($this->security->xss_clean($this->input->post("email")),$pwd);
			echo "<h4 class='success-message'>You will receive an email with instructions on how to reset your password.</h4>";
		}
		elseif($t == 2){
			echo "<h4 class='error-message'>Something went wrong, Try again</h4>";
		}
		elseif($t == 3){
			echo "<h4 class='error-message'>User not found.</h4>";
		}

	}
	
	public function emp_profile(){	
		
		
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		//$this->load->model("usermodel");
		
		$uid = $this->uri->segment(3);
		$this->load->model("usermodel");	
		
		
		$data = $this->usermodel->getUser($uid);
		//var_dump($uid);
		//var_dump($data);exit;
		$address = $this->usermodel->getAddress($uid);

		$empid = $this->usermodel->getEmpId($uid);
		//$data = $this->usermodel->getList('E');
		$data1 = $this->usermodel->getCommonFiles($empid);
		$data2 = $this->getSpecificFiles($empid);
		$data['data1'] = $data1;
		$data['data2'] = $data2;


		$data["data"] = $data;
		$data["address"] = $address;

		//$this->load->view('users/html/profile.php',$data);
		
		$this->load->view("users/html/employee-profile.php",$data);
	}
	
	public function movetoArchive($folder){
		$this->load->library('s3');
		$s3 = new S3();
		
		$data = $this->list_s3_bucket($this->bucket,$folder);
		//var_dump($data);exit;
		
		foreach($data[$folder] as $file)
		{
			$s3->copyObject($this->bucket, $file['s3_key'], $this->archive_bucket, $file['s3_key'], S3::ACL_PRIVATE); 
				
		}
		//exit;
		return true;	
	}
	
	public function test(){
		$this->load->model('usermodel');
		//$name = "JOHN W. ANTHONY JR";
		//$name = "JOHN W.";
		//$name = "ANTHONY JR";
		//$name = "JOHN ANTHONY";
		$name = "JOHN JR";
		
		/*$myfile = fopen("application/logs/newfile.txt", "w") or die("Unable to open file!");
		$txt = "John Doe\n";
		fwrite($myfile, $txt);
		$txt = "Jane Doe\n";
		fwrite($myfile, $txt);
		fclose($myfile);*/
		var_dump(log_message('error', 'Some variable did not contain a value.'));
		var_dump(log_message('info', 'The purpose of some variable is to provide some value.'));
		
		$a = $this->usermodel->getEmpIdByName($name);
		var_dump($a);
	}

}
