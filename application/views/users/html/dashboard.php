 <?php
/*if($this->uri->segment(3) !="nh")
{*/
 include_once 'header.php';
 //}?>

 <!-- BREADCRUMB SECTION -->
 <section class="page-heading">
		 <div class="container">
				<h1>SUPER ADMIN PANEL</h1>
	  </div>
 </section>
 <!-- BREADCRUMB SECTION END -->

	 <!-- MAIN CONTENT SECTION -->
	 <section class="container main-content">

      <?php echo $this->session->flashdata('message');?>

			 <div class="superadmin-user-table">
					 <div class="user-table-header">
							 <span class="admin-acess-row">Admin Access</span>
								<span class="admin-add-new-employee popup-link add_new_employee">+ ADD NEW</span>
					 </div>
					 <ul class="user-table">
						<?php include_once 'partials/admin-list.php'; ?>
					 </ul>
			 </div>

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

 <!-- POPUPS -->
 <section id="add_new_employee" class="popup">
		 <div class="popup-content">
				 <span class="popup-header">Add New Admin</span>
				 <div class="popup-body">
				 <div id="message" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
				 <form id="add_new_employee_form1" method="post" action="#">
						<input type="text" id="new_employee_empid" name="new_employee_empid" placeholder="Employee ID" autofocus required="true" maxlength="9" pattern="[0-9]+" title="Please fill numbers" >
						 <input type="text" id="new_employee_name" name="admin_name" placeholder="Name" required   maxlength="50">
						 <input type="email" id="new_employee_id" name="admin_email" placeholder="Email ID" required="true" maxlength="50" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"  title="abc@xyz.com" >
						 <button type="submit">ADD</button>
				 </form>
				 </div>
		 </div>
 </section>

 <!-- DELETE POPUP -->
 <section id="delete-icon" class="popup">
		 <div class="popup-content">
				 <span class="popup-header"></span>
				 <div class="popup-body">
				 <form id="edit_folder_form" action="deleteListItem/A" method="post">
					 	<input type="hidden" name="uid" value="" id="record_index">
						<button type="submit" id="confirm">YES</button>
						<button type="button" id="cancel">NO</button>
				 </form>
				 </div>
		 </div>
 </section>

 <?php
include_once 'footer.php';
   ?>
