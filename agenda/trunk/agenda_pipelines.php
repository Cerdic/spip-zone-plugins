<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

/**
 * Inserer les infos d'agenda sur les articles et rubriques
 *
 * @param array $flux
 * @return array
 */
function agenda_affiche_milieu($flux) {
	$e = trouver_objet_exec($flux['args']['exec']);
	$out = "";
	if ($e['type']=='rubrique'
	  AND $e['edition']==false
	  AND $id_rubrique = intval($flux['args']['id_rubrique'])){
		$activer = true;
		$res = "";
		$actif = sql_getfetsel('agenda','spip_rubriques','id_rubrique='.intval($id_rubrique));
		$statut="-32";
		$alt = "";
		$voir = "";
		if (!sql_countsel('spip_rubriques','agenda=1'))
			$res .= "<span class='small'>" . _T('agenda:aucune_rubrique_mode_agenda') . "</span><br />";
		else {
			include_spip('inc/rubriques');
			if (sql_countsel('spip_rubriques',sql_in('id_rubrique',calcul_hierarchie_in($id_rubrique))." AND agenda=1 AND id_rubrique<>".intval($id_rubrique))){
				$alt = _T('agenda:rubrique_dans_une_rubrique_mode_agenda');
				$activer = false;
				$statut="-ok-32";
				$voir = _T('agenda:voir_evenements_rubrique');
			}
			elseif(!$actif) {
				$alt = _T('agenda:rubrique_sans_gestion_evenement').'<br />';
				$statut="-non-32";
			}
			if ($actif){
				$alt = _T('agenda:rubrique_mode_agenda').'<br />';
				$statut="-ok-32";
				$voir = _T('agenda:voir_evenements_rubrique');
			}
		}

		if (!$actif){
			if($activer){
				$res .= bouton_action(_T('agenda:rubrique_activer_agenda'),generer_action_auteur('activer_agenda_rubrique',$id_rubrique,self()),'ajax');
			}
		}
		else
			$res .= bouton_action(_T('agenda:rubrique_desactiver_agenda'),generer_action_auteur('activer_agenda_rubrique',"-$id_rubrique",self()),'ajax');
		if ($voir)
			$res .= " | <a href='".generer_url_ecrire('evenements',"id_rubrique=$id_rubrique")."'>$voir</a>";
		if ($res)
			$out .= boite_ouvrir(_T('agenda:agenda').http_img_pack("agenda$statut.png",$alt,"class='statut'",$alt),'simple agenda-statut')
			  . $res
			  . boite_fermer();
	}
	elseif ($e['type']=='article'
	  AND $e['edition']==false){
		$id_article = $flux['args']['id_article'];
		if (autoriser('creerevenementdans','article',$id_article)) {
			$out .= recuperer_fond('prive/objets/contenu/article-evenements',$flux['args']);
		}
	}

	if ($out){
		if ($p=strpos($flux['data'],'<!--affiche_milieu-->'))
			$flux['data'] = substr_replace($flux['data'],$out,$p,0);
		else
			$flux['data'] .= $out;
	}
	return $flux;
}

/**
 * Optimiser la base (evenements a la poubelle, lies a des articles disparus, ou liens mots sur evenements disparus)
 *
 * @param array $flux
 * @return array
 */
function agenda_optimiser_base_disparus($flux){

	# passer a la poubelle
	# les evenements lies a un article inexistant
	$res = sql_select("DISTINCT evenements.id_article","spip_evenements AS evenements
			LEFT JOIN spip_articles AS articles
			ON evenements.id_article=articles.id_article","articles.id_article IS NULL");
	while ($row = sql_fetch($res))
		sql_updateq("spip_evenements",array('statut'=>'poubelle'),"id_article=".$row['id_article']);

	// Evenements a la pouvelle
	sql_delete("spip_evenements", "statut='poubelle' AND maj < ".$flux['args']['date']);

	include_spip('action/editer_liens');
	// optimiser les liens de tous les mots vers des objets effaces
	// et depuis des mots effaces
	$flux['data'] += objet_optimiser_liens(array('mot'=>'*'),array('evenement'=>'*'));

	return $flux;
}


/**
 * Lister les evenements dans le calendrier de l'espace prive (extension organiseur)
 *
 * @param array $flux
 * @return array
 */
function agenda_quete_calendrier_prive($flux){
	$quoi = $flux['args']['quoi'];
	if (!$quoi OR $quoi=='evenements'){
		$start = sql_quote($flux['args']['start']);
		$end = sql_quote($flux['args']['end']);
		$res = sql_select('*','spip_evenements AS E',"((E.date_fin >= $start OR E.date_debut >= $start) AND E.date_debut <= $end)");
		while ($row = sql_fetch($res)){
			$flux['data'][] = array(
				'id' => $row['id_evenement'],
				'title' => $row['titre'],
				'allDay' => false,
				'start' => $row['date_debut'],
				'end' => $row['date_fin'],
				'url' => str_replace("&amp;","&",generer_url_entite($row['id_evenement'],'evenement')),
				'className' => "calendrier-event evenement calendrier-couleur5",
				'description' => $row['descriptif'],
			);
		}
	}
	return $flux['data'];
}
?>
