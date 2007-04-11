<?php
function RechercheEtendue_reconfig_indexation($reactive = false){
	include_spip('inc/meta');
	$liste_rub_no_index = explode(',',$GLOBALS['meta']['recherche_etendue_rubriques_non_indexees']);
	$in = "";
	$liste_rub_no_index = implode(',',array_map('intval',$liste_rub_no_index));
	$in_rub = calcul_mysql_in('id_rubrique',$liste_rub_no_index,'NOT');
	if (count($liste_rub_no_index)){
		$in = "AND $in_rub";
	}
	// mettre a jour les criteres d'indexation
	$INDEX_critere_indexation = array();
	if (isset($GLOBALS['meta']['INDEX_critere_indexation']))
		$INDEX_critere_indexation = unserialize($GLOBALS['meta']['INDEX_critere_indexation']);
	$INDEX_critere_indexation['spip_articles']="statut='publie'$in";
	$INDEX_critere_indexation['spip_breves']="statut='publie'$in";
	$INDEX_critere_indexation['spip_rubriques']="statut='publie'$in";
	$INDEX_critere_indexation['spip_syndic']="statut='publie'$in";
	ecrire_meta('INDEX_critere_indexation',serialize($INDEX_critere_indexation));
	ecrire_metas();
	
	// reactiver les objets marques a non qui sont reactives
	if ($reactive=intval($reactive)){
		spip_query("UPDATE spip_rubriques SET idx='1' WHERE id_rubrique=$reactive AND idx='non'");
		spip_query("UPDATE spip_articles SET idx='1' WHERE id_rubrique=$reactive AND idx='non'");
		spip_query("UPDATE spip_breves SET idx='1' WHERE id_rubrique=$reactive AND idx='non'");
		spip_query("UPDATE spip_syndic SET idx='1' WHERE id_rubrique=$reactive AND idx='non'");
	}
	// mettre a jour les tables et les objets deja indexes
	include_spip('inc/indexation');
	$liste = liste_index_tables();
	$in_rub = calcul_mysql_in('id_rubrique',$liste_rub_no_index);
	foreach($liste as $id_table=>$table)
		if (in_array($table,array('spip_articles','spip_rubriques','spip_breves','spip_syndic'))){
			$res = spip_query("SELECT * FROM $table WHERE NOT(idx='non') AND $in_rub");
			$liste_obj="0";
			$primary = primary_index_table($table);
			while ($row = spip_fetch_array($res))
				$liste_obj.=",".$row[$primary];
			$in_obj = calcul_mysql_in('id_objet',$liste_obj);
			spip_query("DELETE FROM spip_index WHERE id_table=$id_table AND $in_obj");
			spip_query("UPDATE $table SET idx='non' WHERE NOT(idx='non') AND $in_rub");
		}
}


function RechercheEtendue_affiche_droite($flux){
	if ($flux['args']['exec']=='naviguer'){
		global $spip_lang_right;
		$out = "";
		$id_rubrique = $flux['args']['id_rubrique'];
		if ($id_rubrique){
			$liste_rub_no_index = array_flip(explode(',',$GLOBALS['meta']['recherche_etendue_rubriques_non_indexees']));
			if (($off=intval(_request('recherche_etendue_rubrique_off')))!=NULL
				AND !isset($liste_rub_no_index[$off])){
					$liste_rub_no_index[$off] = 1;
					include_spip('inc/meta');
					ecrire_meta('recherche_etendue_rubriques_non_indexees',implode(',',array_keys($liste_rub_no_index)));
					ecrire_metas();
					RechercheEtendue_reconfig_indexation();
			}
			if (($on=intval(_request('recherche_etendue_rubrique_on')))!=NULL
				AND isset($liste_rub_no_index[$on])){
					unset($liste_rub_no_index[$on]);
					include_spip('inc/meta');
					ecrire_meta('recherche_etendue_rubriques_non_indexees',implode(',',array_keys($liste_rub_no_index)));
					ecrire_metas();
					RechercheEtendue_reconfig_indexation($on);
			}
			$acronymes_rubrique_locale_active = $GLOBALS['meta']['acronymes_rubrique_locale_active']?$GLOBALS['meta']['acronymes_rubrique_locale_active']:'non';
			$out .= debut_cadre_relief('',true);
			if (isset($liste_rub_no_index[$id_rubrique])){
				$out .= generer_url_post_ecrire('naviguer', "id_rubrique=$id_rubrique");
				$out .= "<input type='hidden' name='recherche_etendue_rubrique_on' value='$id_rubrique' />\n";
				$out .= "<div><em>"._L("Cette rubrique n'est pas prise en compte par le moteur de recherche")."</em></div>\n";
				$out .= "<div><strong>"._L("Re-activer l'indexation de cette rubrique par le moteur de recherche")."</strong>\n";
				$out .= "<div align='$spip_lang_right'><input type='submit' name='Choisir' value='"._L('Activer')."' class='fondo'></div>\n";
				$out .= "</div></form>";
			}
			else{
				$out .= generer_url_post_ecrire('naviguer', "id_rubrique=$id_rubrique");
				$out .= "<input type='hidden' name='recherche_etendue_rubrique_off' value='$id_rubrique' />\n";
				$out .= "<div>"._L("Desactiver l'indexation de cette rubrique par le moteur de recherche")."<br/>\n";
				$out .= "<div align='$spip_lang_right'><input type='submit' name='Annuler' value='"._L('Desactiver')."' class='fondo'></div>\n";
				$out .= "</div></form>";
			}
			$out .= fin_cadre_relief(true);
			$flux['data'].= $out;
		}
	}
	return $flux;
}

?>