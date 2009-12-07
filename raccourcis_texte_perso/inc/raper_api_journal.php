<?php
	
// inc/raper_api_journal.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	RaPer is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	RaPer is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with RaPer; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de RaPer. 
	
	RaPer est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	RaPer est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// journal du raper

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

/*
 * Afficher le journal (log) en espace privé. Voir:
 * - exec/*_journal.php
 * - action/*_journal.php
 * Ajouter dans la page exec/* raper_raccourci_journal()
 */

/*
 * Boite raccourci, Afficher le journal du plugin
 */
function raper_raccourci_journal () {

	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	$result = ""
		. "<span id='journal-log-raccourci'>"
		. icone_horizontale(
			_T('raper:voir_journal')
			, generer_url_ecrire('raper_voir_journal')
			, _DIR_RAPER_IMG_PACK."log-24.png"
			, ''
			,false
			)
		. "</span>\n"
		;
	
	$action = "raper_journal";
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
			$('#journal-log-result').css({
				'background': 'url(". find_in_path("images/searching.gif").") no-repeat top center'
			});
			$.ajax({
				type: 'POST'
				, data : ''
				, url: '" . $journal_url_action . "'
				, success: function(msg){
					$('#journal-log-result').html(msg);
					$('#journal-log-result').css({'background': 'url()'});
				}
			});
		}
	});

	/* le journal */
	var log_switch = null;
	/*
	 * attendre évènement click sur le bouton
	 */
	$('#journal-log-raccourci .cellule-h').click(function(){
		if(log_switch) {
			/* supprimer la boite de log */
			$('#journal-log-bg').remove();
			$('#journal-log-div').remove();
			log_switch = false;
		}
		else {
			/* creer une boite pour le fond */
			$('body').append(
				'<div id=\'journal-log-bg\'></div>'
				+ '<div id=\'journal-log-div\'>'
				+ '<h1>"._T('raper:titre_page_voir_journal')."<span id=\'journal-log-close\'>'
						+ '<span style=\'display:none\'>X</span>'
					+ '</span></h1>'
				+ '<div id=\'journal-log-result\' style=\'padding:1ex\'></div>'
				+ '<div id=\'journal-log-reload\'><div id=\'journal-log-reload-btn\'></div></div>'
				+ '</div>'
				);
			/* 
			 * prendre toute la surface de l ecran 
			 */
			$('#journal-log-bg').width($('body').width());
			$('#journal-log-bg').height($('body').height());
			$('#journal-log-bg').css({ 
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
			$('#journal-log-div').css({ 
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
			$('#journal-log-div h1').css({
				'position': 'relative'
				, 'font-size': '1.2em'
				, 'padding': '0 8px'
				, 'height': '24px'
				, 'margin-top': '0.25em'
			});

			/*
			 * le bouton close positionné à droite
			 */
			$('#journal-log-close').css({
				'display': 'block'
				, 'position': 'absolute'
				, 'top': '0'
				, 'right': '8px'
				, 'width': '24px'
				, 'height': '24px'
				, 'background': 'url(". find_in_path("images/close-24.png").") no-repeat top right'
				, 'cursor': 'pointer'
			});
			
			/*
			 * la boite pour le résultat
			 */
			$('#journal-log-result').css({
				'width': 'auto'
				, 'height': '360px'
				, 'background': 'url(". find_in_path("images/searching.gif").") no-repeat top center'
			});
			
			/*
			 * le bouton pour recharger
			 */
			$('#journal-log-reload').css({
				'width': 'auto'
				, 'height': '12px'
				, 'text-align': 'right'
				, 'padding': '4px 10px'
			});
			$('#journal-log-reload-btn').css({
				'width': '12px'
				, 'height': '12px'
				, 'margin-left': 'auto'
				, 'background': 'url(". find_in_path("images/reload-12.png").") no-repeat top right'
				, 'cursor': 'pointer'
			});
			$('#journal-log-reload-btn').attr('title', '" . _T('journal:recharger_journal'). "');
			
			
			log_switch = true;
			/*
			 * maintenant que l'objet parent est créé, on peut rajouter les évènements
			 * sur ses enfants
			 */
			$('#journal-log-close').hover(function(){
				$(this).addClass('journal-hover');
			},function(){
				$(this).removeClass('journal-hover');
			});
			$('#journal-log-close').click(function(){
				log_switch = false;
				$('#journal-log-bg').remove();
				$('#journal-log-div').remove();
			});
			$('#journal-log-reload-btn').hover(function(){
				$(this).addClass('journal-hover');
			},function(){
				$(this).removeClass('journal-hover');
			});
			$('#journal-log-reload-btn').click(function(){
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
	
	$result .= raper_envelopper_script(raper_compacter_script($js, 'js'), 'js');
	
	return($result);
}

/*
 * @return: le contenu du journal (log) du plugin
 */
function raper_journal_lire ($logname = NULL, $logdir = NULL, $logsuf = NULL) {
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
		(raper_spip_est_inferieur_193())
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
		. "<div class='verdana2' style='width:80ex;height:360px;margin:0 auto;overflow:scroll;background:url(rien.gif)'>\n"
		. nl2br($result)
		. "</div>\n"
		;
		
	return($result);
}

function raper_envelopper_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		switch($format) {
			case 'css':
				$source = "\n<style type='text/css'>\n<!--\n" 
					. $source
					. "\n-->\n</style>";
				break;
			case 'js':
				$source = "\n<script type='text/javascript'>\n//<![CDATA[\n" 
					. $source
					. "\n//]]>\n</script>";
				break;
			default:
				$source = "\n\n<!-- erreur envelopper: format inconnu [$format] -->\n\n";
		}
	}
	return($source);
} // end raper_envelopper_script()

/*
 * complément des deux 'compacte'. supprimer les espaces en trop.
 */ 
function raper_compacter_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		$source = compacte($source, $format);
		$source = preg_replace(",/\*.*\*/,Ums","",$source); // pas de commentaires
		$source = preg_replace('=[[:space:]]+=', ' ', $source); // réduire les espaces
	}
	return($source);
} // end raper_compacter_script()

