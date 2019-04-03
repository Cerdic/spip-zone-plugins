<?php
if (!defined("_ECRIRE_INC_VERSION")) {return;}
/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function petitesannonces_autoriser() { }

// placer cette fonction dans config/mes_options.php
/**
 * Autorisation de créer un article dans une rubrique $id
 *
 * Il faut pouvoir voir la rubrique et pouvoir créer un article…
 * mais pour les petites annonces on peut être 0minirezo, 1comite ou même 6forum
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
// function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
// 	if ($type=="rubrique") $table_type="spip_rubriques";
// 	
// 	//SI composition=petitesannonces
// 	$id_rubrique_en_petitesannonces = sql_getfetsel("id_rubrique", $table_type, "id_rubrique=".intval($id)." and composition='petitesannonces'");
// 	if (!empty($id_rubrique_en_petitesannonces)){
// 		return ($id and autoriser('voir', 'rubrique', $id) and  in_array($qui['statut'], array('0minirezo', '1comite','6forum')));
// 	} else { // SINON (cas général)
// 		return
// 			$id
// 			and autoriser('voir', 'rubrique', $id)
// 			and autoriser('creer', 'article');
// 	}
// }


/**
 * Autorisation de modifier un article $id
 *
 * Il faut pouvoir publier dans le parent
 * ou, si on change le statut en proposé ou préparation être auteur de l'article
 * pour les petites annonces on peut être aussi 6forum
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel('id_rubrique,statut', 'spip_articles', 'id_article=' . sql_quote($id));
	return
		$r
		and
		(
			autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
			or (
				in_array($qui['statut'], array('0minirezo', '1comite', '6forum'))
				and auteurs_objet('article', $id, 'id_auteur=' . $qui['id_auteur'])
			)
		);
}


/**
 * Autorisation de supprimer (annonce)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_annonce_supprimer($faire, $type, $id, $qui, $opt) {
	// soit on est webmestre soit on est auteur de l'article
	return ($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or ($auteurs = auteurs_objet('article', $id) and in_array($qui['id_auteur'], $auteurs));

}
