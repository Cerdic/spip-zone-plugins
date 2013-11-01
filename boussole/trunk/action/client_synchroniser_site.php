<?php
/**
 * Ce fichier contient l'action `client_supprimer_boussole` utilisée par un site client pour
 * supprimer de façon sécurisée une boussole donnée.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet de synchroniser un site référencé dans la base du client et de synchroniser ses données
 * avec le site idoine (de même id_syndic) de la boussole associée.
 *
 * Cette action est réservée aux webmestres. Elle nécessite deux arguments, l'id du site et
 * l'alias de la boussole.
 * Les données synchronisées sont :
 *
 * - le nom du site
 * - le descriptif du site
 *
 * @return void
 */
function action_client_synchroniser_site_dist(){

	// Securisation et autorisation car c'est une action auteur:
	// -> les argument attendus sont l'id du site et l'alias de la boussole
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Synchronisation du nom et du descriptif du site avec les données du site idoine de la boussole
	if ($arguments) {
		list($boussole, $id_site) = explode(':', $arguments);
		if ($boussole AND ($id_syndic = intval($id_site))) {
			// Récupération des données du site dans la boussole
			// -- trouver l'alias du site connaissant la boussole et son id_syndic
			$site = sql_getfetsel(
						'aka_site',
						'spip_boussoles',
						array(
							'aka_boussole=' . sql_quote($boussole),
							'id_syndic=' . sql_quote($id_syndic)));
			$donnees_site = sql_fetsel(
								array('nom_objet', 'slogan_objet', 'descriptif_objet', 'logo_objet'),
								'spip_boussoles_extras',
								array(
									'aka_boussole=' . sql_quote($boussole),
									'type_objet=' . sql_quote('site'),
									'aka_objet=' . sql_quote($site)));


			// Mettre à jour le nom et le descriptif du site dans la table spip_syndic avec les données de la boussole
			if ($donnees_site) {
				// Mise à jour en BDD des informations du site
				include_spip('inc/filtres');
				sql_updateq('spip_syndic',
							array(
								'nom_site'=> extraire_multi($donnees_site['nom_objet']),
								'descriptif'=> extraire_multi($donnees_site['descriptif_objet'])),
							'id_syndic=' . sql_quote($id_syndic));

				// Mise à jour de son logo normal ("on")
				$iconifier = charger_fonction('iconifier_site', 'inc');
				$iconifier($id_syndic, 'on', $donnees_site['logo_objet']);

				spip_log("ACTION SYNCHRONISER SITE : id_syndic = $id_site - boussole = $boussole", 'boussole' . _LOG_INFO);
			}
		}
	}
}

?>