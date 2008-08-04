<?php

/* Test de sécurité */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_BUG ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_BUG',array());
	return $p;
}


function balise_FORMULAIRE_BUG_stat($args, $filtres) {

	return ($args);
}


function balise_FORMULAIRE_BUG_dyn() {

/*
 * Si tentative d'attaquer un bug déjà existant, on jette.
 */

if (isset($_GET['id_bug'])) return _T('atelier:erreur_protection');

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




$variables['champs']['id_bug'] = '';
$variables['type']['id_bug'] =  'entier';

$variables['champs']['id_projet'] = '';
$variables['type']['id_projet'] =  'entier';

$variables['champs']['titre'] = '';
$variables['types']['titre'] =  'texte';

$variables['champs']['descriptif'] = '';
$variables['types']['descriptif'] =  'texte';

$variables['champs']['version'] = '';
$variables['types']['version'] =  'texte';

$variables['champs']['version_spip'] = '';
$variables['types']['version_spip'] =  'texte';

$variables['champs']['url_site'] = _request('url_site');

foreach ($variables['champs'] as $key => $champ) {
	if (empty($champ)) $variables['champs'][$key] = stripslashes(_request($key));
}

// traitement particulier pour id_bug et id_projet
$variables['champs']['id_bug'] = intval($variables['champs']['id_bug']);
$variables['champs']['id_projet'] = intval($variables['champs']['id_projet']);

$lang = _request('var_lang');

if (!empty($variables['actions']['abandonner'])) {

	// construction de la page de retour
	$message = '<META HTTP-EQUIV="refresh" content="0; url='.$variables['champs']['url_site'].'">';
	
	return $message;
}

/*
 * Gestion de l'identifiant
 * on demande un nouvel identifiant pour l'article si l'utilisateur clique sur l'un des boutons action
 */

$identifiant = false;

foreach ($variables['actions'] as $key => $action) {
	if (!empty($action)) $identifiant = true;
}

if ($identifiant == true) {
	if (!$variables['champs']['id_bug']) { // premier passage
		$variables['champs']['id_bug'] = atelier_request_new_id();
	}
}
// FIN gestion identifiant

if (!empty($variables['actions']['valider'])) {

		// construction du tableau $champs pour les pipelines pre_edition et post_edition
		$champs = array(
			'id_projet' => $variables['champs']['id_pojet'],
			'titre' => $variables['champs']['titre'],
			'descriptif' => $variables['champs']['descriptif'],
			'version' => $variables['champs']['version'],
			'version_spip' => $variables['champs']['version_spip'],

		);
		
		// calcul la date
		$champs['date'] = date('Y-m-d H:i:s');

		sql_update(
			'spip_bugs',
			array(	"titre" => sql_quote($champs['titre']),
				"id_projet" => sql_quote($champs['id_projet']),
				"descriptif" => sql_quote($champs['descriptif']),
				"version" => sql_quote($champs['version']),
				"version_spip" => sql_quote($champs['version_spip']),
				"date" => sql_quote($champs['date'])),
			 array("id_bug=".$variables['champs']['id_bug'])
		);

		// construction de la page de retour
		$message = '<META HTTP-EQUIV="refresh" content="0; url='.$variables['champs']['url_site'].'">'.'merci d\'avoir pris le temps de rapporter un bug';
	
		return $message;

}


// Envoi de toutes les variables principales au formulaire principale
return array('formulaires/formulaire_bug', 0, $variables['champs']);

}

function atelier_request_new_id() {

	sql_insertq(
		'spip_bugs',
		array ('date' => 'NOW()')
	);

	$ret = sql_fetsel(
		array('MAX(id_bug) as id_bug'),
		array('spip_bugs')
	);

	$bug = $ret['id_bug'];

	return $bug;
}

?>
