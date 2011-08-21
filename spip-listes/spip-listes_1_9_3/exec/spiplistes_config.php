<?php
/**
 * Page de configuration du plugin
 * 
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_config () {

	include_spip('inc/distant');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/meta');
	include_spip('inc/config');

	//spiplistes_debug_log ('Appel page de configuration');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $couleur_foncee
		, $spip_lang_right
		;
	
	static $eol = PHP_EOL;
		
	$flag_editable = (($connect_statut == "0minirezo") && ($connect_toutes_rubriques));

	if($flag_editable)
	{

		$adresse_defaut =
			(email_valide($GLOBALS['meta']['email_defaut']))
			? $GLOBALS['meta']['email_defaut']
			: $GLOBALS['meta']['email_webmaster']
			;

		$keys_complement_courrier = array(
			'opt_personnaliser_courrier'
			, 'opt_lien_en_tete_courrier', 'lien_patron'
			, 'opt_ajout_tampon_editeur', 'tampon_patron'
			, 'opt_completer_titre_nom_site'
			, 'opt_ajout_lien_desabo'
			);
		$keys_complement_courrier = array_merge($keys_complement_courrier
												, $_tampon_cles = explode(",", _SPIPLISTES_TAMPON_CLES));
		$tampon_labels = array_flip($_tampon_cles);
		foreach($tampon_labels as $key=>$value) {
			$tampon_labels[$key] = _T('spiplistes:'.$key);
		}
	
		$keys_opt_formabo = array(
			'opt_plier_deplier_formabo' // effet plier/deplier dans le formulaire abonnement
		);
		
		$keys_param_valider = array(
			'email_defaut'
			, 'smtp_server'
			, 'smtp_login'
			, 'smtp_pass'
			, 'smtp_port'
			, 'mailer_smtp'
			, 'smtp_identification'
			, 'smtp_sender'
			, 'spiplistes_lots'
			, 'spiplistes_charset_envoi'
			);
		$keys_str_param_valider = array(
			  'email_reply_to' // adresse mail de retour 
			, 'email_return_path_defaut' // adresse mail de retour pour les erreurs
		);
		$keys_opts_param_valider = array(
			'opt_simuler_envoi' // demande à la méleuse de simuler l'envoi du courrier
			, 'opt_suspendre_trieuse' // suspendre la trieuse. Les listes restent en attente
			, 'opt_suspendre_meleuse' // suspendre les envois de courriers
			, 'opt_smtp_use_ssl' // SMTP avec SSL ?
			);
				
		$keys_console_syslog = array(
			'opt_console_debug'	// console en mode verbose
			, 'opt_console_syslog' // envoyer le journal sur syslog
			, 'opt_log_voir_destinataire' // ecrire adresse mail des destinataires dans les journaux
			);
				
		// initialise les variables postées par le formulaire
		foreach(array_merge(
			array(
				'abonnement_valider', 'abonnement_config'
				, 'opt_format_courrier_defaut'
				, 'param_reinitialise'
				, 'btn_formabo_valider'
				, 'btn_complement_courrier'
				, 'btn_param_valider'
				, 'btn_console_syslog'
				, 'voir_logs'
			)
			, $keys_opt_formabo
			, $keys_complement_courrier
			, $keys_param_valider
			, $keys_str_param_valider
			, $keys_opts_param_valider
			, $keys_console_syslog
			) as $key) {
			$$key = _request($key);
		}
		// historiquement, ajoute le nom du site en
		// fin de titre. Permettre de ne pas le faire.
		$in_post = _request('opt_completer_titre_nom_site');
		$in_meta = spiplistes_pref_lire('opt_completer_titre_nom_site');
		$opt_completer_titre_nom_site =
			// ni dans le POST, ni dans les metas ?
			(!$in_post && !$in_meta)
			// comportement par défaut
			? 'oui'
			// un imput vide n'est jamais renvoyé
			// donc si manquant, c'est un 'non'
			: ($in_post ? $in_post : 'non');

		$doit_ecrire_metas = false;
		$str_log = '';
		if(!isset($GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES])) {
			$GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES] = array();
		}
		
		if($abonnement_valider && $abonnement_config) {
			ecrire_meta('abonnement_config', $abonnement_config);
			spiplistes_ecrire_key_in_serialized_meta(
					'opt_format_courrier_defaut'
					, $opt_format_courrier_defaut
					, _SPIPLISTES_META_PREFERENCES
					);
			$doit_ecrire_metas = true;
			$str_log .= "abonnement_config = $abonnement_config, "
				. "opt_format_courrier_defaut = $opt_format_courrier_defaut, ";
		}
	
		if($btn_formabo_valider) {
			foreach($keys_opt_formabo as $key) {
				//spiplistes_log("$key ".$$key);
				spiplistes_ecrire_key_in_serialized_meta(
					$key
					, ($$key = (!empty($$key) ? $$key : 'non'))
					, _SPIPLISTES_META_PREFERENCES
					);
				$str_log .= $key.' = '.$$key.', ';
			}
			$doit_ecrire_metas = true;
		}
		
		if($btn_complement_courrier) {
			foreach($keys_complement_courrier as $key) {
				spiplistes_ecrire_key_in_serialized_meta(
					$key
					, ($$key = (!empty($$key) ? $$key : 'non'))
					, _SPIPLISTES_META_PREFERENCES
					);
				$str_log .= $key.' = '.$$key.', ';
			}
			$doit_ecrire_metas = true;
		}
		
		if($btn_param_valider) {
			foreach($keys_param_valider as $key)
			{
				if(($key != 'email_defaut') || email_valide($email_defaut)) {
					$str_log .= $key.' = ' 
						. (($key == 'smtp_pass') ? str_repeat('*', strlen($$key)) : $$key)
						. ', ';
					ecrire_meta($key, trim($$key));
				}
			}
			foreach($keys_str_param_valider as $key) {
				
				if(
				   ($key == 'email_reply_to')
					|| ($key == 'email_return_path_defaut')
				  ) {
					$$key = 
						($ii = email_valide($$key))
						? $ii
						: $adresse_defaut
						;
				}
				
				spiplistes_ecrire_key_in_serialized_meta ($key, $$key, _SPIPLISTES_META_PREFERENCES);
				$str_log .= $key.' = '.$$key.', ';
			}
			foreach($keys_opts_param_valider as $key) {
				$$key = (!empty($$key)) ? $$key : 'non';
				spiplistes_ecrire_key_in_serialized_meta ($key, $$key, _SPIPLISTES_META_PREFERENCES);
				$str_log .= $key.' = '.$$key.', ';
			}
			$doit_ecrire_metas = true;
		}
			
		if($btn_console_syslog) {
			if(!spiplistes_server_rezo_local()) {
			}
			foreach($keys_console_syslog as $key) {
				if($key == $opt_log_voir_destinataire) {
					$opt_log_voir_destinataire = (!empty($$key)) ? $$key : 'non';
				}
				if(
					// si pas sur réseau privé et option syslog validé,
					// retire l'option syslog (cas de copie de base du LAN sur celle du WAN)
					($key == 'opt_console_syslog')
					&& !spiplistes_server_rezo_local()
				) {
					$$key = 'non';
				} else {
					$$key = (!empty($$key)) ? $$key : 'non';
				}
				spiplistes_ecrire_key_in_serialized_meta($key, $$key, _SPIPLISTES_META_PREFERENCES);
				$str_log .= $key.' = '.$$key.', ';
			}
			$doit_ecrire_metas = true;
		}
		
		if($doit_ecrire_metas) {
			// recharge les metas en cache 
			spiplistes_ecrire_metas();
		}
		
		if(!empty($str_log)) {
			$str_log = rtrim($str_log, ', ');
			spiplistes_log("CONFIGURE id_auteur #$connect_id_auteur : ".$str_log);
		}
	
		//
		// Adresse mail pour les retours (Reply-to:)
		// @see: http://www.w3.org/Protocols/rfc822/
		$email_reply_to = spiplistes_pref_lire('email_reply_to');
		
		// Adresse mail pour les retours en erreur (Return-path:)
		// @see: http://www.w3.org/Protocols/rfc822/
		// Plus ou moins obsolete, ou non respecte'
		$email_return_path_defaut = spiplistes_pref_lire('email_return_path_defaut');

		$smtp_identification = (isset($GLOBALS['meta']['smtp_identification']) && ($GLOBALS['meta']['smtp_identification']=='oui')) ? "oui" : "non";
		$mailer_smtp = (isset($GLOBALS['meta']['mailer_smtp']) && ($GLOBALS['meta']['mailer_smtp']=='oui')) ? "oui" : "non";
		$smtp_port = (isset($GLOBALS['meta']['smtp_port']) && (!empty($GLOBALS['meta']['smtp_port']))) ? $GLOBALS['meta']['smtp_port'] : "25";
		$smtp_server = (isset($GLOBALS['meta']['smtp_server']) && (!empty($GLOBALS['meta']['smtp_server']))) ? $GLOBALS['meta']['smtp_server'] : "localhost";
		$smtp_sender = (email_valide($GLOBALS['meta']['smtp_sender'])) ? $GLOBALS['meta']['smtp_sender'] : $GLOBALS['meta']['email_webmaster'];
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('icone_configuration_site');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = 'configuration';
	$sous_rubrique = _SPIPLISTES_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la configuration spiplistes est réservée aux supers-admins 
	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ''
		. '<br style="line-height:3em" />' . $eol
		. spiplistes_gros_titre(_T('titre_page_config_contenu'), '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_meta_info(_SPIPLISTES_PREFIX)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>'spiplistes_config'),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>'spiplistes_config'),'data'=>''))
		. debut_droite($rubrique, true)
		;

	//////////////////////////////////////////////////////
	// Boite Mode d'inscription des visiteurs
	$checked1 = $checked2 = '';
	
	$is_checked = 'checked="checked"';
	
	($GLOBALS['meta']['abonnement_config'] == 'simple') ? $checked1 = $is_checked  : $checked2 = $is_checked;
	
	$opt_format_courrier_defaut = spiplistes_format_abo_default();

	$page_result .= ''
		. '<!-- options inscription -->' . $eol
		. debut_cadre_trait_couleur('redacteurs-24.gif', true, '', _T('spiplistes:inscription'))
		. '<form action="' . generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE) . '" method="post">' . $eol
		. debut_cadre_relief('', true, '', _T('spiplistes:mode_inscription'))
		. '<p class="verdana2">' . $eol
		. '<input type="radio" name="abonnement_config" value="simple"'
		. $checked1
		. ' id="statut_simple" />' . $eol
		. '<label for="statut_simple">'._T('spiplistes:abonnement_simple').'</label>' . $eol
		. '</p>' . $eol
		. '<p class="verdana2">' . $eol
		. '<input type="radio" name="abonnement_config" value="membre"'
		. $checked2
		. ' id="statut_membre" />' . $eol
		. '<label for="statut_membre">'._T('spiplistes:abonnement_code_acces').'</label>' . $eol
		. '</p>' . $eol
		. fin_cadre_relief(true)
		
		. '<!-- format de courrier par defaut -->' . $eol
		. debut_cadre_relief('', true, '', _T('spiplistes:format_courrier_defaut'))
		. '<legend>'._T('spiplistes:format_courrier_defaut_desc').'</legend>'
		. '<p class="verdana2">' . $eol
		. '<input type="radio" name="opt_format_courrier_defaut" value="html"'
		. ($opt_format_courrier_defaut == 'html' ? $is_checked : '')
		. ' id="c_format_html" />' . $eol
		. '<label for="c_format_html">'._T('spiplistes:html_description').'</label>' . $eol
		. '</p>' . $eol
		. '<p class="verdana2">' . $eol
		. '<input type="radio" name="opt_format_courrier_defaut" value="texte"'
		. ($opt_format_courrier_defaut == 'texte' ? $is_checked : '')
		. ' id="c_format_texte" />' . $eol
		. '<label for="c_format_texte">'._T('spiplistes:texte_brut').'</label>' . $eol
		. '</p>' . $eol
		. fin_cadre_relief(true)
		
		// bouton de validation
		. '<div style="text-align:right;"><input type="submit" name="abonnement_valider" class="fondo" value="'._T('bouton_valider').'" /></div>' . $eol
		. '</form>' . $eol
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Formulaire abonnement
	$checked1 = ((spiplistes_pref_lire('opt_plier_deplier_formabo') == 'oui') ? "checked='checked'" : '');
	$page_result .= ''
		. debut_cadre_trait_couleur("redacteurs-24.gif", true, '', _T('spiplistes:formulaire_abonnement'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>" . $eol
		. "<p class='verdana2'>" . $eol
		. "<input type='checkbox' name='opt_plier_deplier_formabo' value='oui' $checked1 id='plier_deplier' />" . $eol
		. "<label for='plier_deplier'>"._T('spiplistes:formulaire_abonnement_effet').'</label>' . $eol
		. "</p>" . $eol
		// bouton de validation
		. "<div style='text-align:right;'><input type='submit' name='btn_formabo_valider' class='fondo' value='"._T('bouton_valider')."' /></div>" . $eol
		. "</form>" . $eol
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite parametrage complément du courrier
	$opt_personnaliser_courrier = (spiplistes_pref_lire('opt_personnaliser_courrier') == 'oui');
	$opt_completer_titre_nom_site = (spiplistes_pref_lire('opt_completer_titre_nom_site') == 'oui');
	$opt_lien_en_tete_courrier = (spiplistes_pref_lire('opt_lien_en_tete_courrier') == 'oui');
	$lien_patron = spiplistes_pref_lire('lien_patron');
	$opt_ajout_tampon_editeur = (spiplistes_pref_lire('opt_ajout_tampon_editeur') == 'oui');
	$opt_ajout_lien_desabo = (spiplistes_pref_lire('opt_ajout_lien_desabo') == 'oui');
	$tampon_patron = spiplistes_pref_lire('tampon_patron');
	foreach($_tampon_cles as $key)
	{
		$$key = spiplistes_pref_lire($key);
	}
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_complement-24.png", true, '', _T('spiplistes:complement_des_courriers'))
		. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE), true)
		//
		// personnaliser le courrier (reprend les données de *_auteur)
		. "<!-- personnaliser le courrier -->" . $eol
		. debut_cadre_relief('', true, '', _T('spiplistes:personnaliser_le_courrier'))
		. "<p class='verdana2'>"._T('spiplistes:personnaliser_le_courrier_desc')."</p>"
		. "<label class='verdana2'>"
		. "<input type='checkbox' name='opt_personnaliser_courrier' value='oui' "
			. (($opt_personnaliser_courrier == 'oui') ? "checked='checked'" : '')
			. ' />' . $eol
		. _T('spiplistes:personnaliser_le_courrier_label').'</label>' . $eol
		. fin_cadre_relief(true)
		//
		// ajout du renvoi de tete, lien courrier
		. debut_cadre_relief('', true, '', _T('spiplistes:complement_lien_en_tete'))
		. "<p class='verdana2'>"._T('spiplistes:complement_lien_en_tete_desc')."</p>"
		. '<input type="checkbox" name="opt_lien_en_tete_courrier" value="oui" id="opt-lien-en-tete-courrier" '
			. (($opt_lien_en_tete_courrier) ? 'checked="checked"' : '')
			. ' />' . $eol
		. "<label class='verdana2' for='opt-lien-en-tete-courrier'>"._T('spiplistes:complement_ajouter_lien_en_tete').'</label>' . $eol
		//
		// lien courrier: boite de selection
		. "<div id='div-lien-en-tete-courrier' style='".(!$opt_lien_en_tete_courrier ? "display:none;" : '')."margin-top:1em;'>"
		. '<label class="verdana2" style="padding-left:2ex;">'
		. _T('spiplistes:patron_du_lien').'.' . $eol
		. spiplistes_boite_selection_patrons($lien_patron, true, _SPIPLISTES_PATRONS_TETE_DIR, "lien_patron", 1)
		. '</label>' . $eol
		. "</div>" . $eol // fin bloc div-lien-en-tete-courrier
		. fin_cadre_relief(true)
		//
		// compléter le titre des listes par le nom du serveur ?
		. debut_cadre_relief('', true, '', _T('spiplistes:completer_titre_courrier_nom_site'))
		. '<label class="verdana2" style="padding-left:2ex;">'
			. '<input type="checkbox" name="opt_completer_titre_nom_site" value="oui" id="opt_completer_titre_nom_site" '
			. (($opt_completer_titre_nom_site) ? 'checked="checked"' : '')
			. ' />' . $eol
			. _T('spiplistes:completer_titre_courrier_nom_site_desc') . $eol
		. '</label>' . $eol
		. fin_cadre_relief(true)
		//
		// opt_ajout_lien_desabo
		. debut_cadre_relief('', true, '', _T('spiplistes:lien_gestion_inscription'))
		. '<p class="verdana2">'._T('spiplistes:lien_gestion_inscription_desc').'</p>'.$eol
		. '<input type="checkbox" name="opt_ajout_lien_desabo" value="oui" id="opt_ajout_lien_desabo" '
			. ($opt_ajout_lien_desabo ? 'checked="checked"' : '')
			. ' />' . $eol
		. '<label class="verdana2" for="opt_ajout_lien_desabo">'
			. _T('spiplistes:lien_gestion_inscription_label').'</label>'.$eol
		. fin_cadre_relief(true)
		//
		// ajout tampon editeur
		. debut_cadre_relief('', true, '', _T('spiplistes:complement_tampon_editeur'))
		. "<p class='verdana2'>"._T('spiplistes:complement_tampon_editeur_desc')."</p>"
		. "<input type='checkbox' name='opt_ajout_tampon_editeur' value='oui' id='opt-ajout-tampon-editeur' "
			. ($opt_ajout_tampon_editeur ? "checked='checked'" : '')
			. " />" . $eol
		. "<label class='verdana2' for='opt-ajout-tampon-editeur'>"._T('spiplistes:complement_tampon_editeur_label').'</label>' . $eol
		//
		// coordonnées editeur: bloc coordonnes_editeur
		. "<div id='div-ajout-tampon-editeur' style='".(!$opt_ajout_tampon_editeur ? "display:none;" : '')."margin-top:1em;'>"
		// tampon sélecteur
		. "<label class='verdana2' style='padding-left:2ex;'>"._T('spiplistes:patron_du_tampon_') . $eol
		. spiplistes_boite_selection_patrons($tampon_patron, true, _SPIPLISTES_PATRONS_TAMPON_DIR, "tampon_patron", 1)
		. '</label>'
		. "<ul class='verdana2' style='list-style:none;padding-left:2ex;'>" . $eol
		;
		foreach($_tampon_cles as $key) {
			$value = ($$key == 'non') ? '' : $$key;
			$page_result .= ''
				. "<li><label for='id_$key'>".$tampon_labels[$key].":</label>"
				. "<input type='text' name='$key' id='id_$key' size='40' class='forml' value=\"{$value}\" /></li>" . $eol
				;
		}
	$page_result .= ''
		. "</ul>" . $eol
		. "</div>" . $eol // fin bloc div-ajout-tampon-editeur
		. fin_cadre_relief(true)
		//
		// bouton de validation
		. "<div style='text-align:right;'><input type='submit' name='btn_complement_courrier' class='fondo' value='"._T('bouton_valider')."' /></div>" . $eol
		. spiplistes_form_fin(true)
		. fin_cadre_trait_couleur(true)
		;


	function spiplistes_cadre_input_text($titre, $name, $value, $size=30, $class='forml')
	{
		static $eol = PHP_EOL;
		
		$str = debut_cadre_relief('', true, '', $titre)
		. '<input type="text" name="'.$name.'" value="'.$value.'" size="'.$size.'" class="'.$class.'" />' . $eol
		. fin_cadre_relief(true);
		
		return($str);
	}
	
	/**
	 * Boite parametrage envoi du courrier
	 */
	$opt_smtp_use_ssl = spiplistes_pref_lire('opt_smtp_use_ssl');
	if ($opt_smtp_use_ssl != 'oui') { $opt_smtp_use_ssl = 'non'; }
	
	$page_result .= ''
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_envoyer-24.png', true, '', _T('spiplistes:envoi_des_courriers'))
		. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE), true)
		//
		// adresse email de retour (reply-to)
		. spiplistes_cadre_input_text(_T('spiplistes:adresse_envoi_defaut')
									  , 'email_defaut' , $adresse_defaut
			)
		//
		// adresse email du smtp sender
		. spiplistes_cadre_input_text(_T('spiplistes:adresse_smtp')
									  , 'smtp_sender' , $smtp_sender
			)
		//
		// adresse de retour (reply-to)
		. spiplistes_cadre_input_text(_T('spiplistes:adresse_email_reply_to')
									  , 'email_reply_to' , $email_reply_to
			)
		//
		// adresse return-path de retour (on-error reply-to)
		. spiplistes_cadre_input_text(_T('spiplistes:adresse_on_error_defaut')
									  , 'email_return_path_defaut' , $email_return_path_defaut
			)
		//
		// Méthode d'envoi 
		. debut_cadre_relief('', true, '', _T('spiplistes:methode_envoi'))
		. "<div  class='verdana2'>" . $eol
		. _T('spiplistes:pas_sur')
		. bouton_radio("mailer_smtp", "non", _T('spiplistes:php_mail'), $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');")
		. "<br />" . $eol
		. bouton_radio("mailer_smtp", "oui", _T('spiplistes:utiliser_smtp'), $mailer_smtp == "oui"
			, "changeVisible(this.checked, 'smtp', 'block', 'none');")
		. "</div>" . $eol
		/**
		 * Si 'smtp', affiche bloc de paramétrage
		 */
		. "<ul id='smtp' class='verdana2' style='list-style: none;display:".(($mailer_smtp == "oui") ? "block" : "none")."'>" . $eol
		. "<li>"._T('spiplistes:smtp_hote')." : <input type='text' name='smtp_server' value='$smtp_server' size='30' class='forml' /></li>" . $eol
		. "<li>"._T('spiplistes:smtp_port')." : <input type='text' name='smtp_port' value='$smtp_port' size='4' class='fondl' /></li>" . $eol
		// SSL
		. '<li>'.spiplistes_bouton_checkbox ('opt_smtp_use_ssl', _T('spiplistes:opt_smtp_use_ssl'),  $opt_smtp_use_ssl).'</li>'.$eol
		//
		. "<li>"._T('spiplistes:requiert_identification')." : "
		. bouton_radio("smtp_identification", "oui", _T('item_oui'), ($smtp_identification == "oui"), "changeVisible(this.checked, 'smtp-auth', 'block', 'none');")
		. "&nbsp;"
		. bouton_radio("smtp_identification", "non", _T('item_non'), ($smtp_identification == "non"), "changeVisible(this.checked, 'smtp-auth', 'none', 'block');")."</li>" . $eol
		. "</ul>" . $eol
		. "<ul id='smtp-auth' class='verdana2' style='list-style:none;display:".(($smtp_identification == "oui") ? "block" : "none" )."'>" . $eol
		. "<li>"
			. "<label for='smtp_login'>"._T('item_login')." : </label>" . $eol
			. "<input type='text' id='smtp_login' name='smtp_login' value='".$GLOBALS['meta']['smtp_login']."' size='30' class='fondl' />" . $eol
		. "</li>" . $eol
		. "<li>"
			. "<label for='smtp_pass'>"._T('entree_passe_ldap')." : </label>" . $eol
			. "<input type='password' id='smtp_pass' name='smtp_pass' value='".$GLOBALS['meta']['smtp_pass']."' size='30' class='fondl' />" . $eol
		. "</li>" . $eol
		. "</ul>" . $eol
		. fin_cadre_relief(true)
		//
		// le nombre de lots d'envois
		. debut_cadre_relief('', true, '', _T('spiplistes:parametrer_la_meleuse'))
		. spiplistes_boite_select_de_formulaire (
			spiplistes_array_values_in_keys(explode(";", _SPIPLISTES_LOTS_PERMIS)), $GLOBALS['meta']['spiplistes_lots']
				, 'spiplistes_lots', 'spiplistes_lots'
				, 1, '', 'fondo', _T('spiplistes:nombre_lot')." : ", '', 'verdana2')
		. '<br />' . $eol
		//
		// sélection du charset d'envoi
		. spiplistes_boite_select_de_formulaire (
			spiplistes_array_values_in_keys(explode(";", _SPIPLISTES_CHARSETS_ALLOWED)), $GLOBALS['meta']['spiplistes_charset_envoi']
				, 'spiplistes_charset_envoi', 'spiplistes_charset_envoi'
				, 1, '', 'fondo', _T('spiplistes:jeu_de_caracteres')." : ", '', 'verdana2')
		. fin_cadre_relief(true)
		;
		//
	// options simulation des envois, suspendre le tri, la meleuse
	$page_result .= ''
		. debut_cadre_relief('', true, '', _T('spiplistes:mode_suspendre_trieuse'))
		. spiplistes_form_input_checkbox (
			'opt_suspendre_trieuse'
			, 'oui', _T('spiplistes:suspendre_le_tri_des_listes')
			, (spiplistes_pref_lire('opt_suspendre_trieuse') == 'oui'), true, false)
		. fin_cadre_relief(true)
		//
		. debut_cadre_relief('', true, '', _T('spiplistes:mode_suspendre_meleuse'))
		. spiplistes_form_input_checkbox (
			'opt_suspendre_meleuse'
			, 'oui', _T('spiplistes:suspendre_lenvoi_des_courriers')
			, (spiplistes_pref_lire('opt_suspendre_meleuse') == 'oui'), true, false)
		. fin_cadre_relief(true)
		//
		. debut_cadre_relief('', true, '', _T('spiplistes:mode_simulation'))
		. spiplistes_form_input_checkbox (
			'opt_simuler_envoi'
			, 'oui', _T('spiplistes:simuler_les_envois')
			, (spiplistes_pref_lire('opt_simuler_envoi') == 'oui'), true, false)
		. fin_cadre_relief(true)
		//
		. spiplistes_form_bouton_valider('btn_param_valider', _T('bouton_valider'), true)
		. spiplistes_form_fin(true)
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// La console
		$page_result .= '<a id="regler-console" name="regler-console"></a>'
			. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'console-24.gif', true, '', _T('spiplistes:log_console'))
			. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE), true)
			//
			// la console en mode debug ?
			. debut_cadre_relief('', true, '', _T('spiplistes:log_console_debug'))
			. spiplistes_form_input_checkbox (
				'opt_console_debug'
				, 'oui'
				, _T('spiplistes:log_console_debug_activer')
				, (spiplistes_pref_lire('opt_console_debug') == 'oui'), true, false)
			. fin_cadre_relief(true)
			// 
			. debut_cadre_relief('', true, '', _T('spiplistes:log_details_console'))
			. spiplistes_form_input_checkbox (
				'opt_log_voir_destinataire'
				, 'oui'
				, _T('spiplistes:log_voir_destinataire')
				, (spiplistes_pref_lire('opt_log_voir_destinataire') == 'oui'), true, false)
			. fin_cadre_relief(true)
			;
		// Paramétrer la console de debug/logs si sur LAN
		if(spiplistes_server_rezo_local()) {
			$page_result .= ''
				. debut_cadre_relief('', true, '', _T('spiplistes:log_console_syslog'))
				. '<p class="verdana2">'._T('spiplistes:log_console_syslog_desc', array('IP_LAN' => $_SERVER['SERVER_ADDR'])).'</p>' . $eol
				. spiplistes_form_input_checkbox (
					'opt_console_syslog'
					, 'oui', _T('spiplistes:log_console_syslog_texte')
					, (spiplistes_pref_lire('opt_console_syslog') == 'oui'), true, false)
					. fin_cadre_relief(true)
				;
		}
		$page_result .= ''
			. spiplistes_form_bouton_valider('btn_console_syslog')
			. spiplistes_form_fin(true)
			;
		// voir les journaux SPIP
		if(!($ii = spiplistes_pref_lire('opt_console_syslog')) || ($ii == 'non')) {
		// si syslog non activé, on visualise les journaux de spip
			// lien sur logs ou affiche logs
			/*
			 * CP-20081112: deplace' dans les raccourcis
			 */
			/*
			$page_result .= ''
				. "<a id='view-spiplistes-log' name='view-spiplistes-log' href='#view-spiplistes-log' class='verdana2'>"
					. _T('spiplistes:log_voir_les_journaux')
					. "</a>" . $eol
				. "<div id='view-spiplistes-log-box'></div>" . $eol
				;
			*/
		}
		$page_result .= ''
			. fin_cadre_trait_couleur(true)
			;
	
	// Fin de la page
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
} // exec_config()

