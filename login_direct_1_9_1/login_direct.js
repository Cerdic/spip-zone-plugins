
function login_direct_submit() {
	try {
		jQuery.noConflict();
		var jform = $('#login');
		if ($('#var_login', jform).val() && $('#var_password', jform).val()) {
			//kill la pr�c�dente connexion (fait comme si on avait cliqu� sur le lien "Se connecter sous un autre identifiant)
			if ($('#url_other_login_hidden',jform).val()) {
				$.ajax({type: "GET",
					async: false,
					url: $('#url_other_login_hidden',jform).val(),
					error: function(xmlhttprequest, type, e) {
						alert('Erreur dans l\'envoi des informations au serveur. Merci de r�essayer ult�rieurement. '+e.message);
					}
				});
			}
			
			//post le login en Ajax pour pouvoir r�cup�rer les alea_actuel et alea_futur
			$.ajax({
				type: "POST",
				url: $('#url_action_hidden', jform).val(), 
				data: {var_login: $('#var_login', jform).val(), url: $('input[@name=url]', jform).val()}, 
				success: function(data) {
					//recherche du formulaire dans le HTML r�cup�r� par Ajax
					var jdata = $(document.createElement('div')).html(data);
					var jformdata = $('#login', jdata);
					//si on a une erreur sur le login
					if ($('p.reponse_formulaire', jformdata).is('p')) {
						//supprime le message actuel �ventuel
						$('p', jform).remove('.reponse_formulaire');
						//ins�re le nouveau mesage juste apr�s la legend
						$('p.reponse_formulaire', jformdata).insertAfter($('legend', jform).get(0));
						return false;
					} else { //si le login est OK
						//affectation des valeurs r�cup�r�es au formulaire
						$('input[@name=session_password_md5]', jform).val(calcMD5($("input[@name=session_password_md5]", jformdata).val() + $('#var_password', jform).val()));
						$('input[@name=next_session_password_md5]', jform).val(calcMD5($("input[@name=next_session_password_md5]", jformdata).val() + $('#var_password', jform).val()));
						$('input[@name=essai_login]', jform).val($("input[@name=essai_login]", jformdata).val());
						$('input[@name=session_login_hidden]', jform).val($('#var_login', jform).val());
						$('input[@name=session_password]', jform).val('');
						jform.attr('action', jformdata.attr('action'));
						
						//pour poster les infos avec le mot de passe crypt� selon les normes Spip
						jform.get(0).submit();
						return true;
					}
				}
				, error: function(xmlhttprequest, type, e) {
					alert('Erreur dans l\'envoi des informations au serveur. Merci de r�essayer ult�rieurement: '+e.message);
				}
			}); //fin de $.ajax
		} else { //rien � poster (les deux champs ne sont pas rempli
			return false;
		}
		return false;
	} catch(e) { //erreur dans le javascript ! ne devrait pas arriver
		alert('Erreur: '+e.message);
		return false;
	}
} //fin de login_direct_submit