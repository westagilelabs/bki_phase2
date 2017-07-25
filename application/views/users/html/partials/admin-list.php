<?php
if(count($data) > 0)
{
foreach($data as $d){?>
  <li id="<?php echo $d['uid'];?>">
      <span class="superadmin-user-wrap">
		  <a href="emp_profile/<?php echo $d['uid'];?>"><span class="superadmin-user-name"><?php echo $d['name'];?></span></a>
          <span class="superadmin-user-email"><?php echo $d["email"];?></span>
      </span>
      <span class="superadmin-user-delete">
      <img alt="Delete User" src="<?php echo base_url();?>includes/images/deleteIcon.png" class="delete-icon popup-link delete_user">
      </span>
  </li>
  <?php }
}
else{ ?>

  <li>
      <span class="superadmin-user-wrap">
          Oops list is empty !!
      </span>
  </li>

<?php	 }?>
