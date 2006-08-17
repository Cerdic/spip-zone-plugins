<?php 
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d’élaboration d’information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005
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
* Pour plus de details voir le fichier COPYING.txt ou l’aide en ligne.
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

define('_DIR_PLUGIN_SPIPCARTO',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

/***********************************************************************/
/* function*/
/***********************************************************************/

//------------------------la fonction qui fait tout-----------------------------------

function exec_cartes() {

  include(_DIR_PLUGIN_SPIPCARTO."/inc/carto.php");
  include_spip ("inc/presentation");
  include_spip ("base/abstract_sql");
//  include_spip("inc/objet");
	/***********************************************************************
	* PREFIXE
	***********************************************************************/
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	debut_page(_T("spipcarto:cartes_toutes"), "documents", "cartes");
	debut_gauche();
	
	
	
	debut_droite();
	
	
	//TODO !!!
	if (carte_administrable()) 
	afficher_cartes(_T("spipcarto:cartes_toutes"),
			array(
		"SELECT"=>"cartes.*, COUNT(id_carto_objet) AS objets ",
		"FROM"=>"spip_carto_cartes AS cartes" ,
		"JOIN"=>"spip_carto_objets AS objets ON (cartes.id_carto_carte=objets.id_carto_carte) ",
		"WHERE"=>"cartes.statut!='publie'",
		"GROUP BY"=>"cartes.id_carto_carte",
		"ORDER BY"=>"cartes.titre"));

	afficher_cartes(_T("spipcarto:cartes_toutes"),
			array(
		"SELECT"=>"cartes.*, COUNT(id_carto_objet) AS objets ",
		"FROM"=>"spip_carto_cartes AS cartes" ,
		"JOIN"=>"spip_carto_objets AS objets ON (cartes.id_carto_carte=objets.id_carto_carte) ",
		"WHERE"=>"cartes.statut='publie'",
		"GROUP BY"=>"cartes.id_carto_carte",
		"ORDER BY"=>"cartes.titre"));

	echo "<br />\n";
	
	if (carte_editable()) {
		echo "<div align='right'>";
		$link = generer_url_ecrire('cartes_edit','new=oui&retour='.urlencode(generer_url_ecrire('cartes')));
		icone(_T("spipcarto:carte_creer"), $link, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.png", "creer.gif");
		echo "</div>";
	}



	fin_page();

}
?>