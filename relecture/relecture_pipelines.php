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

			$flux['data'] .= recuperer_fond('prive/squelettes/navigation/article-relecture_ouverte',
								array($id_table_objet => $id));
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
		if ($id = intval($flux['args']['id'])) {
			$table = table_objet($type);
			$id_table_objet = id_table_objet($type);

			$flux['data'] .= recuperer_fond('prive/squelettes/infos/article-voir_relectures_cloturees',
								array($id_table_objet => $id));
		}
	}

	return $flux;
}


/**
 * Surcharge de la fonction charger des formulaires concernes, a savoir :
 * - dater : dans la page relecture permet de choisir la date de fin des commentaires
 * - editer_liens : dans la page relecture permet de choisir les relecteurs
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_formulaire_charger($flux){

	static $forms_concernes = array('dater', 'editer_liens');

	$form = $flux['args']['form'];
	$objet = $flux['data']['objet'];
	$id_objet = intval($flux['data']['id_objet']);

	if ((in_array($form, $forms_concernes)) AND ($objet == 'relecture')) {
		// Rendre editable le formulaire si la relecture n'est pas cloturee
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_objet");
		$statut = sql_getfetsel('statut', $from, $where);
		$flux['data']['editable'] = ($statut !== 'fermee');

		if ($form =='dater') {
			// Identifier le label comme la date de fin des commentaires
			$flux['data']['_label_date'] = _T('relecture:label_relecture_date_fin_commentaire');
		}
		else if ($form =='editer_liens') {
			// Changer le titre du formulaire pour désigner clairement les relecteurs
			$flux['data']['titre'] = _T('relecture:titre_liste_relecteurs');
		}
	}

	return $flux;
}

?>
