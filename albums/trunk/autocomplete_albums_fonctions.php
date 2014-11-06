<?php
/**
 * Fonctions utiles au squelette «autocomplete_albums»
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Retourne une liste d'objets, correspondants éventuellement à un terme de recherche.
 *
 * On peut restreindre la sélection en fonction des liaisons avec un autre type d'objet.
 * Si un terme est donné, on cherche les occurences dans 2 champs :
 *
 * - le champ servant de clé primaire id_xxx.
 * - le champ contenant le titre tel que défini dans la déclaration de la table,
 *   ou un champ spécifique donné en argument.
 *
 * @example
 *
 * - Albums avec `toto` dans le titre : `...autocomplete('album','','','toto')`
 * - Articles avec `toto` dans le titre et ayant des albums liés : `...autocomplete('article','album',true,'toto')`
 * - Auteurs ayant `toto` dans leur email et liés à des albums : `...autocomplete('auteur','album','','toto','email')`
 * - Mot-clés liés à des albums : `...autocomplete('mot','album')`
 *
 * @param string $type
 *     Type d'objet recherché
  * @param string $type2
 *     Type d'objet lié
 * @param bool $pivot2
 *     `true` pour définir l'objet secondaire comme pivot.
 *     Le pivot est l'objet qui porte la table de liens
 *     dont on se sert pour récupérer les liaisons.
 *     Par défaut on cherche dans la table de liens de l'objet principal.
 * @param string $terme
 *     Terme de recherche (optionnel)
 * @param string $champ
 *     Champ spécial dans lequel le terme doit être recherché.
 *     Par défaut, on cherche dans le champ correspondant au titre de l'objet,
 *     tel que précisé dans la déclaration de la table sql.
 * @param int|string $nb
 *     Nombre maximal de résultats, 20 par défaut.
 * @return array
 *     tableau contenant une sélection des objets avec leur identifiant et leur titre.
 */
