<?php

// exec/raper_voir_journal.php

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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/raper_api_globales');
include_spip('inc/raper_api_prive');
include_spip('inc/raper_api_journal');

/*
 *  si javascript non active', c'est cet exec qui est appele'
 */

/*
 * Voir le journal du plugin
 */
function exec_raper_voir_journal () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// la configuration est re'serve'e aux admins tt rubriques
	$autoriser = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('raper:titre_page_voir_journal');
	$rubrique = "voir_journal";
	$sous_rubrique = _RAPER_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	if(!$autoriser) {
		die (raper_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. raper_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. raper_boite_plugin_info()
		//. raper_boite_info_raper(true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>'raper_config'),'data'=>''))
		. creer_colonne_droite($rubrique, true)
		. raper_boite_raccourcis($rubrique, true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>'raper_config'),'data'=>''))
		. debut_droite($rubrique, true)
		;
	
	// affiche milieu
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", $titre_page)
		. raper_journal_lire(_RAPER_PREFIX)
		. fin_cadre_trait_couleur(true)
		;
		
	// Fin de la page
	echo($page_result);
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, raper_html_signature()
		, fin_gauche(), fin_page();
}

