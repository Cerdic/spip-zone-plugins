
/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

// $LastChangedBy$
// $LastChangedDate$



(function($) {
	
	// attendre document chargé 
	$(document).ready(function() {
		
		var $previous, $current;
	
		$("label.face").click(function () { 
			
			if($('#'+$(this).attr('for')).attr('checked')) {
				
			}
			else {
				
				var $current = $('#'+$(this).attr('for')).val();
				
				if($previous != $current) {
			
					// preparer les data a' transmettre en ajax
					var data = 'fontname='+$current;
					
					// appel
					$.ajax({ type: "POST", 
								url: "./?action=foni_font_preview" , 
								data: data , 
								success: function(msg) {
									$("#foni_font_preview")
										.hide()
										.css({'overflow':'scroll','height':'300px'})
										.html(msg)
										.slideDown()
										;
							}
					});
					$previous = $current;
				}
			}
		});

		$('#foni_form_font legend.help span').click(function () {
			var $p = $(this).parent().parent().children('p.help');
			if($p.css('display') == 'none') {
				$p.slideDown();
			}
			else {
				$p.slideUp();
			}
		});
		
		$("#foni_form_font").submit(function() {
			
			var $ns = 0, $msg = '';
			
			$('#foni_fontes_sel td.face').children('input:checked').each(function() {
				
				if($(this).parent().parent().children('td.family').children('input').attr('value') == '') {
					$msg += '- ' + $(this).val() + "\n";
				}
				if($msg != '') {
					$msg = "Famille manquante pour :\n" + $msg;
					alert($msg);
					return(false);
				}
				$ns += 1;
			});
			// si pas de sélection, pas de validation !
			if(!$ns) {
				return(false);
			}
			return(true);
		});
		
	});
	
})(jQuery);
