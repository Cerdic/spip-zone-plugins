<?php
/**
 * Formulaire pour renseigner une redirection
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Installation
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de redirection
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $retour
 * @return array|bool
 */
function formulaires_rediriger_objet_virtuel_charger_dist($objet, $id_objet, $retour = '') {

	include_spip('inc/autoriser');
	if (!autoriser('modifier', $objet, $id_objet)) {
		return false;
	}

	$id_table_objet = id_table_objet($objet);
	$table = table_objet_sql($objet);
	$row = sql_fetsel([$id_table_objet . ' AS id', 'virtuel'], $table, $id_table_objet . '=' . intval($id_objet));
	if (!$row['id']) {
		return false;
	}

	include_spip('inc/lien');
	include_spip('inc/objets_virtuels');

	$redirection = virtuel_redirige($row['virtuel']);
	if (!$redirection and !in_array($table, objets_virtuels_tables_actives())) {
		return false;
	}

	include_spip('inc/texte');
	$valeurs = array(
		'redirection' => $redirection,
		'id' => $id_objet,
		'objet' => $objet,
		'_afficher_url' => ($redirection ? propre("[->$redirection]") : ''),
	);

	return $valeurs;
}

/**
 * Vérifications du formulaire de redirection
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $retour
 * @return array|bool
 */
function formulaires_rediriger_objet_virtuel_verifier_dist($objet, $id_objet, $retour = '') {
	$erreurs = array();

	$redirection = _request('redirection');
	$type = objet_type($objet);
	include_spip('inc/lien');

	// éviter des boucles de redirection
	if (
		($objet == 'article' and $redirection == $id_objet)
		or ($redirection == $type . $id_objet)
		or ($rac = typer_raccourci($redirection) and $rac[0] == $type and $rac[1] == $id_objet)
	){
		$erreurs['redirection'] = _T('info_redirection_boucle');
	}

	return $erreurs;
}


/**
 * Traitement du formulaire de redirection
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $retour
 * @return array|bool
 */
function formulaires_rediriger_objet_virtuel_traiter_dist($objet, $id_objet, $retour = '') {

	$url = preg_replace(',^\s*https?://$,i', '', rtrim(_request('redirection')));
	if ($url) {
		$url = corriger_caracteres($url);
	}

	include_spip('action/editer_objet');
	objet_modifier($objet, $id_objet, ['virtuel' => $url]);

	// Exception : si c'est une rubrique, la publier.
	if (objet_type($objet) == 'rubrique') {
		sql_updateq(table_objet_sql($objet), array('statut' => 'publie'), id_table_objet($objet) . '=' . $id_objet);
	}

	// malheureusement le ajax=wysiwyg n'est pas toujours présent sur l’inclusion prive/objets/contenu/xx,
	// donc ce JS n'actualise pas toujours le centre de la page.
	$js = _AJAX ? '<script type="text/javascript">
		if (window.ajaxReload) {
			$("#objet_virtuel").ajaxReload({args:{virtuel:"' . $url . '"}});
			ajaxReload("navigation");
		}
	</script>' : '';

	return [
		'message_ok' => ($url ? _T('info_redirection_activee') : _T('info_redirection_desactivee')) . $js,
		'redirect' => $retour ? $retour : '',
		'editable' => true
	];
}
