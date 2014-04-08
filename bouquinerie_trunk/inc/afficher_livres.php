<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// ces fonctions seront redondantes avec la prochaine API de spip
// TODO spip 2.0 : supprimer ce fichier pour utiliser l'API objet de spip

// Compatibilite 1.9.3
if (version_compare($GLOBALS['spip_version_code'],'2.0000','<')) {
	include_spip('inc/bouq_presenter_liste');
}


function inc_afficher_livres_dist($titre,$requete,$formater='', $force=false){

	$afficher_langue = '';
	$langue_defaut = '';
	set_langue(&$afficher_langue,&$langue_defaut);

	$arg = array( $afficher_langue, false, $langue_defaut);

	$skel = "afficher_livre_boucle";

	if (version_compare($GLOBALS['spip_version_code'],'2.0000','<'))
		$presenter_liste = charger_fonction('bouq_presenter_liste', 'inc');
	else 
		$presenter_liste = charger_fonction('presenter_liste', 'inc');

	$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
	$styles = array(array('arial11', 7), array('arial11'), array('arial1'), array('arial1'), array('arial1 centered', 100), array('arial1', 38));
 	
	$tableau = array(); // ne sert pas ici
//	return $presenter_liste($requete, $skel, $tableau, $arg, $force, $styles, $tmp_var, $titre, icone_table($type)); pour ajouter une icone
	return $presenter_liste($requete, $skel, $tableau, $arg, $force, $styles, $tmp_var, $titre);
}

function afficher_livre_boucle($row, $own='') {


	$vals = array();

	$id_livre = $row['id_livre'];

	global $connect_statut, $spip_lang_right;
	static $chercher_logo = true;
	list($type,$primary,$afficher_langue, $affrub, $langue_defaut) = $own;

	$date_heure = isset($row['date'])?$row['date']:(isset($row['date_heure'])?$row['date_heure']:"");

	if (isset($row['lang']))
		changer_typo($lang = $row['lang']);
	else $lang = $langue_defaut;
	$lang_dir = lang_dir($lang);


	$vals[] = '<a href="'.generer_url_ecrire("livres_edit",'id_livre='.$row['id_livre']).'"'.
		' title="'._T('bouq:modifier').'">'.
		'<img src="'.find_in_path('images/modifier.png').'"></a>';

//	list($titre,$suite) = afficher_titre_objet($type,$row);
	$titre = isset($row['titre'])?$row['titre']:_T('info_sans_titre_2');

	if ($titre) {
		$titre = "<a href='"
		.  generer_url_ecrire("livres","id_livre=$id_livre")
		.  "'>"
		. $titre
		. "</a>";
	}
	$vals[] = "\n<div>$flogo$titre$suite</div>";


	$s = "";
	if ($afficher_langue){
		if (isset($row['langue_choisie'])){
			$s .= " <span class='spip_xx-small' style='color: #666666' dir='$lang_dir'>";
			if ($row['langue_choisie'] == "oui") $s .= "<b>".traduire_nom_langue($lang)."</b>";
			else $s .= "(".traduire_nom_langue($lang).")";
			$s .= "</span>";
		}
		elseif ($lang != $langue_defaut)
			$s .= " <span class='spip_xx-small' style='color: #666666' dir='$lang_dir'>".
				($lang
				? "(".traduire_nom_langue($lang).")"
				: ''
			)
			."</span>";
	}
	$vals[] = $s;

	$vals[] = '<b>N°'.$id_livre.'</b>';

	return $vals;
}

?>
