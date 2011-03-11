<?php
	
	// inc/spiplistes_api_journal.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
	
	// fonctions utilises en espace privé
	// presentation

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	http://files.spip.org/spip-zone/fond_mp3.zip
	
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

if(!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

/*
 * Afficher le journal (log) en espace prive. Voir:
 * - exec/*_voir_journal.php
 * - action/*_voir_journal.php
 * Ajouter dans la page exec/* spiplistes_raccourci_journal()
 */

/*
 * Boite raccourci, Afficher le journal du plugin
 */
function spiplistes_raccourci_journal ($envelopper = true) {

	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	$result = "";
	
	if($envelopper) {
		$result .= ""
			. debut_cadre_enfonce('', true)
			. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>"
				. _T('titre_cadre_raccourcis')
				. "</span>\n"
			. "<ul class='verdana2' style='list-style:none;padding:1ex;margin:0;'>\n"
			;
	}
	$result .= ""
		. "<li id='spiplistes-log-raccourci'>"
		. icone_horizontale(
			_T('spiplistes:log_voir_le_journal')
			, generer_url_ecrire('spiplistes_voir_journal')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."log-24.png"
			, ''
			,false
			)
		.	(
				(spiplistes_server_rezo_local())
				? "<span class='verdana2' style='display:block;padding:0.5ex;text-align:center'>" 
					// avertir qu'on est en mode debug
					. "<span style='display:block;font-weight:700;color:white;background-color:red;'>" . _T('spiplistes:mode_debug_actif') . "</span>\n"
					// l'adresse IP pour info
					. "<span style='display:block;'>" . $_SERVER['SERVER_ADDR'] . "</span>\n"
					. "</span>\n"
				: ""
			)
		. "</li>\n"
		;
	if($envelopper) {
		$result .= ""
			. "</ul>\n"
			;
	}
	
	if($envelopper)
	{
		$result .= fin_cadre_enfonce(true);
	}

	return($result);
}

/*
 * jQuery pour afficher le journal
 * @return le code jQuery à placer au bon endroit
 */
function spiplistes_raccourci_journal_jquery () {

	$action = "spiplistes_journal";
	$action_arg = "";
	$journal_url_action = generer_action_auteur($action, $action_arg);
	// $.ajax n'aime pas &amp;
	$journal_url_action = preg_replace("/&amp;/", "&", $journal_url_action);
	
		
	/*
	 * le JS nécessaire
	 */
	
	$js = "
$(document).ready(function(){
	
	$.fn.extend({
		log_loader: function() {
			$('#spiplistes-log-result').css({
				'background': 'url(". find_in_path("images/searching.gif").") no-repeat top center'
			});
			$.ajax({
				type: 'POST'
				, data : ''
				, url: '" . $journal_url_action . "'
				, success: function(msg){
					$('#spiplistes-log-result').html(msg);
					$('#spiplistes-log-result').css({'background': 'url()'});
				}
			});
		}
	});

	/* le journal */
	var log_switch = null;
	/*
	 * attendre évènement click sur le bouton
	 */
	$('#spiplistes-log-raccourci .cellule-h').click(function(){
		if(log_switch) {
			/* supprimer la boite de log */
			$('#spiplistes-log-bg').remove();
			$('#spiplistes-log-div').remove();
			log_switch = false;
		}
		else {
			/* creer une boite pour le fond */
			$('body').append(
				'<div id=\'spiplistes-log-bg\'></div>'
				+ '<div id=\'spiplistes-log-div\'>'
				+ '<h1>".spiplistes_journal_titre()."<span id=\'spiplistes-log-close\'>'
						+ '<span style=\'display:none\'>X</span>'
					+ '</span></h1>'
				+ '<div id=\'spiplistes-log-result\' style=\'padding:1ex\'></div>'
				+ '<div id=\'spiplistes-log-reload\'><div id=\'spiplistes-log-reload-btn\'></div></div>'
				+ '</div>'
				);
			/* 
			 * prendre toute la surface de l ecran 
			 */
			$('#spiplistes-log-bg').width($('body').width());
			$('#spiplistes-log-bg').height($('body').height());
			$('#spiplistes-log-bg').css({ 
				'position': 'absolute'
				, 'top':'0'
				, 'left': '0'
				/* un peu de transparence pour le fond */
				, 'filter': 'alpha(opacity=70)'	/* IE */	
				, '-moz-opacity': '.70'	/* Mozilla */	
				, 'opacity':'.70' /* les autres */
				, 'background-color': '#333'
				, 'color': 'white'
				/* passer au dessus de tous */
				, 'z-index': '2000'
			});
			var log_x = Math.round(($('body').width() - 600) / 2);
			var log_y = Math.round(($('body').height() - 400) / 8);
			log_x = (log_x >= 0) ? log_x : 0;
			log_y = (log_y >= 0) ? log_y : 0;
			
			/*
			 * la fenetre de résultat
			 */
			$('#spiplistes-log-div').css({ 
				'position': 'absolute'
				, 'top': log_y
				, 'left': log_x
				, 'margin': '100px auto'
				, 'width': '600px'
				, 'height': '450px'
				, 'background-color': 'white'
				, 'color': 'black'
				, 'z-index': '2001'
			});
			
			/*
			 * le titre de cette fenêtre
			 */
			$('#spiplistes-log-div h1').css({
				'position': 'relative'
				, 'font-size': '1.2em'
				, 'padding': '0 8px'
				, 'height': '24px'
				, 'margin-top': '0.25em'
			});

			/*
			 * le bouton close positionné à droite
			 */
			$('#spiplistes-log-close').css({
				'display': 'block'
				, 'position': 'absolute'
				, 'top': '0'
				, 'right': '8px'
				, 'width': '24px'
				, 'height': '24px'
				, 'cursor': 'pointer'
				, 'background': 'url(". find_in_path(_DIR_PLUGIN_SPIPLISTES_IMG_PACK . "close-24.png").") no-repeat top right'
			});
			
			/*
			 * la boite pour le résultat
			 */
			$('#spiplistes-log-result').css({
				'width': 'auto'
				, 'height': '360px'
				, 'background': 'url(". find_in_path(_DIR_PLUGIN_SPIPLISTES_IMG_PACK . "searching.gif").") no-repeat top center'
			});
			
			/*
			 * le bouton pour recharger
			 */
			$('#spiplistes-log-reload').css({
				'width': 'auto'
				, 'height': '12px'
				, 'text-align': 'right'
				, 'padding': '4px 10px'
				, 'cursor': 'pointer'
			});
			$('#spiplistes-log-reload-btn').css({
				'width': '12px'
				, 'height': '12px'
				, 'margin-left': 'auto'
				, 'background': 'url(". find_in_path(_DIR_PLUGIN_SPIPLISTES_IMG_PACK . "reload-12.png").") no-repeat top right'
			});
			$('#spiplistes-log-close').attr('title', '" . _T('spiplistes:fermer_journal'). "');
			$('#spiplistes-log-reload-btn').attr('title', '" . _T('spiplistes:recharger_journal'). "');
			
			log_switch = true;
			/*
			 * maintenant que l'objet parent est créé, on peut rajouter les évènements
			 * sur ses enfants
			 */
			$('#spiplistes-log-close').hover(function(){
				$(this).addClass('spiplistes-hover');
			},function(){
				$(this).removeClass('spiplistes-hover');
			});
			$('#spiplistes-log-close').click(function(){
				log_switch = false;
				$('#spiplistes-log-bg').remove();
				$('#spiplistes-log-div').remove();
			});
			$('#spiplistes-log-reload-btn').hover(function(){
				$(this).addClass('spiplistes-hover');
			},function(){
				$(this).removeClass('spiplistes-hover');
			});
			$('#spiplistes-log-reload-btn').click(function(){
				$(document).log_loader();
			});
			/*
			 * Demande le journal via Ajax
			 */
			$(document).log_loader();
		}
		return(false);
	});
		
}); // end $(document).ready()
	";
	
	$result = spiplistes_envelopper_script(spiplistes_compacter_script($js, 'js'), 'js');
	
	return($result);
}

/*
 * @return: le contenu du journal (log) du plugin
 */
function spiplistes_journal_lire ($logname = NULL, $logdir = NULL, $logsuf = NULL) {
	// definition des constantes 1.9.3 pour les SPIP anterieurs
	if (!defined('_DIR_LOG')){
		define('_DIR_LOG',defined('_DIR_TMP')?_DIR_TMP:_DIR_SESSION);
	}
	if (!defined('_FILE_LOG_SUFFIX')){
		define('_FILE_LOG_SUFFIX','.log');
	}
	if (!defined('_FILE_LOG')){
		define('_FILE_LOG','spip');
	}
	
	$logname = ($logname === NULL ? _FILE_LOG : $logname);
	
	$logfile = 
		(spiplistes_spip_est_inferieur_193())
		? _DIR_TMP . $logname . '.log' 
		: ($logdir===NULL ? _DIR_LOG : $logdir)
			. (test_espace_prive()?'prive_':'') //distinguer les logs prives et publics
	  		. $logname
			. ($logsuf === NULL ? _FILE_LOG_SUFFIX : $logsuf)
		;

	$result =
		(file_exists($logfile))
		//? file_get_contents($logfile, false, null, 0, 2048)
		? file_get_contents($logfile)
		: _T('fichier_introuvable', array('fichier', $logfile))
		;
	
	$result = ""
		. "<div class='verdana2' style='height:360px;overflow:scroll;background:url(rien.gif)'>\n"
		. nl2br($result)
		. "</div>\n"
		;
		
	return($result);
}

function spiplistes_journal_titre() {
	return(_T('spiplistes:titre_page_voir_journal'));
}
 
?>