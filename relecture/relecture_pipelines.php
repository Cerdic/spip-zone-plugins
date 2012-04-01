<?php

/**
 * Ajout de l'onglet Ajouter les plugins dont l'url depend du l'existence ou pas d'un depot
 * de plugins
 *
 * @param array $flux
 * @return array
 */
function relecture_ajouter_onglets($flux) {
    return $flux;
}


/**
 * Affichage du bloc relecture de l'article en cours d'affichage :
 * - bouton ouvrir une relecture
 * - ou informations sur la relecture en cours
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_affiche_gauche($flux) {

	if (($type = $flux['args']['exec'])=='article'){
		if ($id = intval($flux['args']['id_article'])) {
			$table = table_objet($type);
			$id_table_objet = id_table_objet($type);

			$flux['data'] .= recuperer_fond('prive/squelettes/navigation/article_relecture', array($id_table_objet => $id));
		}
	}

	return $flux;
}


/**
 * Affichage dans la boite d'informations de l'article en cours d'affichage :
 * - du lien menant a l'historique des relectures cloturees
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_boite_infos($flux){

	if (($type = $flux['args']['type'])=='article') {
		if ($id = intval($flux['args']['id']) AND autoriser('voirrelectures', $type, $id)) {
			$from = 'spip_relectures';
			$where = array("id_article=$id", "etat=" . sql_quote('fermee'));
			$nb_relectures_fermees = sql_countsel($from, $where);

			if ($nb_relectures_fermees > 0) {
				include_spip('inc/presentation');
				$flux['data'] .= icone_horizontale(
									_T('relecture:bouton_historique_relectures'),
									generer_url_ecrire('relectures',"id_article=$id"),
									"relecture-ok-24.png");
			}
		}
	}

	return $flux;
}

?>
