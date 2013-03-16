<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function simplecal_classement($param_annee, $param_mois, $param_mode, $param_idrub) { 
	if (!empty($param_idrub)){
		$param_idrub = intval($param_idrub);
	} else {
		$param_idrub = 0;
	}
	
	//die("annee='$param_annee' - mois='$param_mois' - mode='$param_mode' - rub='$param_idrub'");
	
	
	$sous_titre = "";
	$nom_mois = array(
		1 => ucfirst(_T('date_mois_1')), 
		2 => ucfirst(_T('date_mois_2')), 
		3 => ucfirst(_T('date_mois_3')), 
		4 => ucfirst(_T('date_mois_4')), 
		5 => ucfirst(_T('date_mois_5')), 
		6 => ucfirst(_T('date_mois_6')), 
		7 => ucfirst(_T('date_mois_7')), 
		8 => ucfirst(_T('date_mois_8')), 
		9 => ucfirst(_T('date_mois_9')), 
		10 => ucfirst(_T('date_mois_10')), 
		11 => ucfirst(_T('date_mois_11')), 
		12 => ucfirst(_T('date_mois_12'))
	);
	
	// Filtres
	$filtre = "";

	$liste_a = simplecal_get_liste_annees($param_idrub);
	if (count($liste_a)>0){
		$filtre .= '<ul id="simplecal-filtres">';
		
		// Restriction a la rubrique ?
		if ($param_idrub != 0){
			$param_rub = "id_rubrique=$param_idrub";
		} else {
			$param_rub = "";
		}
		
		// Lien Tous
		$filtre .= '<li>';
		$actif = (!$param_annee && !$param_mois && !$param_mode);
		if ($actif){
			$filtre .= '<span>'._T('simplecal:tous').'</span>';
		} else {
			$href_tous = generer_url_ecrire("evenements", $param_rub);
			$filtre .= '<a href="'.$href_tous.'">'._T('simplecal:tous').'</a>';
		}
		$filtre .= '<small> ['.simplecal_get_nb_tous($param_idrub).']</small>';
		$filtre .= '</li>';
		
		// Lien A venir
		$filtre .= '<li class="marge-bas1">';
		$actif = (!$param_annee && !$param_mois && $param_mode);
		if ($actif){
			$filtre .= '<span>'._T('simplecal:a_venir').'</span>';
			$sous_titre = _T('simplecal:a_venir');
		} else {
			$tmp = "mode=avenir";
			if ($param_idrub != 0){
				$tmp .= "&".$param_rub;
			}
			$href_avenir = generer_url_ecrire("evenements", $tmp);
			$filtre .= '<a href="'.$href_avenir.'">'._T('simplecal:a_venir').'</a>';
		}
		$filtre .= '<small> ['.simplecal_get_nb_avenir($param_idrub).']</small>';
		$filtre .= '</li>';
		
		// Pour chaque Annee
		foreach ($liste_a as $row){
			$annee = $row['annee'];
			$nb_a = $row['nb'];
			$actif = ($param_annee && $param_annee==$annee && !$param_mois);
			
			$filtre .= '<li>';
			if ($actif) {
				$filtre .= '<span>'.$annee.'</span>';
				$sous_titre = $annee;
			} else {
				$tmp = "annee=".$annee;
				if ($param_idrub != 0){
					$tmp .= "&".$param_rub;
				}
				$href_a = generer_url_ecrire("evenements",$tmp);
				$filtre .= '<a href="'.$href_a.'">'.$annee.'</a>';
			}
			$filtre .= '<small> ['.$nb_a.']</small>';
			
			//---
			$liste_m = simplecal_get_liste_mois($annee, $param_idrub);
			//---
			if (count($liste_m)>0){
				$filtre .= '<ul>';
				
				// Pour chaque Mois
				foreach ($liste_m as $row_m){
					$mois = $row_m['mois'];
					$nb_m = $row_m['nb'];
					$actif = ($param_annee && $param_annee==$annee && $param_mois && $param_mois==$mois);
					
					$filtre .= '<li>';
					if ($actif) {
						$filtre .= '<span>'.$nom_mois[intval($mois)].'</span>';
						$sous_titre = $nom_mois[intval($mois)]." ".$annee;
					} else {
						$tmp = "annee=".$annee."&mois=".$mois;
						if ($param_idrub != 0){
							$tmp .= "&".$param_rub;
						}
						$href_m = generer_url_ecrire("evenements",$tmp);
						$filtre .= '<a href="'.$href_m.'"'.$classe.'>'.$nom_mois[intval($mois)].'</a>';
					}
					$filtre .= '<small> ['.$nb_m.']</small>';
					$filtre .= '</li>';
				}
				$filtre .= "</ul>";
			}
			//---
			$filtre .= '</li>';
		}
		$filtre .= "</ul>";
	}
	
	
	$entete = "";
	if ($filtre){
		$entete .= '<strong>'.strtoupper(_T('simplecal:filtres')).' :</strong>';
		if ($param_idrub != 0){
			$entete .= ' <small>('._T('simplecal:filtres_rubrique_concernee').')</small>';
		}
	}
	
	$s = $entete.$filtre;
	
	return $s; 
}

