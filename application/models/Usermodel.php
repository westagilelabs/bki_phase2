<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class UserModel extends CI_Model
{
  function __construct(){
    parent::__construct();
    $this->load->helper('url');
  }

  public function getList($role="",$uid="")
  {
    $data= array();
	
	if(!empty($role))
	{
		
		if($role == "NSA"){
			$this->db->where("role != 'SA' ");
		}
		else
			$this->db->where("role",$role);
	}
		
    if(!empty($uid))
        $this->db->where("uid",$uid);
    
	$this->db->order_by('uid','DESC');
    $query =  $this->db->get('users');
    if($query->num_rows() > 0)
	{
		// If there is a user, then create session data
		foreach($query->result() as $row)
		{
			 $data[] = array('uid' => $row->uid,
				'empid' => $row->empid,
				'name' => $row->name,
				'folder' => $row->folder,
				'first_login' => $row->first_login,
				'email' => $row->email);
		}
		return $data;
    }
    return $data;

  }
  public function getEmployeesBySorted($role=""){
	  
	  $q = "SELECT * FROM users ";
	  
	  if(empty($role))
		   $q = $q." WHERE role != 'SA' ";
	  else 
		   $q = $q." WHERE role = '$role' ";
   
	  if(!empty($this->uri->segment(4)))
	  {
		  $q = $q." AND name  LIKE '%".urldecode($this->uri->segment(4))."%'";
	  }	  
	  $q = $q." ORDER BY fname,mname,lname ";
	  $query = $this->db->query($q);
	  
		if($query->num_rows() > 0)
		{
			// If there is a user, then create session data
			foreach($query->result() as $row)
			{
				 $data[] = array('uid' => $row->uid,
					'empid' => $row->empid,
					'name' => $row->name,
					'role'=>$row->role,
					'folder' => $row->folder,
					'first_login' => $row->first_login,
					'email' => $row->email);
			}
			return $data;
		}
		return $data;
  }
  public function getUser($uid)
  {
    $data= array();	
		
    if(!empty($uid))
        $this->db->where("uid",$uid);
    
	//$this->db->order_by('uid','DESC');
    $query =  $this->db->get('users');
    if($query->num_rows() > 0)
	{
		// If there is a user, then create session data
		foreach($query->result() as $row)
		{
			 $data[] = array('uid' => $row->uid,
				'empid' => $row->empid,
				'name' => $row->name,
				'folder' => $row->folder,
				'first_login' => $row->first_login,
				'email' => $row->email);
		}
		return $data;
    }
    return $data;

  }

  
  public function saveListItem($flag,$file = array(""),$pwd = "")
  {
	 $table  = "users";
	 $f = "";
    switch ($this->uri->segment(3)) {
      case 'A':
          $role = "A";
          break;
      case 'SA':
          $role = "SA";
          break;
      case 'E':
          $role = "E";
          break;
      default:
        $role = "U";
		$table = "common_files";
		break;
    }
    if(!empty($role))
    {
		//TODO : Add employee id
      if($role =="A")
      {
		  $a = $this->security->xss_clean($this->input->post("admin_name"));
		  $aa = explode(" ",$a);
        $data = array("name" => $this->security->xss_clean($this->input->post("admin_name")),
				"empid"=>$this->security->xss_clean($this->input->post("new_employee_empid")),
              "email"=> $this->security->xss_clean($this->input->post("admin_email")),
              "pwd" => md5($pwd),
			  "first_login"=>1,
              "role" => $role,
			  "fname" => ($aa[0]) ? $aa[0] : "",
			  "mname" => ($aa[1]) ? $aa[1] : "",
			  "lname" => ($aa[2]) ? $aa[2] : ""
        );
		 if($flag == "update"){
			  unset($data['pwd']);
			  unset($data['role']);
			  unset($data['first_login']);
		  }
      }
      else if($role =="E")
      {
		  $a = $this->security->xss_clean($this->input->post("new_employee_name"));
		  $aa = explode(" ",$a);
		  
		   $data = array(
				"name" => $this->security->xss_clean($this->input->post("new_employee_name")),
				"empid"=>$this->security->xss_clean($this->input->post("new_employee_empid")),
			  "email"=> $this->security->xss_clean($this->input->post("new_employee_email")),
			  "pwd" => md5($pwd),
			  "first_login"=>1,
			  "role" => $role,
			  "fname" => ($aa[0]) ? $aa[0] : "",
			  "mname" => ($aa[1]) ? $aa[1] : "",
			  "lname" => ($aa[2]) ? $aa[2] : ""
			);
		
		  if($flag == "update"){
			  unset($data['pwd']);
			  unset($data['role']);
			  unset($data['first_login']);
		  }
        
      }
	  else if($role =="U")
      {
		  $emps = $this->getEmployeeIDs();
		  
		  //var_dump($emps);exit;
		  $json = json_encode($emps);
		  //var_dump($this->checkFileName($file['file_name']));exit;
		  $a = explode(".",$file['file_name']);
		  $b = explode("-",$a[0]);
		  $bc = count($b);

			//var_dump($b);
			//var_dump($bc);	
			$aa = "";
				 $c = 0;
			  for($j=$bc; $j>=0;$j--){
				  if($c > 3)
				  {
					  $aa = $aa."".$b[$j];
				  }
				 $c++;
			  }
		  
		  //var_dump($aa);
		 // exit;
		  $dd = $this->checkFileName($file['file_name']);
		  //var_dump($dd);exit;
		  if($dd)
		  {		  
			  $f = "update";
		  }
		  
			  $data = array("file_name" => $file['file_name'],
			  "date"=> date('Y-m-d H:i:s'),
			  "unallowed" => "",
			  "allowed"=>$json,
			  "size" => $file['file_size'],
			  "status" => "A" );
		  
      }
      if($flag == "insert" &&  empty($f)){
		 // var_dump($data);exit;
          return $b = $this->db->insert($table,$data);
      }
      else {       
		if($f){			
			 $this->db->where("file_name",$file['file_name']);			
			 return $b = $this->db->update($table,$data);
			  echo $this->db->last_query();
		}
		else{
			$this->db->where("uid",$this->input->post("uid"));
			return $b = $this->db->update($table,$data);
		}
      }

    }
    else {
      //TODO:Error Messae
    }

  }

  public function deleteListItem()
  {
    $uid = $this->security->xss_clean($this->input->post("uid"));
    switch ($this->security->xss_clean($this->uri->segment(3))) {
			case 'A':
				$role = "A";
				break;
			case 'E':
					$role = "E";
				break;
		}
		//var_dump($this->input->post("uid"));exit;
    if(!empty($uid)){
		if($role =='E')
			$this->db->where("empid ='$uid'");
		else
			$this->db->where("uid ='$uid'");
		//$this->db->where("role ='$role'");
  		if($this->db->delete("users"))
  		{
  			return true;
  		}
  		return false;
    }
    else {
      return false;
    }

  }

  public function editProfile($uid = "",$image = false)
  {
    $this->load->library('session');
	if(empty($uid))
		$uid = $this->session->userdata('uid');
    $this->db->select("COUNT(1) as cnt");
		$this->db->from("address");
    $this->db->where("uid ='$uid'");
    $query  =$this->db->get();


    //$aid = $this->session->user_data('uid');
	if(!$image)
	{
		if($this->input->post("profile_email") != $this->input->post("profile_email_old"))
		{
			if($this->CheckEmailID($this->input->post("profile_email"))){
				return false;
			}
		}
		$data_info = array("name" => $this->input->post("profile_name"),
			  "email"=> $this->input->post("profile_email")
		);
		$data_address = array("address" => $this->input->post("profile_address"),
			  "city"=> $this->input->post("profile_city"),
			  "state" => $this->input->post("profile_state"),
			  "zip" => $this->input->post("profile_zip"),
			  "phone" => $this->input->post("profile_phone"),
			  "profile" =>$this->input->post("profile-pic-hidden")
		);
		$this->db->where("uid ='$uid'");
		$this->db->update("users",$data_info);
		$this->session->set_userdata('name',$this->input->post("profile_name"));
	}
	else{
		$data_address = array(
			  "profile" =>$this->input->post("profile-pic-hidden")
		);
	}
    

    if($query->result()[0]->cnt == 0){
      $data_address["uid"] = $uid;
      return $this->db->insert("address",$data_address);
    }else{
      $this->db->where("uid ='$uid'");
     return $this->db->update("address",$data_address);
    }
  }
  //#end

  public function getAddress($uid)
  {
    $this->db->where("uid = '$uid'");
    $query =  $this->db->get('address');
    if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
			     $data[] = $row;
      }
      return $data;
    }
  }

  public function updatePassword()
  {
    $this->load->library('session');
    $uid = $this->session->userdata('uid');
    $cpwd = md5($this->security->xss_clean($this->input->post("old_password")));
    $npwd = $this->security->xss_clean($this->input->post("new_password"));
    $cnpwd = $this->security->xss_clean($this->input->post("confirm_new_password"));

    $this->db->select("COUNT(1) as cnt");
		$this->db->from("users");
    $this->db->where("uid ='$uid'");
    $this->db->where("pwd ='$cpwd'");
    $query  =$this->db->get();

    if($query->result()[0]->cnt == 0){
      return false;
    }
    else{
      if($npwd == $cnpwd)
      {
        $data_info = array("pwd" => md5($npwd),"first_login"=>0);
        $this->db->where("uid ='$uid'");
        return $this->db->update("users",$data_info);
      }
      else{
        return false;
      }

    }
  }
  
  public function getCommonFiles(){
	  $data= array();	
	$this->db->order_by('date','DESC');
    $query =  $this->db->get('common_files');
    if($query->num_rows() > 0)
	{
		return $query->result() ;
    }
    return $data;
  }
  
   public function getEmployeeIDs(){
	  $data= array();	
	$this->db->where('status','A');
	//$this->db->where("folder IS NULL");
    $query =  $this->db->get('users');
	//echo $this->db->last_query();
    if($query->num_rows() > 0)
	{
		foreach($query->result() as $row)
		{
			$data[] = $row->empid;
		}
    }
    return $data;
  }
  
  
  
  public function getEmpId($uid){
	    $this->db->where("uid = '$uid'");
		$query =  $this->db->get('users');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
		  return $data;
		}
  }
  
  public function getEmpIdByEmail($email){
	    $this->db->where("email = '$email'");
		$this->db->where("role != 'SA'");
		$query =  $this->db->get('users');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
		  return $data;
		}
		return false;
  }
  
  public function CheckEmpId($empid){
		if(!empty($empid))
			$this->db->where("empid = '$empid'");
		$query =  $this->db->get('users');
		//var_dump($query->num_rows());exit;
		if($query->num_rows() > 0)
		{			
		  return true;
		}
		return false;
  }
  
  public function CheckEmailID($email){
		if(!empty($email))
			$this->db->where("email = '$email'");
		$query =  $this->db->get('users');
		//var_dump($query->num_rows());exit;
		if($query->num_rows() > 0)
		{			
		  return true;
		}
		return false;
  }
  
  public function deleteCommonFolder(){
	 if($this->db->truncate('common_files'))
		 return true;
	 else
		 return false;
  }
  
  public function deleteCommonFile($empid,$file){
		if(!empty($file))
			$this->db->where("file_name = '$file'");
		
		//var_dump($file);exit;
		$newA = [];
		$skip = true;
		$query =  $this->db->get('common_files');
		if($query->num_rows() > 0)
		{
			
			foreach($query->result() as $row)
			{
				$ja = "";
				$aw = $row->allowed;
				$awa = (array)(json_decode($row->allowed));
				//var_dump(empty(json_decode($row->allowed)));exit;
				if(!empty($awa)){					
					
					if(($key = array_search($empid, $awa)) !== false) {
						unset($awa[$key]);
					}		
					$skip = false;	
									
				}		
					//$a = $this->getEmployeeIDs();
					
					$awa1 = array_values($awa);
				$ja = json_encode($awa1);
				//$ja = $awa;
				if(!$skip)
				{
					$update = array("allowed"=>$ja);
					$file = $row->file_name;
					if(!empty($file))
						$this->db->where("file_name = '$file'");
					
					
					$res = $this->db->update("common_files",$update);
				}
			}
		}
		//exit;
		return true;
  }
  
  public function getCommonFilesCount(){
	  $this->db->where('status','A');
	  $query = $this->db->get('common_files');
	  //echo "<pre>";
	  $cnt = 0;
	  foreach($query->result() as $file){
		  if($file->unallowed)
		  {
			$arra[] = json_decode($file->unallowed);
			$cnt = $cnt+count(json_decode($file->unallowed));		  
		  }
		 
	  }
	 return $cnt;
  }
  
  public function getEmpIdByName($name){
	
	$this->db->where(" name LIKE  '%$name%' ");//Fname Lname combo
	$this->db->where(" folder IS  NULL "); //check not archieved
	$query =  $this->db->get('users');
	//echo $this->db->last_query();
	//echo "<br>";
	if($query->num_rows() > 0)
	{
		foreach($query->result() as $row)
		{			
			return $row->empid;
		}	  
	}
	else{
		$a = explode(" ",$name);
		//var_dump($a);
		$n2 = "";
		$n = $a[0] ? $a[0] : "";
		$n1 = $a[1] ? $a[1] : "";
		if(isset($a[2])){
			$n2 = $a[2];
		}
		//echo CI_VERSION; 
		$this->db->where(" name LIKE  '%$n%$n1%$n2%' ");//Fname Lname combo
		$this->db->where(" folder IS  NULL "); //check not archieved
		$query =  $this->db->get('users');
		//echo "Secnd check <br/>";
		//echo $this->db->last_query();
		//echo "<br>";
		//exit;
		foreach($query->result() as $row)
		{			
			return $row->empid;
		}
	}
  }
  
   public function getEmpIdByName1($name){

	//$this->db->like("name",$name);
	$this->db->like("name",$name);
	//$query = $this->db->where(" folder IS  NULL ")->get('users'); //check not archieved
	$query = $this->db->get('users'); //check not archieved
	//echo $this->db->last_query();
	//echo "<br/>";
	//var_dump($query->num_rows());exit; 
	if($query->num_rows() > 0)
	{
		foreach($query->result() as $row)
		{			
			return $row->empid;
		}	  
	}
  }
  
  public function forgotPassword($email,$pwd){
	$this->db->where("email = '$email'");
	$query =  $this->db->get('users');

	if($query->num_rows() > 0)
	{
		$data = array("pwd" => md5($pwd),"first_login"=>1);
		$this->db->where("email = '$email'");
		
		$res = $this->db->update('users',$data);
		if($res){
			return 1;
		}
		return 2;
	}
	else{
		return 3;
	}
  }
  
  public function checkEmpIds($emp){
		//$empids = implode(',',$emp);
		$dd = array();
		$this->db->where("empid IN ('" . implode("','", $emp) . "') ");
		$query =  $this->db->get('users');
		$res = $query->result();
		//echo $this->db->last_query();
		foreach($query->result() as $row)
		{
			$dd[] =  $row->empid;
		}
		$not_found = array_diff($emp, $dd);
		return $not_found;
		
  }
  
    public function checkFileName($filename){
		//$empids = implode(',',$emp);
		$dd = array();
		$this->db->where("file_name",$filename);
		//$this->db->where("file_name like $filename%");
		$query =  $this->db->get('common_files');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$dd['allowed'] =  $row->allowed;
				$dd['unallowed'] =  $row->unallowed;
			}
			return $dd;
		}
		return false;		
  }
  
  public function getUsers(){
	  $query =  $this->db->get('users');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$dd[] =  $row->empid;
			}			
		}
		return $dd;
  }
  /*public function getUsersNULL(){
	  $this->db->where("folder IS NULL");
	  $query =  $this->db->get('users');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$dd[] =  $row->empid;
			}			
		}
		return $dd;
  }*/
  
  public function setArchive($empid){
	  $this->db->where("empid",$empid);
	 // $query =  $this->db->get('users');
	  
	  if($this->db->update("users",array("folder"=>"I","archived_date"=>date('Y-m-d'))))
	  {
		  return true;
	  }
	   return false;
  }
  
  public function checkFirstLogin($uid){
	  $this->db->where("uid",$uid);
	  $query = $this->db->where("first_login",1)->get('users');
	 if($query->num_rows() > 0)
	 {
		  return true;
	  }
	   return false;
  }
  public function updateNewPassword($e,$pwd){
	  $this->db->where("email",$e);
	 if($this->db->update('users',array("pwd"=>$pwd,"first_login"=>1)))
	 {
		  return true;
	  }
	   return false;
  }
  
  
  

}
?>
