<?php
/**
 * Ce fichier contient l'action `client_synchroniser_site` utilisée par un site client pour
 * synchroniser les données d'un site référencé avec celles du même site appartenant à une boussole donnée.
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
 * - l'alias de la boussole
 * - l'id du site référencé
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
			$from = array('spip_boussoles as b', 'spip_boussoles_extras as x');
			$select = array('x.nom_objet', 'x.slogan_objet', 'x.descriptif_objet', 'x.logo_objet');
			$where = array(
						'b.aka_boussole=' . sql_quote($boussole), 'id_syndic=' . sql_quote($id_syndic),
						'b.aka_boussole=x.aka_boussole', 'b.aka_site=x.aka_objet',
						'x.type_objet=' . sql_quote('site'));
			$donnees_site = sql_fetsel($select, $from, $where);

			// Mettre à jour le nom et le descriptif du site dans la table spip_syndic avec les données de la boussole
			if ($donnees_site) {
				// Mise à jour en BDD des informations du site
				include_spip('inc/filtres');
				include_spip('action/editer_site');
				$contenu = array(
							'nom_site'=> extraire_multi($donnees_site['nom_objet']),
							'descriptif'=> extraire_multi($donnees_site['descriptif_objet']));
				$erreur = site_modifier($id_syndic, $contenu);

				if (!$erreur) {
					// Mise à jour de son logo normal ("on")
					$iconifier = charger_fonction('iconifier_site', 'inc');
					$iconifier($id_syndic, 'on', $donnees_site['logo_objet']);

					spip_log("ACTION SYNCHRONISER SITE : id_syndic = $id_site - boussole = $boussole", _BOUSSOLE_LOG . _LOG_INFO);
				}
			}
		}
	}
}

?>