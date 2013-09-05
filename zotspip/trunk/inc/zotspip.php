<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Générer une URL pour la read API de Zotero
function zotero_url($params) {
	include_spip('inc/config');
	if (strpos($params,'?'))
		return 'https://api.zotero.org/'.lire_config('zotspip/type_librairie').'s/'.lire_config('zotspip/id_librairie').'/'.$params.'&key='.lire_config('zotspip/api_key');
	else
		return 'https://api.zotero.org/'.lire_config('zotspip/type_librairie').'s/'.lire_config('zotspip/id_librairie').'/'.$params.'?key='.lire_config('zotspip/api_key');
}


// Accéder à l'API Zotero
function zotero_get($params) {
	include_spip('inc/distant');
	$url = zotero_url($params);
	$ret = recuperer_page($url);
	if ($ret)
		spip_log("$url chargé avec succès.",'zotspip');
	else {
		spip_log("ECHEC chargement de la page $url. Voir prive_spip.log pour le code HTTP renvoyé.",'zotspip');
		spip_log("ECHEC chargement de la page $url.");
	}
	return $ret;
}

// Extraire l'identifiant d'un item à partir de l'URL
function zotspip_extraire_itemkey($url){
	if (preg_match('#items/(.*)\?#',$url,$matches))
		return $matches[1];
	else
		return '';
}

// Extraire l'identifiant d'une collection à partir de l'URL
function zotspip_extraire_collectionkey($url){
	if (preg_match('#collections/(.*)$#',$url,$matches))
		return $matches[1];
	else
		return '';
}

