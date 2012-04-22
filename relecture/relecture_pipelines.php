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

			$flux['data'] .= recuperer_fond('prive/squelettes/infos/article-voir_relectures',
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


function relecture_pre_insertion($flux) {

	// Traitements particuliers de l'objet relecture dans le cas d'une ouverture :
	if ($flux['args']['table'] == 'spip_relectures') {
		if ($id_article = intval(_request('id_article'))) {
			// - recuperation des informations de l'article concerne (id, chapo, texte, descriptif, ps et la revision courante)
			$select = array('id_article, chapo AS article_chapo', 'descriptif AS article_descr', 'texte AS article_texte', 'ps AS article_ps');
			$from = 'spip_articles';
			$where = array("id_article=$id_article");
			$article = sql_fetsel($select, $from, $where);
			foreach ($article as $_cle => $_valeur) {
				$flux['data'][$_cle] = $_valeur;
			}

			// - mise a jour de la revision d'ouverture
			// - correction de la date de fin de commentaire positionnee par defaut a cause de la configuration
			// - mise a jour de la date d'ouverture
			$from = 'spip_versions';
			$where = array("objet=" . sql_quote('article'), "id_objet=$id_article");
			$revision = sql_getfetsel('max(id_version) AS revision_ouverture', $from, $where);
			$flux['data']['revision_ouverture'] = $revision;
			$flux['data']['date_ouverture'] = $flux['data']['date_fin_commentaire'];
			$flux['data']['date_fin_commentaire'] = date('Y-m-d H:i:s', strtotime("+1 week"));

			// - surcharge la valeur du statut mis par le traitement par defaut
			$flux['data']['statut'] = 'ouverte';
		}
	}

	return $flux;
}

?>
