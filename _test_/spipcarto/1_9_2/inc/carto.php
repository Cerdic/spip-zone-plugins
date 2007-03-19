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

//
// Ce fichier ne sera execute qu'une fois
if (defined("_ECRIRE_INC_CARTE")) return;
define("_ECRIRE_INC_CARTE", "1");


// Conversion coordonnees interface DHTML -> WKT
// 	- Modification des separateurs decimaux & separateurs de paires de coordonnees
// 	- Calcul des Y par rapport au coin inferieur
//  TODO : Calcul des coordonnees dans l'espace (pas de reprojection en terme de SIG)
function coords2wkt(	$selection_type,
						$selection_coords, 
						$callageGeo = "", 
						$url_carte){
	$GeoWidth 	= ($callageGeo['bottom_right']['x']-$callageGeo['top_left']['x']);
	$GeoHeight 	= ($callageGeo['bottom_right']['y']-$callageGeo['top_left']['y']);
	
	// Recuperer largeur/hauteur de la carte associee :
	$image = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = $url_carte"));
	$ImgWidth=$image['largeur'];
	$ImgHeight=$image['hauteur'];

	//echo "info : $ImgWidth, $ImgHeight // $GeoWidth, $GeoHeight";

	$coordpair = explode (';',$selection_coords);
	
	for ($i=0; $i<sizeof($coordpair); $i++)
	{		
		$coord[$i] 		= explode(',',$coordpair[$i]);
		$coord[$i][0] 	= $coord[$i][0]*$GeoWidth/$ImgWidth+$callageGeo['top_left']['x'];
		$coord[$i][1] 	= ($ImgHeight-$coord[$i][1])*$GeoHeight/$ImgHeight+$callageGeo['top_left']['y'];		// Calcul de l'Y par rapport au coin inferieur
 		//$coord[$i][1] 	= $coord[$i][1];		// recu et stocke en pixels ... non ok si approche SIG
		$coordpair[$i] 	= implode(' ',$coord[$i]);
	}
	$wktCoords = implode(',', $coordpair);
	
	return $selection_type."(".$wktCoords.")";			// Geometrie au format WKT
}

// TODO : Faut il rendre fixe la taille de l'interface DHTML ?

function embed_carto_url($id_carte, $url_parse) {
	$lURL = $url_parse['path']."/carto.php?id_map=".$id_carte."&scale=".$_GET['scale']."&x=".$_GET['x']."&y=".$_GET['y'];
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

	$cpt = $from.$where;
	$tmp_var = substr(md5($cpt), 0, 4);
	
	$res = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
	$cpt = $res['n'];
	if ($cpt==0) return;

	if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);

	$nb_aff = 1.5 * _TRANCHES;
	$deb_aff = intval(_request('t_' .$tmp_var));
	if ($cpt > $nb_aff) {
		$nb_aff = (_TRANCHES); 
		$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
	}
			
	if (!$icone) $icone = "../"._DIR_PLUGIN_SPIPCARTO."/img/carte-24.gif";

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
		$statut = $row['statut'];
		
		$link = generer_url_ecrire("carto_cartes_edit","id_carte=".$id_carte."&retour=".urlencode(generer_url_ecrire("carto_cartes")));
		
		$puce=puce_statut_carto_carte($id_carte,$statut);
/*		if ($objets) {
			$puce = 'puce-verte-breve.gif';
		}
		else {
			$puce = 'puce-orange-breve.gif';
		}
*/
		echo "<tr class='tr_liste'><td>".$puce;
		echo "</td><td class=\"arial11\">";
		echo "<a href=\"".$link."\">";
		echo  typo($titre);
		echo "</a></td><td>";
		
		//articles lies
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
	
	//TODO : Possibilites :
	// - vue non zoomable de la taille de l'image uploade (Actuel)
	// - vue zoomable largeur fixe, hauteur variable , ...
	
	// Recuperer largeur/hauteur de la carte associee :
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
   <td colspan="7"><script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/js/x_core_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/js/x_dom_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/js/x_event_nn4.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/js/navTools.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_SPIPCARTO.'/js/graphTools.js"></script>
<form id="carto_form" method="post" action="#nouveau_objet" name="carto_form">
 <input type="hidden" name="exec" value="carto_cartes_edit"/>
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
      dhtmlBox.surf_unit = \' m².\';
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
   <td colspan="7">
   <table width="500"><tr>
   <td>'._T("spipcarto:carte_draw").'
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
   </td>
  </tr>
 </table>