// Mise à jour de la base de données
// $forcer : forcer la mise à jour complète de la base
// $n integer : nombre d'items à mettre à jour simultanément (max 50)
function zotspip_maj_items($forcer=false, $n=50) {
	if ($n>50) $n=50;
	
	if ($forcer)
		$zotspip_maj_items = array('forcer' => true, 'start' => 0);
	else
		$zotspip_maj_items = isset($GLOBALS['meta']['zotspip_maj_items']) ? unserialize($GLOBALS['meta']['zotspip_maj_items']) : array('forcer' => false, 'start' => 0);
	
	$feed = zotero_get('items/?format=atom&order=dateModified&sort=desc&content=json,csljson&limit='.$n.'&start='.$zotspip_maj_items['start']);
	// On vérifie qu'on a bien eu un retour
	if (!$feed)
		return 0;
	
	// On parse le flux ATOM reçu
	include_spip('inc/xml');
	$xml = spip_xml_parse($feed, false);
	
	if (spip_xml_match_nodes(',^entry,', $xml, $entrees)){
		include_spip('base/abstract_sql');
		foreach ($entrees['entry'] as $entree) {
			$id_zitem = spip_xml_aplatit($entree['zapi:key']);
			$updated = spip_xml_aplatit($entree['updated']);
			
			// Faire une vérification sur la date de maj (seulement si on ne force pas)
			if (!$zotspip_maj_items['forcer']) {
				if ($updated==sql_getfetsel('updated','spip_zitems','id_zitem='.sql_quote($id_zitem))) {
					effacer_meta('zotspip_maj_items');
					return 1;
				}
			}
			
			// On initialise la ligne SQL à insérer
			$insertion = array(
				'id_zitem' => $id_zitem,
				'id_parent' => '0', // 0 si pas de parent
				'annee' => isset($entree['zapi:year']) ? spip_xml_aplatit($entree['zapi:year']) : NULL,
				'titre' => '',
				'auteurs' => '',
				'resume' => '',
				'date' => '',
				'pages' => '',
				'publication' => '',
				'editeur' => '',
				'type_ref' => '',
				'volume' => '',
				'numero' => '',
				'doi' => '',
				'isbn' => '',
				'issn' => '',
				'url' => '',
				'extras' => '',
				'mimetype' => '',
				'poids' => 0,
				'fichier' => '',
				'json' => '',
				'csljson' => '',
				'updated' => $updated,
				'date_ajout' => spip_xml_aplatit($entree['published'])
			);
			
			// On récupère le parent et/ou le lien du fichier
			$links = array(); // NB : il faut réinitialiser $links sinon les résultats s'accumulent
			if (spip_xml_match_nodes(',^link,', $entree, $links)) {
				foreach (array_keys($links) as $link){
					list($balise, $attributs) = spip_xml_decompose_tag($link);
					if ($attributs['rel'] == 'enclosure') {
						$insertion['fichier'] = $attributs['href'];
						$insertion['poids'] = $attributs['length'];
					}
					if ($attributs['rel'] == 'up')
						$insertion['id_parent'] = zotspip_extraire_itemkey($attributs['href']);
				}
			}
			
			// Récupération du code json et de csljson
			$subcontents = array();
			if (spip_xml_match_nodes(',^zapi:subcontent,', $entree['content type="application/xml"'][0], $subcontents)) {
				foreach ($subcontents as $cle_subcontent => $subcontent){
					list($balise, $attributs) = spip_xml_decompose_tag($cle_subcontent);
					if ($attributs['zapi:type']=='json')
						$insertion['json'] = spip_xml_aplatit($subcontent);
					if ($attributs['zapi:type']=='csljson')
						$insertion['csljson'] = spip_xml_aplatit($subcontent);
				}
			}
			$data = json_decode($insertion['json'],true);
			
			// Gestion des champs (NB : on stocke une quantité plus limitée des champs, juste à des fins de tri)
			$correspondances = array(
				'abstractNote' => 'resume',
				'billNumber' => 'numero',
				'blogTitle' => 'publication',
				'bookTitle' => 'publication',
				'caseName' => 'titre',
				'codePages' => 'pages',
				'company' => 'editeur',
				'contentType' => 'mimetype',
				'date' => 'date',
				'dateDecided' => 'date',
				'dateEnacted' => 'date',
				'dictionaryTitle' => 'publication',
				'distributor' => 'editeur',
				'docketNumber' => 'numero',
				'documentNumber' => 'numero',
				'DOI' => 'doi',
				'encyclopaediaTitle' => 'publication',
				'episodeNumber' => 'numero',
				'extras' => 'extras',
				'firstPage' => 'pages',
				'forumTitle' => 'publication',
				'genre' => 'type_doc',
				'institution' => 'editeur',
				'interviewMedium' => 'publication',
				'ISBN' => 'isbn',
				'ISSN' => 'issn',
				'issue' => 'numero',
				'issueDate' => 'date',
				'itemType' => 'type_ref',
				'label' => 'editeur',
				'letterType' => 'type_doc',
				'manuscriptType' => 'type_doc',
				'mapType' => 'type_doc',
				'meetingName' => 'publication',
				'mimeType' => 'mimetype',
				'nameOfAct' => 'titre',
				'network' => 'editeur',
				'note' => 'resume',
				'numPages' => 'pages',
				'pages' => 'pages',
				'patentNumber' => 'numero',
				'postType' => 'type_doc',
				'presentationType' => 'type_doc',
				'proceedingsTitle' => 'publication',
				'conferenceName' => 'conference',
				'programTitle' => 'titre',
				'publicationTitle' => 'publication',
				'publicLawNumber' => 'numero',
				'publisher' => 'editeur',
				'reportNumber' => 'numero',
				'reportType' => 'type_doc',
				'series' => 'collection',
				'seriesNumber' => 'numero',
				'seriesTitle' => 'collection',
				'studio' => 'editeur',
				'subject' => 'titre',
				'thesisType' => 'type_doc',
				'title' => 'titre',
				'university' => 'editeur',
				'url' => 'url',
				'version' => 'numero',
				'volume' => 'volume'
			);
			foreach ($correspondances as $zot => $spip)
				if (isset($data[$zot]))
					$insertion[$spip] = $data[$zot];
			
			// Vider le cache des documents distants
			if ($insertion['type_ref'] == 'attachment') {
				include_spip('inc/invalideur');
				purger_repertoire(_DIR_VAR."cache-zotspip/$id_zitem/");
				include_spip('inc/flock');
				spip_unlink(_DIR_VAR."cache-zotspip/$id_zitem/");
			}
			
			// Gestion des creators
			$creators = array();
			if (is_array($data['creators'])) {
				$rang = 1;
				foreach($data['creators'] as $creator) {
					$creators[] = array(
						'auteur' => isset($creator['name']) ? $creator['name'] : ($creator['lastName'] . ($creator['firstName'] ? (', '.$creator['firstName']) : '')),
						'id_zitem' => $id_zitem,
						'role' => $creator['creatorType'],
						'rang' => $rang
					);
					$rang++;
					if ($insertion['auteurs'] == '')
						$insertion['auteurs'] .= isset($creator['name']) ? $creator['name'] : ($creator['lastName'] . ($creator['firstName'] ? (' '.$creator['firstName']) : ''));
					else
						$insertion['auteurs'] .= isset($creator['name']) ? (', '.$creator['name']) : (', '.$creator['lastName'] . ($creator['firstName'] ? (' '.$creator['firstName']) : ''));
				}
			}
			
			// Gestion des tags
			$tags = array();
			if (is_array($data['tags'])) {
				foreach ($data['tags'] as $tag)
					$tags[] = array(
						'tag' => $tag['tag'],
						'id_zitem' => $id_zitem
					);
			}
			
			// Insertion en base de données
			sql_replace('spip_zitems',$insertion);
			sql_delete('spip_zcreators','id_zitem='.sql_quote($id_zitem));
			if (count($creators)) sql_insertq_multi('spip_zcreators',$creators);
			sql_delete('spip_ztags','id_zitem='.sql_quote($id_zitem));
			if (count($tags)) sql_insertq_multi('spip_ztags',$tags);
			
		}
	}
	
	// Faut-il continuer la synchronisation ? 
	$links = array();
	if (spip_xml_match_nodes(',^link rel="next",', $xml, $links)) {
		$link_next = array_keys($links);
		list($balise, $attributs) = spip_xml_decompose_tag($link_next[0]);
		if (preg_match('#start=([0-9]+)#',$attributs['href'],$matches)) {
			if ($matches[1]>$zotspip_maj_items['start']) {
				$zotspip_maj_items['start'] = $matches[1];
				ecrire_meta('zotspip_maj_items',serialize($zotspip_maj_items));
				ecrire_metas();
				return -5;
			}
		}
	}
	
	// Sinon, c'est qu'on a fini la synchronisation
	effacer_meta('zotspip_maj_items');
	return 1; //0 si rien à faire, 1 si effectuée, -5 si tâche pas finie
}

