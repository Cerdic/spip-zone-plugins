// lido_header_prive.js

	$(document).ready(function(){

		/* afficher en gras le label des selections radio courantes */
		$('input[@type=radio][@checked]').each(function () {
			var id = $(this).attr('id');
			if(id) {
				$('label[@for=' + id + ']').css('font-weight', 'bold');
			}
		});
		
		/* mettre à jour la liste des rubriques/breves lors d'un clic sur radio article/brève */
		$('input[@name=lido_table_destination]').click( function() { 
			var lido_type, lido_id_rubrique, lido_url, lido_data;
			lido_type = $(this).val();
			$('#boite_chercher_rubrique select option:selected').each(function () {
				lido_id_rubrique = $(this).val();
			});
			lido_url = $('#lido_url_action').val();
			lido_data = 'type=' + lido_type + '&id_rubrique=' + lido_id_rubrique;

			/* demande la liste des rubriques/secteurs (articles/breves) */
			$.ajax({
				type: 'post'
				, url: lido_url
				, data: lido_data
				, success: function(data) {
					/* remplace la boite liste des rubriques */
					$('#boite_chercher_rubrique').empty().append(data);
					return true;
				}
			}); /* end $.ajax */
			if(lido_type == 'articles') {
				$('#bloc_attribuer_auteur').show();
			} else {
				$('#bloc_attribuer_auteur').hide();
			}
		});
		$('input[@name=lido_prevenir_moderateur]').click( function() { 
			if($(this).val() == 'oui') {
				$('#lido_bloc_email_moderateur').show();
			} else {
				$('#lido_bloc_email_moderateur').hide();
			}
		});
		
	});