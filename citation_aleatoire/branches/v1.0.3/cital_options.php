<?php

// cital_options.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of CitAl (Citation Aleatoire).
	
	CitAl is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	CitAl is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with CitAl; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de CitAl. 
	
	CitAl est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	CitAl est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
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

// *_options est appelé à chaque hit

// _DIR_PLUGIN_CITAL pour SPIP 1.9.1
if(!defined('_DIR_PLUGIN_CITAL')) {
	$p = explode(basename(_DIR_PLUGINS).'/',str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_CITAL',(_DIR_PLUGINS.end($p)).'/');
}
define('_DIR_CITAL_IMG_PACK', _DIR_PLUGIN_CITAL.'images/');
define('_DIR_CITATIONS', 'citations/');
define('_DIR_CITAL_CITATIONS', _DIR_PLUGIN_CITAL._DIR_CITATIONS);

define('_CITAL_PREFIX', 'cital');

