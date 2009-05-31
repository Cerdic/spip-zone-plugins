<?php 
	// menudep_mes_options.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
	
	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Menudep est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
		
	define("_MENUDEP_PREFIX", "menudep");
	define("_MENUDEP_LANG", _MENUDEP_PREFIX.":");
	define("_DIR_PLUGIN_MENUDEP_IMG_PACK", _DIR_PLUGIN_MENUDEP."images/");
	define("_MENUDEP_META_PREFERENCES", _MENUDEP_PREFIX."_preferences");
	
	define("_MENUDEP_DEFAULT_VALUES_ARRAY", 
	 	serialize(
			array(
				'menudep_id' => "#navigation"		// id du bloc contenant le menu de navigation
				, 'menudep_div' => "div.rubriques"	// conteneur des éléments du menu dans menu_id
				, 'menudep_a' => "div>ul>li>a"	// chemin de l'ancre dans menu_id
				, 'menudep_class' => "on"	// classe de la rubrique courante
				, 'menudep_absolute' => "non"	// sous-menu flottant (position absolute)
				, 'menudep_zindex' => "999"	// position z-index css
				, 'menudep_tempo' => "512"	// temporisation des évènements (suspend) en millisecondes
				, 'menudep_top' => "-1ex"	// position verticale
				, 'menudep_left' => "5ex"	// position horizontale
				, 'menudep_bgcolor' => "white" // couleur par défaut du fond
				, 'menudep_border' => "1px solid gray" // bordure par défaut 
				, 'menudep_speedin' => "fast" // vitesse show|hide
				, 'menudep_speedout' => "slow" // vitesse show|hide
				, 'menudep_replier' => "oui" // replier le précédent sous-menu déplié
				, 'menudep_reavant' => "non" // replier le précédent sous-menu déplié avant de déplier le nouveau
				, 'menudep_heriter' => "oui" // héritage du style pour les boites flottantes
			)
		)
	);
?>