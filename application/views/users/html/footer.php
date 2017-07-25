  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script  src="<?php echo base_url();?>includes/js/main.js"></script>
  <script async src="<?php echo base_url();?>includes/js/custom.js"></script>
  <script src="<?php echo base_url();?>includes/js/html5shiv.js"></script>
  <!-- polyfil for HTML5 validation-->
  <?php $user_agent = $_SERVER['HTTP_USER_AGENT']; 
 if (stripos( $user_agent, 'Chrome') !== false)
{
    //echo "Google Chrome";
}
else if (stripos( $user_agent, 'Safari') !== false)
{ ?>
	<!--<script src="<?php echo base_url();?>includes/js/dist/better-dom.js"></script>
	<script src="<?php echo base_url();?>includes/js/dist/better-i18n-plugin.js"></script>
	<script src="<?php echo base_url();?>includes/js/dist/better-popover-plugin.js"></script>
	<script src="<?php echo base_url();?>includes/js/dist/better-form-validation.js"></script>-->
	
<?php 
//var_dump($user_agent);
}
?>
	

</body>
</html>
