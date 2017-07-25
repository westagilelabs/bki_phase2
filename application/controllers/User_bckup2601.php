<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	private $bucket = "bbatchelor-uploads";
	private $s3URL = "//s3.amazonaws.com/bbatchelor-uploads/";

	
	public function __construct(){
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('session');
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
		if(! $result){
			// If user did not validate, then show them login page again
			$msg = 'Invalid username and/or password.<br />';
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
				
				$flag = $this->usermodel->saveListItem($flag);
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
							
				
				$this->sendMail($em);
				//$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Successfully created !!!</h4>");
				echo "<h4 class='success-message' id='user-message'>Successfully created !!!</h4>";
		}else {
				if($error)
				{
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>");
					echo "<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>";
				}
				else if($error1)
				{
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>");
					echo "<h4 class='error-message' id='user-message'>Failed to save..Duplicate Email ID</h4>";
				}
				else
				{
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message' >Something went wrong !!!</h4>");
					echo "<h4 class='error-message' id='user-message' >Something went wrong !!!</h4>";
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
		if($this->input->post("new_employee_empid") != $this->input->post("new_employee_empid1"))
		{
			if(!$this->usermodel->CheckEmpId($this->security->xss_clean($this->input->post("new_employee_empid"))))
				$flag = $this->usermodel->saveListItem($flag);
			else
				$error = true;
		}
		else{
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
			//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>");
			echo "<h4 class='error-message' id='user-message'>Failed to save..Duplicate Employee ID</h4>";
		}
		else{
				
			if($flag){
					//$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Updated Successfully !!!</h4>");
					echo "<h4 class='success-message' id='user-message'>Updated Successfully !!!</h4>";
			}else {
					//$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Something went wrong !!!</h4>");
					echo "<h4 class='error-message' id='user-message'>Something went wrong !!!</h4>";
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
		switch ($this->uri->segment(3)) {
			case 'A':
				$redirect = "/user/dashboard";
				break;
			case 'E':
				$redirect = "/user/manage";
				break;
		}

		if($flag){
				$this->session->set_flashdata("message","<h4 class='success-message' id='user-message'>Deleted Successfully !!!</h4>");
		}else {
				$this->session->set_flashdata("message","<h4 class='error-message' id='user-message'>Something went wrong !!!</h4>");
		}

		redirect($redirect,'refresh');
	}
	
	public function deleteFile(){
		
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
			$uri = $uri."/".$file;
			$mode = "file";
		}
		else if($this->security->xss_clean($this->input->post("action")) =="all"){
			//echo "in all";exit;
			$mode = "all";
		}
		
		
		
		$this->load->library("S3");
		$s3 = new S3();
		
		$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Something went wrong !!!</h4>");

		if($mode == "folder")
		{			
			
			//TODO : delete common file access
			$this->deleteCommonFile($folder);
			
			//exit;
			if($this->delete_s3_items($this->bucket,$folder))
			{
				$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully !!!</h4>");
				
				
				redirect("user/manage#manage_files");
			}
		}			
		else if($mode == "file")
		{
			$this->deleteCommonFile($folder,$file);
			//var_dump($uri);exit;
			if($s3->deleteObject($this->bucket,$uri))
			{
				$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully !!!</h4>");
				redirect("user/manage#manage_files");
			}
		}
		else if($mode == "all")
		{
			$this->load->model('usermodel');
			$this->usermodel->deleteCommonFolder();
			if($this->delete_s3_folders($this->bucket, "/"))
			{
				$this->session->set_flashdata("message","<h4 class='success-message' id='files-message'>Deleted Successfully !!!</h4>");
				redirect("user/manage#manage_files");
			}
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
			$data = $this->usermodel->getList();
		}
		else if($this->is_role_a())
		{
			$data = $this->usermodel->getList('E');
			$data2 = $this->getSpecificFiles();
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
		$this->load->view('users/html/manageEmployees.php',$data);
	}

	public function profile()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		$role = $this->session->userdata('role');
		$uid = $this->session->userdata('uid');
		$this->load->model("usermodel");
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


		// 		$file_success = true;
		// 		$uid = $this->session->userdata("uid");
		// 		$total = count($_FILES['upload_new_common_file']['name']);
		// 		$allowed = array('jpg','png','jpeg','gif');
		// 		// Loop through each fil1e
		// 		for($i=0; $i<$total; $i++) {
		// 			//Get the temp file path
		// 			$tmpFilePath = $_FILES['upload_new_common_file']['tmp_name'][$i];
		// 			//Make sure we have a filepath
		// 			if ($tmpFilePath != ""){
		// 				//Setup our new file path
		// 				$fname = explode(".",$_FILES['upload_new_common_file']['name'][$i]);
		// 				$newFilePath = "./uploads/" . $uid.$fname[1];
		//
		// 				if(in_array(strtolower($fname[1]),$allowed))
		// 				{
		// 					//Upload the file into the temp dir
		// 					if(move_uploaded_file($tmpFilePath, $newFilePath)) {
		// 						$file_success = true;
		// 					}
		// 					else
		// 					{
		// 							$file_success = false;
		// 					}
		// 				}
		// 				else
		// 				{
		// 						//TODO : Wrong File type
		// 						$file_success = false;
		// 				}
		//
		// 			}
		// 		}
		//
		// if($file_success)
		// {
			$this->load->model("usermodel");
			$flag = $this->usermodel->editProfile();

			if($flag){
					$this->session->set_flashdata("message","<h4 class='success-message'>Updated Successfully !!!</h4>");
			}else {
					$this->session->set_flashdata("message","<h4 class='error-message'>Something went wrong !!!</h4>");
			}
	//	}

		redirect("/user/profile");
		//$post_data = $this->input->post("admin_name");
	}

	public function updatePassword()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		$this->load->model("usermodel");
		$flag = $this->usermodel->updatePassword();
		if($flag){
				echo "<h4 class='success-message'>Updated Successfully !!!</h4>";
		}else {
				echo "<h4 class='error-message'>Current password is wrong!!!</h4>";
		}
		//redirect("/user/profile");
	}

	public function files()
	{
		if(!$this->is_logged_in())
		{
			redirect("user/index",'refresh');
		}
		
		$empid = $this->session->userdata('empid');
		$this->load->model("usermodel");
		//$data = $this->usermodel->getList('E');
		$data1 = $this->usermodel->getCommonFiles($empid);
		$data2 = $this->getSpecificFiles($empid);
		$data['data1'] = $data1;
		$data['data2'] = $data2;
		//echo "<pre>";var_dump($data);exit;
		$this->load->view("users/html/files.php",$data);

	}
	public function do_upload()
	{
		//var_dump($_FILES);exit;
		$tt =false;
		$it = false;
		if($this->uri->segment(3) == 'retry'){
			$tt = true;
			$it = true;
		}if($this->uri->segment(4)){
			$fpath = $this->uri->segment(4);
		}
		
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
			//TODO: do validation first
			$rrt = $this->uploadSpecificFile($files,$empid,$it);
			if(is_bool($rrt) && $rrt ){
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
				else if($ret->value == false){
					$fpath = $ret->fpath;
					//do_upload/retry/$fpath
					$ret = "<h4 class='error-message' id='files-message'>Some employee details are not found with in system. Still <a href='do_upload/retry/$fpath'> Continue </a> </h4>";
				}
			}			
			
		
			if($rrt)
			{
				$this->session->set_flashdata("message",$ret);						
				//ob_end_flush();
				//redirect("user/manage");
				echo("<script>location.href = '/index.php/user/manage#manage_files';</script>");
			}
			else{
				$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Something went wrong.</h4>");
				ob_end_flush();
				echo("<script>location.href = '/index.php/user/manage#manage_files';</script>");
			}
			
		}			
		
		if($common_upload )
		{
			//echo "into common";exit;
			$total = count($files['name']);
			$allowed = array('pdf');
			// Loop through each fil1e
			for($i=0; $i<$total; $i++) {
				//Get the temp file path
				$tmpFilePath = $files['tmp_name'][$i];
				//Make sure we have a filepath
				if ($tmpFilePath != ""){
					//Setup our new file path
					$newFilePath = "./uploads/" . $files['name'][$i];
					$fname = explode(".",$files['name'][$i]);

					if(in_array(strtolower($fname[1]),$allowed))
					{
						$folderpath = "common_files";
						$date = "-".date('Y-m-d');
						$folderName = "$folderpath/$fname[0]$date.$fname[1]";  // path on s3 bucket.
						
						//Upload the file into the temp dir
						if($s3->putObjectFile($tmpFilePath ,$this->bucket ,$folderName ,S3::ACL_PUBLIC_READ )) {
							
							$fileinfo = $s3->getObjectInfo($this->bucket, $folderName);
							//var_dump($fileinfo);exit;
							$file['file_name'] = $fname[0].$date.".".$fname[1];
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
							$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Something went wrong , Try again !!</h4>");
							redirect("user/manage#manage_files");							
						}
					}
					else
					{
						//Wrong File type
						$data = array('messae' => "Unsupported file type");
						$this->session->set_flashdata("message","<h4 class='error-message' id='files-message'>Unsupported file type</h4>");
						redirect("user/manage#manage_files");
					}
				}
			}
		}//if common file ends
	}
	
	public function uploadSpecificFile($files,$empid,$it){
		//var_dump($empid);exit;
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
	
	public function list_s3_bucket($bucket_name)
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
	
	public function sendMail($email){
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
		
		$html = "Your Account has been created..<br/> <br/>
				Username : $email<br/> <br/>
				Password is :Password1@<br/><br/>				
				You can <a href='http://ec2-54-85-211-219.compute-1.amazonaws.com/'>login</a> Here";
				
		$this->email->from('admin@bbatchelor.com', 'Batchelor & Kimbal Admin');
		$this->email->to($email);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');
		$this->email->subject('Account Created');
		$this->email->message($html);
		$this->email->send();
	}
	
	public function sendForgotMail($email){
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
		
		$html = "Your Account has been reset..<br/> <br/>
				Username : $email<br/> <br/>
				Password is :Password1@<br/><br/>
				You can <a href='http://ec2-54-85-211-219.compute-1.amazonaws.com/'>login</a> Here";
		$this->email->from('admin@bbatchelor.com', 'Batchelor & Kimbal Admin');
		$this->email->to($email);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');
		$this->email->subject('Account Recovery');
		$this->email->message($html);
		$this->email->send();
	}
	
	public function forgotPassword(){
		$this->load->model('usermodel');
		$t = $this->usermodel->forgotPassword($this->security->xss_clean($this->input->post("email")));
		if($t == 1){
			
			$this->sendForgotMail($this->security->xss_clean($this->input->post("email")));
			//$this->session->set_flashdata("message","<h4 class='success-message'>Email sent </h4>");
			//redirect("user/index");	
			echo "<h4 class='success-message'>Email sent.. </h4>";
		}
		elseif($t == 2){
			//$this->session->set_flashdata("message","<h4 class='error-message'>Something went wrong , Try again</h4>");
			//redirect("user/index");	
			echo "<h4 class='error-message'>Something went wrong , Try again..</h4>";
		}
		elseif($t == 3){
			//$this->session->set_flashdata("message","<h4 class='error-message'>We didn't find entered email id with us.</h4>");
			//redirect("user/index");
			echo "<h4 class='error-message'>We didn't find entered email id with us..</h4>";			
		}
		
	}

}
