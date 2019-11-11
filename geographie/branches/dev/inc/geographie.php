<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_GEOGRAPHIE_ENDPOINT_BASE_URL')) {
	/**
	 * Préfixe des URL du service web de ITIS.
	 */
	define('_GEOGRAPHIE_ENDPOINT_BASE_URL', 'https://demopot.smellup.net/http.api/ezrest/');
}


/**
 * Chargement des pays à partir du serveur de Nomenclatures Officielles.
 *
 * @return bool `true` si le chargement s'est bien passé, `false` sinon.
 */
function pays_charger() {

	$chargement_ok = true;

	// Construire l'URL de la fonction de recherche
	$url = _GEOGRAPHIE_ENDPOINT_BASE_URL . 'pays';

	// Acquisition des données spécifiées par l'url
	$requeter = charger_fonction('geographie_requeter', 'inc');
	$data = $requeter($url);

	if (!empty($data['donnees']['pays'])) {
		// On crée les objets pays contenus dans la réponse. Etant donné que les liens avec ces objets
		// sont basés sur les codes alpha2 permanents, on peut vider la table et la recréer entièrement.
		sql_delete('spip_geo_pays');

		// Ajout des pays un par un en utilisant l'API objet
		include_spip('action/editer_objet');

		foreach ($data['donnees']['pays'] as $_pays) {
			objet_inserer('geo_pay', null, $_pays);
		}
	}

	return $chargement_ok;
}
