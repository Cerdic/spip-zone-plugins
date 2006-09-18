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

define('_DIR_PLUGIN_SPIPCARTO',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function spipcarto_ajouterBoutons($boutons_admin) {
  
  if (lire_meta('activer_carto')=='oui')
  	$boutons_admin['naviguer']->sousmenu["carto_cartes"]= new Bouton(
																   "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.png",  // icone
																   _L('spipcarto:cartes') //titre
																   );
  return $boutons_admin;
}

function spipcarto_affiche_droite($flux){
  if (lire_meta('activer_carto')=='oui'){
  include_spip ("inc/carto");
	if (_request('exec')=='articles_edit'){
		$flux['data'] .= spipcarto_afficher_insertion_carte($flux['args']['id_article']);
	}
  }
	return $flux;
}


function spipcarto_ajouterOnglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['spipcarto']= new Bouton(
											  "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", 'SpipCarto',
											  generer_url_ecrire("config_spipcarto"));
  return $flux;
}


function spipcarto_pre_propre($texte) {
//est-ce bien necessaire ... 
	if (lire_meta('activer_carto')=='oui'){
		static $reset;
		$cartes = array();
		$maj_liens = ($_GET['exec']=='articles' AND $id_article = intval($_GET['id_article']));
		if ($maj_liens) {
			if (!$reset) {
				$query = "DELETE FROM spip_carto_cartes_articles WHERE id_article=$id_article";
				spip_query($query);
				$reset = true;
			}
		}
	
	//  include_spip ("base/abstract_sql");
		
		if (is_int(strpos($texte, '<map')) &&
		  (preg_match_all(",<map(\d+)([|])?([a-zA-Z.]+)?([(])?([a-zA-Z0-9\,\-.]+)?([)])?>,", $texte, $regs, PREG_SET_ORDER))) {	
		
//			include_spip ("public/assembler");
			foreach ($regs as $r) {
				$id_carte = $r[1];
				$cartes[$id_carte] = $id_carte;
				$cherche = $r[0];
				
				//TODO : traiter l'alignement avant replace
				//Le mieux etant de les traiter ici, dans le plugin
				//car le code doit rester identique a celui de Spip
				//$align = $r[3];
				
				//TODO : voir si on ne peut pas faire un reglage plus fin ...
				// voir aussi pour les variables passees en POST (pa de cache ???)
				// en attendant : 60s ca permet de tester facilement le cache et le recalcul
				$ledelai="60";
				//TODO : peut etre d'autres parametres ... ???
				//forcer un type de carte ?
				//$type_carte = $r[3];
				
				//l'url du site parsee, ca peut servir ...
				//$taburl=parse_url(lire_meta('adresse_site'));
				
				
				//mettre a jour la table de liaison avec les articles
				if ($maj_liens && $cartes &&(lire_meta('carto_preview_ecrire')!='oui')) {
					global $couleur_claire;
					$remplace['texte']="<div><table class='gauche'><tr>" .
//							"<td class='cellule36' style='width: 100px;'><a href='../carto.php?id_map=".$r[1]."&fond=".$r[3]."&args=".$r[5]."' class='selection' target='_blank'><img src='../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.png' alt=' '/>" .
							"<td class='cellule36' style='width: 100px;'><a href='?exec=carto_cartes_edit&id_carte=".$r[1]."&retour=".rawurlencode(self())."' class='selection' target='_blank'><img src='../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.png' alt=' '/>" .
							"<span>Carte ".$r[1]."</span></a></td></tr></table></div>";				
					$texte = str_replace($cherche, $remplace['texte'], $texte);
				}
				//recuperation de la map en cache
/*
				else {
					$lecontexte['id_carto_carte']=$id_carte;
					$lecontexte['args']=$r[5];
					if ($GLOBALS['rep_cartes']) $rep_fond=$GLOBALS['rep_cartes'];
					else $rep_fond="spipcarto";
					if ($r[3]) $lecontexte['fond']=$rep_fond."/carto_".$r[3];
					else $lecontexte['fond']=$rep_fond."/carto";
					//$lecontexte['fond']="bloc.php3";
					$remplace=inclure_page($lecontexte['fond'],$lecontexte);
				}
				//TODO : ajouter alignement ici
				$texte = str_replace($cherche, $remplace['texte'], $texte);
*/
			}
			//mettre a jour la table de liaison avec les articles
			//TODO : spip_abstract_insert ?
			if ($maj_liens) {
				$query = "INSERT INTO spip_carto_cartes_articles (id_article, id_carto_carte) ".
				"VALUES ($id_article, ".join("), ($id_article, ", $cartes).")";
				spip_query($query);
			}
		}
	}	
	return $texte;
}

?>
