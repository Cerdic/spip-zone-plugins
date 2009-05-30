<?php
/*
 * Created on 27 mars 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 function exec_gmaps()
 {
 	global $spip_lang_right;
		include_spip("inc/presentation");
	include_spip('public/assembler');

	//Rendu de la page
	debut_page(_T("gmaps:gmaps"));
	//Colonne de gauche
	debut_gauche();
	debut_boite_info();
		echo "<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"._T('gmaps:generateur_balise').'</div>';
		debut_cadre_relief("../"._DIR_PLUGIN_GMAPS. "images/frame.png",false,"",_T("gmaps:boite_fenetre_titre"));
			echo '<label for="align">'._T("gmaps:boite_fenetre_alignement").'</label>';
			echo '<select id="align" onchange="updatetag()">';
			$align=array('none','left','right');
			foreach($align as $al)
			{	echo '<option value="'.$al.'">'._T('gmaps:boite_fenetre_alignement_'.$al).'</option>';	}
			echo '</select><br />';

			echo '<label for="height">'._T("gmaps:boite_fenetre_hauteur").'</label>';
			echo '<input type="text" id="height" onchange="updatetag()" />';

			echo '<label for="width">'._T("gmaps:boite_fenetre_largeur").'</label>';
			echo '<input type="text" id="width" onchange="updatetag()" />';
		fin_cadre_relief();
		echo '<div style="display:none">';
			debut_cadre_relief("../"._DIR_PLUGIN_GMAPS. "images/debug.png",false,"","Debug");
				echo '<label for="zoom">Zoom :</label>';
				echo '<input type="text" id="zoom" onchange="updatetag()" />';

				echo '<label for="type">Type :</label>';
				echo '<input type="text" id="type" onchange="updatetag()" />';

				echo '<br /><label for="gmap_insert">Pts selectionn&eacute;s :</label>';
				echo '<div id="gmap_insert"></div>';
			fin_cadre_relief();
		echo '</div>';
		debut_cadre_relief("../"._DIR_PLUGIN_GMAPS. "images/markerOff.png",false,"",_T("gmaps:boite_points_titre"));
			echo '<input type="radio" name="ptstype" id="ptstype2" value="select" checked="checked" onclick="updatetag()" /><label for="ptstype2">'._T('gmaps:boite_points_type_select').'</label><br />';
			echo '<input type="radio" name="ptstype" id="ptstype3" value="rubrique" onclick="updatetag()" /><label for="ptstype3">'._T('gmaps:boite_points_type_rubrique').'</label><br />';
			echo '<input type="radio" name="ptstype" id="ptstype1" value="tous" onclick="updatetag()" /><label for="ptstype1">'._T('gmaps:boite_points_type_tous').'</label><br />';
		fin_cadre_relief();
		echo '<br />';
		debut_boite_info();
			echo '<p>'._T('gmaps:code_insert').'</p>';
			echo '<textarea id="balisegmap" style="width:98%;height:30px">&lt;gmap&gt;</textarea>';
		fin_boite_info();
	fin_boite_info();
	echo '<br />';
	//Colonne de droite (si mode écran large, sinon en dessous de colonne de gauche)
	creer_colonne_droite();
	debut_cadre_relief();
	echo _T("gmaps:infos_droite");
	fin_cadre_relief();
	echo '<br />';
	echo icone(_T("gmaps:apikey_button"), "javascript:googlekey()", "../"._DIR_PLUGIN_GMAPS. "images/key.png", "edit.gif");

	//Partie Centrale
	debut_droite();
	debut_cadre_couleur();
	echo '<div id="map" style="height:350px"></div>';
	fin_cadre_couleur();
	echo fin_gauche();
	echo fin_page();
}
?>
