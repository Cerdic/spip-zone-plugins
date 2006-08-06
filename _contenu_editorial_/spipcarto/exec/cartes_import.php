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
function exec_cartes_import() {
include_spip ("inc/carto");
//include_spip ("inc/carto_fonctions");
include_spip ("inc/carto_import");
include_spip ("inc/presentation");
include_spip ("inc/documents");
include_spip ("inc/mots");


// si XML disponible / Activation de l'import GPX
if (extension_loaded ("xml"))
	$xml = true;
else
	$xml = false;
	
//TODO : voir integration d'une option de configuration de Spip
//bof, je prefere attendre d'avoir un systeme de gestion des modules
//meme si il faut le faire ...
//include_ecrire("inc_config.php3");


$id_carte = intval($_REQUEST['id_carte']);
$new =stripslashes($_REQUEST['new']);
$retour=stripslashes($_REQUEST['retour']);
$step=intval($_REQUEST['step']);
$typeImport=stripslashes($_REQUEST['typeImport']);
			
$flag_editable=carte_editable($id_carte);
$id_carte = intval($id_carte);
$flag_mots = lire_meta("carto_mots");
//$flag_mots = "oui";
$nouveau=false;
$dir = 'upload';						// Répertoire où chercher les fichiers


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

		


$param="id_carte=".$id_carte;
if ($retour) $param.='&retour='.$retour;
$carte_link = generer_url_ecrire("cartes_edit",$param);
$carte_importlink = generer_url_ecrire("carte_import",$param);

$param.="supp_carte=".$id_carte;
$carte_supplink = generer_url_ecrire("cartes_edit",$param);




//
// Affichage de la page
//

debut_page("&laquo; $titre &raquo;", "documents", "cartes");

debut_gauche();

if ($new!="oui") {
	# modifs de la description d'un des docs joints
//	maj_documents($id_carte, 'carto_carte');
	
	# affichage
	afficher_documents_colonne($id_carte, 'carto_carte', true);
}

debut_droite();

//
// Importer des objets / Titre
//
if ($id_carte) {
	gros_titre($titre);
	
	
	avertissement_carto_import ();
	
	
	
	//
	// Importer des objets / Formulaire
	//	1.Sélection d'un fichier du répertoire upload
	//	2.Analyse du fichier
	// 	3.Importation
	switch ($step)
	{	case 2:
			debut_cadre_relief("../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
			
			switch ($typeImport)
			{	case "TXT":
					echo "<form>";
					echo "<input type='hidden' name='id_srs' id='srs_carte' ".
							"value=\"".$id_srs."\" size='40'>";
					echo "<input type='hidden' name='id_carte' id='id_carte' ".
					"value=\"".$id_carte."\" size='40'>";
					echo "<input type='hidden' name='typeImport' id='typeImport' ".
									"value=\"".$typeImport."\" size='40'>";
							
				
					//	2.Analyse du fichier texte  
					echo "<strong class='verdana2'>"._T("spipcarto:import_objet_sel_texte") . "</strong><br/>";
					echo "<input type='hidden' name='step' value='3'>";
					echo "<input type='hidden' name='file' value='".$file."'>";
				
					// TODO : Paramètrage du séparateur de champ
					// TODO : Filtrer les champs de type numérique et les autres pour le choix des champs pour les coordonnées
					
					$rec = new csvUtil($dir."/".$file, ";");		// Ouverture du fichier & lecture
					$i = 0;
					$nbcol = $rec->numCols();
					while ($i<$nbcol) {								// Création d'une liste des colonnes du fichiers
							$collist .= "<option value='".$i."'>".$rec->getField(0, $i)."</option>";
							$i++;
					}
					echo "<ul>";
					// Création du formulaire d'importation
					echo "<li>"._T("spipcarto:objet_titre")."<select name='col'>" .
							"<option value='null'>"._T("spipcarto:objet_nouvel")."</option>\n".
							$collist.
							"</select>\n".
							_T("spipcarto:objet_nouvel_default")."</li>";
					
					echo "<li>"._T("info_texte")."<select name='desc'>" .
							"<option value='null'>null</option>\n".
							$collist.
							"</select>\n".
							_L("(null par défaut)")."</li>";
			
					echo "<li>"._T("spipcarto:objet_url").
							"<br/><input id='link_prefix' name='link_prefix' value='' size='6'/>\n" .
							"<select name='link'>" .
							"<option value='null'>null</option>\n".
							$collist.
							"</select>\n".
							"<input id='link_sufixe' name='link_sufixe' value='' size='3'/>\n" .
							_T("spipcarto:objet_lien_default")."</li>";
			
			
					echo "<li>"._T("spipcarto:objet_x").":<select name='x'>" .
							"<option value='0'>0</option>\n".
							$collist.
							"</select>\n".
							_T("spipcarto:objet_coord_default")."</li>";
					
					echo "<li>"._T("spipcarto:objet_y").":<select name='y'>" .
							"<option value='0'>0</option>\n".
							$collist.
							"</select>\n".
							_T("spipcarto:objet_coord_default")."</li>";
					echo "</ul>";
					
					echo "\n  <div align='".$GLOBALS['spip_lang_right']."'><input name='ok_ftp' type='Submit' value='"._T('bouton_suivant')."' class='fondo'></div>";
					echo "</form>";		
			break;
			case "GPX":
				if ($xml){
					echo "<form>";
					echo "<input type='hidden' name='id_srs' id='srs_carte' ".
							"value=\"".$id_srs."\" size='40'>";
					echo "<input type='hidden' name='id_carte' id='id_carte' ".
					"value=\"".$id_carte."\" size='40'>";
					echo "<input type='hidden' name='typeImport' id='typeImport' ".
									"value=\"".$typeImport."\" size='40'>";
							
					//	2.Analyse du fichier GPX  
					// TODO : Lecture du fichier GPX
					// TODO : Parse le fichier 
					// TODO : Choix des attributs (Mapping GPX -> SPIP-CARTO-OBJET)
							
					echo "<strong class='verdana2'>"._T("spipcarto:import_objet_sel_gpx") . "</strong><br/>";
					echo "<input type='hidden' name='step' value='3'>";
					echo "<input type='hidden' name='file' value='".$file."'>";


					/*
					 * Structure d'un waypoint 
						 <time>2003-07-02T10:29:58Z</time>
						 <name>0101</name>
						 <cmt>MONTGERON</cmt>
						 <desc>0101</desc>
						 <sym>Waypoint</sym>
						 <type>Gas Station</type>
						 
						 -> Importer les types via des mots clés ?
					 */
					$collist = "<option value='name'>name</option>";
					$collist .= "<option value='cmt'>cmt</option>";
					$collist .= "<option value='desc'>desc</option>";
					$collist .= "<option value='sym'>sym</option>";
					$collist .= "<option value='time'>time</option>";
					$collist .= "<option value='type'>type</option>";
					
					// Création du formulaire d'importation
							
				}
				
			break;
			}

			fin_cadre_relief();	
		break;
		case 3:
			switch ($typeImport)
			{	case "TXT": 
					// 	3.Importation du fichier texte en base
					debut_cadre_relief("../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
					echo "<strong class='verdana2'>"._T("spipcarto:objet_import") . "</strong> ";
				
					$rec = new csvUtil($dir."/".$file, ";");		// Ouverture du fichier & lecture
					$i = 1;
					$nbErr = 0;
					$nb = $rec->numRows();
					while ($i<$nb) {
							if (!is_null($rec->getField($i,$col)) && 
								is_numeric((float)str_replace($rec->getField($i,$x), ',', '.')) && 
								is_numeric((float)str_replace($rec->getField($i,$y), ',', '.')))		// Champ code non null et x et y numérique
							{	$sql = sprintf ("Insert into spip_carto_objets (id_carto_carte, titre, texte, url_objet, url_logo, geometrie) values 		(%d, '%s', '%s', '%s', '', 'point(%f %f)');\n",
									($carte=="null"?"0":$id_carte),
									($col=="null"?"Nouvel Objet":addslashes ($rec->getField($i,$col))),
									($desc=="null"?"":addslashes ($rec->getField($i,$desc))),
									($link=="null"?"":addslashes ($link_prefix.$rec->getField($i,$link).$link_sufixe)),
									($x=="0"?0:$rec->getField($i, $x)),
									($y=="0"?0:$rec->getField($i, $y))
									);
								//echo $sql;
							}else
								$nbErr++;
							
							$i++;

							spip_query ($sql);
							// TODO : Tous les cas d'erreur 
					}		
					echo "<br/><strong class='verdana2'>"._T("spipcarto:objet_import_nombre"). ($i-$nbErr). "</strong> ";
					fin_cadre_relief();	
							
				break;
				case "GPX":
				if ($xml){
					// TODO : Lecture des WPT et conversion en objet ponctuel
					// TODO : Insertion en base.
					echo $dir."/".$file;
							
				}
				break;	
			}
		break;
		default: 
			// ----------------------
			// Import / Format CSV 
			debut_cadre_relief("../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
			echo "<form>";
			echo "<input type='hidden' name='typeImport' id='typeImport' ".
					"value=\"TXT\" size='40'>";
			echo "<input type='hidden' name='id_srs' id='srs_carte' ".
					"value=\"".$id_srs."\" size='40'>";
			echo "<input type='hidden' name='id_carte' id='id_carte' ".
					"value=\"".$id_carte."\" size='40'>";
			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_visible("IMPORT-TXT");
			echo "<strong>"._T("spipcarto:import_texte")."</strong>";
			echo "<br /></div>";
			echo debut_block_visible("IMPORT-TXT");			
			
			//	1.Sélection d'un fichier du répertoire upload
			$texte_upload = texte_upload_file($dir, '', 'txt');
			if ($texte_upload) {
				echo "<p><div style='color: #505050;'>";
				if ($forcer_document) echo '<input type="hidden" name="forcer_document" value="oui">';
				echo "\n"._T('info_selectionner_fichier')."&nbsp;:<br />";
				echo "\n<select name='file' size='1' class='fondl'>";
				echo $texte_upload;
				echo "\n</select>";
				echo "<input type='hidden' name='dir' id='dir' ".
					"value=\"".$dir."\" size='40'>";
				echo "\n  <div align='".$GLOBALS['spip_lang_right']."'><input name='ok_ftp' type='Submit' value='"._T('bouton_suivant')."' class='fondo'></div>";
			
				echo "</div>\n";
				echo "<input type='hidden' name='step' value='2'";
			}else
				echo "<strong class='verdana2'>"._T("spipcarto:import_no_fichier") . "</strong> ";
				
			echo fin_block();
			fin_cadre_relief();	
			echo "</form>";		
			
			
			// ----------------------
			// Import / Format GPX - GPS
			/*if ($xml){ 
			debut_cadre_relief("carte-24.gif");
			echo "<form>";
			echo "<input type='hidden' name='typeImport' id='typeImport' ".
					"value=\"GPX\" size='40'>";
			echo "<input type='hidden' name='id_srs' id='srs_carte' ".
					"value=\"".$id_srs."\" size='40'>";
			echo "<input type='hidden' name='id_carte' id='id_carte' ".
					"value=\"".$id_carte."\" size='40'>";
			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_invisible("IMPORT-GPS");
			echo "<strong>"._T("spipcarto:import_gpx")."</strong>";
			echo "<br /></div>";
			echo debut_block_invisible("IMPORT-GPS");
			echo "<strong class='verdana2'>"._L("Sélectionner un fichier") . "</strong> ";
			
			$texte_upload = texte_upload_file($dir, '', 'gpx');
			if ($texte_upload) {
				echo "<p><div style='color: #505050;'>";
				if ($forcer_document) echo '<input type="hidden" name="forcer_document" value="oui">';
				echo "\n"._T('info_selectionner_fichier')."&nbsp;:<br />";
				echo "\n<select name='file' size='1' class='fondl'>";
				echo $texte_upload;
				echo "\n</select>";
				echo "<input type='hidden' name='dir' id='dir' ".
					"value=\"".$dir."\" size='40'>";
				echo "\n  <div align='".$GLOBALS['spip_lang_right']."'><input name='ok_ftp' type='Submit' value='"._T('bouton_suivant')."' class='fondo'></div>";
			
				echo "</div>\n";
				echo "<input type='hidden' name='step' value='2'";
			}else
				echo "<strong class='verdana2'>"._T("spipcarto:import_no_fichier") . "</strong> ";
				
			
			echo fin_block();
			fin_cadre_relief();	
			echo "</form>";		
			}*/
			
			
			// ----------------------
			// Import / Format Shapefile
			/*
			echo "<form>";
			echo "<input type='hidden' name='typeImport' id='typeImport' ".
					"value=\"SHP\" size='40'>";
			echo "<input type='hidden' name='id_srs' id='srs_carte' ".
					"value=\"".$id_srs."\" size='40'>";
			echo "<input type='hidden' name='id_carte' id='id_carte' ".
					"value=\"".$id_carte."\" size='40'>";
			debut_cadre_relief("carte-24.gif");
			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_invisible("IMPORT-SHP");
			echo "<strong>"._L("Import fichier ESRI (SHP)")."</strong>";
			echo "<br /></div>";
			echo debut_block_invisible("IMPORT-SHP");
			echo "<strong class='verdana2'>"._L("Sélectionner un fichier.") . "</strong> ";		
			echo fin_block();
			fin_cadre_relief();	
			echo "</form>";		
			*/			
		break;
		
	}
	
	
}


		
//
if ($retour) {
	echo "<br />\n";
	echo "<div align='$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif", "rien.gif");
	echo "</div>\n";
}




fin_page();
}
?>