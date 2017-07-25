<?php 



 	//echo "<pre>";print_r($data1);echo "next files";print_r($data2);exit;
	$formatted = $formatted1 = array();
	$c = 0;
	$d = 0;
	$fold = 0;
	foreach($data as $d){
		$eid = $d['empid'];

			$fold++;
			//var_dump($eid);
			foreach($data1 as $d1){
				$distance = [];
				$un = json_decode($d1->unallowed);
				$aw = json_decode($d1->allowed);
				$aw = array($aw);
				//echo "<pre>";
				//echo $eid."<br/>";
				//var_dump($aw);
				//var_dump(in_array($eid,$aw[0]));
				//exit;
				//var_dump($aw);exit;
				//var_dump( in_array($eid,$aw[0]));
				if(!is_null($aw)){
					if(in_array($eid,(array)$aw[0]))
					{
						$url = "https://s3.amazonaws.com/bbatchelor-uploads/common_files/".$d1->file_name;
						$formatted[$eid][] = array(
												'fid' =>$c,
												'file_name'  => $d1->file_name,
												'size' => $d1->size,
												'date'  => $d1->date,
												'type' =>"common",
												'url' =>$url);
						//var_dump($formatted[$eid]);exit;
						foreach ($formatted[$eid]  as $key => $row) {
							$distance[$key] = $row['date'];
						}

						array_multisort($distance, SORT_DESC, $formatted[$eid] );

						$c++;
					}
				}
			}
			//echo "<pre>";var_dump($data2);exit;
			if(isset($data2[$eid])){
				foreach($data2[$eid] as $d2){
					//echo "<pre>";var_dump($d2);exit;
					$formatted[$eid][] = array(
											'fid' =>$c,
											'file_name'  => $d2['file_name'],
											'size'  => $d2['file_size'],
											'date'  => $d2['created_on'],
											'type' =>"individual",
											'url' => $d2['s3_link']);

					foreach ($formatted[$eid]  as $key => $row) {
						$distance[$key] = $row['date'];
					}
					
					//var_dump($distance);
					//var_dump($formatted[$eid]);exit;
					@array_multisort($distance, SORT_DESC, $formatted[$eid] );

					$c++;
				}
			}


	}
?>
<span id="msg"><?php echo $this->session->flashdata('message');?></span>
<span id="files-message1"><?php 

