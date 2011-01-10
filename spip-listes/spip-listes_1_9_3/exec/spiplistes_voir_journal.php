<?php

	// exec/spiplistes_voir_journal.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

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
	
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_presentation');
include_spip('inc/spiplistes_api_journal');

/*
 *  si javascript non activé, appel de cet exec
 */

/*
 * Voir le journal du plugin
 */
function exec_spiplistes_voir_journal () {

	static $eol = PHP_EOL;
	
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
	
	$message_gauche = '<p class="verdana2">' . $message_gauche . '</p>' . $eol;
		
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = spiplistes_journal_titre();
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique =  'voir_journal';

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . ' - ' . trim($titre_page), $rubrique, $sous_rubrique));

	if(!$autoriser) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ''
		. '<br style="line-height:3em" />' . $eol
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_meta_info(_SPIPLISTES_PREFIX)
		. $message_gauche
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron() // ne pas gener l'edition
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
	
	// affiche milieu
	$page_result .= ''
		. debut_cadre_trait_couleur("administration-24.gif", true, '', $titre_page)
		. spiplistes_journal_lire(_SPIPLISTES_PREFIX)
		. fin_cadre_trait_couleur(true)
		;
		
	// Fin de la page
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();

}

