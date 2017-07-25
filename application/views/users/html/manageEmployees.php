<?php
if($this->uri->segment(3) !="nh")
{
include_once 'header.php';
} 	
 ?>

	<div class="tabs manage-page-container">
		<!-- BREADCRUMB SECTION -->
		<section class="page-heading tab-links">
			<div class="container">
				<ul>
					<li class="active"><a href="#manage_emp" id="emp">MANAGE EMPLOYEES</a></li>
					<li><a href="#manage_files" id="files">MANAGE FILES</a></li>
				</ul>
			</div>
			
		</section>
		<!-- BREADCRUMB SECTION END -->
		<section class="container main-content tab-content" id="searchbar">
			

			<form id="searchEmployee">
				<div class="inner-addon right-addon">
					<i class="glyphicon glyphicon-search"></i>
					<input id="employee_name" type="text" name="employee_name" class="manage-emp-search-input" placeholder="Search employee">
					<button type="button" id="searchBtn" value="search" class="manage-emp-search-button" onclick="getEmployeesBysearch()">SEARCH</button> 
				</div>
		
			</form>			
		</section>
		
		<!-- MAIN CONTENT SECTION -->
		<section class="container main-content tab-content" id="load-div">
      <?php include_once 'partials/employee-list.php';?>
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
			<div id="message" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
	        <form id="add_new_employee_form" method="post" action="saveListItem/E">
				<input type="text" id="new_employee_empid" name="new_employee_empid" placeholder="Employee ID" required="true" maxlength="9" pattern="[0-9]+" title="Please fill numbers" autofocus autocomplete="off">
	            <input type="text" id="new_employee_name" name="new_employee_name" placeholder="Name" required="true" maxlength="50" autocomplete="off">
	            <input type="email" id="new_employee_id" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"  name="new_employee_email" placeholder="Email ID" title="abc@xyz.com" required="true" maxlength="50" autocomplete="off">
	            <button type="submit">ADD</button>
	        </form>
	        </div>
	    </div>
	</section>

	<!-- EDIT EMPLOYEE -->
	<section id="edit_employees" class="popup">
	    <div class="popup-content">
	        <span class="popup-header">Edit Employee</span>
	        <div class="popup-body">
			<div id="message1" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
	        <form id="edit_employees_form" method="post" action="editListItem/E" validate>
              <input type="hidden" name="uid" id="edit_employee_uid" value="">
			   <input type="text" id="edit_employee_empid" name="new_employee_empid"  placeholder="Employee ID" required="true" maxlength="9" pattern="[0-9]+" title="Please fill numbers">
			   <input type="hidden" id="edit_employee_empid1" name="new_employee_empid1" value="">
			   <input type="hidden" id="edit_employee_id1" name="edit_employee_id1" value="">
	            <input type="text" id="edit_employee_name" name="new_employee_name"  placeholder="Name" required="true" maxlength="50">
	            <input type="email" id="edit_employee_id"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="abc@xyz.com" name="new_employee_email"  placeholder="Email ID" required="true" maxlength="50">
	            <button type="submit" id="edit_employee_btn" onclick="removeChars()">DONE</button>
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
            <div id="message1" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
	        <form id="edit_folder_form1" action="deleteFile" method="post">
              <input type="hidden" name="uid" value="" id="record_index">
			  <input type="hidden" name="empid" value="" id="record_ul">
			  <input type="hidden" name="action" value="" id="action">
			  <input type="hidden" name="file_name" value="" id="record_file_name">
			  <input type="hidden" name="file_type" value="" id="record_file_type">
	            <button type="submit" id="confirm">YES</button>
	            <button type="button" id="cancel">NO</button>
	        </form>
	        </div>
	    </div>
	</section>
<script>
function removeChars(){
	$('#employee_name').val('');
}
</script>

  <?php if($this->uri->segment(3) !="nh")
  {
  include_once 'footer.php';
   } ?>
