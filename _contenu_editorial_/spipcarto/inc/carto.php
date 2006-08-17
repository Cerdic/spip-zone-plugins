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

//
// Ce fichier ne sera execute qu'une fois
if (defined("_ECRIRE_INC_CARTE")) return;
define("_ECRIRE_INC_CARTE", "1");


// Conversion coordonnées interface DHTML -> WKT
// 	- Modification des séparateurs décimaux & séparateurs de paires de coordonnées
// 	- Calcul des Y par rapport au coin inférieur
//  TODO : Calcul des coordonnées dans l'espace (pas de reprojection en terme de SIG)
function coords2wkt(	$selection_type,
						$selection_coords, 
						$callageGeo = "", 
						$url_carte){
	$GeoWidth 	= ($callageGeo['bottom_right']['x']-$callageGeo['top_left']['x']);
	$GeoHeight 	= ($callageGeo['bottom_right']['y']-$callageGeo['top_left']['y']);
	
	// Récupérer largeur/hauteur de la carte associée :
	$image = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = $url_carte"));
	$ImgWidth=$image['largeur'];
	$ImgHeight=$image['hauteur'];

	//echo "info : $ImgWidth, $ImgHeight // $GeoWidth, $GeoHeight";

	$coordpair = explode (';',$selection_coords);
	
	for ($i=0; $i<sizeof($coordpair); $i++)
	{		
		$coord[$i] 		= explode(',',$coordpair[$i]);
		$coord[$i][0] 	= $coord[$i][0]*$GeoWidth/$ImgWidth+$callageGeo['top_left']['x'];
		$coord[$i][1] 	= ($ImgHeight-$coord[$i][1])*$GeoHeight/$ImgHeight+$callageGeo['top_left']['y'];		// Calcul de l'Y par rapport au coin inférieur
 		//$coord[$i][1] 	= $coord[$i][1];		// recu et stocké en pixels ... non ok si approche SIG
		$coordpair[$i] 	= implode(' ',$coord[$i]);
	}
	$wktCoords = implode(',', $coordpair);
	
	return $selection_type."(".$wktCoords.")";			// Géométrie au format WKT
}

// TODO : Faut il rendre fixe la taille de l'interface DHTML ?

function embed_carto_url($id_carte, $url_parse) {
	$lURL = $url_parse['path']."/carto.php?id_carte=".$id_carte."&scale=".$_GET['scale']."&x=".$_GET['x']."&y=".$_GET['y'];
	return $lURL;
}
function embed_fond_carte_url($fond_carte) {
	$lURL = "../spip_carto.php?fond_carte=".$fond_carte."&scale=".$_GET['scale']."&x=".$_GET['x']."&y=".$_GET['y'];
	return $lURL;
}

function afficher_cartes($titre_table, $requete, $icone = '') {
	global $connect_id_auteur, $connect_statut;
	global $spip_lang_right, $couleur_claire, $spip_lang;

	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'] ? $requete['FROM'] : 'spip_articles AS articles';
	$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
	$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
	$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
	$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
	$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';

	$cpt = "$from$where";
	$tmp_var = substr(md5($cpt), 0, 4);
	
	$res = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
	$cpt = $res['n'];

	if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);

	$nb_aff = 1.5 * _TRANCHES;
	$deb_aff = intval(_request('t_' .$tmp_var));
	if ($cpt > $nb_aff) {
		$nb_aff = (_TRANCHES); 
		$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
	}
			
	if (!$icone) $icone = "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.png";

	if ($titre_table) echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";

	echo $tranches;

 	$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
	$num_rows = spip_num_rows($result);

	$ifond = 0;
	$premier = true;
	
	$compteur_liste = 0;

	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
	while ($row = spip_fetch_array($result)) {
		$id_carte= $row['id_carto_carte'];
		$objets= $row['objets'];
		$titre = $row['titre'];
		
		$link = generer_url_ecrire("cartes_edit","id_carte=".$id_carte."&retour=".urlencode(generer_url_ecrire("cartes")));
		if ($objets) {
			$puce = 'puce-verte-breve.gif';
		}
		else {
			$puce = 'puce-orange-breve.gif';
		}

		echo "<tr class='tr_liste'><td class=\"arial11\">";
		echo "<a href=\"".$link."\">";
		echo  "<img src='img_pack/$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
		echo  typo($titre);
		echo "</a></td><td>";
		
		//articles liés
		afficher_articles(_T("spipcarto:carte_articles_use"),
			array(
				"FROM"=>"spip_articles AS articles, spip_carto_cartes_articles AS lien",
				"WHERE"=>"lien.id_article=articles.id_article AND id_carto_carte=$id_carte AND statut!='poubelle'",
				"ORDER BY"=>"titre"));
		
		echo "</a></td></tr>";
		
	}
	spip_free_result($result);
	
	echo "</table>";
	echo "</div>\n";

	return;
}


