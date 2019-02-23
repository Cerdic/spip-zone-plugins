<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;
function notifications_objets_location_client_dist($quoi, $id_objets_location, $options) {
	include_spip('inc/config');

	$config = lire_config('location_objets');
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	$options['id_objets_location'] = $id_objets_location;
	$options['qui'] = 'client';
	$nom_site = $GLOBALS['meta']['nom_site'];
	$subject = _T('objets_location:sujet_votre_objets_location_sur', array(
		'nom' => $nom_site
	));

	/* Chercher des chaines de langues spécifiques pour les différents statuts */
	$lang = $options['lang'];

	$var_objets_location = 'i18n_objets_location_' . $lang;
	$chaine_statut = 'sujet_votre_objets_location_' . $options['statut'];

	if (isset($GLOBALS[$var_objets_location][$chaine_statut]))
		$subject = _T('objets_location:texte_statut_' . $chaine_statut, array(
			'nom' => $nom_site
		));

	$email = $options['email'];
	$message = recuperer_fond('notifications/contenu_objets_location_mail', $options);

	// Envoyer les emails

	$o = array(
		'html' => $message
	);

	$envoyer_mail($email, $subject, $o);

	// Si présent - l'api de notifications_archive
	if ($archiver = charger_fonction('archiver_notification', 'inc', true)) {
		$envoi = 'reussi';
		if (!$envoyer_mail)
			$envoi = 'echec';

		$o = array(
			'recipients' => $email,
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
