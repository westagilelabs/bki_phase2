$(document).ready(function() {

//	MAKE HEADER FIXED ON TOP AFTER SCROLL
	var stickyOffset = 100;

	$(window).scroll(function(){
		var sticky = $('.nav-wrap');

		if ($(window).scrollTop() > 100)
		{
			$(".nav-placeholder").height(sticky.outerHeight());
			sticky.addClass('sticky');
			sticky.removeClass('unstick');
		}
		else {
			sticky.removeClass('sticky');
			sticky.addClass('unstick');
			$(".nav-placeholder").height(0);
		}
		$('.site-user-options').hide();
		$('.upload-file-options').hide();
	});

//	MAKE TOP HEADER BAR ICON TOGGLE ARROWS IN MOBILE
	$(document).on('click', '.top-bar .close',function() {
		$('.top-bar').toggleClass('open');
	});
//	OPEN USER DROPDOWN ON CLICK
	$(document).on('click', '.site-user',function(e) {
		$('.site-user-options').toggle();
	});


//	OPEN USER DROPDOWN ON CLICK
	$(document).on('click', '.upload_new_folder',function(e) {
		$('.upload-file-options').toggle();
	});

//	OPEN POPUP
	$(document).on('click', '.popup-link', function(e) {
		var class_name = $(this).attr('class');
		$('.popup').each(function(){
			if(class_name.indexOf($(this).attr('id')) != -1){
				$(this).find('input').val('');
				$(this).addClass('display-popup');
				$('html').addClass('hide-body');
			}
		});
	});


//	EDIT EMPLOYEE
	$(document).on('click', '.edit-employee',function() {
		var edit_row_item = $(this).parent('.superadmin-user-delete').parent('li').index();
		$('#edit_employee_name').val($(this).parent('.superadmin-user-delete').siblings('.superadmin-user-wrap').find('.superadmin-user-name').text());
		$('#edit_employee_id').val($(this).parent('.superadmin-user-delete').siblings('.superadmin-user-wrap').find('.superadmin-user-email').text());
		
		$('#edit_employee_uid').val(parseInt($(this).parent('.superadmin-user-delete').parent('li').attr('data-uid')));
		
		$('#edit_employee_empid1').val(parseInt($(this).parent('.superadmin-user-delete').parent('li').attr('id')));
		
		
		$('#edit_employee_empid').val(parseInt($(this).parent('.superadmin-user-delete').parent('li').attr('id')));
		$(document).on('click', '#edit_employee_btn', function(){
			//$('.popup').hide();
			//$('.popup').removeClass('display-popup');
			//$('html').removeClass('hide-body');
			var edit_employee_name = $(this).siblings('#edit_employee_name').val();
			var edit_employee_id = $(this).siblings('#edit_employee_id').val();
			
			$('#edit_employee_empid').val($(this).siblings('#edit_employee_empid').val());
			//$('#edit_employee_id1').val($(this).siblings('#edit_employee_empid').val());
			var edit_employee_empid = $(this).siblings('#edit_employee_empid').val();
			$('.user-table li').eq(edit_row_item).find('.superadmin-user-name').text(edit_employee_name);
			$('.user-table li').eq(edit_row_item).find('.superadmin-user-email').text(edit_employee_id);
			
			edit_row_item = undefined;
		});

	});

//	ADD NEW EMPLOYEE
	$(document).on('click', '#add_new_employee_btn', function() {
		var new_employee_name = $(this).siblings('#new_employee_name').val();
		var new_employee_id = $(this).siblings('#new_employee_id').val();
		var cloned_div=null;
		$('.popup').hide();
		$('.popup').removeClass('display-popup');
		$('html').removeClass('hide-body');
		if(!$('.add_new_employee').parent('.user-table-header').next('.user-table').children('li').find('.edit-employee').length){
			cloned_div = '<li><span class="superadmin-user-wrap"><span class="superadmin-user-name"></span><span class="superadmin-user-email"></span></span><span class="superadmin-user-delete"><img alt="Delete User" src="../images/deleteIcon.png" class="delete-icon popup-link delete_user new_user_delete"></span></li>';
		}
		else{
			cloned_div = '<li><span class="superadmin-user-wrap"><span class="superadmin-user-name"></span><span class="superadmin-user-email"></span></span><span class="superadmin-user-delete"><img alt="Edit User" src="../images/editIcon.png" class="edit-employee popup-link edit_employees new_user_edit"><img alt="Delete User" src="../images/deleteIcon.png" class="delete-icon popup-link delete_user new_user_delete"></span></li>';
		}
		$('.user-table').prepend(cloned_div);

		var cloned_first_child = $('.user-table li:first-child');
		cloned_first_child.find('.superadmin-user-name').text(new_employee_name);
		cloned_first_child.find('.superadmin-user-email').text(new_employee_id);

	});

//	ADD NEW FOLDER
	$(document).on('click', '#add_new_folder_btn', function() {
		var new_folder_name = $(this).siblings('#new_folder_name').val();
		var cloned_folder_div = '<li><span class="superadmin-user-wrap"><span class="superadmin-user-name"></span></span><span class="superadmin-user-delete"><img alt="Edit Folder" src="../images/editIcon.png" class="edit-employee edit-folder popup-link edit_folder new_folder_edit"><img alt="Delete Folder" src="../images/deleteIcon.png" class="delete-icon popup-link delete_folder new_folder_delete"></span></li>',
		cloned_new_folder_first_child = null;
		$('.popup').hide();
		$('.popup').removeClass('display-popup');
		$('html').removeClass('hide-body');
		if($('.manage-user-table').find('li').length)
		{
			if($('.manage-employee-folder').is(":visible")){
				$('.manage-employee-folder').prepend(cloned_folder_div);
				cloned_new_folder_first_child= $('.manage-employee-folder li').eq(0);
				cloned_new_folder_first_child.find('.superadmin-user-name').text(new_folder_name);
			}
			else{
				$('.all-folders-list').prepend(cloned_folder_div);
				cloned_new_folder_first_child= $('.all-folders-list li').eq(0);
				cloned_new_folder_first_child.find('.superadmin-user-name').text(new_folder_name);
			}
		}
		else{
			var cloned_parent_folder_div = '<li><span class="all-folders-parent"><span class="superadmin-user-wrap"><span class="superadmin-user-name"></span><span class="superadmin-user-email"><span class="folder-number">212</span> Folders, <span class="file-number">22</span> Files</span></span><span class="superadmin-user-delete"><img alt="Edit Folder" src="../images/editIcon.png" class="edit-employee edit-folder popup-link edit_folder new_folder_edit"><img alt="Delete Folder" src="../images/deleteIcon.png" class="delete-icon popup-link delete_folder new_folder_delete"></span></span><ul class="all-folders-list"></ul></li>';
			$('.manage-user-table').prepend(cloned_parent_folder_div);
			var cloned_new_parent_folder_first_child= $('.manage-user-table li').eq(0);
			cloned_new_parent_folder_first_child.find('.superadmin-user-name').text(new_folder_name);
		}
	});


//	DELETE FOLDER LIST ITEM
	$(document).on('click', '.delete-icon', function(){
		var class_name = $(this).attr('class');
		var delete_row_item = $(this).parent('.superadmin-user-delete').parent('li').index();
		var delete_row_item_id = parseInt($(this).parent('.superadmin-user-delete').parent('li').attr("id"));
		var delete_row_ul_id = parseInt($(this).parent('.superadmin-user-delete').parent('li').parent('ul').attr("data-empid"));
		var delete_row_file_name = $(this).attr("data-file-name");
		
		if(class_name.indexOf('delete_folder') != -1){
			$('#delete-icon').find('.popup-header').text('Delete Folder?');
			$('#record_ul').val(delete_row_item_id);
			$('#action').val("folder");

			if($(this).parent('.superadmin-user-delete').parents('.all-folders-list').length){
				$('#confirm').on('click', function(){
					$('.all-folders-list li').eq(delete_row_item).remove();
					delete_row_item = undefined;
					$('.popup').hide();
					$('html').removeClass('hide-body');
					$('.popup').removeClass('display-popup');
				});
			}
			if($(this).parent('.superadmin-user-delete').parents('.user-table').length){
				var parent_table =$(this).parent('.superadmin-user-delete').parents('.user-table');
				$('#confirm').on('click', function(){
					parent_table.find('li').eq(delete_row_item).remove();
					delete_row_item = undefined;
					$('.popup').hide();
					$('html').removeClass('hide-body');
					$('.popup').removeClass('display-popup');
				});
			}
		}


		else if(class_name.indexOf('delete_parent_folder') != -1){

			$('#delete-icon').find('.popup-header').text('Delete All The Folders?');
			
			$('#record_index').val(delete_row_item_id);
			$('#record_ul').val(delete_row_ul_id);
			$('#action').val("all");
			$('#record_file_name').val(delete_row_file_name);

			
			
			$('#confirm').on('click', function(){
				$('.popup').hide();
				$('html').removeClass('hide-body');
				$('.popup').removeClass('display-popup');
				$('.all-folders-parent').parent('li').remove();
				delete_row_item = undefined;
			});
		}
	
		else if(class_name.indexOf('delete_user') != -1){
			$('#delete-icon').find('.popup-header').text('Delete The Employee?');
			$('#record_index').val(delete_row_item_id);
			$("#record_ul").parent('form').attr('action','deleteListItem/E');
			$('#confirm').on('click', function(){
				$('.popup').hide();
				$('html').removeClass('hide-body');
				$('.popup').removeClass('display-popup');
				$('.user-table li').eq(delete_row_item).remove();
				delete_row_item = undefined;

			});
		}
		
		else if(class_name.indexOf('delete_file') != -1){
			$('#delete-icon').find('.popup-header').text('Delete The File?');
			$('#record_index').val(delete_row_item_id);
			$('#record_ul').val(delete_row_ul_id);
			$('#action').val("file");
			$('#record_file_name').val(delete_row_file_name);
			$('#confirm').on('click', function(){
				$('.popup').hide();
				$('html').removeClass('hide-body');
				$('.popup').removeClass('display-popup');
				$('.user-table li').eq(delete_row_item).remove();
				delete_row_item = undefined;

			});
		}
		$('#cancel').on('click', function(){
			$('.popup').hide();
			$('html').removeClass('hide-body');
			$('.popup').removeClass('display-popup');
			delete_row_item = undefined;

		});

	});


//	CLOSE USER DROPDOWN WHEN CLICKED OUTSIDE
	$(document).on('click', function(event){
		if( !event ) event = window.event;
		if (!$(event.target).closest('.site-user-options').length && !$(event.target).closest('.site-user').length) {
			$('.site-user-options').hide();

		}
		if (!$(event.target).closest('.upload-file-options').length && !$(event.target).closest('.upload_new_folder').length) {
			$('.upload-file-options').hide();

		}
		if (!$(event.target).closest('.popup-content').length && !$(event.target).closest('.popup-link').length) {
			if($(".display-popup").css('display') == 'flex')
			{			
				if(window.location.href.indexOf('/user/manage') > 0 || window.location.href.indexOf('/user/dashboard') > 0)
				{
					window.location.reload();
				}
			}
			$('.popup').hide();
			$('html').removeClass('hide-body');
			$('.popup').removeClass('display-popup');
		}
	});

	$(document).on('touchstart', function(){
		if (!$(event.target).closest('.site-user-options').length && !$(event.target).closest('.site-user').length) {
			$('.site-user-options').hide();

		}
		if (!$(event.target).closest('.upload-file-options').length && !$(event.target).closest('.upload_new_folder').length) {
			$('.upload-file-options').hide();

		}
		if (!$(event.target).closest('.popup-content').length && !$(event.target).closest('.popup-link').length) {
			$('.popup').hide();
			$('html').removeClass('hide-body');
			$('.popup').removeClass('display-popup');
		}

	});

//	CLOSING OF CHANGE PASSWORD POPUP
	/*$('#change_password_btn').on('click', function(e)  {
		$('.popup').hide();
		$('html').removeClass('hide-body');
		$('.popup').removeClass('display-popup');
	});*/

//	OPENING AND CLOSING OF TABS IN MANAGE PAGE
	$('.tabs .tab-links a').on('click', function(e)  {
		var currentAttrValue = $(this).attr('href');
		$('.tabs ' + currentAttrValue).addClass('active').siblings().removeClass('active');
		$(this).parent('li').addClass('active').siblings().removeClass('active');

		e.preventDefault();
	});

//	OPENING SUBPAGE OF MANAGE FILES/FOLDERS
	$('.all-folders-list li').find('.superadmin-user-wrap').click(function() {
		var detail_row = $(this).index();
		var parentli = $(this).parent().attr("id");
		var empid = $(this).parent("li").attr("id");
		var row_user_name = $(this).find('.superadmin-user-name').text();
		$('.manage-user-table').hide();
		$('.'+parentli).show();
		$('#empid').val(empid);
		$('.upload-file-options li:eq(0)').hide();
		//$('.manage-employee-folder').show();
		$('#manage_files').find('.admin-acess-row').html('<span class="back-to-all">All</span> > '+row_user_name);

		$('.back-to-all').click(function() {
			$('.manage-user-table').show();
			$('.manage-employee-folder').hide();
			$('#manage_files').find('.admin-acess-row').html('Files & Folders');
			$('.upload-file-options li:eq(0)').show();
		});
	});

	// CHANGING PROFILE PIC
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('.profile-pic-holder img').attr('src', e.target.result);
				$('#profile-pic-hidden').val(e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#profile_pic_input").change(function(){
		
		if(Validate("")){
		readURL(this);
		}
		//	$('#profile-details-form').submit();
	});

	//Profile Pic Uploaded for IE
	$('.profile-pic-holder').click(function() {
		$('#' + $(this).attr('for')).click();
	});

});