// Mise à jour des collections
// $forcer : forcer la mise à jour complète de la base
function zotspip_maj_collections($forcer=false) {
	$feed = zotero_get('collections/?format=atom&order=dateModified&sort=desc');
	// On vérifie qu'on a bien eu un retour
	if (!$feed)
		return 0;
	
	// On parse le flux ATOM reçu
	include_spip('inc/xml');
	$xml = spip_xml_parse($feed, false);
	
	if (spip_xml_match_nodes(',^entry,', $xml, $entrees)){
		include_spip('base/abstract_sql');
		foreach ($entrees['entry'] as $entree) {
			$id_zcollection = spip_xml_aplatit($entree['zapi:key']);
			$updated = spip_xml_aplatit($entree['updated']);
			
			// Faire une vérification sur la date de maj (seulement si on ne force pas)
			if (!$zotspip_maj_items['forcer']) {
				if ($updated==sql_getfetsel('updated','spip_zcollections','id_zcollection='.sql_quote($id_zcollection))) {
					return 1;
				}
			}
			
			// On initialise la ligne SQL à insérer
			$insertion = array(
				'id_zcollection' => $id_zcollection,
				'id_parent' => '0', // 0 si pas de parent
				'zcollection' => importer_charset(html_entity_decode(spip_xml_aplatit($entree['title']), ENT_QUOTES, "UTF-8"),'utf-8'),
				'updated' => $updated
			);
			
			// On récupère la collection parente
			$links = array(); // NB : il faut réinitialiser $links sinon les résultats s'accumulent
			if (spip_xml_match_nodes(',^link,', $entree, $links)) {
				foreach (array_keys($links) as $link){
					list($balise, $attributs) = spip_xml_decompose_tag($link);
					if ($attributs['rel'] == 'up')
						$insertion['id_parent'] = zotspip_extraire_collectionkey($attributs['href']);
				}
			}
			
			// Items de la collection
			$items = zotero_get("collections/$id_zcollection/items/?format=keys");
			$items = explode("\n",trim($items));
			$zitems_zcollections = array();
			foreach ($items as $item)
				$zitems_zcollections[] = array('id_zitem' => $item, 'id_zcollection' => $id_zcollection);
			
			// Insertion en base de données
			sql_replace('spip_zcollections',$insertion);
			sql_delete('spip_zitems_zcollections','id_zcollection='.sql_quote($id_zcollection));
			if (count($zitems_zcollections)) sql_insertq_multi('spip_zitems_zcollections',$zitems_zcollections);
		}
	}
	
	return 1; //0 si rien à faire, 1 si effectuée, -5 si tâche pas finie
}

