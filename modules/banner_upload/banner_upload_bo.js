$(document).ready(function()
{	
	$('.blmod_run_code').click(function(){
	
		var div_id = $(this).attr('id');		
		var id = div_id.split('-');			
	
		if(id[1] != '')
		{
			$.ajax({
				type: 'GET',
				url: '../modules/banner_upload/run_code.php?b_id='+id[1],
				dataType: 'html',
				cache: false,
				success: function(msg){
					run_code_windows(msg, id[1]);
				}
			});
		}
	});
	
	$('#run_code_mask, #run_code_exit').click(function(){
		close_mask();
	});
	
	function run_code_windows(msg, block_id)
	{
		$('#run_code').append(msg);
		add_mask(block_id);
	}	
	
	function add_mask(block_id)
	{
		var width = $(document).width();
		var height = $(document).height();
		
		$('#run_code_mask').css({'display': 'block', 'width':width, 'height':height});

		var win_w = $(window).width();
		
		var left = win_w / 2 - 250;
		
		var p = $('#blmod_run_code-'+block_id);
		var position = p.position();
		var top = position.top - 150;

		$('#run_code_box').css({'display': 'block', 'left':left, 'top':top});
	}
	
	function close_mask()
	{
		$('#run_code_mask').hide();		
		$('#run_code_box').hide();
		$('#run_code').empty();
	}
});	