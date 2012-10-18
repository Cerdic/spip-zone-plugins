/* BEGIN LICENSE BLOCK ----------------------------------
*
* This file is part of Noviny, a Dotclear 2 theme.
*
* Copyright (c) 2003-2008 Olivier Meunier and contributors
* Licensed under the GPL version 2.0 license.
* See LICENSE file or
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* -- END LICENSE BLOCK --------------------------------- */

// Comment form display
$(function() {
	if ($('body.dc-post, body.dc-page').length == 0) { return; }
	if ($('#pr').length > 0) { return; }
	
	var link = $('<a href="#">' + $('#comment-form h3:first').text() + '</a>').click(function() {
		$('#comment-form fieldset:first').show(200);
		$('#c_name').focus();
		$(this).parent().removeClass('add-comment').html($(this).text());
		return false;
	});
	$('#comment-form h3:first').empty().append(link).addClass('add-comment');
	$('#comment-form fieldset:first').hide();
});


// Comment form display
$(function() {
		
	var link = $('#sidentifier:first').click(function() {
		$('#sidentifierform').show(200);
		$('#sidentifierform').show(200);
		// $(this).parent().removeClass('add-comment').html($(this).text());
		return false;
	});
	//$('#sidentifier:first').empty().append(link).addClass('add-comment');
	$('#sidentifierform').hide();
	$('#sidentifierform').hide();
});

// affichage article dans rubrique (image, texte)
$(function() {
	$("span.date").each(
		function(){
			text=$(this).html();
			annee=text.substr(0,4);
			mois=text.substr(4,2);
			jour=text.substr(6,2);
			text=jour+"-"+mois+"-"+annee;
			$(this).html(text);
			return true;
		}
	)
}
)

 

$(function(){ 
	$("a").click(
		function(){text=$(this).attr("class");
			
			$('div.evt_img').each(
				function(){
					if ($(this).attr("id")==text){
						$(this).fadeIn("slow");
					}
					else{
						$(this).css({ display: "none" });
					}
				}			
			)
		}
		);
	}); 





$(function(){ 
	$("a.choix1").click(
		function(){
			$('#choix1').fadeIn("slow");
			
			$('#choix2').fadeOut("fast");
			
			$('#choix3').fadeOut("fast");
			
			$('#choix4').fadeOut("fast");
			
			$('#choix5').fadeOut("fast");
			
			}
			);
	}); 






