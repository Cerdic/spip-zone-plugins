<?php


/**
 * Configuration des contenus
 * @param array $flux
 * @return array
 */
function albums_affiche_milieu($flux){
	if ($flux["args"]["exec"] == "configurer_contenu") {
		$flux["data"] .=  recuperer_fond('prive/squelettes/inclure/configurer',array('configurer'=>'configurer_albums'));
	}
	return $flux;
}


/**
 * Pipeline afficher_complement_objet
 * afficher les albums sur les fiches objet
 * sur lesquelles ils ont ete actives
 * 
 * @param  $flux
 * @return
 */
function albums_afficher_complement_objet($flux) {

	if ($type=$flux['args']['type']
		AND $id=intval($flux['args']['id'])
		AND (autoriser('ajouteralbum',$type,$id))) {

		$texte = recuperer_fond('prive/objets/contenu/albums_objet', array(
			'table_source' => 'albums',
			'objet_source' => 'album',
			'objet' => $type,
			'id_objet' => $id,
		));
		if ($p=strpos($flux['data'],"<!--afficher_complement_objet-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
	
}


/**
 * (Pas utilise pour l instant)
 * Compter les albums dans un objet
 *
 * @param array $flux
 * @return array
 */
function albums_objet_compte_enfants($flux){
	if ($objet = $flux['args']['objet']
	  AND $id=intval($flux['args']['id_objet'])) {
		// juste les publies ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['album'] = sql_countsel('spip_albums AS D JOIN spip_albums_liens AS L ON D.id_album=L.id_album', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie')");
		} else {
			$flux['data']['album'] = sql_countsel('spip_albums AS D JOIN spip_albums_liens AS L ON D.id_album=L.id_album', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie' OR D.statut='prepa')");
		}
	}
	return $flux;
}


/**
 * Associer un album a un objet
 *
 * @param array $flux
 * @return array
 */
function albums_post_insertion($flux) {
	
	// LIENS
	// si variable $associer_objet renseignée...
	if ($associer_objet = _request('associer_objet') AND isset($associer_objet) AND preg_match(',^\w+\|[0-9]+$,',$associer_objet)){

		$objet_source = objet_type($flux['args']['table']);		// objet_source nouvellement créé (album...)
		$id_objet_source = $flux['args']['id_objet']; 			// id_objet_source nouvellement créé (id_album...)
		list($objet,$id_objet) = explode('|',$associer_objet);		// objet et id_objet

		// si l'objet source est un album, qu'il y a un objet et un id_objet valides...
		if (	
			$objet_source == 'album'
			AND isset($objet)
			AND isset($id_objet)
			
		){
			// si autorisation modifier objet...
			if (autoriser('modifier',$objet,$id_objet)){
				include_spip('action/editer_liens');
				objet_associer(array($objet_source=>$id_objet_source),array($objet=>$id_objet));
				if (isset($flux['args']['redirect']))
					$flux['args']['redirect'] = parametre_url ($flux['args']['redirect'], "id_lien_ajoute", $id_objet_source, '&');
			}
		}
		
	}

	return $flux;
}


/**
 * Mise a jour des liens apres edition
 * @param array $flux
 * @return array
 */
function albums_post_edition($flux){
	// si on institue un objet, mettre ses albums lies a jour
	if ($flux['args']['table']!=='spip_albums'){
		$type = isset($flux['args']['type'])?$flux['args']['type']:objet_type($flux['args']['table']);
		// verifier d'abord les doublons !
		include_spip('inc/autoriser');
		if (autoriser('autoassocieralbum',$type,$flux['args']['id_objet'])){
			$table_objet = isset($flux['args']['table_objet'])?$flux['args']['table_objet']:table_objet($flux['args']['table'],$flux['args']['serveur']);
			$marquer_doublons_album = charger_fonction('marquer_doublons_album','inc');
			$marquer_doublons_album($flux['data'],$flux['args']['id_objet'],$type,id_table_objet($type, $flux['args']['serveur']),$table_objet,$flux['args']['table'], '', $flux['args']['serveur']);
		}

		if($flux['args']['action']=='instituer' OR isset($flux['data']['statut'])){
			include_spip('base/abstract_sql');
			$id = $flux['args']['id_objet'];
			$albums = array_map('reset',sql_allfetsel('id_album','spip_albums_liens','id_objet='.intval($id).' AND objet='.sql_quote($type)));
			include_spip('action/editer_objet');
			foreach($albums as $id_album)
				// mettre a jour le statut si necessaire
				objet_instituer($id_album);
		}
	}
	else {
		if ($flux['args']['table']!=='spip_albums'){
			// verifier les doublons !
			$marquer_doublons_album = charger_fonction('marquer_doublons_album','inc');
			$marquer_doublons_album($flux['data'],$flux['args']['id_objet'],$flux['args']['type'],id_table_objet($flux['args']['type'], $flux['args']['serveur']),$flux['args']['table_objet'],$flux['args']['spip_table_objet'], '', $flux['args']['serveur']);
		}
	}
	return $flux;
}


// CSS PUBLIC
function albums_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/albums.css').'" type="text/css" media="all" />';
	return $flux;
}


// Optimiser la base de donnee en supprimant les liens orphelins
function albums_optimiser_base_disparus($flux){

	// albums a la pouvelle
	sql_delete("spip_albums", "statut='poubelle' AND maj < ".$flux['args']['date']);

	// optimiser les liens morts entre documents et albums
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('document'=>'*'),array('album'=>'*'));

	return $flux;
}

?>