// Nettoyer la base de données
function zotspip_nettoyer() {
	include_spip('base/abstract_sql');
	// Suppression des items qui ne sont plus dans la base
	$feed = zotero_get('items/?format=keys');
	if ($feed) {
		$items_zotero = explode("\n",trim($feed));
		$requete = sql_allfetsel('id_zitem','spip_zitems');
		$items_spip = array();
		foreach ($requete as $item)
			$items_spip[] = $item['id_zitem'];
		$diff = array_diff($items_spip,$items_zotero);
		foreach ($diff as $id_zitem)
			zotspip_supprimer_item($id_zitem);
	}
	// Suppression des collections qui ne sont plus dans la base
	$feed2 = zotero_get('collections/?format=keys');
	if ($feed2) {
		$collections_zotero = explode("\n",trim($feed2));
		$requete = sql_allfetsel('id_zcollection','spip_zcollections');
		$collections_spip = array();
		foreach ($requete as $collection)
			$collections_spip[] = $collection['id_zcollection'];
		$diff = array_diff($collections_spip,$collections_zotero);
		foreach ($diff as $id_zcollection)
			zotspip_supprimer_collection($id_zcollection);
	}
	return 1;
}

// Supprimer un item
function zotspip_supprimer_item($id_zitem) {
	include_spip('base/abstract_sql');
	sql_delete('spip_zcreators','id_zitem='.sql_quote($id_zitem));
	sql_delete('spip_tags','id_zitem='.sql_quote($id_zitem));
	sql_delete('spip_zitems_zcollections','id_zitem='.sql_quote($id_zitem));
	sql_delete('spip_zitems','id_zitem='.sql_quote($id_zitem));
}


// Supprimer une collection
function zotspip_supprimer_collection($id_zcollection) {
	include_spip('base/abstract_sql');
	sql_delete('spip_zcollections','id_zcollection='.sql_quote($id_zcollection));
	sql_delete('spip_zitems_zcollections','id_zcollection='.sql_quote($id_zcollection));
}


// Télécharge le schéma de données Zotero
function zotspip_maj_schema_zotero() {
	include_spip('inc/distant');
	
	// Lire le cache du schéma de données
	lire_fichier_securise(_DIR_TMP . 'schema_zotero.php', $schema);
	$schema = @unserialize($schema);
	
	if (!$schema)
		$schema = array(
			'itemTypes' => array(),
			'itemFields' => array(),
			'itemTypeFields' => array(),
			'creatorTypes' => array()
		);
	
	// Si on n'a pas commencé la synchronisation
	if (!isset($schema['sync'])) {
		$itemTypes = recuperer_page("https://api.zotero.org/itemTypes");
		if (!$itemTypes) {
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypes. Voir prive_spip.log pour le code HTTP renvoyé.",'zotspip');
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypes.");
			return 0;
		} else
			spip_log("https://api.zotero.org/itemTypes chargé avec succès.",'zotspip');
		$itemTypes = json_decode($itemTypes,true);
		$schema['itemTypes'] = array();
		foreach ($itemTypes as $itemType)
			$schema['itemTypes'][] = $itemType['itemType'];
		$schema['sync'] = $schema['itemTypes'];
		$itemFields = recuperer_page("https://api.zotero.org/itemFields");
		if (!$itemFields) {
			spip_log("ECHEC chargement de https://api.zotero.org/itemFields. Voir prive_spip.log pour le code HTTP renvoyé.",'zotspip');
			spip_log("ECHEC chargement de https://api.zotero.org/itemFields");
			return 0;
		} else
			spip_log("https://api.zotero.org/itemTypes chargé avec succès.",'zotspip');
		$itemFields = json_decode($itemFields,true);
		$schema['itemFields'] = array();
		foreach ($itemFields as $itemField)
			$schema['itemFields'][] = $itemField['field'];
	}
	
	// On synchronise par palier de 10
	for ($i = 1; $i <= min(10,count($schema['sync'])); $i++) {
		$type = $schema['sync'][0];
		$itemTypeFields = recuperer_page("https://api.zotero.org/itemTypeFields?itemType=$type");
		if (!$itemTypeFields) {
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypeFields?itemType=$type. Voir prive_spip.log pour le code HTTP renvoyé.",'zotspip');
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypeFields?itemType=$type");
			return 0;
		} else
			spip_log("https://api.zotero.org/itemTypes chargé avec succès.",'zotspip');
		$itemTypeFields = json_decode($itemTypeFields,true);
		$schema['itemTypeFields'][$type] = array();
		foreach ($itemTypeFields as $itemTypeField)
			$schema['itemTypeFields'][$type][] = $itemTypeField['field'];
		$schema['creatorTypes'][$type] = array();
		$creatorTypes = recuperer_page("https://api.zotero.org/itemTypeCreatorTypes?itemType=$type");
		if (!$creatorTypes) {
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypeCreatorTypes?itemType=$type. Voir prive_spip.log pour le code HTTP renvoyé.",'zotspip');
			spip_log("ECHEC chargement de https://api.zotero.org/itemTypeCreatorTypes?itemType=$type");
			return 0;
		} else
			spip_log("https://api.zotero.org/itemTypes chargé avec succès.",'zotspip');
		$creatorTypes = json_decode($creatorTypes,true);
		foreach ($creatorTypes as $creatorType)
			$schema['creatorTypes'][$type][] = $creatorType['creatorType'];
		unset($schema['sync'][0]);
		$schema['sync'] = array_values($schema['sync']);
	}
	
	if (!count($schema['sync']))
		unset($schema['sync']);

	// Sauver le schéma en cache
	ecrire_fichier_securise(_DIR_TMP . 'schema_zotero.php', serialize($schema));
	
	if (isset($schema['sync']))
		return -5; // Continuer la synchronisation
	else
		return 1;
}

