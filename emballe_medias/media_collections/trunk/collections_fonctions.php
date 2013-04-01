<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction collection_auteur
 * 
 * Fonction utilisée par les autorisations
 * Vérifie qu'un auteur est auteur associé à une collection
 * Il peut alors associer des medias à la collection
 * 
 * @param int $id 
 * 		L'identifiant numérique de la collection à tester
 * @param array $qui
 * 		Les informations de session de l'auteur à tester
 * @return bool
 * 		true/false 
 */
function collection_auteur($id=0,$qui=array()){
	if(!$qui['id_auteur'])
		return false;
	return $qui['id_auteur'] == sql_getfetsel('id_auteur','spip_auteurs_liens','objet="collection" AND id_objet='.intval($id).' AND id_auteur='.intval($qui['id_auteur']));
}

/**
 * Fonction collection_auteur
 * 
 * Fonction utilisée par les autorisations
 * Vérifie qu'un auteur est auteur associé à une collection
 * Il peut alors associer des medias à la collection
 * 
 * @param int $id 
 * 		L'identifiant numérique de la collection à tester
 * @param array $qui
 * 		Les informations de session de l'auteur à tester
 * @return bool
 * 		true/false 
 */
function collection_admin($id=0,$qui=array()){
	if(!$qui['id_auteur'])
		return false;
	return $qui['id_auteur'] == sql_getfetsel('id_admin','spip_collections','id_collection='.intval($id));
}

/**
 * Fonction titre_type_collection
 * 
 * Renvoie une chaîne de caractère définissant un type de collection
 * 
 * Par exemple pour le type de collection "coop" en base, renverra "coopératif"
 * Ces titres sont définis par des chaînes de langue dans le pipeline "collections_liste_types"
 * 
 * @param string $type_collection
 * 		La valeur du champ type_collection en base
 * @return string
 */
function titre_type_collection($type_collection){
	$types = pipeline('collections_liste_types',array());
	return $types[$type_collection] ? $types[$type_collection] : $type_collection;
}

/**
 * Fonction titre_genre_collection
 * 
 * Renvoie une chaîne de caractère définissant un genre de collection
 * 
 * Par exemple pour le genre de collection "mixed" en base, renverra "Mix (tous les types)"
 * Ces titres sont définis par des chaînes de langue dans le pipeline "collections_liste_genres"
 * 
 * @param string $genre_collection
 * 		La valeur du champ genre en base
 * @return string
 */
function titre_genre_collection($genre_collection){
	$genres = pipeline('collections_liste_genres',array());
	return $genres[$genre_collection] ? $genres[$genre_collection] : $genre_collection;
}
?>