<?php

/* Test de sécurité */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_AMELIORATION ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_AMELIORATION',array());
	return $p;
}


function balise_FORMULAIRE_AMELIORATION_stat($args, $filtres) {

	return ($args);
}


function balise_FORMULAIRE_AMELIORATION_dyn() {

/*
 * Si tentative d'attaquer une amélioration déjà existant, on jette.
 */

if (isset($_GET['id_amelioration'])) return _T('atelier:erreur_protection');

$variables = array(
	'champs' => array(),
	'types' => array(),
	'actions' => array()
);



$variables['actions']['valider'] = '';
$variables['actions']['abandonner'] = '';


/*
 * Récuperation des valeurs pour toutes les actions
 */

foreach ($variables['actions'] as $key => $action) {
		$variables['actions'][$key] = _request($key);
}




$variables['champs']['id_amelioration'] = '';
$variables['type']['id_amelioration'] =  'entier';

$variables['champs']['id_projet'] = '';
$variables['type']['id_projet'] =  'entier';

$variables['champs']['titre'] = '';
$variables['types']['titre'] =  'texte';

$variables['champs']['descriptif'] = '';
$variables['types']['descriptif'] =  'texte';

$variables['champs']['url_site'] = _request('url_site');

foreach ($variables['champs'] as $key => $champ) {
	if (empty($champ)) $variables['champs'][$key] = stripslashes(_request($key));
}

// traitement particulier pour id_amelioration et id_projet
$variables['champs']['id_amelioration'] = intval($variables['champs']['id_amelioration']);
$variables['champs']['id_projet'] = intval($variables['champs']['id_projet']);

$lang = _request('var_lang');

if (!empty($variables['actions']['abandonner'])) {

	// construction de la page de retour
	$message = '<META HTTP-EQUIV="refresh" content="0; url='.$variables['champs']['url_site'].'">';
	
	return $message;
}

/*
 * Gestion de l'identifiant
 * on demande un nouvel identifiant pour l'amelioration si l'utilisateur clique sur l'un des boutons action
 */

$identifiant = false;

foreach ($variables['actions'] as $key => $action) {
	if (!empty($action)) $identifiant = true;
}

if ($identifiant == true) {
	if (!$variables['champs']['id_amelioration']) { // premier passage
		$variables['champs']['id_amelioration'] = atelier_request_new_id();
	}
}
// FIN gestion identifiant

if (!empty($variables['actions']['valider'])) {

		// construction du tableau $champs pour les pipelines pre_edition et post_edition
		$champs = array(
			'id_projet' => $variables['champs']['id_projet'],
			'titre' => $variables['champs']['titre'],
			'descriptif' => $variables['champs']['descriptif'],
		);
		
		// calcul la date
		$champs['date'] = date('Y-m-d H:i:s');

		sql_update(
			'spip_ameliorations',
			array(	"titre" => sql_quote($champs['titre']),
				"id_projet" => sql_quote($champs['id_projet']),
				"descriptif" => sql_quote($champs['descriptif']),
				"date" => sql_quote($champs['date'])),
			 array("id_amelioration=".$variables['champs']['id_amelioration'])
		);

		// construction de la page de retour
		$message = '<META HTTP-EQUIV="refresh" content="0; url='.$variables['champs']['url_site'].'">'.'merci d\'avoir pris le temps de poster une demande d\'am&eacute;lioration, celle-ci sera signal&eacute; aux responsables du projet et si elle rentre dans le cadre du projet, sera int&eacute;gr&eacute;e aux versions futures.';
	
		return $message;

}


// Envoi de toutes les variables principales au formulaire principale
return array('formulaires/formulaire_amelioration', 0, $variables['champs']);

}

function atelier_request_new_id() {

	sql_insertq(
		'spip_ameliorations',
		array ('date' => 'NOW()')
	);

	$ret = sql_fetsel(
		array('MAX(id_amelioration) as id_amelioration'),
		array('spip_amelirations')
	);

	$amelioration = $ret['id_amelioration'];

	return $amelioration;
}

?>
