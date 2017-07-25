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

		<!-- BREADCRUMB SECTION -->
		<section class="page-heading">
		    <div class="container">
			     <h1>EMPLOYEE PORTAL</h1>
			</div>
		</section>
		<!-- BREADCRUMB SECTION END -->

		<!-- MAIN CONTENT SECTION -->
		<section class="container main-content profile-content ">
        <?php echo $this->session->flashdata('message');?>
		<?php echo $this->session->flashdata('message1');?>
		<?php echo $this->session->flashdata('msg_update');?>
		<div id="fileerror"></div>
		    <div class="superadmin-user-table profile-detail-container">
		        <div class="user-table-header">
		            <span class="admin-acess-row">Employee Details</span>
		        </div>
		        <div class="profile-detail-body">
		        <form id="profile-details-form" name="profile-details-form" method="post" action="editProfile">
							<div>
								<div class="profile-basic-info-wrap">
									<label for="profile_pic_input" class="profile-pic-holder">
									<input id="profile_pic_input" type="file" name="profile_pic_input" accept="image/*"/  class="input">
                    <img alt="Profile Pic" src="<?php if(isset($address[0]->profile) && !empty($address[0]->profile) ){echo $address[0]->profile;}else{ echo base_url();?>includes/images/profilePic.png<?php }?>" id="profilepic">
					<input type="hidden" name="old_image" value="<?php echo $address[0]->profile; ?>">
					<div class="change-password-note profile-image-note">Image size should not exceed 150kb</div>
									</label>
									
                  <input type="hidden" name="profile-pic-hidden" id="profile-pic-hidden" value="<?php echo @$address[0]->profile ?>">

									<span class="profile-basic-info">
										<span class="profile-edit-info-wrap"><label for="profile_name">Name:</label>
										<input id="profile_name" type="text" name="profile_name" value="<?php echo $data[0]['name']; ?>" <?php $this->CI =&get_instance();if($this->CI->is_role_e()){echo "readonly = 'true'";}?>></span>
										<span class="profile-edit-info-wrap"><label for="profile_email">Email:</label>
										<input id="profile_email" type="email" name="profile_email" value="<?php echo $data[0]['email']; ?>"></span>
										<input type="hidden" id="profile_email1" type="email" name="profile_email_old" value="<?php echo $data[0]['email']; ?>"></span>
									</span>
									<div>
									</div>
								</div>
								<div class="profile-other-info-wrap">
									<span class="profile-edit-info-wrap"><label for="profile_address">Address:</label>
									<input id="profile_address" type="text" name="profile_address" value="<?php echo @$address[0]->address; ?>"></span>
									<span class="profile-other-info">
										<span class="profile-edit-info-wrap"><label for="profile_city">City:</label>
										<input id="profile_city" type="text" name="profile_city" value="<?php echo @$address[0]->city; ?>" pattern="[a-zA-Z\s]+"></span>
										<span class="profile-edit-info-wrap"><label for="profile_state">State:</label>
										<input id="profile_state" type="text" name="profile_state" value="<?php echo @$address[0]->state; ?>" pattern="[a-zA-Z\s]+"></span>
										<span class="profile-edit-info-wrap"><label for="profile_zip">ZIP:</label>
										<input id="profile_zip" type="tel" name="profile_zip" value="<?php echo @($address[0]->zip) ? $address[0]->zip : '' ; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57' pattern="^[0-9]{4,5}$" title="Length should be 4 or 5 numbers"></span>
										<span class="profile-edit-info-wrap"><label for="profile_phone">Phone:</label>
										<input id="profile_phone" type="tel" name="profile_phone" pattern="^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$" value="<?php echo @$address[0]->phone; ?>" onkeypress='return validatePhone(event);' title="(xxx)-xxx-xxxx"></span>
									</span>
								</div>
								</div>
								<div class="profile-action-buttons">
									<button type="button" class="popup-link change_password">CHANGE PASSWORD</button>
									<button class="update-button" type="submit">UPDATE INFO</button>
								</div>
            </form>
               </div>
			  
				<!--<div  style="<?php if($this->session->userdata("role") !="SA"){echo "display:none;";} ?>">
				 <form action="resendConfirmation" method="post">
					<div class="user-table-header ">
						<span class="admin-acess-row">Resend Email Confirmation</span>
					</div>
					<input type="text" placeholder="Email ID" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
					<button type="submit">RESEND</button>
				</form>
		   </div>-->
		   <div class="user-table-header " style="<?php if($this->session->userdata("role") !="SA"){echo "display:none;";} ?>">
						<span class="admin-acess-row">Resend Email Confirmation</span>
		   </div>
		   <div  class="profile-detail-body" style="<?php if($this->session->userdata("role") !="SA"){echo "display:none;";} ?>">
				 <form class="resend-email-form" action="resendConfirmation" method="post">
				 <span class="profile-edit-info-wrap">
					<input type="text" placeholder="Email ID" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"></span>
					
					<button type="submit" class="update-button resend-email-btn">RESEND</button>
				</form>
		   </div>
		   
		   
			
		    </div>
			
			
			<?php if($this->session->userdata('role') == 'A' ){?>
			
		    <div class="superadmin-user-table profile-doc-container">
		        <div class="user-table-header">
		            <span class="admin-acess-row">My Documents</span>
		        </div>
				<!-- .user-table -->
				
				<ul class="user-table">
				<?php
						 //files looping
						  if(@count($formatted[$eid]) > 0)
							{							
								foreach($formatted[$eid] as $d1){
								?>
							   <li id="<?php echo $d1['fid'];?>">
									<span class="superadmin-user-wrap">
										<span class="superadmin-user-name"><?php echo $d1['file_name'];?></span>
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
			<?php }?>
		</section>
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
	<section id="change_password" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Change Password</span>
	        <div class="popup-body">
			<div id="message" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
	        <form id="add_change_employee_form" action="updatePassword" method="post">
	            <input type="password" id="old_password" name="old_password" placeholder="Old Password" required="required" value="" >
	            <input type="password" id="new_password" name="new_password" placeholder="New Password" required="required" pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="Passwords must be at least 8 characters in length and must contain at least one capital letter and one number" minlength="8">
	             <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm New Password" required="required" title="Passwords must be at least 8 characters in length and must contain at least one capital letter and one number" pattern="(?=.*\d)(?=.*[A-Z]).{8,}" minlength="8">
	            <button type="submit" id="change_password_btn">UPDATE</button>
			
	        </form>
			<div  class="change-password-note">Passwords must be at least 8 characters in length and must contain at least one capital letter and one number</div>
	        </div>
	    </div>
	</section>

	
	<?php include_once 'footer.php'; ?>