function simplecal_get_liste_annees($id_rubrique){
	/*
	select DATE_FORMAT(date_debut,'%Y') as annee
	from spip_evenements 
	where date_debut not like '%0000%'
	union
	select DATE_FORMAT(date_fin,'%Y') as annee
	from spip_evenements 
	where date_fin not like '%0000%'
	*/

	$from = "spip_evenements";
	$order_by = "annee desc";

	$select1 = "distinct DATE_FORMAT(date_debut,'%Y') as annee";
	$select2 = "distinct DATE_FORMAT(date_fin,'%Y') as annee";
	$where1 = "date_debut not like '%0000%'";
	$where2 = "date_fin not like '%0000%'";
	
	// Pour le calcul des annees invisibles (ex : du xx/yy/2009 au xx/yy/2011 => 2010 invisible
	$select3 = "distinct DATE_FORMAT(date_debut,'%Y') as annee1, DATE_FORMAT(date_fin,'%Y') as annee2";
	$where3 = $where1." and ".$where2;
	
	
	if ($id_rubrique!=0){
		$where_rub = " and id_rubrique = ".$id_rubrique;
	} else {
		$where_rub = "";
	}
	
	// ----------------------
	//  Acces restreint ?
	// ----------------------
	$where_rub_exclure = simplecal_get_where_rubrique_exclure();
	// ----------------------
		
	$liste_a1 = sql_allfetsel($select1, $from, $where1.$where_rub.$where_rub_exclure, "", $order_by, "");
	$liste_a2 = sql_allfetsel($select2, $from, $where2.$where_rub.$where_rub_exclure, "", $order_by, "");
	$liste_a3 = sql_allfetsel($select3, $from, $where3.$where_rub.$where_rub_exclure, "", "", "");

	$annees = array();
	
	foreach ($liste_a1 as $row){
		$a = $row['annee'];
		if (!in_array($a, $annees)){
			$annees[] = $a;
		}
	}
	
	foreach ($liste_a2 as $row){
		$a = $row['annee'];
		if (!in_array($a, $annees)){
			$annees[] = $a;
		}
	}
	
	foreach ($liste_a3 as $row){
		$a1 = intval($row['annee1']);
		$a2 = intval($row['annee2']);
		if ($a2 - $a1 > 1){
			for ($a = $a1 ; $a<=$a2 ; $a++){
				if (!in_array("$a", $annees)){
					$annees[] = $a;
				}
			}
		}
	}
	
	rsort($annees);
	
	$tab = array();
	foreach ($annees as $annee){
		$date_min = $annee."-01-01";
		$date_max = $annee."-12-31";
		$where = "((date_debut like '%".$annee."%'";
		$where .= " OR date_fin like '%".$annee."%')";
		$where .= " OR (date_debut <= '$date_max' AND date_fin >= '$date_min'))"; 
		$nb = sql_countsel($from, $where.$where_rub.$where_rub_exclure);
		$tab[] = array("annee"=>$annee, "nb"=>$nb);
	}
	
	return $tab;
}




