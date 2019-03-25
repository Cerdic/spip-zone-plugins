<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action pour supprimer un article/annonce
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, annonce, #ID_ARTICLE}|oui)
 *         [(#BOUTON_ACTION{<:supprimer_annonce:>,
 *             #URL_ACTION_AUTEUR{supprimer_annonce, #ID_ARTICLE, #URL_RUBRIQUE},
 *             danger, <:confirmer_supprimer_annonce:>})]
 *     ]
 *     ```
**/
function action_supprimer_annonce_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_articles',  'id_article=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_annonce_dist $arg pas compris");
	}
}
