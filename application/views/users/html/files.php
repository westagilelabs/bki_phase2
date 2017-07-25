<?php include_once 'header.php'; 
 	//echo "<pre>";print_r($data1);echo "next files";print_r($data2);exit;
	$formatted = array();
	$c = 0;

		$eid = $this->session->userdata("empid");
		foreach($data1 as $d1){
			$distance = [];
			$un = json_decode($d1->unallowed);
			$aw = json_decode($d1->allowed);
			$aw = array($aw);
			//echo "<pre>";
			//var_dump($aw);exit;
			if(!is_null($aw)){
				if(in_array($eid,$aw[0]))
				{
					$url = "https://s3.amazonaws.com/bbatchelor-uploads/common_files/".$d1->file_name;
					$formatted[$eid][] = array(
											'fid' =>$c,									
											'file_name'  => $d1->file_name,
											'size' => $d1->size,
											'date'  => $d1->date,
											'url' =>$url);
											
					foreach ($formatted[$eid]  as $key => $row) {
						$distance[$key] = $row['date'];
					}

					array_multisort($distance, SORT_DESC, $formatted[$eid] );
				
					$c++;
				}
			}			
		}
		//echo "<pre>";var_dump($data2);exit;
		if(isset($data2)){
			foreach($data2 as $d2){
				//echo "<pre>";var_dump($d2);exit;
				$formatted[$eid][] = array(
										'fid' =>$c,
										'file_name'  => $d2['file_name'],
										'size'  => $d2['file_size'],
										'date'  => $d2['created_on'],
										'url' => $d2['s3_link']);
				
				foreach ($formatted[$eid]  as $key => $row) {
					$distance[$key] = $row['date'];
				}

				array_multisort($distance, SORT_DESC, $formatted[$eid] );
	
				$c++;
			}
		}
		
	
	//echo "<pre>";echo $c;print_r($formatted);exit;
	
 ?>
	<div class="manage-page-container">
		<!-- BREADCRUMB SECTION -->
		<section class="page-heading">
			<div class="container">
				<h1>VIEW FILES</h1>
			</div>
		</section>
		<!-- BREADCRUMB SECTION END -->

		<!-- MAIN CONTENT SECTION -->
		<section class="container main-content employee-content">
			
		   <!-- MANAGE FILES AND FOLDERS TAB -->
		    <div class="superadmin-user-table">
					<div class="user-table-header">
						<span class="admin-acess-row">Files</span>
					</div>

					<!-- USER FOLDER/FILES VIEW -->
					<ul class="manage-employee-folder user-table">
					<?php
						 //files looping
						  if(@count($formatted[$eid]) > 0)
							{							
								foreach($formatted[$eid] as $d1){
								?>
							   <li id="<?php echo $d1['fid'];?>">
									<span class="superadmin-user-wrap">
										<a class="download-link" href="<?php echo $d1['url'];?>" target="_blank"><span class="superadmin-user-name"><?php echo $d1['file_name'];?></span></a>
										<span class="superadmin-user-email"><?php echo round(($d1['size'])/1000);?> kb</span>
										<span class="superadmin-user-email"><?php echo date('m-d-Y',strtotime($d1['date']));?> </span>
									</span>
									<a class="download-link" href="<?php echo $d1['url'];?>" download>
								<img alt="download" src="<?php echo base_url();?>includes/images/downloadIcon.png">
							</a>
								</li>
								<?php 
								  }
							}else{ ?>
							<li> <span class="superadmin-user-wrap"> No Documents Found</span></li>
					  <?php }
						  ?>
					</ul>
				</div>

		</section>
	</div>
		<!-- MAIN CONTENT SECTION END -->

		<!-- FOOTER SECTION -->
		<footer class="site-footer">
			<div class="container">
			<div class="row">
				<section class="footer-content">
					<ul class="footer-menu">
						<li class="menu-item"><a href="http://help.bkimechanical.com">HELP DESK</a></li>
					</ul>
				</section>
				<section class="footer-content">
					<span class="copyright">&#169; 2016 Batchelor &amp; Kimball,
						Inc.</span>
				</section>
				<section class="footer-content">
					<ul class="social-icons">
						<li><a class="fa fa-facebook" href="https://www.facebook.com/bkimechanical" target="_self"></a></li>
						<li><a class="fa fa-twitter"
							href="https://twitter.com/bkimechanical" target="_self"></a></li>
						<li><a class="fa fa-linkedin" href="https://www.linkedin.com/company/batchelor-&-kimball-inc-" target="_self"></a></li>
					</ul>
				</section>
			</div>
			</div>
		</footer>
		<!-- FOOTER SECTION END -->
	</div>

	<!-- POPUPS -->

	<!-- ADD EMPLOYEE -->
	<section id="add_new_employee" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Add New Employee</span>
	        <div class="popup-body">
	        <form id="add_new_employee_form">
	            <input type="text" id="new_employee_name" placeholder="Name">
	            <input type="email" id="new_employee_id" placeholder="Email ID">
	            <button type="button" id="add_new_employee_btn">ADD</button>
	        </form>
	        </div>
	    </div>
	</section>

	<!-- EDIT EMPLOYEE -->
	<section id="edit_employees" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Edit Employee</span>
	        <div class="popup-body">
	        <form id="edit_employees_form">
	            <input type="text" id="edit_employee_name" placeholder="Name">
	            <input type="email" id="edit_employee_id" placeholder="Email ID">
	            <button type="button" id="edit_employee_btn">DONE</button>
	        </form>
	        </div>
	    </div>
	</section>

		<!-- ADD NEW FOLDER -->
	<section id="add_new_folder" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Add New Folder</span>
	        <div class="popup-body">
	        <form id="new_folder_form">
	            <input type="text" id="new_folder_name" placeholder="Folder Name">
	            <button type="button" id="add_new_folder_btn">ADD</button>
	        </form>
	        </div>
	    </div>
	</section>

	<!-- EDIT FOLDER -->
	<section id="edit_folder" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Edit Folder Name</span>
	        <div class="popup-body">
	        <form id="edit_folder_form">
	            <input type="text" id="edit_folder_name" placeholder="Folder Name">
	            <button type="button" id="edit_folder_btn">DONE</button>
	        </form>
	        </div>
	    </div>
	</section>

	<!-- DELETE POPUP -->
	<section id="delete-icon" class="popup">
	    <div class="popup-content">
	        <span class="popup-header"></span>
	        <div class="popup-body">
	        <form id="edit_folder_form">
	             <button type="button" id="confirm">YES</button>
	            <button type="button" id="cancel">NO</button>
	        </form>
	        </div>
	    </div>
	</section>
<?php include_once 'footer.php'; ?>
