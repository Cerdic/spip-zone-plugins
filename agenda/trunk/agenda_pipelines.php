<?php


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
			include_spip('inc/agenda_gestion');
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
				$res .= bouton_action(_T('agenda:rubrique_activer_agenda'),generer_action_auteur('rubrique_activer_agenda',$id_rubrique,self()),'ajax');
			}
		}
		else
			$res .= bouton_action(_T('agenda:rubrique_desactiver_agenda'),generer_action_auteur('rubrique_activer_agenda',"-$id_rubrique",self()),'ajax');
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
		$afficher = autoriser('creerevenementdans','article',$id_article);
		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
			 $evenements = recuperer_fond('prive/objets/contenu/article-evenements',$contexte);
			 $out .= $evenements;
		}
	}
	elseif ($e['type']=='mot'
	  AND $e['edition']==false
	  AND $id_mot = intval($flux['args']['id_mot'])){
		foreach($_GET as $key=>$val)
			$contexte[$key] = $val;
	 $evenements = recuperer_fond('prive/contenu/agenda_evenements',$contexte);
	 $out .= $evenements;
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


function agenda_editer_contenu_objet($flux){
	if ($flux['args']['type']=='groupe_mot'){
		// ajouter l'input sur les evenements
		$checked = in_array('evenements',$flux['args']['contexte']['tables_liees']);
		$checked = $checked?" checked='checked'":'';
		$input = "<div class='choix'><input type='checkbox' class='checkbox' name='tables_liees&#91;&#93;' value='evenements'$checked id='evenements' /><label for='evenements'>"._T('agenda:item_mots_cles_association_evenements')."</label></div>";
		$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->",$flux['data']);
	}
	return $flux;
}

function agenda_libelle_association_mots($libelles){
	$libelles['evenements'] = 'agenda:info_evenements';
	return $libelles;
}


function agenda_objets_extensibles($objets){
		return array_merge($objets, array('evenement' => _T('agenda:evenements')));
}

function agenda_afficher_nombre_objets_associes_a($flux){
	if ($flux['args']['objet']=='mot'
	  AND $id_mot=$flux['args']['id_objet']){
		$aff_articles = sql_in('A.statut',  ($GLOBALS['connect_statut'] =="0minirezo")  ? array('prepa','prop','publie') : array('prop','publie'));
		$nb = sql_countsel("spip_mots_liens AS L LEFT JOIN spip_evenements AS E ON E.id_evenement=L.id_objet AND L.objet='evenement' LEFT JOIN spip_articles AS A ON E.id_article=A.id_article", "L.id_mot=".intval($id_mot)." AND $aff_articles");
		if ($nb)
			$flux['data'][] = singulier_ou_pluriel($nb, "agenda:info_un_evenement", "agenda:info_nombre_evenements");
	}
	return $flux;
}

/**
 * Declarer evenement comme un objet interpretable dans les url
 * ?evenement12
 * 
 * @param array $objets
 * @return array
 */
function agenda_declarer_url_objets($objets){
	$objets[] = 'evenement';
	return $objets;
}

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
