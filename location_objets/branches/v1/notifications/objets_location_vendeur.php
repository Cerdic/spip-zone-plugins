<?php
if (! defined("_ECRIRE_INC_VERSION"))
	return;

function notifications_objets_location_vendeur_dist($quoi, $id_objets_location, $options) {
	include_spip('inc/config');

	$config = lire_config('location_objets');
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	$options['id_objets_location'] = $id_objets_location;
	$options['qui'] = 'vendeur';
	$dest = (
			isset($config['vendeur_' . $config['vendeur']]) and intval($config['vendeur_' . $config['vendeur']])
			) ?
			$config['vendeur_' . $config['vendeur']] :
			array(1);

	$sql = sql_select('email', 'spip_auteurs', 'id_auteur IN (' . implode(',', $dest) . ')');

	$email = array();
	while ($data = sql_fetch($sql)) {
		$email[] = $data['email'];
	}

	$subject = _T('objets_location:sujet_une_location_sur', array(
		'nom' => $GLOBALS['meta']['nom_site']
	));

	/* Chercher des chaines de langues spécifiques pour les différents statuts */
	$lang = $options['lang'];

	$var_location = 'i18n_location_' . $lang;
	$chaine_statut = 'sujet_une_location_' . $options['statut'];

	if (isset($GLOBALS[$var_location][$chaine_statut]))
		$subject = _T('objets_location:' . $chaine_statut, array(
			'nom' => $GLOBALS['meta']['nom_site']
		));

	$message = recuperer_fond('notifications/contenu_objets_location_mail', $options);

	//
	// Envoyer les emails
	//
	$envoyer_mail($email, $subject, array(
		'html' => $message
	));

	// Si présent - l'api de notifications_archive
	if ($archiver = charger_fonction('archiver_notification', 'inc', true)) {
		$envoi = 'reussi';
		if (! $envoyer_mail)
			$envoi = 'echec';

		$o = array(
			'recipients' => implode(',', $email),
			'sujet' => $subject,
			'texte' => $message,
			'html' => 'oui',
			'id_objet' => $id_objets_location,
			'objet' => 'objets_location',
			'envoi' => $envoi,
			'type' => $quoi
		);

		$archiver($o);
	}
}
