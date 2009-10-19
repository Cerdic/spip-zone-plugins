<?php

/**
*
 * Plugin « Puce active pour les articles syndiqués»
 * Licence GNU/GPL
 * 
  */
  
if (!defined("_ECRIRE_INC_VERSION")) return;

require _DIR_RESTREINT . 'inc/afficher_objets.php';


// Surcharge de la fonction inc_afficher_objets_dist
// Déviation vers la fonction afficher_objet_boucle_paas lorsque la variable $type est égale à syndic_article
function inc_afficher_objets($type, $titre,$requete,$formater='', $force=false){

	if ($afficher = charger_fonction("afficher_{$type}s",'inc',true)){
		return $afficher($titre,$requete,$formater);
	}

	if (($GLOBALS['meta']['multi_rubriques'] == 'oui'
	     AND (!isset($GLOBALS['id_rubrique'])))
	OR $GLOBALS['meta']['multi_articles'] == 'oui') {
		$afficher_langue = true;

		if (isset($GLOBALS['langue_rubrique'])) $langue_defaut = $GLOBALS['langue_rubrique'];
		else $langue_defaut = $GLOBALS['meta']['langue_site'];
	} else $afficher_langue = $langue_defaut = '';

	$arg = array($afficher_langue, false, $langue_defaut, $formater, $type,id_table_objet($type));
	if (!function_exists($skel = "afficher_{$type}s_boucle")){
		if ($type == 'syndic_article' AND ( _SPIP_AJAX)){					/* Modif ici*/
		$skel = "afficher_objet_boucle_paas";
		} else {
		$skel = "afficher_objet_boucle";}		
	}

	$presenter_liste = charger_fonction('presenter_liste', 'inc');
	$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
	$styles = array(array('arial11', 7), array('arial11'), array('arial1'), array('arial1'), array('arial1 centered', 100), array('arial1', 38));

	$tableau = array(); // ne sert pas ici
	
	return $presenter_liste($requete, $skel, $tableau, $arg, $force, $styles, $tmp_var, $titre, icone_table($type));
}

// Modification de la fonction afficher_objet_boucle appelé ci-dessus
// Suppression de l'appel à la fonction afficher_numero_edit pour ne pas faire apparaître le lien de modification de statut.
//Cela  a pour conséquence de supprimer la colonne de droite dans la présentation des articles syndiqués.
function afficher_objet_boucle_paas($row, $own)
{
	global $connect_statut, $spip_lang_right;
	static $chercher_logo = true;

	list($afficher_langue, $affrub, $langue_defaut, $formater,$type,$primary) = $own;
	$vals = array();
	$id_objet = $row[$primary];
	if (autoriser('voir',$type,$id_objet)){

		$date_heure = isset($row['date'])?$row['date']:(isset($row['date_heure'])?$row['date_heure']:"");

		$statut = isset($row['statut'])?$row['statut']:"";
		if (isset($row['lang']))
		  changer_typo($lang = $row['lang']);
		else $lang = $langue_defaut;
		$lang_dir = lang_dir($lang);
		$id_rubrique = isset($row['id_rubrique'])?$row['id_rubrique']:0;

		$puce_statut = charger_fonction('puce_statut', 'inc');
		$vals[] = $puce_statut($id_objet, $statut, $id_rubrique, $type);

		list($titre,$suite) = afficher_titre_objet($type,$row);
		$flogo = '';
		if ($chercher_logo) {
			if ($chercher_logo !== true
			    OR $chercher_logo = charger_fonction_logo_if())
			  if ($logo = $chercher_logo($id_objet, $primary, 'on')) {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$flogo = "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			  }
		}
		if ($titre) {
			$titre = "<a href='"
			.  lien_voir_objet($type,$primary,$id_objet)
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

		$vals[] = afficher_complement_objet($type,$row);

		$s = "";
		if ($affrub && $id_rubrique) {
			$rub = sql_fetsel("id_rubrique, titre", "spip_rubriques", "id_rubrique=$id_rubrique");
			$id_rubrique = $rub['id_rubrique'];
			$s .= "<a href='" . generer_url_ecrire("naviguer","id_rubrique=$id_rubrique") . "' style=\"display:block;\">".typo($rub['titre'])."</a>";
		} else
		if ($statut){
			if ($statut != "prop")
					$s = affdate_jourcourt($date_heure);
				else
					$s .= _T('info_a_valider');
		}
		$vals[] = $s;
										// Ligne supprimée ici
	}
	return $vals;
}


?>