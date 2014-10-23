jQuery(document).ready(function($) {

	// turn delete column into edit/delete actions
	$('.s301r-delete-head').addClass('s301r-actions-head').removeClass('s301r-delete-head').text('Actions');
	$('.s301r-delete').html('<!--<a href="#" class="s301r-edit-link">Edit</a>&nbsp;--><a href="#" class="s301r-delete-link">Delete</a>');
	
	// ajax delete
	$('.s301r-delete-link').click(function(){
		var confirm_delete = confirm('Are you sure you want to delete this redirect?');
		if (confirm_delete) {
			var row = $(this).parent().parent();

			// do ajax
			var data = {
				'action': 's301r_delete_redirect',
				'row_id': row.attr('id'),
				'delete_nonce': s301r_ajax.delete_nonce
			};

			$.post(s301r_ajax.ajax_url, data, function(response) {
				if (response === 'success') {
					row.remove();
				}
				else {
					alert('Something seems to have gone wrong. The redirect was not deleted.')
				}
			});
		}

		return false;
	});

	// edit link
	$('.s301r-edit-link').click(function(){
		// swap text for inputs
		var row = $(this).parent().parent();
		var index = row.attr('id');
		var request_cell = row.find('td.s301r_request').first();
		var destination_cell = row.children('td.s301r_destination').first();

		request_cell.html( '<input type="text" name="301_redirects["edit_request"]["'+index+'"]" value="'+request_cell.text()+'" />' )
		destination_cell.html( '<input type="text" name="301_redirects["edit_destination"]["'+index+'"]" value="'+destination_cell.text()+'" />' )
		return false;
	});

	// documentation
	$('.simple_301_redirects .documentation').hide().before('<p><a class="reveal-documentation" href="#">Documentation</a></p>')
	$('.reveal-documentation').click(function(){
		$(this).parent().siblings('.documentation').slideToggle();
		return false;
	});
});

