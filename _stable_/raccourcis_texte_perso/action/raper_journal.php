<?php 

// action/raper_journal.php

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
/* Ajax, renvoie le contenu du log
/**/
function action_raper_journal () {
	
	global $connect_toutes_rubriques, $connect_login, $connect_statut, $spip_lang_rtl;
	
	if (!$connect_statut) {
		$auth = charger_fonction('auth', 'inc');
		$auth = $auth();
	}

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	$autoriser_lire = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	
	if($autoriser_lire) {

		$result = raper_journal_lire(_RAPER_PREFIX);
	
		echo($result);
		
	}
}

?>