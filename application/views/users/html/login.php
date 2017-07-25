<!DOCTYPE html>
<html class="login-page-html">
	<head>
		<meta charset="UTF-8">
		<title>LOGIN - Batchelor & Kimball, Inc. - Company</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description"
			content="Batchelor & Kimball is a mechanical contractor specializing in the design and installation of commercial HVAC, process, mechanical and plumbing systems.  We partner with our clients to deliver results from engineering and construction to operations and maintenance.
		">
		<meta name="keywords"
			content="Batchelor & Kimball, mechanical contractor, commercial HVAC, plumbing system,">
			<link rel="shortcut icon" href="<?php echo base_url();?>includes/images/bkiFavIcon.jpg" type="image/x-icon" />
		<link rel="stylesheet" href="<?php echo base_url();?>includes/css/main.css" media="all">
	</head>
<body class="login-page-body">

<!-- LOGIN CONTENT -->
	<section class="login-popup">
	    <div class="login-popup-content">
	        <span class="popup-header"><img alt="B&K" src="<?php echo base_url();?>includes/images/bkiLogo.png"></span>
	        <div class="popup-body">
					<?php if(!empty($msg)){?>
					<h4 class="error-message"><?php echo $msg;?></h4>
					<?php }?>
	        <form id="login_form" action="<?php echo base_url();?>index.php/user/checkLogin" method="post">
	            <input type="email" id="login_name" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" name="login_name" placeholder="Username" required="true" title="abc@xyz.com">
	            <input type="password" id="login_password" name="login_password" placeholder="Password" required="true">
	            <span class="login-extra-link-wrap">
	            <span class="login-extra-link forgot_password popup-link">Forgot Password?</span>
	            </span>
	          	<button type="submit">LOGIN</button>
	        </form>
	        </div>
	    </div>
	</section>

	<!-- POPUPS -->
	<!-- FORGOT PASSWORD -->
	<section id="forgot_password" class="popup">
	    <div class="popup-content">
	         <span class="popup-header"><img alt="B&K" src="<?php echo base_url();?>includes/images/bkiLogo.png"></span>
	        <div class="popup-body">
			 <div id="message" style="display:none;"><?php echo $this->session->flashdata('message');?></div>
	        <span class="popup-body-text">Whoops! Don't worry, this happens to us all the time too. Simply enter your email address below and we'll help you reset your password.</span>
	        <form id="forgot_password_form" action="forgotPassword" method="post">
	            <input type="email" id="forgot_pwd_id" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="Email ID" required title="abc@xyz.com">
	             <a href="<?php echo base_url();?>index.php/user/index" class="login-extra-link">Login</a>
	            <button type="submit">RESET PASSWORD</button>
	        </form>
	        </div>
	    </div>
	</section>
	 <?php include_once 'footer.php'; ?>
