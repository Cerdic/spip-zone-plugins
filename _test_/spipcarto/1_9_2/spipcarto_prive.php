<?php
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d'elaboration d'information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005-2006
*
* Stephane Laurent, Franeois-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Leger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jegou - Mathieu Gehin - Michel Briand - 
* Mose - Olivier Frerot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.
* 
e -
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
e -
*
\***************************************************************************/

$GLOBALS['sq_cartes'][]='map';
$GLOBALS['sq_cartes'][]='logo';
$GLOBALS['sq_cartes'][]='lien';
$GLOBALS['sq_cartes'][]='zone';
$GLOBALS['sq_cartes'][]='svg';
$GLOBALS['sq_cartes'][]='svgx';
$GLOBALS['sq_cartes'][]='geosvgwms';

if (!defined('_DIR_PLUGIN_SPIPCARTO')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIPCARTO',(_DIR_PLUGINS.end($p))."/");
}

include_spip("base/carto");
function spipcarto_header_prive($flux) {
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPCARTO."css/carto_style_prive.css' type='text/css' media='all' />\n";
	return $flux;
}


function afficher_liste_carto_objets($choses,$nb_aff=20) {
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2("Objets", "../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_carto_objets as carto_objets');
  $select= array();
  $select[] = 'id_carto_objet';
  $select[] = 'titre';
  $select[] = 'url_objet';
  $select[] ='id_carto_carte';
//  $select[] = 'statut';
  $where = array('carto_objets.id_carto_objet IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
	$i++;
	$vals = '';
	
	$id_carto_objet = $row['id_carto_objet'];
	$tous_id[] = $id_carto_objet;
	$titre = $row['titre'];
	$id_carto_carte = $row['id_carto_carte'];
	$url_objet = $row['url_objet'];
	
	$vals[] = "<input type='checkbox' name='id_choses[]' value='$id_carto_objet' id='id_chose$i'/>";
	
	// Le titre (et la langue)
	$s = "<div>";
	
	$s .= "<a href=\"carto_cartes_edit.php?id_carte=$id_carto_carte#objet$id_carto_objet\" style=\"display:block;\">";
	
	$s .= typo($titre);
	$s .= "</a>";
	$s .= "</div>";
	
	$vals[] = $s;
	
	// L'url
	$s = "<a href=\"$url_objet\" style=\"display:block;\">lien</a>";
	$vals[] = $s;
	
	// Le numero (moche)
	if ($options == "avancees") {
	  $vals[] = "<b>"._T('info_numero_abbreviation')."$id_carto_objet</b>";
	}
	
	
	$table[] = $vals;
  }
  spip_free_result($result);
  
  if ($options == "avancees") { // Afficher le numero (JMB)
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 80, 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	}
  } else {
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 100, 100);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100);
	  $styles = array('', 'arial2', 'arial1');
	}
  }
  afficher_liste($largeurs, $table, $styles);
  
  echo afficher_liste_fin_tableau();
}
?>