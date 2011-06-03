<?php
	
	// inc/fmp3_api_journal.php
	
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
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de de'tails. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

/**
 * Afficher le journal (log) en espace privé. Voir:
 * - exec/*_voir_journal.php
 * - action/*_voir_journal.php
 * Ajouter dans la page exec/* fmp3_raccourci_journal()
 */

/**
 * Boite raccourci, Afficher le journal du plugin
 */
function fmp3_raccourci_journal () {

	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	$result = ""
		. debut_cadre_enfonce('', true)
		. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>"
			. _T('titre_cadre_raccourcis')
			. "</span>\n"
		. $result
		. "<ul class='verdana2' style='list-style:none;padding:1ex;margin:0;'>\n"
		. "<li id='fmp3-log-raccourci'>"
		. icone_horizontale(
			_T('fmp3:voir_journal')
			, generer_url_ecrire('fmp3_voir_journal')
			, _DIR_FMP3_IMAGES."log-24.png"
			, ''
			,false
			)
		. "</li>\n"
		. "</ul>\n"
		.	(
				(defined("_FMP3_DEBUG") && _FMP3_DEBUG)
				? "<div class='verdana2' style='padding:0.5ex;text-align:center'>" 
					// avertir qu'on est en mode debug
					. "<div style='font-weight:700;color:white;background-color:red;'>" . _T('fmp3:mode_debug_actif') . "</div>\n"
					// l'adresse IP pour info
					. "<div>" . $_SERVER['SERVER_ADDR'] . "</div>\n"
					. "</div>\n"
				: ""
			)
		. fin_cadre_enfonce(true)
		;
	
	$action = "fmp3_journal";
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
			$('#fmp3-log-result').css({
				'background': 'url(". find_in_path("images/searching.gif").") no-repeat top center'
			});
			$.ajax({
				type: 'POST'
				, data : ''
				, url: '" . $journal_url_action . "'
				, success: function(msg){
					$('#fmp3-log-result').html(msg);
					$('#fmp3-log-result').css({'background': 'url()'});
				}
			});
		}
	});

	/* le journal */
	var log_switch = null;
	/*
	 * attendre évènement click sur le bouton
	 */
	$('#fmp3-log-raccourci .cellule-h').click(function(){
		if(log_switch) {
			/* supprimer la boite de log */
			$('#fmp3-log-bg').remove();
			$('#fmp3-log-div').remove();
			log_switch = false;
		}
		else {
			/* creer une boite pour le fond */
			$('body').append(
				'<div id=\'fmp3-log-bg\'></div>'
				+ '<div id=\'fmp3-log-div\'>'
				+ '<h1>"._T('fmp3:titre_page_voir_journal')."<span id=\'fmp3-log-close\'>'
						+ '<span style=\'display:none\'>X</span>'
					+ '</span></h1>'
				+ '<div id=\'fmp3-log-result\' style=\'padding:1ex\'></div>'
				+ '<div id=\'fmp3-log-reload\'><div id=\'fmp3-log-reload-btn\'></div></div>'
				+ '</div>'
				);
			/* 
			 * prendre toute la surface de l ecran 
			 */
			$('#fmp3-log-bg').width($('body').width());
			$('#fmp3-log-bg').height($('body').height());
			$('#fmp3-log-bg').css({ 
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
			var log_y = Math.round(($('body').height() - 400) / 4);
			log_x = (log_x >= 0) ? log_x : 0;
			log_y = (log_y >= 0) ? log_y : 0;
			
			/*
			 * la fenetre de résultat
			 */
			$('#fmp3-log-div').css({ 
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
			$('#fmp3-log-div h1').css({
				'position': 'relative'
				, 'font-size': '1.2em'
				, 'padding': '0 8px'
				, 'height': '24px'
				, 'margin-top': '0.25em'
			});

			/*
			 * le bouton close positionné à droite
			 */
			$('#fmp3-log-close').css({
				'display': 'block'
				, 'position': 'absolute'
				, 'top': '0'
				, 'right': '8px'
				, 'width': '24px'
				, 'height': '24px'
				, 'background': 'url(". find_in_path("images/close-24.png").") no-repeat top right'
			});
			
			/*
			 * la boite pour le résultat
			 */
			$('#fmp3-log-result').css({
				'width': 'auto'
				, 'height': '360px'
				, 'background': 'url(". find_in_path("images/searching.gif").") no-repeat top center'
			});
			
			/*
			 * le bouton pour recharger
			 */
			$('#fmp3-log-reload').css({
				'width': 'auto'
				, 'height': '12px'
				, 'text-align': 'right'
				, 'padding': '4px 10px'
			});
			$('#fmp3-log-reload-btn').css({
				'width': '12px'
				, 'height': '12px'
				, 'margin-left': 'auto'
				, 'background': 'url(". find_in_path("images/reload-12.png").") no-repeat top right'
			});
			$('#fmp3-log-reload-btn').attr('title', '" . _T('fmp3:recharger_journal'). "');
			
			
			log_switch = true;
			/*
			 * maintenant que l'objet parent est créé, on peut rajouter les évènements
			 * sur ses enfants
			 */
			$('#fmp3-log-close').hover(function(){
				$(this).addClass('fmp3-hover');
			},function(){
				$(this).removeClass('fmp3-hover');
			});
			$('#fmp3-log-close').click(function(){
				log_switch = false;
				$('#fmp3-log-bg').remove();
				$('#fmp3-log-div').remove();
			});
			$('#fmp3-log-reload-btn').hover(function(){
				$(this).addClass('fmp3-hover');
			},function(){
				$(this).removeClass('fmp3-hover');
			});
			$('#fmp3-log-reload-btn').click(function(){
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
	
	$result .= fmp3_envelopper_script(fmp3_compacter_script($js, 'js'), 'js');
	
	return($result);
}

/*
 * @return: le contenu du journal (log) du plugin
 */
function fmp3_journal_lire ($logname = NULL, $logdir = NULL, $logsuf = NULL) {
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
		(fmp3_spip_est_inferieur_193())
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
 
?>