<?php
/*
 * Gestion du formulaire d'ajout de sélection éditoriale
 *
 * Basé sur le formulaire editer_liens
 * 2 différences :
 *   - Juste de l'ajout, pas besoin d'afficher les sélections liées
 *   - Le bouton « créer et associer » crée directement une nouvelle sélection
 *
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Chargement du formulaire d'ajout de sélection
 *
 * @param string $objet
 * @param string|int $id_objet
 * @param array $options
 *    redirect : URL de redirection
 *    url_fermer : transforme le bouton ferme en lien ajax
 *    ajaxreload : identifiant d'un bloc ajax à recharger après le submit
 *
 * @return array
 */
function formulaires_ajouter_selection_charger_dist($objet, $id_objet, $options) {

	// On récupère les valeurs de editer_liens
	$charger_editer_liens = charger_fonction('charger', 'formulaires/editer_liens');
	$valeurs = $charger_editer_liens('selection', $objet, $id_objet, $options);

	return $valeurs;
}

/**
 * Traiter le post des informations d'édition de liens
 *
 * @param string $objet
 * @param string|int $id_objet
 * @param array $options
 *    redirect : URL de redirection
 *    url_fermer : transforme le bouton ferme en lien ajax
 *    ajaxreload : identifiant d'un bloc ajax à recharger après le submit
 *
 * @return array
 */
function formulaires_ajouter_selection_traiter_dist($objet, $id_objet, $options) {

	$retours = array('editable' => true);

	// Soit on en crée une nouvelle
	if (_request('creer_selection')) {
		$ajouter_selection_objet = charger_fonction('ajouter_selection_objet', 'action');
		$id_selection = $ajouter_selection_objet("$objet-$id_objet");
		set_request('id_lien_ajoute', $id_selection);
		if ($redirect) {
			$redirect = ancre_url($redirect, "selection$id_selection");
		}
	
	// Soit on on associe une existante : récupérer les traitements de editer_liens
	} else {
		$traiter_editer_liens = charger_fonction('traiter', 'formulaires/editer_liens');
		$retours = $traiter_editer_liens('selection', $objet, $id_objet, array('redirect' => $redirect));
	}

	// Redirection
	// Si on a demandé ajax, on ne lance pas de redirection, on renvoit un petit morceau de code JS dans message_ok
	if (
		_request('var_ajax')
		and isset($options['ajaxreload'])
		and $ajaxreload = $options['ajaxreload']
	) {
		$redirect_ajax = str_replace('&amp;', '&', $redirect);
		$args = '{id_selection_ajoutee:' . _request('id_lien_ajoute') . '}';
		$js = "if (window.jQuery) { ajaxReload('$ajaxreload', {history: true, href: '$redirect_ajax', args: $args}); }";
		$retours['message_ok'] = '<script type="text/javascript">'.$js.'</script>';
	}
	// Sinon on recharge tout
	else {
		$retours['redirect'] = $redirect;
	}

	return $retours;
}