//UPLOADING FILES

function showMyFiles(fileInput){
	var new_uploaded_file_item = null,
	uploaded_file = fileInput.files;

	for (var i = 0; i < uploaded_file.length; i++) {
		var new_uploaded_file = uploaded_file[i];
		var new_uploaded_file_name = new_uploaded_file.name;
		var uploaded_file_size = new_uploaded_file.size;
		var new_uploaded_file_size = (uploaded_file_size / (1024)).toFixed(2);
		var new_uploaded_file = '<li><span class="superadmin-user-wrap"><span class="superadmin-user-name">'+new_uploaded_file_name+'</span><span class="superadmin-user-email"><span class="folder-size">'+new_uploaded_file_size+' kb</span></span></span><span class="superadmin-user-delete"><img alt="Delete Folder" src="../images/deleteIcon.png" class="delete-icon popup-link delete_folder new_file_delete"></span></li>';


		if($('.manage-employee-folder').is(":visible")){
			$('.manage-employee-folder').prepend(new_uploaded_file);
		}
		else{
			$('.all-folders-list').prepend(new_uploaded_file);
		}

	}

}

$(document).ready(function(){
	if(window.location.href.indexOf('manage#manage_files') !=-1)
	{
		$($('a[href="#manage_files"]')[0]).trigger('click');
		
	}
	
});

