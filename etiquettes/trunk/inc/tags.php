<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Trouver/creer le groupe de mot nomme
 * @param string $type_mot
 * @return int
 */
function tags_get_id_groupe($type_mot='tags'){
	static $groupes = array();
	if (!isset($groupes[$type_mot])){
		$groupes[$type_mot] = sql_fetsel('*','spip_groupes_mots','titre='.sql_quote($type_mot));
		if (!$groupes[$type_mot]){
			include_spip('action/editer_groupe_mots');
			$id_groupe = groupemots_inserer();
			groupemots_modifier($id_groupe,
				array(
				'titre' => $type_mot,
				)
			);
			$groupes[$type_mot] = sql_fetsel('*','spip_groupes_mots','titre='.sql_quote($type_mot));
		}
	}
	return $groupes[$type_mot]['id_groupe'];
}

/**
 * Trouver/creer le(s) mot(s) nomme(s) dans le groupe nomme
 * @param array|string $tags
 * @param string $type_mot
 * @return array
 */
function tags_get_id_mot($tags, $type_mot='tags'){
	$id_groupe = tags_get_id_groupe($type_mot);
	$search = $tags;
	if (!is_array($search))
		$search = array($search);

	include_spip('base/abstract_sql');
	$deja = sql_allfetsel('id_mot,titre','spip_mots',array("id_groupe=".intval($id_groupe),sql_in('titre',$search)));
	$ids = array_map('reset',$deja);
	$deja = array_map('end',$deja);
	$adds = array_diff($search,$deja);
	if (count($adds)){
		include_spip('action/editer_mot');
		foreach ($adds as $titre){
			$id_mot = mot_inserer($id_groupe);
			mot_modifier($id_mot,array('titre'=>$titre));
			$ids[] = $id_mot;
		}
	}
	return is_array($tags)?$ids:reset($ids);
}

/**
 * Decouper des chaines de plusieurs tags en un tableau de tag
 * les tags sont séparés par des virgules
 *
 * @param $tags
 * @return array
 */
function tags_decouper_tags($tags){
	$tags = implode(',',$tags);
	$tags = preg_replace(",\s\s+,"," ",$tags);
	$tags = explode(',',$tags);
	$tags = array_map('trim',$tags);
	$tags = array_filter($tags);
	$tags = array_unique($tags);
	return $tags;
}

/**
 * @param string $objet
 * @param int $id_objet
 * @param array $tags
 * @param string $type_mot
 * @param bool $ajouter
 * @param bool $supprimer
 * @return array
 */
function tags_tagger($objet,$id_objet,$tags,$type_mot='tags',$ajouter=true,$supprimer=true){
	include_spip('base/abstract_sql');

	// retrouver les mots (les creer ainsi que le groupe si necessaire)
	$id_groupe = tags_get_id_groupe($type_mot);
	$ids = tags_get_id_mot($tags, $type_mot);

	include_spip('action/editer_liens');
	// recuperer les tags deja existant
	$liens = objet_trouver_liens(array('mot'=>'*'),array($objet=>$id_objet));
	$deja = array();
	foreach ($liens as $lien)
		$deja[]=$lien['id_mot'];

	$invalider = false;
	// si on peut ajouter, ajouter les ids manquants
	// facile
	if ($ajouter){
		$adds = array_diff($ids,$deja);
		if (count($adds)){
			$invalider = true;
			objet_associer(array('mot'=>$adds),array($objet=>$id_objet));
		}
	}

	// si on peut supprimer, il faut filtrer les mots deja la
	// par leur appartenance au bon groupe
	if ($supprimer){
		$deja = sql_allfetsel('id_mot','spip_mots',array('id_groupe='.intval($id_groupe),sql_in('id_mot',$deja)));
		$deja = array_map('reset',$deja);
		// et maintenant on peut supprimer ceux qui sont en trop
		$remove = array_diff($deja,$ids);
		if (count($remove)){
			$invalider = true;
			objet_dissocier(array('mot'=>$remove),array($objet=>$id_objet));
		}
	}

	if ($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur("id=$objet/$id_objet");
	}

	// et on rechecke les tags reels
	$liens = objet_trouver_liens(array('mot'=>'*'),array($objet=>$id_objet));
	$deja = array();
	foreach ($liens as $lien)
		$deja[]=$lien['id_mot'];
	$t = sql_allfetsel('titre','spip_mots',array('id_groupe='.intval($id_groupe),sql_in('id_mot',$deja)));
	$t = array_map('reset',$t);
	return $t;
}