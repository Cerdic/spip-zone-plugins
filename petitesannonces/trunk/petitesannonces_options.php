<?php
if (!defined("_ECRIRE_INC_VERSION")) {return;}

define('_DEBUG_AUTORISER', true);


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

function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
	if ($type=="rubrique") $table_type="spip_rubriques";
	
	//SI composition=petitesannonces
	$id_rubrique_en_petitesannonces = sql_getfetsel("id_rubrique", $table_type, "id_rubrique=".intval($id)." and composition='petitesannonces'");
	if (!empty($id_rubrique_en_petitesannonces)){
		return ($id and autoriser('voir', 'rubrique', $id) and  in_array($qui['statut'], array('0minirezo', '1comite','6forum')));
	} else { // SINON (cas général)
		return
			$id
			and autoriser('voir', 'rubrique', $id)
			and autoriser('creer', 'article');
	}
}