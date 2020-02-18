$(document).ready(function(){	
	$('.blmod_banner_popup_close').click(function() {
		$('#b'+$(this).attr('id')).hide();
	});	
	
	$('.blmod_banner_click_count_swf').mousedown(function(){	
		var div_id = $(this).attr('id');
		
		send_data(div_id); 
	});	
	
	$('.blmod_banner_click_count').mousedown(function(){
		var div_id = $(this).attr('id');

		send_data(div_id); 
	});
	
	function send_data(div_id)
	{
		var file_url = $('#blmod_click_file').text();
		
		var id = div_id.split('-');	
		
		$.post(file_url, {b_id: id[1]}, function(data){
		   $('#blmod_banner_popup-'+id[1]).hide();
		});
	}
});