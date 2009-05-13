<?php

// mon_diplome_options.php

 
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Plom.
	
	Plom is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Plom is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Plom; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Plom. 
	
	Plom est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Plom est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a' la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	

// pour SPIP 1.9.1
if(!defined('_DIR_PLUGIN_PLOM')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PLOM',(_DIR_PLUGINS.end($p)).'/');
}


define("_DIR_IMAGES_PLOM", _DIR_PLUGIN_PLOM."images/");

define("_PLOM_PAGE_DEFAUT", "mon_diplome");
define("_PLOM_MODELE_DEFAUT", "mon_diplome");

$plom_options = array(
	
	'id_auteur' => null // # de celui identifié
	
	// titre du document
	, 'titre_document' => ""
	
	// le fond du diplome peut être un PDF
	, 'appliquer_fond' => "oui"
	, 'modele_fond' => "mon_diplome"
	
	// le modèle par défaut (texte)
	, 'modele_texte' => 'mon_diplome'
	
	// format sortie, proprietes PDF, etc...
	, 'format' => "A4"
	, 'orientation' => "landscape"
	, 'creator' => "mon_diplome+html2pdf+fpdf/SPIP"
	, 'author' => $GLOBALS['meta']['nom_site']
	, 'subject' => _T('plom:pdf_sujet_defaut')
	, 'title' => _T('plom:pdf_titre_defaut')
	
	// squelette de sortie
	// doit se trouver a la racine du plug ou squelettes
	// ici, 'mon_diplome.html'
	, 'page' => "mon_diplome"
	
	// prefs complémentaires
	, 'SetAutoPageBreak' => false
	, 'SetAutoPageBreakMargin' => 0
	, 'MarginLeft' => 0
	, 'MarginTop' => 0
	, 'SetDisplayMode' => "real" // 'fullpage' || 'fullwidth' || 'real' || 'default'
	
);

?>