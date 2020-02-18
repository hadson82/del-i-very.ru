$(document).ready(function()
{	
	$(function()
	{
		$("#content_images_dd ul").sortable({ handle: ".blmmod_handle", opacity: 0.6, cursor: 'move', update: function()
		{
			fix_sort_images();
			
			var block = $("#block_name").attr("class");			
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings&block='+block; 
			
			$.post("../modules/banner_upload/order_update.php", order, function(theResponse){
				$("#dd_message").html(theResponse);
			}); 															 
		}								  
		});
	});
	
	fix_sort_images();
	
	function fix_sort_images()
	{		
		$('.order_top').css({'display': 'block'});	
		$('.order_down').css({'display': 'block'});			
			
		$('#content_images_dd ul li:first-child .order_top').css({'display': 'none'});
		$('#content_images_dd ul li:last-child .order_down').css({'display': 'none'});
	}
});	