$('a[href="#manage_emp"]').on('click',function(){
	$('#files-message').hide();
	
});
$('a[href="#manage_files"]').on('click',function(){
	$('#user-message').hide();
});

var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];    
function Validate(oForm) {
    var arrInputs = document.getElementsByClassName("input");
    for (var i = 0; i < arrInputs.length; i++) {
        var oInput = arrInputs[i];
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }
                
                if (!blnValid) {
                    alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
					//document.getElementById('profilepic').src = '';
                    return false;
                }
            }
        }
    }
  
    return true;
}

 $("#forgot_password_form").on("submit", function(e){
	 $('#message').show();
	 $('#message').html('<h4>Please wait....<h4>');
        $.ajax({
          url: 'forgotPassword',
          type: 'post',
          data: $('#forgot_password_form').serialize(),
          success: function(data) {              
			  $('#message').html(data);
		  }
         });
         e.preventDefault();
    });

 $("#add_change_employee_form").on("submit", function(e){
	 $('#message').show();
	 $('#message').html('<h4>Please wait....<h4>');
        $.ajax({
          url: 'updatePassword',
          type: 'post',
          data: $('#add_change_employee_form').serialize(),
          success: function(data) {              
			  $('#message').html(data);
		  }
         });
         e.preventDefault();
    });	
	
