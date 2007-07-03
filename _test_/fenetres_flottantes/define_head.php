<?php

function FenFlo_insertion_in_head($flux)
{
	$ajout_script="<link rel=\"stylesheet\" href=\""._DIR_PLUGINS."fenetres_flottantes/floating-windows.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\""._DIR_PLUGINS."fenetres_flottantes/interface.js\"></script>
	<script type=\"text/javascript\">
$(document).ready(
	
	function()
	{
		
		$('#windowOpen').bind(
			'click',
			function() {
				if($('#window').css('display') == 'none') {
					$(this).TransferTo(
						{
							to:'window',
							className:'transferer2', 
							duration: 0,
							complete: function()
							{
								$('#window').show();
							}
						}
					);
				}
				this.blur();
				return false;
			}
		);
		$('#windowClose').bind(
			'click',
			function()
			{
				$('#window').TransferTo(
					{
						to:'windowOpen',
						className:'transferer2', 
						duration: 400
					}
				).hide();
			}
		);
		$('#windowMin').bind(
			'click',
			function()
			{
				$('#windowContent').SlideToggleUp(300);
				$('#windowBottom, #windowBottomContent').animate({height: 10}, 300);
				$('#window').animate({height:40},300).get(0).isMinimized = true;
				$(this).hide();
				$('#windowResize').hide();
				$('#windowMax').show();
			}
		);
		$('#windowMax').bind(
			'click',
			function()
			{
				var windowSize = $.iUtil.getSize(document.getElementById('windowContent'));
				$('#windowContent').SlideToggleUp(300);
				$('#windowBottom, #windowBottomContent').animate({height: windowSize.hb + 13}, 300);
				$('#window').animate({height:windowSize.hb+43}, 300).get(0).isMinimized = false;
				$(this).hide();
				$('#windowMin, #windowResize').show();
			}
		);
		$('#window').Resizable(
			{
				minWidth: 200,
				minHeight: 60,
				maxWidth: 1800,
				maxHeight: 1800,
				dragHandle: '#windowTop',
				handlers: {
					se: '#windowResize'
				},
				onResize : function(size, position) {
					$('#windowBottom, #windowBottomContent').css('height', size.height-33 + 'px');
					var windowContentEl = $('#windowContent').css('width', size.width - 25 + 'px');
					if (!document.getElementById('window').isMinimized) {
						windowContentEl.css('height', size.height - 48 + 'px');
					}
				}
			}
		);
	}
);
</script>
<script language=\"JavaScript\" type=\"text/javascript\">var client_id = 1;</script>

<script language=\"javascript\" type=\"text/javascript\">
function addLoadEvent(func) {
   var oldonload = window.onload;
   if (typeof window.onload != \"function\") {
      window.onload = func;
   } else {
      window.onload = function() {
         if (oldonload) {
            oldonload();
         }
         func();
      };
   }
}
addLoadEvent(function(){
		$('#windowOpen').click();

		
});


			

</script>";
	
	return $flux.$ajout_script;
}

?>
