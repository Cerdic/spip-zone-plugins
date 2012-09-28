<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Albums sur la page de visualisation des objets
**/
function albums_afficher_complement_objet($flux) {
	$texte = "";

	$e = trouver_objet_exec($flux['args']['type']);
	$type = $e['type'];

	if (!$e['edition'] AND in_array(table_objet_sql($type),lire_config('albums/objets'))) {
		$texte .= '<div id="albums" class="albums">';
		$texte .= recuperer_fond('prive/squelettes/contenu/albums_afficher_complement_objet', array(
			'table_source' => 'albums',
			'objet' => $type,
			'id_objet' => intval($flux['args']['id']),
			'associer_objet' => $type . '|' . intval($flux['args']['id'])
			),
			array('ajax'=>true)
		);
		$texte .= '</div>';
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--afficher_complement_objet-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
* Objets associes et auteur sur la page de visualisation d'un album
**/
function albums_affiche_milieu($flux){
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	if (!$e['edition'] AND $e['type']=='album') {

		// auteur
		$texte .= recuperer_fond('prive/squelettes/contenu/albums_affiche_milieu_auteur', array(
			'id_album' => $flux['args'][$e['id_table_objet']]
		));

		// objets associes
		$texte .= recuperer_fond('prive/squelettes/contenu/albums_affiche_milieu_objets_lies', array(
			'id_album' => $flux['args'][$e['id_table_objet']]
			),
			array('ajax'=>true)
		);
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Compagnons
 */
function albums_compagnon_messages($flux) {

	$exec = $flux['args']['exec'];
	$pipeline = $flux['args']['pipeline'];
	$aides = &$flux['data'];

	switch ($pipeline) {
		case 'affiche_milieu':
			switch ($exec) {
				case 'albums':
					$aides[] = array(
						'id' => 'albums_info',
						'titre' => _T('album:c_albums_info'),
						'texte' => _T('album:c_albums_info_texte'),
						'statuts'=> array('1comite', '0minirezo', 'webmestre')
					);
					break;
			}
			break;
	}
	return $flux;
}


/**
 * Mise a jour des liens vers les albums apres edition d'un objet
 * Base sur la pipeline du plugin 'medias'
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


/**
* Css sur les pages publiques
*/
function albums_insert_head_css($flux) {
	if (!defined('_ALBUMS_INSERT_HEAD_CSS') OR !_ALBUMS_INSERT_HEAD_CSS){
		include_spip("inc/config");
		$cfg = (defined('_ALBUMS_INSERT_HEAD_CSS')?_ALBUMS_INSERT_HEAD_CSS:lire_config("albums/insert_head_css",1));
		if ($cfg){
			$flux .= '<link rel="stylesheet" href="'.find_in_path('css/albums.css').'" type="text/css" />';
		}
	}

	return $flux;
}


/**
* Optimiser la base de donnee en supprimant les liens orphelins
*/
function albums_optimiser_base_disparus($flux){

	// albums a la pouvelle
	sql_delete("spip_albums", "statut='poubelle' AND maj < ".$flux['args']['date']);

	// optimiser les liens morts entre documents et albums
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('document'=>'*'),array('album'=>'*'));

	return $flux;
}

?>