$("#add_new_employee_form").on("submit", function(e){
	 $('#message').show();
	 $('#message').html('<h4>Please wait....<h4>');
        $.ajax({
          url: 'saveListItem/E',
          type: 'post',
          data: $('#add_new_employee_form').serialize(),
          success: function(data) {              
			  $('#message').html(data);
			  if($(data).attr('class') == 'success-message')
			  {
				  setTimeout(function(){
					   $("body").trigger('click');
				  },2000)
				  
			  }
		  }
         });
         e.preventDefault();
    });	
	
	$("#edit_employees_form").on("submit", function(e){
	 $('#message1').show();
	 $('#message1').html('<h4>Please wait....<h4>');
        $.ajax({
          url: 'editListItem/E',
          type: 'post',
          data: $('#edit_employees_form').serialize(),
          success: function(data) {    
						
			  $('#message1').html(data);
			  if($(data).attr('class') == 'success-message')
			  {
				  setTimeout(function(){
					   $("body").trigger('click');
				  },2000)
				  
			  }
				 
		  }
         });
         e.preventDefault();
    });	
	
	$("#add_new_employee_form1").on("submit", function(e){
	 $('#message').show();
	 $('#message').html('<h4>Please wait....<h4>');
        $.ajax({
          url: 'saveListItem/A',
          type: 'post',
          data: $('#add_new_employee_form1').serialize(),
          success: function(data) {              
			  $('#message').html(data);
			  if($(data).attr('class') == 'success-message')
			  {
				  setTimeout(function(){
					   $("body").trigger('click');
				  },2000)
				  
			  }
		  }
         });
         e.preventDefault();
    });	
	
	
	

	var password = document.getElementById("new_password");
    var confirm_password = document.getElementById("confirm_new_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
