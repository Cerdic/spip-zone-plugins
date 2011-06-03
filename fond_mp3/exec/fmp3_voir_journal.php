<?php

	// exec/fmp3_voir_journal.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

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
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');
include_spip('inc/fmp3_api_journal');

/**
 *  si javascript non activé, appel de cet exec
 */

/**
 * Voir le journal du plugin
 */
function exec_fmp3_voir_journal () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// la configuration est réservée aux admins tt rubriques
	$autoriser = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;

	$taille_cache = spip_fetch_array(spip_query("SELECT SUM(taille) AS n FROM spip_caches WHERE type='t'"));
	$message_gauche = 
		($taille_cache = $taille_cache['n']) 
		? _T('taille_cache_octets', array('octets' => taille_en_octets($taille_cache)))
		: _T('taille_cache_vide')
		;
	
	$message_gauche = "<p class='verdana2'>" . $message_gauche . "</p>\n";
		
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('fmp3:titre_page_voir_journal');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "voir_journal";
	$sous_rubrique = _FMP3_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	if(!$autoriser) {
		die (fmp3_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. fmp3_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, _FMP3_PREFIX)
		. debut_gauche($rubrique, true)
		. fmp3_boite_plugin_info(_FMP3_PREFIX)
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. fmp3_boite_aide_info(true)
		. debut_droite($rubrique, true)
		;
	
	// affiche milieu
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", $titre_page)
		. fmp3_journal_lire(_FMP3_PREFIX)
		. fin_cadre_trait_couleur(true)
		;
		
	// Fin de la page
	echo($page_result);
	echo fmp3_html_signature(_FMP3_PREFIX), fin_gauche(), fin_page();
}

?>