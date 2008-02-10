<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// --
function genea_ajouter_boutons($boutons_admin){
	//global $connect_statut, $connect_toutes_rubriques;
	//$connect_statut == "0minirezo" && $connect_toutes_rubriques
	// si on est admin
	if (autoriser('voir','genea')){
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['genea_naviguer']= new Bouton(
			url_absolue(find_in_path('/img_pack/arbre-24.png')),  // icone
			'genea:titre' //titre
			);
	}
	return $boutons_admin;
}

function genea_calculer_rubriques($flux){
	global $table_prefix;
	$date_tmp = getdate();
	// Publier et dater les rubriques qui ont un arbre genealogique
	$r = spip_query("SELECT rub.id_rubrique AS id
	FROM ".$table_prefix."_rubriques AS rub, ".$table_prefix."_genea AS fille
	WHERE rub.id_rubrique = fille.id_rubrique
//	AND rub.date_tmp <= fille.date_heure AND fille.statut='publie'
	GROUP BY rub.id_rubrique");
	print_r ($row);
	while ($row = spip_fetch_array($r))
		@spip_query("UPDATE " . $table_prefix . "_rubriques SET statut_tmp='publie', date_tmp='".strtotime(normaliser_date($date_tmp))."' WHERE id_rubrique=".$row['id']);
	return $flux;
}

function genea_affiche_droite($flux){
	global $table_prefix;
	switch ($flux['args']['exec']) {
		case 'naviguer':
			if (!empty($flux['args']['id_rubrique'])) {
				$row=spip_fetch_array(spip_query("SELECT id_genea FROM " . $table_prefix. "_genea WHERE id_rubrique=".$flux['args']['id_rubrique']));
				$id_genea = $row['id_genea'];
				if (!empty($id_genea)) {
					$url_lien = generer_url_ecrire('genea_naviguer', "action=voir&id_genea=$id_genea") ;
					$flux['data'] .= debut_cadre_relief('',true);
					$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" ;
					$flux['data'] .= _T('genea:titre_encart_rubrique') . " :</b>\n";
					$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
					$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
					$flux['data'] .= "<img src='".url_absolue(find_in_path("/img_pack/arbre-24.png"))."' width='24' alt='";
					$flux['data'] .= _T('genea:titre_encart_rubrique') . "' /></span></a></td>\n" ;
					$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
					$flux['data'] .= _T('genea:texte_encart_rubrique') . "</a><br />";
					$flux['data'] .= "<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>"._T('genea:titre_arbre_numero')." :<br />";
					$flux['data'] .= "<span class='spip_xx-large'>$id_genea</span>";
					$flux['data'] .= "</div></td></tr></table>\n</div>\n" ;
					$flux['data'] .= fin_cadre_relief(true);
				}
			}
			break;
		default :
	}
	return $flux;
}

function genea_affiche_milieu($flux){
	$exec =  $flux['args']['exec'];
	if ($exec=='rubriques_edit'){
		$id_rubrique = $flux['args']['id_rubrique'];
		$flux['data'] .= "<div id='genea_editer_infos-$id_rubrique'>";
		$flux['data'] .= debut_cadre_enfonce(url_absolue(find_in_path("/img_pack/arbre-24.png")), true, "", "");
//		$flux['data'] .= debut_block("genea_$id_rubrique",true);
		$flux['data'] .= "<p>TEST MILIEU => $id_rubrique</p>";
//		$flux['data'] .= fin_block(true);
		$flux['data'] .= fin_cadre_enfonce(true);
		$flux['data'] .= "</div>";
	}
	return $flux;
}
?>