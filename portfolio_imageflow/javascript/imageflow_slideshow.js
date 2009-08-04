/*
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */

jQuery(document).ready(function(){
	
	jQuery.fn.extend({
	  content: function(i) {
	    return (this.attr('content') == i);
	  }
	});

	var active_description = (jQuery('#x-imageflow-active_description').content('oui'));
	var active_alert = (jQuery('#x-imageflow-active_alert').content('oui'));
	var active_desc_effets = (jQuery('#x-imageflow-active_desc_effets').content('oui'));
	
	// parametres transmis via les metas ?
	// précharger les images ?
	if(jQuery('#x-imageflow-preloader').content('oui')) {
		var tmp_img = new Image();
		$("#imageflow #images img").each(function(){
			tmp_img.src = $(this).attr("name");
		});
	}
	// activer le lien URL sur l'image finale ?
	if(jQuery('#x-imageflow-active_link').content('oui')) {
		$('#affichage').hover(function(){
			$(this).addClass('mouse-hover');
		},function(){
			$(this).removeClass('mouse-hover');
		});
		$('#affichage').click(function(){
			var l = $(this).attr('longdesc'); 
			// url ?
			if(l.match(/^[a-z]{3,7}:\/\//)) {
				window.open(l, $(this).attr('title'));
			}
			// champ data ? et afficher la légende contenue dans longdesc ?
			else if(l.match(/^data:/) && active_description) {
				var i = l.indexOf(',');
				if(i > 0) {
					/* urldecoder */
					var s = l.substr(i + 1);
					var m = s.length;
					var o = '';
					i = 0;
					while(i < m) {
						if (s.substr(i, 3).match(/^%[0-9a-fA-F]{2}/)) {
							o += unescape(s.substr(i,3));
							i += 3;
						} else {
							o += s.substr(i, 1);
							i++;
						}
					}
					// afficher le longdesc dans une boite alerte javascript ?
					// Attention au charset. 
					if (active_alert) {
							alert(o);
					}
					else {
						// ou dans une boite 
						$('#affichage')
							.after("<div id='affichage_legend' class='affichage_legend' style='display:block;position:relative'>petito</div>");
						$('#affichage_legend')
							.addClass('affichage_legend')
							.prepend(o).show(10000);
						if(active_desc_effets) {
							$('#affichage_legend').fadeOut(10000);
						}
					}
				}
				/* efface la legende si le curseur sort de lightbox */
				$('#lightbox').mouseout( function() { 
					$('#affichage_legend').remove();
				});
			}
		});
	}
	
});