function afficher_carte_interface($id_carte,$retour,$fichier,$callage, $id_img = "-1") {
	
	$widthgeo=$callage['bottom_right']['x']-$callage['top_left']['x'];
	$heightgeo=$callage['top_left']['y']-$callage['bottom_right']['y'];
	
	//TODO : Possibilités :
	// - vue non zoomable de la taille de l'image uploadé (Actuel)
	// - vue zoomable largeur fixe, hauteur variable , ...
	
	// Récupérer largeur/hauteur de la carte associée :
	$image = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = $id_img"));
	
	if ($image) {
		$width=$image['largeur'];
		$height=$image['hauteur'];
	}else{
		$width=400;
		$height=300;
	};
	
	$returned=' <table border="0">
  <tr> 
   <td colspan="7"><script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/spipcarto/js/x_core_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/spipcarto/js/x_dom_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/spipcarto/js/x_event_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/spipcarto/js/navTools.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/spipcarto/js/graphTools.js"></script>
<form method="post" action="#nouveau_objet" name="carto_form">
 <input type="hidden" name="exec" value="cartes_edit"/>
 <input type="hidden" name="id_carte" value="'.$id_carte.'"/>
 <input type="hidden" name="retour" value="'.$retour.'"/>
 <input type="hidden" name="selection_type" />
 <input type="hidden" name="selection_coords" />
<script type="text/javascript">
/*<![CDATA[*/ 
    var dhtmlDivs = new String();
    document.image = new Image;
    document.image.src = \''.$fichier.'\';
    document.image.usemap=\'#map'.$id_carte.'\';
//   if (xIE) {
      dhtmlDivs = \'<div id="mapImageDiv" class="dhtmldiv" style="background-image:url(\'; 
      dhtmlDivs += document.image.src;
      dhtmlDivs += \');visibility:hidden;background-repeat:no-repeat;"></div>\';
/*    } else {
      dhtmlDivs = \'<div id="mapImageDiv" class="dhtmldiv" style="visibility:hidden"><img \';
      dhtmlDivs += \'src="\' + document.image.src + \'" alt="Main map" title="" \';
      dhtmlDivs += \'width="'.$width.'px" height="'.$height.'px" /></div>\';    
    }
*/
    dhtmlDivs += \'<div id="myCanvasDiv" class="dhtmldiv"></div>\';
    dhtmlDivs += \'<div id="myCanvas2Div" class="dhtmldiv"></div>\';
    dhtmlDivs += \'<div id="myCanvas3Div" class="dhtmldiv"></div>\';
    dhtmlDivs += \'<div id="mainDHTMLDiv" class="dhtmldiv"></div>\';
    dhtmlDivs += \'<div id="diplayContainerDiv" class="dhtmldiv">\';
    dhtmlDivs += \'<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>\';
    dhtmlDivs += \'<td width="50%"><div id="displayCoordsDiv" class="dhtmlDisplay"></div></td>\';
    dhtmlDivs += \'<td align="right" width="50%"><div id="displayMeasureDiv" class="dhtmlDisplay"></div></td>\';
    dhtmlDivs += \'</tr></table></div>\';
    document.write(dhtmlDivs);
                  
    var polys= new Array();
    var polycount= 0;
    var rects= new Array();
    var rectcount= 0;
    var lines= new Array();
    var linecount= 0;
    var circles= new Array();
    var circlecount= 0;
    var points= new Array();
    var pointcount= 0;
    	
    function dboxDrawObj (dhtmlBox){
	  for (var i=0; i<polys.length; i++) dhtmlBox.drawPoly(polys[i],1);
	  for (var i=0; i<rects.length; i++) dhtmlBox.drawRect(rects[i],1);
	  for (var i=0; i<lines.length; i++) dhtmlBox.drawLine(lines[i],1);
	  for (var i=0; i<circles.length; i++) dhtmlBox.drawCircle(circles[i],1);
	  for (var i=0; i<points.length; i++) dhtmlBox.drawPoint(points[i],1);
	}
    
    function dboxInit()
    {
      myform = document.forms[\'carto_form\'];
      // DHTML drawing and navigating tools
      dhtmlBox = new dhtmlBox();
          
      //DHTML parameters
      dhtmlBox.dispPos = \'bottom\';
      dhtmlBox.thickness = 2;
      dhtmlBox.cursorsize = 4;
      dhtmlBox.jitter = 10; // minimum size of a box dimension
      dhtmlBox.d2pts = 3;   // the distance between two points (measure tools);
      dhtmlBox.nbPts = 5;   // number of points for the last vertex
      
      // map units values
	  // dynamically given by the server
      dhtmlBox.mapHeight = '.$heightgeo.';
      dhtmlBox.boxx = '.$callage['top_left']['x'].';
      dhtmlBox.boxy = '.($callage['bottom_right']['y']).';
      dhtmlBox.pixel_size = '.$widthgeo/$width.';
      dhtmlBox.dist_msg = \'Approx. distance: \';
      dhtmlBox.dist_unit = \' m.\';
      dhtmlBox.surf_msg = \'Approx. surface: \';
      dhtmlBox.surf_unit = \' mÂ².\';
      dhtmlBox.coord_msg = \'Coords (m): \';
  
          
      dhtmlBox.initialize();
	  dboxDrawObj (dhtmlBox);
    }

    window.onload = function() {
      dboxInit();
      xHide(xGetElementById(\'mapAnchorDiv\')); 
    }
	/*]]>*/
  </script>
	<div id="mapAnchorDiv" style="position:relative; width:'.$width.'px; height:'.$height.'px;"> 
     <table>
      <tr> 
       <td align="center" valign="middle" width="'.$width.'px" height="'.$height.'px">
		<div id="loadbar">'._T('spipcarto:carte_loading').'</div>
	   </td>
      </tr>
     </table>
    </div>
   </td>
  </tr>
  <tr>
   <td colspan="7">&nbsp;</td>
  </tr>
  <tr>
   <td> '._T("spipcarto:carte_draw").'
    <!--<input type="radio" name="tool" value="rectangle,submit,crossHair,zoom_in"  id="zoom_in" onclick="dhtmlBox.changeTool()" />
          zoom_in<br />-->
   </td>
   <td>
    <!--<input type="radio" name="tool" value="point,submit,crossHair,zoom_out"  id="zoom_out" onclick="dhtmlBox.changeTool()" />
          zoom out<br />-->
   </td>
   <td>
    <!--<input type="radio" name="tool" value="pan,submit,move,pan"  id="pan" onclick="dhtmlBox.changeTool()" />
          pan<br />-->
   </td>
   <td>
    <!--<input type="radio" name="tool" value="rectangle,submit,help,query"  id="query" onclick="dhtmlBox.changeTool()" />
          Rectangle<br />-->
   </td>
   <td>
    <input type="radio" name="tool" value="point,submit,crossHair,point"   id="point" onclick="dhtmlBox.changeTool()" />
          '._T('spipcarto:carte_point').'<br />
   </td>
   <td>
    <input type="radio" name="tool" value="line,submit,crossHair,line"   id="line" onclick="dhtmlBox.changeTool()" />
          '._T('spipcarto:carte_line').'<br />
   </td><td><input type="radio" name="tool" value="polygon,submit,crossHair,polygon"   checked="checked"  id="polygon" onclick="dhtmlBox.changeTool()" />
          '._T('spipcarto:carte_polygon').'<br />
   </td>
  </tr>
 </table>
</form>';
	return $returned;
}

function carte_editable() {
	return true;
}
function carte_administrable() {
	return true;
}
//
// Afficher un pave cartes dans la colonne de gauche
// (edition des articles)

function spipcarto_afficher_insertion_carte($id_article) {
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;
	global $clean_link;

	// Ajouter une carte
	echo "\n<p>";
	debut_cadre_relief("../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", false);

	echo "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
	echo bouton_block_invisible("ajouter_carte");
	echo "<strong class='verdana3' style='text-transform: uppercase;'>"
		._T("spipcarto:carte_insert")."</strong>";
	echo "</div>\n";

	echo debut_block_invisible("ajouter_carte");
	echo "<div class='verdana2'>";
	echo _T("spipcarto:carte_insert_texte");
	echo "</div>";

	$cartes_article=array();
	$query = "SELECT id_carto_carte FROM spip_carto_cartes_articles where id_article=".$id_article;
	$result = spip_query($query);
	while($ligne=spip_fetch_array($result)) {
		$cartes_article[]=$ligne[0];
	}
	$query = "SELECT id_carto_carte, titre FROM spip_carto_cartes ORDER BY titre";
	$result = spip_query($query);
	if (spip_num_rows($result)) {
		echo "<br />\n";
		while ($row = spip_fetch_array($result)) {
			$id_carte = $row['id_carto_carte'];
			$inclus=(in_array($id_carte,$cartes_article));
			$titre = typo($row['titre']);
			
$param="id_carte=".$id_carte;
$param.='&retour='.generer_url_ecrire("articles_edit","id_article=".$id_article);
$link = generer_url_ecrire("cartes_edit",$param);

			echo "<div class='verdana3' style='border:1px;background-color: $couleur_claire;'>";
			echo bouton_block_invisible("lien_carte$id_carte");
			echo "<a href='".$link."'>".$titre."</a>\n";
			echo debut_block_invisible("lien_carte$id_carte");
			echo "<div class='arial1' align='$spip_lang_right' style='background-color: white;color: black; padding: 2px;margin: 0px;' ".
				"title=\""._T("spipcarto:carte_raccourci").
				"\">";
			//liste des squelettes disponible : dans mes_options
			$sq_cartes=$GLOBALS['sq_cartes'];
			if (!is_array($sq_cartes)) $sq_cartes=array('map');
			foreach ($sq_cartes as $sq_carte){
				echo "<div align='right' style='color:#333333'";
				if ($inclus) echo "style=\"text-weight: bold;\"> &lt;map".$id_carte."|".$sq_carte."&gt;";
				else echo "><a href=\"javascript:barre_inserer('<map".$id_carte."|".$sq_carte.">',document.formulaire.texte)\">&lt;map".$id_carte."|".$sq_carte."&gt;</a>";
				echo "</div>";
			}

			echo "</div>";
			echo fin_block();
			echo "</div>";
		}
	//	echo "</div>";
	//	echo "</div>";
	}

	// Creer une carte
	if (carte_editable()) {
		echo "\n<br />";
$param="new=oui";
if ($retour) $param.='&retour='.$retour;
$link = generer_url_ecrire("cartes_edit",$param);
		icone_horizontale(_T("spipcarto:carte_creer"),
			$link, "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif", "creer.gif");
	}

	echo fin_block();

	fin_cadre_relief();
}


/*
 * Formulaire d'affichage des SRS 
 * 
 * - Initialisation avec la valeur d'un SRS
 * - 2 options : 
 * 		* Sélection d'un SRS déjà en base
 *  	* Ajout d'un SRS dans la base
 * 
 */
function afficher_srs ($id_srs=''){

	$query = "SELECT * FROM spip_carto_srs order by code asc";
	$result = spip_query($query);
	$nombre_srs = spip_num_rows($result);

	if (!$nombre_srs) return;

	echo "<a name='srs'></a>";
	
	debut_cadre_enfonce("../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif", false, "", _T('spipcarto:carte_srs'));
	
	echo "<select name='id_srs' id='id_srs'>";
	while ($row = spip_fetch_array($result)) {
		$id_carto_srs = $row['id_carto_srs'];
		$label = $row['label'];
		$code = typo($row['code']);
		
		if ($id_carto_srs == $id_srs)
			echo "<option selected value='".$id_carto_srs."'>".typo($label)." (".typo($code) .")"."</option>";
		else
			echo "<option value='".$id_carto_srs."'>".typo($label)." (".typo($code) .")"."</option>";
	}
	echo "</select>";

	fin_cadre_enfonce();

}

?>