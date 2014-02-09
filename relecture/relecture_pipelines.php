<?php

/* ----------------------- LIBRAIRIES ----------------------- */

/**
 * Inclusion dans la page de suivi d'une relecture du script jquery ui accordion
 *
 * @param array $script
 * @return array
 *
**/
function relecture_jqueryui_plugins($scripts) {

	$page = _request('exec');
	if ($page == 'relecture') {
		$scripts[] = "jquery.ui.accordion";
	}

	return $scripts;
}

/**
 * Insertion du script jquery qtip
 *
 * @param $flux
 * @return mixed
 */
function relecture_header_prive($flux){
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path('lib/jquery.qtip/jquery.qtip.js') .'"></script>';
	return $flux;
}

/**
 * Insertion de la css du script jquery qtip
 *
 * @param $flux
 * @return mixed
 */
function relecture_header_prive_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path('lib/jquery.qtip/jquery.qtip.css') .'" />';
	return $flux;
}

/* ----------------------- AFFICHAGES ----------------------- */

/**
 * Affichage en rappel dans la page d'accueil pour l'auteur connecte :
 * - des relectures auxquelles il participe en tant que relecteur
 * - des relectures qu'il administre en tant qu'auteur de l'article
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_affiche_milieu($flux) {

	if (($type = $flux['args']['type-page'])=='accueil') {
		$flux['data'] .= recuperer_fond('prive/squelettes/contenu/accueil-relectures');
	}

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

//			$flux['data'] .= recuperer_fond('prive/squelettes/navigation/article-relecture',
//								array($id_table_objet => $id));
		}
	}

	return $flux;
}


/**
 * Affichage dans la boite d'informations de l'article en cours d'affichage :
 * - du statut modifié de par la relecture en cours
 * - du lien menant à la relecture en cours
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

//			$flux['data'] .= recuperer_fond('prive/squelettes/infos/article-relecture',
//								array($id_table_objet => $id));
		}
	}

	return $flux;
}


/**
 * Surcharge de la fonction charger des formulaires concernes, a savoir :
 * - relecture / dater : dans la page relecture permet de choisir la date de fin des commentaires
 * - relecture / editer_liens : dans la page relecture permet de choisir les relecteurs
 * - relecture / instituer_objet : renforce le test standard base sur l'autorisation modifier en testant si tous les
 *   commentaires ont ete traites
 * - article / instituer_objet : dans la page de l'article en cours de relecture bloque le statut de l'article
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_formulaire_charger($flux){

	$form = $flux['args']['form'];
	$objet = $flux['data']['objet'] ? $flux['data']['objet'] : $flux['data']['_objet'];
	$id_objet = intval($flux['data']['id_objet']) ? intval($flux['data']['id_objet']) : intval($flux['data']['_id_objet']);

	if ($objet == 'relecture') {

		if ($form == 'dater') {
			// Identifier le label comme la date de fin des commentaires
			$flux['data']['_label_date'] = _T('relecture:label_date_fin_commentaire');
			// Le formulaire n'est editable que si l'autorisation modifier est accordee.
			$flux['data']['editable'] = autoriser('modifier', 'relecture', $id_objet);
		}
		else if ($form == 'editer_liens') {
			// Changer le titre du formulaire pour désigner clairement les relecteurs
			$flux['data']['titre'] = _T('relecture:titre_liste_relecteurs');
			// Le formulaire n'est editable que si l'autorisation modifier est accordee.
			$flux['data']['editable'] = autoriser('modifier', 'relecture', $id_objet);
		}
		else if ($form == 'instituer_objet') {
			// A ce stade, le formulaire instituer_objet est deja testé avec l'autorisation modifier.
			// On rajoute le fait qu'il n'y ait plus de commentaires a traiter
			// TODO : verifier qu'on le fait ici et pas dans l'autorisation instituer sur le verifier ?
			// Le formulaire n'est editable que si l'autorisation modifier est accordee.
			$flux['data']['editable'] = autoriser('instituer', 'relecture', $id_objet);
		}
	}
	else if ($objet == 'article') {
		if ($form == 'instituer_objet') {
			// Si une relecture est ouverte sur l'article alors on interdit de modifier
			// le statut de l'article qui reste a "en cours de redaction"
			$from = 'spip_relectures';
			$where = array("id_article=$id_objet", "statut=" . sql_quote('ouverte'));
			$flux['data']['editable'] = (sql_countsel($from, $where) == 0);
		}
	}

	return $flux;
}


/* ----------------------- OBJETS ----------------------- */

