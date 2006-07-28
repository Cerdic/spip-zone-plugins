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

function exec_cartes_edit() {
  include(_DIR_PLUGIN_SPIPCARTO."/inc/carto.php");
//include_spip ("inc/carto");
include_spip ("spipcarto_fonctions");
include_spip ("inc/carto_import");
include_spip ("inc/presentation");
include_spip ("inc/documents");
include_spip ("inc/mots");

$id_carte = intval($_REQUEST['id_carte']);
if (intval($_REQUEST['id_carto_carte'])) $id_carte = intval($_REQUEST['id_carto_carte']);
$retour=$_REQUEST['retour'];

$flag_editable=carte_editable($id_carte);
$flag_mots = lire_meta("carto_mots");

$titre = stripslashes($_REQUEST['titre']);
$url_carte = stripslashes($_REQUEST['url_carte']);
$texte = stripslashes($_REQUEST['texte']);
$callage = stripslashes($_REQUEST['callage']);
$id_srs = intval($_REQUEST['id_srs']);
$new =stripslashes($_REQUEST['new']);
$supp_carte=stripslashes($_REQUEST['supp_carte']);
$supp_objet=stripslashes($_REQUEST['supp_objet']);
$supp_objet_all=stripslashes($_REQUEST['supp_objet_all']);
$supp_confirme=stripslashes($_REQUEST['supp_confirme']);
$supp_rejet=stripslashes($_REQUEST['supp_rejet']);
$modif_carte=stripslashes($_REQUEST['modif_carte']);

$selection_type=stripslashes($_REQUEST['selection_type']);
$selection_coords=stripslashes($_REQUEST['selection_coords']);
$modif_objet=stripslashes($_REQUEST['modif_objet']);
$objet_titre=stripslashes($_REQUEST['objet_titre']);
$objet_texte=stripslashes($_REQUEST['objet_texte']);
$url_objet=stripslashes($_REQUEST['url_objet']);
$url_logo=stripslashes($_REQUEST['url_logo']);
$geometrie=stripslashes($_REQUEST['geometrie']);

$retour=stripslashes($_REQUEST['retour']);
//$retour=$_REQUEST['retour'];

$flag_editable=carte_editable($id_carte);
$flag_mots = lire_meta("carto_mots");
//$flag_mots = "oui";
$nouveau=false;
//
// Modifications aux donnees de base de la carte
//
if (carte_administrable($id_carte)) {
	if ($supp_carte = intval($supp_carte) AND $supp_confirme AND !$supp_rejet) {
		//TODO : passer tout ca en spip_abstract ...
		$query = "DELETE FROM spip_carto_cartes WHERE id_carto_carte=$supp_carte";
		$result = spip_query($query);
		Header("Location: $retour");
		exit;
	}
}


if ($flag_editable) {
	if ($new == 'oui') {
		$titre = _T("spipcarto:carte_nouvelle");
		$url_carte = "";
		$texte = "";
		$callage = "";
		$id_srs = 1;
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		//TODO : passer tout ca en spip_abstract ...
		spip_query("INSERT INTO spip_carto_cartes (titre,url_carte,texte,callage,id_srs) VALUES ('".addslashes($titre)."','".$url_carte."','".addslashes($texte)."','".addslashes($callage)."','".$id_srs."')");
		$id_carte = spip_insert_id();
//		echo "!!!".$id_carte."!!!"; 
	}

	else {
//die(var_dump($titre));
		if ($modif_carte == $id_carte) {
			//TODO : generer automatiquement callage si document spip
			if (!$callage and $titre)
				if ($document_id=intval($url_carte)) {
					//TODO : gerer type image et peut etre mime
					$image = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = $document_id"));
					if ($image) $callage=array2worldfile($image);
				}
			//TODO : passer tout ca en spip_abstract ...
			$query = "UPDATE spip_carto_cartes SET ".
			"titre='".addslashes($titre)."', ".
			"url_carte='".$url_carte."', ".
			"texte='".addslashes($texte)."', ".
			"callage='".addslashes($callage)."', ".
			"id_srs='".intval($id_srs)."' ".
			"WHERE id_carto_carte=$id_carte";

			$result = spip_query($query);
		}
	}
		
}

		

//$flag_mots = "oui";
if ($id_carte) {
			//TODO : passer tout ca en spip_abstract ...
			$query = "SELECT * FROM spip_carto_cartes WHERE id_carto_carte=$id_carte";
			$result = spip_query($query);
			if ($row = spip_fetch_array($result)) {
				$titre = entites_html($row['titre']);
				$url_carte = $row['url_carte'];
				$texte = entites_html($row['texte']);
				$callage = entites_html($row['callage']);
				$id_srs = intval($row['id_srs']);
				$js_titre = "";
			}
		}
		
//
// Modifications des objets de la carte
//

if ($id_carte && $flag_editable) {
	// Ajout d'un objet
	if ($selection_type) {
		$geometrie=coords2wkt($selection_type,$selection_coords, worldfile2array($row['callage']), $url_carte); // Si pas de fonction de zoom le callage correspond bien à la valeur. Sinon avec zoom, il faut passer les coordonnées géographiques de la vue en cours ....
		$objet_titre = _T("spipcarto:objet_nouvel");
		$url_objet = "#";
		//TODO : passer tout ca en spip_abstract ...
		spip_query("INSERT INTO spip_carto_objets (id_carto_carte,titre,geometrie,url_objet) VALUES (".$id_carte.",'".addslashes($objet_titre)."','".$geometrie."','".$url_objet."')");
		$id_objet = spip_insert_id();
		$objet_visible=$id_objet;
		$nouveau=true;
	}
	// Modif d'un objet
	if ($modif_objet) {
		//TODO : passer tout ca en spip_abstract ...
		$query = "UPDATE spip_carto_objets SET ".
			//"id_carto_carte=".intval($id_carte).", ".
			"titre='".addslashes($objet_titre)."', ".
			"texte='".addslashes($objet_texte)."', ".
			"url_objet='".$url_objet."', ".
			"url_logo='".$url_logo."', ".
			"geometrie='".addslashes($geometrie)."' ".
			"WHERE id_carto_objet=$modif_objet";
			$result = spip_query($query);
			$objet_visible=$modif_objet;
	}
	if ($id_objet) {
			$objet_visible=$id_objet;
	}
	

	// Suppression d'un objet
	if ($supp_objet) {
		//TODO : passer tout ca en spip_abstract ...
		$query = "DELETE FROM spip_carto_objets WHERE id_carto_objet=$supp_objet";
		$result = spip_query($query);
	}
	// Suppression de tous les objets de la carte
	if ($supp_objet_all) {
		//TODO : passer tout ca en spip_abstract ...
		$query = "DELETE FROM spip_carto_objets WHERE id_carto_carte=$id_carte";
		$result = spip_query($query);
	}
}



$param="id_carte=".$id_carte;
if ($retour) $param.='&retour='.$retour;
$carte_link = generer_url_ecrire("cartes_edit",$param);
$carte_supplink = generer_url_ecrire("cartes_edit",$param.'&supp_carte='.$id_carte);
$carte_importlink = generer_url_ecrire("cartes_import",$param);
$carte_suppallobjlink = generer_url_ecrire("cartes_edit",$param.'&supp_objet_all=ok');

//
// Affichage de la page
//

debut_page("&laquo; $titre &raquo;", "documents", "cartes");

debut_gauche();

debut_boite_info();

echo "<div align='center'>\n";

echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('spipcarto:carte_numero')."</b></font>\n";
echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_carte</b></font>\n";
if ($url_carte && ($new!="oui")) {
	echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('spipcarto:carte_apercu')."</b></font>\n";
	$sq_cartes=$GLOBALS['sq_cartes'];
	if (!is_array($sq_cartes)) $sq_cartes=array('map');
	foreach ($sq_cartes as $sq_carte)
		icone_horizontale( _T('spipcarto:carte_'.$sq_carte), "../carto.php?id_carto_carte=".$id_carte."&fond=".$sq_carte."&var_mode=recalcul", "racine-24.gif", "rien.gif");
}
echo "</div>\n";

fin_boite_info();



if ($new!="oui") {
	# modifs de la description d'un des docs joints
	maj_documents($id_carte, 'carto_carte');
	
	# affichage
	afficher_documents_colonne($id_carte, 'carto_carte', true);

	global $connect_statut;
	if (($connect_statut == "0minirezo")&&(in_array('mots_partout',liste_plugin_actifs()))) {
		debut_cadre_relief("mot-cle-24.gif");
		$mp_retour=$carte_link;
		icone_horizontale(_T('motspartout:titre_page'),generer_url_ecrire("mots_partout","nom_chose=carto_objets&limit=carto_cartes&id_limit=".$id_carte."&retour=".urlencode($mp_retour)), "mot-cle-24.gif");
		fin_cadre_relief();
	}
}

debut_droite();

if ($supp_carte && !$supp_confirme && !$supp_rejet) {
	echo "<p><strong>"._T("spipcarto:carte_warning")."</strong> ";
	echo _T("spipcarto:carte_supp_confirm")."</p>\n";

	echo "<form method='post' action='".generer_url_ecrire('cartes_edit')."'>";
	if ($retour)
		echo '<input type="hidden" name="retour" value="'.$retour.'">'; 
	echo '<input type="hidden" name="id_carte" value="'.$id_carte.'">'; 
	echo '<input type="hidden" name="suppcarte" value="'.$id_carte.'">'; 
	echo "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondl'>";
	echo " &nbsp; ";
	echo "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondl'>";
	echo "</form><br />\n";
}


if ($id_carte) {
	debut_cadre_relief("../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
	echo "<br/>";
	gros_titre($titre);
	
	if ($texte) {
		echo "<p><div align='left' border: 1px dashed #aaaaaa;'>";
		echo "<strong class='verdana2'>"._T('info_descriptif')."</strong> ";
		echo propre($texte);
		echo "</div>\n";
	}
	
	if ($url_carte!="") {
		
		echo "<br />";
		debut_cadre_relief();
		
		echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>";
		echo bouton_block_visible("dhtml_carte");
		echo "<strong class='verdana3' style='text-transform: uppercase;'>&nbsp;"._T("spipcarto:objet_add")."</strong>";
		echo "</div>\n";
		
		echo debut_block_visible("dhtml_carte");
				
		
		//echo "<div style='margin: 10px; padding: 10px; border: 1px dashed $couleur_foncee;'>";
		
		//remplacer id par appel à spip_carte
		//TODO : gerer zoom et position
		//if (intval($url_carte)>0) $leurl_carte="../spip_carto.php?fond_carte=".intval($url_carte);
		//else $leurl_carte=$url_carte;
		$lurl_carte="../spip_carto.php?fond_carte=".base64_encode($url_carte);
		$tabcallage=worldfile2array($callage);
//   	echo"<div style=\"position:relative\">";
//   	echo"<div style=\"position:absolute\">";
   		echo afficher_carte_interface($id_carte,$retour,$lurl_carte,$tabcallage, $url_carte);
//   	echo"<hr/>aaa";
//	echo"</div>";
//	echo"</div>";
				
		echo fin_block();
		//echo "</div>\n";
		
		fin_cadre_relief();
	}
	
	//articles liés
	afficher_articles(_T("spipcarto:carte_articles_use"),
		", spip_carto_cartes_articles AS lien WHERE lien.id_article=articles.id_article ".
		"AND id_carto_carte=$id_carte AND statut!='poubelle' ORDER BY titre");
	
	
	fin_cadre_relief();
}


//
// Icones retour et suppression
//
if ($retour) {
	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "rien.gif");
	echo "</div>\n";
}
if ($id_carte && carte_administrable($id_carte)) {
	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";
	icone(_T("spipcarto:carte_supp"), 	$carte_supplink, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "supprimer.gif");
	echo "</div>\n";
}


//
// Edition des donnees de la carte
//
if ($flag_editable) {
	echo "<p>";
	debut_cadre_formulaire();

	echo "<div class='verdana2'>";
  echo "<form method='get' action='".generer_url_ecrire('cartes_edit',"")."'>";
	echo '<input type="hidden" name="exec" value="cartes_edit">'; 
	echo '<input type="hidden" name="id_carte" value="'.$id_carte.'">'; 
	echo '<input type="hidden" name="modif_carte" value="'.$id_carte.'">'; 
	if ($retour)
		echo '<input type="hidden" name="retour" value="'.$retour.'">'; 
	
	echo "<strong><label for='titre_carte'>"._T("spipcarto:carte_titre")."</label></strong> "._T('info_obligatoire_02');
	echo "<br />";
	echo "<input type='text' name='titre' id='titre_carte' CLASS='formo' ".
		"value=\"".$titre."\" size='40'$js_titre><br />\n";

	echo "<strong><label for='desc_carte'>"._T('info_texte')."</label></strong>";
	echo "<br />";
	echo "<textarea name='texte' id='desc_carte' CLASS='forml' rows='4' cols='40' wrap='soft'>";
	echo $texte;
	echo "</textarea><br />\n";

	if ($new=="oui") {
		echo "<input type='hidden' name='url_carte' id='url_carte_carte' ".
			"value=\"".$url_carte."\" size='40'>";
		
		echo "<textarea name='callage' id='callage_carte' style='visibility:hidden;'>";
		echo $callage;
		echo "</textarea>";

		afficher_srs ();

		/*echo "<input type='hidden' name='id_srs' id='srs_carte' ".
			"value=\"".$id_srs."\" size='40'>";*/
	}
	else {
		echo "<strong><label for='url_carte_carte'>"._T("spipcarto:carte_fond")."</label></strong> "._T('info_obligatoire_02'). "<br />"._T("spipcarto:carte_fond_numero");
		echo "<br />";
		echo "<input type='text' name='url_carte' id='url_carte_carte' CLASS='formo' ".
			"value=\"".$url_carte."\" size='40'><br />\n";
		
		echo "<strong><label for='callage_carte'>"._T("spipcarto:carte_callage")."</label></strong>";
		echo "<br />";
		echo "<textarea name='callage' id='callage_carte' CLASS='forml' rows='4' cols='40' wrap='soft'>";
		echo $callage;
		echo "</textarea><br />\n";


		afficher_srs ($id_srs);
		/*echo "<strong><label for='srs_carte'>"._T("spipcarto:carte_srs")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='id_srs' id='srs_carte' CLASS='formo' ".
			"value=\"".$id_srs."\" size='40'><br />\n";*/
	}
	
	
	echo "<div align='right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	echo "</form>";

	//
	// Modifier / supprimer les objets
	//
	if ($new!="oui") {
		echo "<a name='objets'></a>";
		echo "<p><hr><p>\n";
		echo "<div class='verdana3'>";
		echo "<strong>"._T("spipcarto:carte_objets")."</strong><br />\n";
		echo _T("spipcarto:carte_import");
		echo "</div>\n";

		echo "<br />\n";
		echo "<div align='$spip_lang_right'>";
		// Icone importer des objets
		icone(_T("spipcarto:carte_import_objet"), $carte_importlink, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "creer.gif");
		echo "</div>\n";
	}

	$query = "SELECT * FROM spip_carto_objets WHERE id_carto_carte=$id_carte";
	$result = spip_query($query);
	
	if (spip_num_rows($result)>0) {
		// Icone supprimer tous les objets de la carte
		echo "<div align='$spip_lang_right'>";
		icone(_T("spipcarto:carte_supp_objets"), $carte_suppallobjlink, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "supprimer.gif");
		echo "</div>\n";
		
//map ?
//		$laMap='<map name="map1">';

		while ($t = spip_fetch_array($result)) {
	
			$id_objet= $t['id_carto_objet'];
			$titre_objet= entites_html($t['titre']);
			$lien_objet= $t['url_objet'];
			$texte_objet= entites_html($t['texte']);
			$url_logo = $t['url_logo'];
			$geometrie = entites_html($t['geometrie']);
	
			$visible = ($id_objet == $objet_visible);
			if ($nouveau&&$visible) echo "<a name='nouveau_objet'></a>";
			else if ($visible) echo "<a name='objet_visible'></a>";
			echo "<a name='objet$id_objet'></a><p>\n";
			debut_cadre_relief();
				
  echo "<form method='post' action='".generer_url_ecrire("cartes_edit","#objet_visible")."'>";
  echo '<input type="hidden" name="id_carte" value="'.$id_carte.'">'; 
  $mlink='supp_objet='.$id_objet.'&id_carte='.$id_carte;
  if ($retour) {
  	$mlink.='&retour='.$retour;
  	echo '<input type="hidden" name="retour" value="'.$retour.'">'; 
  }
  echo '<input type="hidden" name="modif_objet" value="'.$id_objet.'">'; 
  echo '<input type="hidden" name="id_carte" value="'.$id_carte.'">'; 

				echo "<table>";
					echo "<tr valign='top'><td>";
					echo "<strong>".$titre_objet."</strong>";
					echo "</td><td width='20px'></td><td>";					
					echo "<br/><div style='float:right;'>";
					$link = generer_url_ecrire('cartes_edit',$mlink);
					icone_horizontale(_T("spipcarto:objet_supp"), $link , "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "supprimer.gif");
					echo "</div>\n";
					echo "</td></tr>";					
					echo "<tr><td>";
					if ($nouveau) {
						echo "<script type='text/javascript'><!-- \nvar antifocus_champ = false; // --></script>\n";
						$js = " onfocus=\"if(!antifocus_champ){this.value='';antifocus_champ=true;}\"";
					}
					else $js = "";
					echo "<strong><label for='titre_$id_objet'>"._T("spipcarto:objet_titre")."</label></strong> :";
					echo "<br />";
					echo "<input type='text' name='objet_titre' id='titre_$id_objet' value=\"".
						$titre_objet."\" class='fondo verdana2' size='30'$js><br />\n";
					//bloc_edition_objet($t, $carte_link);
					
					echo "<strong><label for='desc_$id_objet'>"._T('info_texte')."</label></strong>";
					echo "<br />";
					echo "<textarea name='objet_texte' id='desc_$id_objet' CLASS='forml' rows='4' cols='25' wrap='soft'>";
					echo $texte_objet;
					echo "</textarea><br />\n";
		
					echo "</td><td width='20px'></td><td>";					
					echo "<strong><label for='lien_$id_objet'>"._T("spipcarto:objet_lien")."</label></strong> :";
					echo "<br />";
					echo "<input type='text' name='url_objet' id='lien_$id_objet' value=\"".
						$lien_objet."\" class='fondo verdana2' size='30'$><br />\n";
		
					echo "<strong><label for='logo_$id_objet'>"._T("spipcarto:objet_logo")."</label></strong> :";
					echo "<br />";
					echo "<input type='text' name='url_logo' id='logo_$id_objet' value=\"".
						$url_logo."\" class='fondo verdana2' size='30'$><br />\n";
		
					echo "<strong><label for='geo_$id_objet'>"._T('spipcarto:objet_geom')."</label></strong><br/>";
					echo "<input type='text' name='geometrie' id='geo_$id_objet' class='fondo verdana2' size='30' value='";
					echo $geometrie."'/><br/>\n";
					echo "</td></tr>";
				echo "</table>";
	
				echo "<div align='right'>";
				echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo verdana2'></div>\n";
				echo "</div>\n";
				echo "</form>";
				
				
				// Objet géométrique pour l'affichage DHTML
				$mygeom = wkt2coords($geometrie, "JSDHTML", $callage, $url_carte); 
				$geoArray= explode(',',$mygeom);
				echo "<script type=\"text/javascript\">/*<![CDATA[*/";
		  		echo "geoArray_".$id_objet."=new Array();\n";
		  		for ($i=0; $i<count($geoArray) ; $i=$i+2) {
					echo "var geoPoint_".$id_objet."_".($i/2)."=new Array(".$geoArray[$i].",".$geoArray[$i+1].");\n";
					echo "geoArray_".$id_objet."[".($i/2)."]=geoPoint_".$id_objet."_".($i/2).";\n";
		  		}
		  		$geoTypex= explode('(',$geometrie);
		  		$geoType= $geoTypex[0];

		  		switch ($geoType){
		  			case 'circle':
		  			case 'CIRCLE':
		  						$geoType='circle';
		  						break;
		  			case 'rect':
		  			case 'RECT':
		  			case 'rectangle':
		  			case 'RECTANGLE':
		  						$geoType='rect';
		  						break;
		  			case 'line':
		  			case 'LINE':
		  						$geoType='line';
		  						break;
		  			case 'point':
		  			case 'POINT':
		  						$geoType='point';
		  						break;
					case 'poly':
		  			case 'POLY':
		  			case 'polygon':
		  			case 'POLYGON':
		  			default:
		  						$geoType='poly';
		  						break;
		  		}
				
				echo $geoType."s[".$geoType."count++]=geoArray_".$id_objet.";";
      			echo "</script>";
//map ?
//				$laMap.='<area shape="'.wkt2shape($geometrie,"HTML").'" coords="'.wkt2coords($geometrie,"HTML",$callage,$url_carte).'" href="#objet'.$id_objet.'" alt="'.$titre_objet.'"/>';				
				if ($flag_mots!='non' AND $options == 'avancees') {
					$tab_id['id_carte']=$id_carte;
					$tab_id['id_objet']=$id_objet;
					//TODO : passer les ,ouceau, cherche et supp que si bon mot
					((!$nouveau) && ($visible)) ? formulaire_mots('carto_objets', $tab_id, $nouv_mot, $supp_mot, $cherche_mot, $flag_editable): formulaire_mots('carto_objets', $tab_id, null, null, null, $flag_editable);
				}
				
				fin_cadre_relief();
			}
		}
	
//map ?
//	$laMap.='</map>';
	echo "</div>\n";
//map ?
//	echo $laMap;
	fin_cadre_formulaire();
}


fin_page();
}
?>
