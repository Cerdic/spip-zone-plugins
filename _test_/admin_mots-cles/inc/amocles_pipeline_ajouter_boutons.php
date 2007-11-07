<?php

	// inc/amocles_pipeline_ajouter_boutons.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
	
	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	Amocles est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/

/*
	Ajoute le bouton des mots-cl�s dans la barre principale en espace priv�
	pour les administrateurs de mots-cl�s
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut r�activer le plugin (config/plugin: d�sactiver/activer)
	
*/

spip_log("amocles_ajouter_boutons.php +-", _AMOTSCLES_PREFIX);

function amocles_ajouter_boutons($boutons_admin) {

	include_spip("inc/amocles_api");
	
	if (
		in_array($GLOBALS['auteur_session']['id_auteur'], amocles_admins_groupes_mots_get_ids())
		// ne pas dupliquer l'icone pour super-admin
		&& !$GLOBALS['connect_toutes_rubriques']
		) {
	  // bouton dans la barre "naviguer"
	  $boutons_admin['naviguer']->sousmenu[_AMOCLES_PREFIX] = new Bouton(
		_DIR_IMG_PACK."mot-cle-24.gif"  // icone
		, _T('icone_mots_cles')	// titre
		, "mots_tous"
		);
	}

	return $boutons_admin;
}

?>