/**
 * Surcharge de l'insertion standard d'un objet relecture :
 * - informations sur l'article
 * - date de fin des commentaires
 *
 * Surcharge de l'insertion standard d'un objet commentaire :
 * - element et repere du commentaire dans le texte de cet element
 *
 * @param array $flux
 * @return array
 *
**/
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
				if ($_cle == 'id_article')
					$flux['data'][$_cle] = intval($_valeur);
				else {
					// On ne recupere que les textes qui comportent des mots !
					$texte = trim($_valeur);
					if ($texte)
						$flux['data'][$_cle] = $texte;
				}
			}

			// - correction de la date de fin de commentaire positionnee par defaut a cause de la configuration
			$flux['data']['date_fin_commentaire'] = date('Y-m-d H:i:s', strtotime("+1 week"));

			// - Le statut, la date d'ouverture et la revision de l'article a l'ouverture sont mis a jour dans la fonction
			// instituer surchargee dans le pipeline pre_edition
		}
	}
	else if ($flux['args']['table'] == 'spip_commentaires') {
		if ($id_relecture = intval(_request('id_relecture'))) {
			include_spip('inc/session');

			// - ajout des informations de base sur le commentaire
			$flux['data']['id_relecture'] = $id_relecture;
			$flux['data']['element'] = _request('element');
			$flux['data']['repere'] = serialize(array(intval(_request('index_debut')), intval(_request('index_fin'))));

			// - l'auteur du commentaire (auteur connecte)
			$flux['data']['id_emetteur'] = session_get('id_auteur');

			// - Le statut est mis a jour dans la fonction instituer surchargee dans le pipeline pre_edition
		}
	}

	return $flux;
}


/**
 * Surcharge de l'action instituer standard d'un objet en incluant des traitements prealables pour une relecture :
 * - pour une ouverture, on ecrase le statut a ouverte car il est automatiquement mis a prepa par defaut
 * - pour une cloture, date et revision de cloture
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_pre_edition($flux) {

	$table = $flux['args']['table'];
	$id = intval($flux['args']['id_objet']);
	$action = $flux['args']['action'];

	// Traitements particuliers de l'objet relecture dans le cas d'une cloture :
	if (($table == 'spip_relectures')
	AND ($id)) {

		// Instituer
		if ($action == 'instituer') {

			// Recherche de l'id de l'article sur lequel porte la relecture
			$from = 'spip_relectures';
			$where = array("id_relecture=$id");
			$id_article = sql_getfetsel('id_article', $from, $where);
			// Determination de la revision courante de l'article
			$from = 'spip_versions';
			$where = array("objet=" . sql_quote('article'), "id_objet=$id_article");
			$revision = sql_getfetsel('max(id_version) AS revision', $from, $where);

			// -- Ouverture
			if (($flux['args']['statut_ancien'] == 'ouverte')
			AND (!isset($flux['data']['statut']))) {
				// - mise a jour de la date d'ouverture
				$flux['data']['date_ouverture'] = date('Y-m-d H:i:s');

				// - mise a jour de la revision d'ouverture
				$flux['data']['revision_ouverture'] = $revision;
			}

			// -- Cloture
			if (($flux['args']['statut_ancien'] == 'ouverte')
			AND ($flux['data']['statut'] == 'fermee')) {
				// - mise a jour de la date de cloture
				$flux['data']['date_cloture'] = date('Y-m-d H:i:s');

				// - mise a jour de la revision de cloture
				$flux['data']['revision_cloture'] = $revision;
			}
		}
	}
	else if (($table == 'spip_commentaires'   )
	AND ($id)) {

		// Instituer
		if ($action == 'instituer') {

			// -- Ouverture
			if ($flux['args']['statut_ancien'] == 'prepa') {
				// - mise a jour du "vrai" statut de la relecture
				$flux['data']['statut'] = 'ouvert';

				// - mise a jour de la date d'ouverture
				$flux['data']['date_ouverture'] = date('Y-m-d H:i:s');
			}

			// -- Cloture
			if (($flux['args']['statut_ancien'] == 'ouvert')
			AND (in_array($flux['data']['statut'], array('accepte', 'refuse', 'poubelle')))) {
				// - mise a jour de la date de cloture
				$flux['data']['date_cloture'] = date('Y-m-d H:i:s');
			}
		}
	}

	return $flux;
}

?>
