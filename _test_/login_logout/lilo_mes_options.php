<?php 

	// lilo_mes_options.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	LiLo est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/
	
	
	
	define("_LILO_PREFIX", "lilo");
	define("_LILO_LANG", _LILO_PREFIX.":");
	define("_DIR_PLUGIN_LILO_IMG_PACK", _DIR_PLUGIN_LILO."images/");
	define("_LILO_META_PREFERENCES", _LILO_PREFIX."_preferences");
	
	define("_LILO_DEFAULT_VALUES_ARRAY", 
	 	serialize(
			array(
				  'lilo_login_voir_logo' => 'non'
				, 'lilo_login_voir_erreur' => 'non'
				, 'lilo_login_session_remember' => 'oui'
				, 'lilo_statut_voir_logo' => 'oui'
				, 'lilo_statut_voir_boutons_admins' => 'oui'
				, 'lilo_statut_transparent' => 'non'
				, 'lilo_statut_position' => 'tr'
				, 'lilo_statut_fixed' => 'oui'
				, 'lilo_statut_bgcolor' => '00f'
			)
		)
	);


?>