// Renvoie le tableau des valeurs à transmettre pour un item Zotero
function form_item_zotero_charger() {
	$contexte = array('itemType' => '');
	include_spip('zotspip_fonctions');
	foreach (schema_zotero('itemFields') as $field)
		if ($field=='date')
			$contexte['itemDate'] = '';
		else
			$contexte[$field] = '';
	$contexte['creatorType'] = array('author');
	$contexte['firstName'] = array('');
	$contexte['lastName'] = array('');
	$contexte['tags'] = array('');
	
	return $contexte;
}

// Renvoie le json de l'item Zotero saisie
function form_item_zotero_traiter() {
	$itemType = _request('itemType');
	if ($itemType !='') {
		$zitem = array('itemType' => $itemType);
		
		$zitem['creators'] = array();
		$creatorType = _request('creatorType');
		$firstName = _request('firstName');
		$lastName = _request('lastName');
		foreach ($creatorType as $cle => $valeur)
			if ($lastName[$cle])
				$zitem['creators'][] = array(
					'creatorType' => $creatorType[$cle],
					'firstName' => $firstName[$cle],
					'lastName' => $lastName[$cle]
				);
		
		include_spip('zotspip_fonctions');
		$itemTypeFields = schema_zotero('itemTypeFields');
		foreach ($itemTypeFields[$itemType] as $field)
			if ($field=='date')
				$zitem['date'] = _request('itemDate');
			else
				$zitem[$field] = _request($field);
		
		$zitem['tags'] = array();
		$tags = _request('tags');
		foreach ($tags as $tag)
			if ($tag)
				$zitem['tags'][] = array('tag' => $tag);
		
		return json_encode($zitem);
	} else
		return '';
}

// Poster un résultat vers l'API Zotero
function zotero_poster($params_url,$datas,$methode='POST') {
	include_spip('inc/distant');
	$url = zotero_url($params_url);
	// Préparer les données
	list($type, $postdata) = prepare_donnees_post($datas, '');
	$datas = $type . 'Content-Length: '.strlen($postdata)."\r\n\r\n".$postdata;
	// ouvrir la connexion et envoyer la requete et ses en-tetes
	list($f, $fopen) = init_http($methode, $url, false, '', $datas, _INC_DISTANT_VERSION_HTTP, '');
	if (!$f) {
		spip_log("ECHEC init_http $url");
		spip_log("ECHEC init_http $url",'zotspip');
		return false;
	}
	$headers = recuperer_entetes($f, '');
	fclose($f);
	$result = @file_get_contents($url);
	
	$ret_http =  (is_array($headers)) ? implode(',',$headers) : $headers;
	spip_log("$methode sur $url - HTTP $ret_http",'zotspip');
	
	return array(
		'headers' => $headers,
		'result' => $result
	);
}


?>