$mr = $this->session->flashdata('data');
$mm = $this->session->flashdata('names');
if($mr !="" && count($mr) > 0)
{
	echo "<h4 class='error-message'>Missing Records:</h4>";
	
	$cnt = count($mr);
	$mm = (array)$mm;
	
	if(count($mm) > 0)
	{
		$i=0;
		foreach($mm as $m)
		{
			if(!empty($m) && count($m) >0 )
			{
				if(($cnt-1) == $i)
				{
					echo $m."";
				}
				else
				{
					if(preg_match('/,/',$m))
						echo $m;
					else
						echo $m.", ";	
				}
			}
					
			$i++;
		}
	}
	else {
		$i=0;
		foreach($mr as $m){
			if(($cnt-1) == $i)
				{
					echo $m."";
				}
				else
				{
					if(preg_match('/,/',$m))
						echo $m;
					else
						echo $m.", ";	
				}
			$i++;
		}
	}
	
}
?></span>
<!-- MANAGE EMPLOYEE TAB -->
  <div class="superadmin-user-table tab active" id="manage_emp">
      <div class="user-table-header">
          <span class="admin-acess-row">Employee List</span>
           <span class="admin-add-new-employee popup-link add_new_employee">+ ADD NEW</span>
      </div>
      <ul class="user-table">
        <?php
  if(count($data) > 0)
        {
			$a=$this->session->userdata();
			
        foreach($data as $d){
			?>
         <li id="<?php echo $d['empid'];?>" data-uid=<?php echo $d['uid']?> style="display:<?php if($a['uid'] == $d['uid']){echo "none;";}?>">
              <span class="superadmin-user-wrap">
                  <a href="emp_profile/<?php echo $d['uid'];?>"><span class="superadmin-user-name"><?php echo $d['name'];?></span></a>
                  <span class="superadmin-user-email"><?php echo $d['email'];?></span>
              </span>
              <span class="superadmin-user-delete" style="display:<?php if($a['role'] == $d['role']){echo "none";}?> ">
				  <img alt="Edit User" src="<?php echo base_url()?>includes/images/editIcon.png" class="edit-employee popup-link edit_employees">
				  <img alt="Delete User" src="<?php echo base_url()?>includes/images/deleteIcon.png" class="delete-icon popup-link delete_user">
              </span>
          </li>
          <?php 
		  }
        }else{ ?>

       <li>
           <span class="superadmin-user-wrap">
               List is empty
           </span>
       </li>

        <?php	 }?>
      </ul>
  </div>

  <!-- MANAGE FILES AND FOLDERS TAB -->
  <div class="superadmin-user-table tab" id="manage_files">
    <form action="do_upload" id="file_upload" enctype="multipart/form-data" method="post">
  <input type="hidden" name="empid" value="" id="empid">
      <div class="user-table-header">
        <span class="admin-acess-row">Files</span>
        <span class="modify-files-wrap">
            <span class="admin-add-new-employee upload_new_folder">+ UPLOAD
            NEW FILE</span>
          <ul class="upload-file-options">
		  <?php $this->CI =&get_instance();
		  if(!$this->CI->is_role_a()){ ?>
             <li>
                 <label class="admin-add-new-employee" for="upload_new_common_file">Upload
           Common File</label>
          <input id="upload_new_common_file" name="upload_new_common_file[]" type="file"  onchange="return validate_fileupload(this.value,this);" accept="application/pdf,application/vnd.ms-excel" multiple="multiple" />
             </li>
		  <?php }?>
            <li>
             <label class="admin-add-new-employee" for="upload_new_employee_file">Upload
           Employee File</label>
          <input id="upload_new_employee_file" name="upload_new_employee_file[]" type="file" accept="application/pdf,application/vnd.ms-excel" multiple="multiple" onchange="return validate_fileupload(this.value,this);">
            </li>
        </ul>
        </span>
      </div>
  </form>

    <!-- ALL FOLDER VIEW -->
    <ul class="manage-user-table">
    <!-- PARENT FOLDER -->
      <li>
          <span class="all-folders-parent" id="all-folders">
              <span class="superadmin-user-wrap">
                  <span class="superadmin-user-name">All</span>
                  <span class="superadmin-user-email">
                  <span class="folder-number"><?php echo $fold;?></span> Folders, <span class="file-number"><?php echo $c;?></span> Files</span>
               </span>
               <span class="superadmin-user-delete">
                   <img alt="Delete Folder" src="<?php echo base_url()?>includes/images/deleteIcon.png" class="delete-icon popup-link delete_parent_folder">
             </span>
           </span>

           <!-- EMPLOYEE LIST WITH FOLDERS -->
        <ul class="all-folders-list">
        <?php
    $tt= 0 ;
        if(count($data) > 0)
  {
    foreach($data as $d){
      @$tt = $tt+count($formatted[$d['empid']]);

    ?>
     <li id="<?php echo $d['empid'];?>"  data-role="<?php echo $this->session->userdata("role");?>" style="display:<?php if($a['uid'] == $d['uid']){echo "none;";}?>">
        <span class="superadmin-user-wrap">
           <span class="superadmin-user-name"><?php echo $d['name'];?></span>
          <span class="superadmin-user-email"><?php echo @count($formatted[$d['empid']]);?> Files</span>
        </span>
        <span class="superadmin-user-delete" style="display:<?php if($a['role'] == $d['role']){echo "none";}?> ">
        <img alt="Delete User" src="<?php echo base_url()?>includes/images/deleteIcon.png" class="delete-icon popup-link delete_folder">
        </span>
      </li>
    <?php 
	}
  }else{ ?>
    <li><span class="superadmin-user-wrap"> No Employees Found</span></li>
  <?php }
    ?>

        </ul>
		
	
      </li>
    </ul>
	

    <?php
    //echo $tt;
    foreach($data as $dd){?>
    <!-- USER FOLDER/FILES VIEW -->
    <ul class="manage-employee-folder user-table <?php echo $dd['empid'];?>" data-empid="<?php echo $dd['empid'];?>">
      <?php
	  //var_dump($formatted1[$dd['empid']]);
	  if(isset($formatted[$dd['empid']])){
		  $ff = $formatted[$dd['empid']];
	  }
	  /*else{
		  $ff = $formatted1[$dd['empid']];
	  }*/
       //files looping
        if(@count( $formatted[$dd['empid']]) > 0)
        {

          foreach( $formatted[$dd['empid']] as $d1){
			  //var_dump($d1);exit;
          ?>
           <li id="<?php echo $d1['fid'];?>" data-file-type="<?php echo $d1['type'];?>">
            <span class="superadmin-user-wrap">
              <a class="download-link" href="<?php echo $d1['url'];?>" target="_blank">
			  <span class="superadmin-user-name"><?php echo $d1['file_name'];?></span></a>
              <span class="superadmin-user-email"><?php echo round(($d1['size'])/1000);?> kb</span>
			  <span class="superadmin-user-email"><?php echo date('m-d-Y',strtotime($d1['date']));?> </span>
            </span>
            <span class="superadmin-user-delete" style="display:<?php if($a['role'] == $dd['role']){echo "none";}?> ">
              <img alt="Delete User" src="<?php echo base_url()?>includes/images/deleteIcon.png" class="delete-icon popup-link delete_file" data-file-name="<?php echo  $d1['file_name'];?>" >
            </span>
          </li>
          <?php
            }
        }else{ ?>
    <li><span class="superadmin-user-wrap"> No Files Found</span></li>
  <?php }
    ?>

    </ul>
    <?php

    }?>
  </div>
