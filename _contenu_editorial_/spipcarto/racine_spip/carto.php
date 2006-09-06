<?php 
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d'élaboration d'information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005-2006
*
* Stéphane Laurent, François-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Léger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jégou - Mathieu Géhin - Michel Briand - 
* Mose - Olivier Frérot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.
* 
— -
This program is free software ; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation ; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY ; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program (COPYING.txt) ; if not, write to
the Free Software Foundation, Inc.,
59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
or check http://www.gnu.org/copyleft/gpl.html
— -
*
\***************************************************************************/
# ou est l'espace prive ?
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

//specifier ici le nom du sous repertoire du dossier squelette contenant
//les squelettes de carte
//squelettes de carte disponibles 
//(preview dans carto_cartes.php3 et raccourcis dans carto_carte_edit.php3)
if ($GLOBALS['rep_cartes']) $rep_fond=$GLOBALS['rep_cartes'];
else $rep_fond="spipcarto";
//  valeurs par defaut
//$delai=24*3600;

$flag_preserver = true;

if (isset($contexte_inclus['fond'])&&($contexte_inclus['fond']!="")){
		if (strstr($fond, '/')
		OR preg_match(',^formulaire_,i', $fond))
			die ("Faut pas se gener");
		else $fond = $rep_fond."/carto_".$contexte_inclus['fond'];
}
elseif (isset($_GET["fond"])&&($_GET["fond"]!="")){
		if (strstr($fond, '/')
		OR preg_match(',^formulaire_,i', $fond))
			die ("Faut pas se gener");
		else $fond = $rep_fond."/carto_".$_GET["fond"];
}
else $fond = $rep_fond."/carto";

// Securite 

if (!find_in_path("$fond.html")) {
	spip_log("carto.php: find_in_path ne trouve pas le squelette $fond");
	$fond = '404';
}

if (isset($contexte_inclus['delais'])) $delais = intval($contexte_inclus['delais']);

# au travail...
include _DIR_RESTREINT_ABS.'public.php';

?>