/**
 * renvoie une boite select pour un formulaire
 * 
 * @param array $array_values 
 * @param int $selected 
 * @param int $select_id 
 * @param string $select_name 
 * @param int $size[optional]
 * @param string $select_style[optional]
 * @param string $select_class[optional]
 * @param string $label_value[optional]
 * @param string $label_style[optional]
 * @param string $label_class[optional]
 * @param bool $multiple[optional]
 * @return string
 */
function spiplistes_boite_select_de_formulaire ($array_values, $selected,
												$select_id, $select_name,
												$size=1, $select_style='', $select_class='',
												$label_value='', $label_style='',
												$label_class='', $multiple=false
	) {
	static $eol = PHP_EOL;
	$result = '';
	foreach($array_values as $key=>$value) {
		$result .= "<option".mySel($value, $selected).">$key</option>" . $eol;
	}
	$result = ''
		. (
			(!empty($label_value))
			? "<label for='$select_id'"
				.(!empty($label_style) ? " style='$label_style'" : '')
				.(!empty($label_class) ? " class='$label_class'" : '')
				.">$label_value</label>" . $eol 
			: ''
			)
		. "<select name='$select_name' size='$size'"
			.(($multiple && ($size>1)) ? " multiple='multiple'" : '')
			.(!empty($select_style) ? " style='$select_style'" : '')
			.(!empty($select_class) ? " class='$select_class'" : '')
			." id='$select_id'>" . $eol
		. $result
		. "</select>" . $eol
		;
	return ($result);
} // spiplistes_boite_select_de_formulaire()

/**
 * renvoie tableau avec key => value 
 * 
 * @param array $array
 * @return array
 */
function spiplistes_array_values_in_keys ($array) {
	$result = array();
	foreach($array as $value) {
		$result[$value] = $value;
	}
	return($result);
} // spiplistes_array_values_in_keys()

/**
 * Petit input checkbox
 *
 * @param string $btn_name
 * @param string $txt_label
 * @param string $cur_val
 * @return string
 */
function spiplistes_bouton_checkbox ($btn_name, $txt_label, $cur_val) {
	static $id;
	static $eol = PHP_EOL;
	$chk = ($cur_val == 'oui') ? 'checked="checked"' : '';
	$id++;
	$texte = '<p class="verdana2">' . $eol
		. '<input type="checkbox" name="'.$btn_name.'" value="oui" id="'.$btn_name.'-'.$id.'" '.$chk.' />' . $eol
		. '<label for="'.$btn_name.'-'.$id.'">'.$txt_label.'</label>' . $eol
		. '</p>' . $eol;
	return ($texte);
}