function filtre_albums_autocomplete($type='',$type2='',$pivot2=false,$terme='',$champ='',$nb=''){

	// vérifications préliminaires
	if (
		(!$type = objet_info($type,'type')) // type d'objet valide
		OR ($type2 AND !$type2 = objet_info($type2,'type')) // type d'objet secondaire valide
	)
		return;

	// Objet principal (celui recherché)
	$table_objet_sql = table_objet_sql($type); // ex. spip_mots
	$table_objet = table_objet($type); // ex. mots
	// champ contenant le titre d'après la déclaration sql de la table (une chaîne du genre «titre, '' AS lang»)
	$champ_titre = objet_info($type,'titre');
	// champ utilisé pour la recherche : s'il est donné on vérifie qu'il est valide, sinon on prend le champ de titre
	$champ = ($champ and in_array($champ,array_keys(objet_info($type,'field')))) ? $champ.' AS titre' : $champ_titre;
	// nombre maximal de résultats
	if (!intval($nb) OR is_null($nb)) $nb = 20;
	else $nb = intval($nb);

	// Objet secondaire (optionnel)
	if ($type2) {
		$table_objet_sql2 = table_objet_sql($type2);
		$table_objet2 = table_objet($type2);
	}

	// En fonction du pivot qui porte les liens, on fait des requêtes différentes.
	// Comme on ne peut pas utiliser l'alias «titre» du SELECT dans le WHERE,
	// on fait des sous-requêtes.
	$pivot = ($type2) ? ($pivot2) ? 'secondaire' : 'principal' : '';
	$res = array();
	switch ($pivot){

		/*
		1) pas de pivot : on cherche des `$type` tout simplement
		exemple: des albums...
		SELECT titre,id
		FROM (
			SELECT id_album AS id, titre, lang AS lang
			FROM spip_albums
		) sub
		WHERE sub.titre LIKE '%terme%'
		*/
		case '':
			// clé primaire de l'objet (id_xxx)
			$id_table_objet = id_table_objet($type);
			// préparer la requête
			$alias = 'sub'; // alias de la sous-requête
			$select = array(
				"$id_table_objet AS id",
				$champ
			);
			$from = $table_objet_sql;
			$getselect = '('.sql_get_select($select, $from).') '.$alias;
			if ($terme) {
				$where[] = ($id=intval($terme)) ?
					array('OR', "$alias.titre LIKE ".sql_quote("%$terme%"), "$alias.id=".$id)
					: "$alias.titre LIKE ".sql_quote("%$terme%");
			}
			$groupby = "id";
			$orderby = '';
			$limit = "0,$nb";
			$res = sql_allfetsel('titre,id',$getselect,$where,$groupby,$orderby,$limit);
			//var_dump(sql_allfetsel('titre,id',$getselect,$where,'','','','','',false));
			break;

		/*
		2) l'objet principal sert de pivot : on cherche des `$type` liés à des `$type2`
		exemple: auteurs liés à des albums
		SELECT titre,id
		FROM (
			SELECT T1.id_auteur AS id, nom AS titre, '' AS lang
			FROM spip_auteurs AS T1
			INNER JOIN spip_auteurs_liens AS L1 ON (L1.id_auteur = T1.id_auteur)
			WHERE L1.objet = 'album'
		) sub
		WHERE sub.titre LIKE '%terme%'
		*/
		case 'principal':
			// clé primaire de l'objet (id_xxx) et sa table de liens
			include_spip('action/editer_liens');
			if (!$a = objet_associable($type)) return;
			list($id_table_objet, $table_objet_sql_liens) = $a;
			// préparer la requête
			$alias = 'sub'; // alias de la sous-requête
			$select = array(
				"T1.$id_table_objet AS id",
				$champ
			);
			$from = "$table_objet_sql AS T1"
				." INNER JOIN $table_objet_sql_liens AS L1"
				." ON (L1.$id_table_objet = T1.$id_table_objet)";
			$where_sub[] = "L1.objet=".sql_quote($type2);
			$getselect = '('.sql_get_select($select, $from, $where_sub).') '.$alias;
			if ($terme) {
				$where[] = ($id=intval($terme)) ?
					array('OR', "$alias.titre LIKE ".sql_quote("%$terme%"), "$alias.id=".$id)
					: "$alias.titre LIKE ".sql_quote("%$terme%");
			}
			$groupby = "id";
			$orderby = '';
			$limit = "0,$nb";
			// requête
			$res = sql_allfetsel('titre,id',$getselect,$where,$groupby,$orderby,$limit);
			//var_dump(sql_allfetsel('titre,id',$getselect,$where,'','','','','',false));
			break;
 
		/*
		3) l'objet secondaire sert de pivot : on cherche des `$type` auxquels sont liés des `$type2`
		exemple: articles auxquels sont liés des albums
		SELECT titre,id
		FROM (
			SELECT T1.id_article AS id, titre, lang
			FROM spip_articles AS T1
			INNER JOIN spip_albums_liens AS L2 ON ( L2.objet = 'article' AND L2.id_objet = T1.id_article )
		) sub
		WHERE sub.titre LIKE '%terme%'
		*/
		case 'secondaire':
			// clé primaire de l'objet (id_xxx) et sa table de liens
			include_spip('action/editer_liens');
			if (!$a = objet_associable($type2)) return;
			list($id_table_objet2, $table_objet_sql_liens2) = $a;
			$id_table_objet = id_table_objet($type);
			// préparer la requête
			$alias = 'sub'; // alias de la sous-requête
			$select = array(
				"T1.$id_table_objet AS id",
				$champ
			);
			$from = "$table_objet_sql AS T1"
				." INNER JOIN $table_objet_sql_liens2 AS L2"
				." ON ( L2.objet = ".sql_quote($type)." AND L2.id_objet = T1.$id_table_objet )";
			$getselect = '('.sql_get_select($select, $from).') '.$alias;
			if ($terme) {
				$where[] = ($id=intval($terme)) ?
					array('OR', "$alias.titre LIKE ".sql_quote("%$terme%"), "$alias.id=".$id)
					: "$alias.titre LIKE ".sql_quote("%$terme%");
			}
			$groupby = "id";
			$orderby = '';
			$limit = "0,$nb";
			// requête
			$res = sql_allfetsel('titre,id',$getselect,$where,$groupby,$orderby,$limit);
			//var_dump(sql_allfetsel('titre,id',$getselect,$where,'','','','','',false));
			break;

	}

	return $res;
}

?>