function simplecal_get_liste_mois($annee, $id_rubrique){
	$from = "spip_evenements";
	$order_by = "mois desc";
	
	$select1 = "distinct DATE_FORMAT(date_debut,'%m') as mois";
	$select2 = "distinct DATE_FORMAT(date_fin,'%m') as mois";
	$where1 = "date_debut like '%".$annee."%'";
	$where2 = "date_fin like '%".$annee."%'";
	
	// Pour le calcul des mois invisibles - ex : du xx/09/2011 au xx/02/2012 
	// => 10,11,12 invisibles pour 2011
	// =>       01 invisible  pour 2012
	$select3 = "distinct DATE_FORMAT(date_debut,'%Y-%m') as anmois1, DATE_FORMAT(date_fin,'%Y-%m') as anmois2";
	$where3 = "DATE_FORMAT(date_debut,'%Y') <= DATE_FORMAT(date_fin,'%Y') ";
	
	if ($id_rubrique!=0){
		$where_rub = " and id_rubrique = ".$id_rubrique;
	} else {
		$where_rub = "";
	}
	
	// ----------------------
	//  Acces restreint ?
	// ----------------------
	$where_rub_exclure = simplecal_get_where_rubrique_exclure();
	// ----------------------
	
	$liste_m1 = sql_allfetsel($select1, $from, $where1.$where_rub.$where_rub_exclure, "", $order_by, "");
	$liste_m2 = sql_allfetsel($select2, $from, $where2.$where_rub.$where_rub_exclure, "", $order_by, "");
	$liste_m3 = sql_allfetsel($select3, $from, $where3.$where_rub.$where_rub_exclure, "", "", "");

	
	$tab_mois = array();
	
	foreach ($liste_m1 as $row){
		$m = $row['mois'];
		if (!in_array($m, $tab_mois)){
			$tab_mois[] = $m;
		}
	}
	
	foreach ($liste_m2 as $row){
		$m = $row['mois'];
		if (!in_array($m, $tab_mois)){
			$tab_mois[] = $m;
		}
	}

	
	foreach ($liste_m3 as $row){
		$am1 = $row['anmois1'];
		$am2 = $row['anmois2'];
		$a1 = intval(substr($am1,0,4));
		$a2 = intval(substr($am2,0,4));
		$m1 = intval(substr($am1,5,2));
		$m2 = intval(substr($am2,5,2));
		
		// ex : du xx/09/2011 au xx/02/2013 
		// 2011 : 09,10,11,12
		// 2012 : 01,02,03,04,05,06,07,08,09,10,11,12
		// 2013 : 01,02
		
		
		for ($a = $a1; $a<=$a2; $a++){
			// On ne traite que l'annee concernee
			if ($a == $annee){
				
				// Annees identiques => Les mois de m1 a m2
				if ($a1 == $a2){
					for ($m = $m1 ; $m<=$m2 ; $m++){
						$m < 10 ? $s="0$m":$s="$m"; 
						if (!in_array($s, $tab_mois)){
							$tab_mois[] = $s;
						}
					}
				}
				
				// Annees differentes => Les mois de a1 (de m1 a decembre)
				if ($a1 != $a2 and $a1 == $a){
					for ($m = $m1 ; $m<=12 ; $m++){
						$m < 10 ? $s="0$m":$s="$m"; 
						if (!in_array($s, $tab_mois)){
							$tab_mois[] = $s;
						}
					}
				}
				// Annees differentes => Les mois de a2 (de janvier a m2)
				if ($a1 != $a2 and $a2 == $a){
					for ($m = 1 ; $m<=$m2 ; $m++){
						$m < 10 ? $s="0$m":$s="$m"; 
						if (!in_array($s, $tab_mois)){
							$tab_mois[] = $s;
						}
					}
				}
				// Annees differentes => Les mois de l'annee invisible (de janvier a decembre)
				if ($a1 != $a2 and $a1 != $a and $a2 != $a){
					for ($m = 1 ; $m<=12 ; $m++){
						$m < 10 ? $s="0$m":$s="$m"; 
						if (!in_array($s, $tab_mois)){
							$tab_mois[] = $s;
						}
					}
				}
			}
		}
		
		
	}
	
	
	rsort($tab_mois);
	
	$tab = array();
	foreach ($tab_mois as $mois){
		$date_min = $annee."-".$mois."01";
		$date_max = $annee."-".$mois."31";
		$where = "((date_debut like '%".$annee."-".$mois."%'";
		$where .= " OR date_fin like '%".$annee."-".$mois."%')";
		$where .= " OR (date_debut <= '$date_max' AND date_fin >= '$date_min'))"; 
		$nb = sql_countsel($from, $where.$where_rub.$where_rub_exclure);
		$tab[] = array("mois"=>$mois, "nb"=>$nb);
	}
	
	return $tab;
	
}

function simplecal_get_nb_tous($id_rubrique){
	$from = "spip_evenements as e";
	
	if ($id_rubrique != 0){
		$where = "id_rubrique=$id_rubrique";
	} else {
		$where = "";
	}
	
	// ----------------------
	//  Acces restreint ?
	// ----------------------
	$where_rub_exclure = simplecal_get_where_rubrique_exclure(!empty($where));
	// ----------------------
	
	$nb = sql_countsel($from, $where.$where_rub_exclure);
	
	return $nb;
}

function simplecal_get_nb_avenir($id_rubrique){
	$from = "spip_evenements as e";
	$where = " (e.date_debut >= DATE_FORMAT(NOW(),'%Y-%m-%d')";
	$where .= " OR e.date_fin >= DATE_FORMAT(NOW(),'%Y-%m-%d'))";
	
	if ($id_rubrique != 0){
		$where .= " AND id_rubrique=$id_rubrique";
	} 
	
	// ----------------------
	//  Acces restreint ?
	// ----------------------
	$where_rub_exclure = simplecal_get_where_rubrique_exclure();
	// ----------------------
	
	$nb = sql_countsel($from, $where.$where_rub_exclure);
	
	return $nb;
}


?>