<?php if($this->uri->segment(3) !="nh")
{ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<?php if($this->session->userdata("role") == "SA"){
					$title = "Batchelor & Kimball, Inc. | Super admin panel ";
					
					if($this->uri->segment(2) == "manage")
						$title = "Batchelor & Kimball, Inc. | Manage employees";
					if($this->uri->segment(2) == "profile")
						$title = "Batchelor & Kimball, Inc. | Profile";
					
					
				}	else if($this->session->userdata("role") == "A"){
					$title = "Batchelor & Kimball, Inc. | Manage employees";
					if($this->uri->segment(2) == "profile")
						$title = "Batchelor & Kimball, Inc. | Profile";
				}else{
					$title = "Batchelor & Kimball, Inc. | Files";
					if($this->uri->segment(2) == "profile")
						$title = "Batchelor & Kimball, Inc. | Profile";
				}?>
		<title><?php echo $title;?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description"
			content="Batchelor & Kimball is a mechanical contractor specializing in the design and installation of commercial HVAC, process, mechanical and plumbing systems.  We partner with our clients to deliver results from engineering and construction to operations and maintenance.
		">
		<meta name="keywords"
			content="Batchelor & Kimball, mechanical contractor, commercial HVAC, plumbing system,">
			<link rel="shortcut icon" href="<?php echo base_url();?>includes/images/bkiFavIcon.jpg" type="image/x-icon" />
		<link rel="stylesheet" href="<?php echo base_url();?>includes/css/main.css" media="all">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css?ver=4.6.1"
			type="text/css" media="none" onload="if(media!='all')media='all'">
	</head>
	<?php }?>
<body>

	<div class="site-wrapper">
		<input type="hidden" id="refresh" value="no">
		<!-- HEADER SECTION -->

		<!-- HEADER TOP BAR SECTION -->
		<section class="top-bar">
			<div class="container">
				<ul class="right">
					<li>
							<span class="fa fa-user"></span>
							<a href="http://batchelorkimball-fc.viewpointforcloud.com/FieldDirect">Customer
							Login</a>
						</li>
					<li>
							<span class="fa fa-"></span>
							<a href="http://bkimechanical.flywheelsites.com/24-hour-service/">24-Hour
							Service</a>
					</li>
					<li>
							<span class="fa fa-envelope-o"></span>
							<a href="mailto:contactus@bkimechanical.com">contactus@bkimechanical.com</a>
					</li>
					<li>
							<span class="fa fa-phone"></span>
							<a href="tel:770.482.2000">770.482.2000</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<span class="close fa fa-chevron-down"></span>

		</section>

		<!-- HEADER LOGO SECTION -->
		<header class="site-header sticky">
		<div class="nav-placeholder"></div>
			<nav class="nav-wrap unstick">
				<div class="container">
					<div class="logo-wrap">
						<a id="sticky_logo" href="http://bkimechanical.flywheelsites.com/">
							<img alt="Site logo" src="<?php echo base_url();?>includes/images/bkiLogo.png">
						</a>
					</div>
					<div class="site-user">
						<span class="site-username-wrap"><span
							class="site-username" style="text-transform: capitalize;"><?php $f = (array)json_decode($_COOKIE['user']); echo $f["name"]; ?></span> <span class="arrow-down"></span>
						</span>

						<ul class="site-user-options">
								<li><a href="<?php echo base_url();?>index.php/user/profile">Profile</a></li>
								<?php if($f["role"] == "SA"){?>
								 <li><a href="<?php echo base_url();?>index.php/user/dashboard">Super Admin Panel</a></li>
								<?php }
										if($f["role"] == "SA" || $f["role"] == "A"){?>
									<li><a href="<?php echo base_url();?>index.php/user/manage">Manage Employees</a></li>
								<?php }?>
								<?php if($f["role"] == "E"){?>
								 	<li><a href="<?php echo base_url();?>index.php/user/files">View Files</a></li>
								<?php }?>
								<li><a href="http://help.bkimechanical.com">Help Desk</a></li>
							<li><a href="<?php echo base_url();?>index.php/user/logout">Logout</a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<!-- HEADER SECTION END -->