</form><script type="text/javascript">  
		$(\'#bug\').click(function(){
//		$(\'body\').click(\'mouseover\',function(){
//		$(function(){
//		$(document).ready(function(){
//		$(\'#carto_form\').one(\'mouseover\',function(){
//			$(\'div.dhtmldiv\').each(function(){alert($(this).attr(\'id\')+\'/\'+$(this).css(\'top\')+\'/\'+$(this).css(\'left\'))})
			$(\'#mapImageDiv\').css(\'top\',\'40px\').css(\'left\',\'10px\');
			$(\'#myCanvasDiv\').css(\'top\',\'40px\').css(\'left\',\'10px\');
			$(\'#myCanvas2Div\').css(\'top\',\'40px\').css(\'left\',\'10px\');
			$(\'#myCanvas3Div\').css(\'top\',\'40px\').css(\'left\',\'10px\');
			$(\'#mainDHTMLDiv\').css(\'top\',\'40px\').css(\'left\',\'10px\');
			$(\'#diplayContainerDiv\').css(\'top\',\'0px\').css(\'left\',\'250px\');
		});	
</script>
';
	return $returned;
}

function autoriser_carto_carte_administrer_dist($faire, $type, $id, $qui, $opt) {
	//webmestre ?
	return autoriser('defaut', $type, $id, $qui, $opt);
}
function autoriser_carto_carte_modifier_dist($faire, $type, $id, $qui, $opt) {
	//webmestre ?
	return autoriser('defaut', $type, $id, $qui, $opt);
}
function autoriser_carto_carte_joindredocument_dist($faire, $type, $id, $qui, $opt) {
	//webmestre ?
	return autoriser('defaut', $type, $id, $qui, $opt);
}
function autoriser_carto_carte_voir_dist($faire, $type, $id, $qui, $opt) {
	//webmestre ?
	$autorisation = autoriser('defaut', $type, $id, $qui, $opt);
	if (!$autorisation) {
		$s = spip_query("SELECT carte.statut as statut, count(objets.id_carto_objet) as publie_objets " .
				"FROM spip_carto_cartes AS carte LEFT JOIN spip_carto_objets AS objets " .
				"ON (objets.id_carto_carte=carte.id_carto_carte) " .
				"WHERE carte.id_carto_carte=".intval($id)." ".
				"GROUP BY statut");
		$r = spip_fetch_array($s);
		//rubrique visible
		if (($r['statut']=='publie') && ($r['publie']))
			return true;
		else return false;
	}
	else return $autorisation;
}
function carte_editable() {
	return true;
}
function carte_administrable() {
	global $connect_statut,$connect_toutes_rubriques;
	return ($connect_statut=='0minirezo' && $connect_toutes_rubriques);
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
$link = generer_url_ecrire("carto_cartes_edit",$param);

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
$param.='&retour='.generer_url_ecrire("articles_edit","id_article=".$id_article);
$link = generer_url_ecrire("carto_cartes_edit",$param);
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
 * 		* Selection d'un SRS deje en base
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
// http://doc.spip.org/@puce_statut_carto_carte
function puce_statut_carto_carte($id, $statut) {
	global $spip_lang_left, $dir_lang, $connect_statut, $options;
	
	switch ($statut) {
	case 'publie':
		$clip = 2;
		$puce = 'verte';
		$title = _T('info_carto_carte_publie');
		break;
	case 'prepa':
		$clip = 0;
		$puce = 'blanche';
		$title = _T('info_carto_carte_redaction');
		break;
	case 'prop':
		$clip = 1;
		$puce = 'orange';
		$title = _T('info_carto_carte_propose');
		break;
	case 'refuse':
		$clip = 3;
		$puce = 'rouge';
		$title = _T('info_carto_carte_refuse');
		break;
	case 'poubelle':
		$clip = 4;
		$puce = 'poubelle';
		$title = _T('info_carto_carte_supprime');
		break;
	}
	$puce = "puce-$puce.gif";
	
	if ($connect_statut == '0minirezo' AND $options == 'avancees') {
	  // les versions de MSIE ne font pas toutes pareil sur alt/title
	  // la combinaison suivante semble ok pour tout le monde.
	  $titles = array(
			  "blanche" => _T('texte_statut_en_cours_redaction'),
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));
	  $action = "onmouseover=\"montrer('statutdecalcarto_carte$id');\"";
	  $inser_puce = "\n<div class='puce_article' id='statut$id'$dir_lang>"
			. "\n<div class='puce_article_fixe' $action>" .
		  http_img_pack("$puce", "", "id='imgstatutcarto_carte$id' style='margin: 1px;'") ."</div>"
			. "\n<div class='puce_article_popup' id='statutdecalcarto_carte$id' onmouseout=\"cacher('statutdecalcarto_carte$id');\" style=' margin-left: -".((11*$clip)+1)."px;'>\n"
			. afficher_script_statut($id, 'carto_carte', -1, 'puce-blanche.gif', 'prepa', $titles['blanche'], $action)
			. afficher_script_statut($id, 'carto_carte', -12, 'puce-orange.gif', 'prop', $titles['orange'], $action)
			. afficher_script_statut($id, 'carto_carte', -23, 'puce-verte.gif', 'publie', $titles['verte'], $action)
			. afficher_script_statut($id, 'carto_carte', -34, 'puce-rouge.gif', 'refuse', $titles['rouge'], $action)
			. afficher_script_statut($id, 'carto_carte', -45, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'], $action)
		. "</div></div>";
	} else {
		$inser_puce = http_img_pack("$puce", "", "id='imgstatutcarto_carte$id' style='margin: 1px;'");
	}
	return $inser_puce;
}

// http://doc.spip.org/@puce_statut_carto_objet
function puce_statut_carto_objet($id, $statut, $type, $droit) {
	global $spip_lang_left, $dir_lang;

	$puces = array(
		       0 => 'puce-orange-breve.gif',
		       1 => 'puce-verte-breve.gif',
		       2 => 'puce-rouge-breve.gif',
		       3 => 'puce-blanche-breve.gif');

	switch ($statut) {
			case 'prop':
				$clip = 0;
				$puce = $puces[0];
				$title = _T('titre_carto_objet_proposee');
				break;
			case 'publie':
				$clip = 1;
				$puce = $puces[1];
				$title = _T('titre_carto_objet_publiee');
				break;
			case 'refuse':
				$clip = 2;
				$puce = $puces[2];
				$title = _T('titre_carto_objet_refusee');
				break;
			default:
				$clip = 0;
				$puce = $puces[3];
				$title = '';
	}

	$type1 = "statut$type$id"; 
	$inser_puce = http_img_pack($puce, "", "id='img$type1' style='margin: 1px;'");

	if (!$droit) return $inser_puce;
	
	$type2 = "statutdecal$type$id";
	$action = "onmouseover=\"montrer('$type2');\"\n";

	  // les versions de MSIE ne font pas toutes pareil sur alt/title
	  // la combinaison suivante semble ok pour tout le monde.

	return	"<div class='puce_carto_objet' id='$type1'$dir_lang>"
		. "<div class='puce_carto_objet_fixe' $action>"
		. $inser_puce
		. "</div>"
		. "\n<div class='puce_carto_objet_popup' id='$type2' onmouseout=\"cacher('$type2');\" style=' margin-left: -".((9*$clip)+1)."px;'>\n"
		. afficher_script_statut($id, $type, -1, $puces[0], 'prop',_T('texte_statut_propose_evaluation'), $action)
		. afficher_script_statut($id, $type, -10, $puces[1], 'publie',_T('texte_statut_publie'), $action)
	  	. afficher_script_statut($id, $type, -19, $puces[2], 'refuse',_T('texte_statut_refuse'), $action)
		.  "</div></div>";
}
function dupliquer_carte($id_carte){
	$carte_origin = spip_fetch_array(spip_query("SELECT * FROM spip_carto_cartes WHERE id_carto_carte = ".intval($id_carte)));
	spip_abstract_insert("spip_carto_cartes",
						'(url_carte, titre, texte, callage, id_srs)',
						"('".addslashes($carte_origin['url_carte'])."', '".addslashes($carte_origin['titre'])."', '".addslashes($carte_origin['texte'])."', '".addslashes($carte_origin['callage'])."', '".addslashes($carte_origin['id_srs'])."')");
	$new_id=spip_insert_id();
	$r = spip_query("SELECT * FROM spip_carto_objets WHERE id_carto_carte = ".intval($id_carte));
	while ($obj_origin=spip_fetch_array($r)){
		spip_abstract_insert("spip_carto_objets",
						'(id_carto_carte, titre, texte, url_objet, url_logo, geometrie)',
						"(".$new_id.", '".addslashes($obj_origin['titre'])."', '".addslashes($obj_origin['texte'])."', '".addslashes($obj_origin['url_objet'])."', '".addslashes($obj_origin['url_logo'])."', '".addslashes($obj_origin['geometrie'])."')");
	}
	return $new_id;
}

?>