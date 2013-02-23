<?php
/**
 * Action permettant de renvoyer à un site utilisateur les informations complètes sur une boussole
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_informer_boussole_dist(){

	// Securisation: argument attendu est l'alias de la boussole
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$alias = $securiser_action();

	// Suppression de la boussole connue par son alias
	if ($alias) {
		// Acquerir la liste des boussoles prêtes à être diffusées
		$boussoles = array();
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		if ($boussoles) {
			// Vérifier que la boussole demandée est bien disponible sur le serveur
			if (in_array($alias, $boussoles)) {
				// Identifier si la boussole demandée est fournie de façon complète (incluant les traductions dans
				// le XML) ou minimale (les traductions sont dans les fichiers de langue)
				if ($xml = find_in_path("boussole_traduite-${alias}.xml")) {
					// XML avec traductions
				}
				elseif ($xml = find_in_path("boussole-${alias}.xml")) {
					// XML sans traductions
					// -- génération du fichier XML
					include_spip('inc/filtres');
					$versionner = charger_filtre('info_plugin');
					$page = recuperer_fond('xml_boussole', array('alias' => $alias, 'xml' => $xml, 'version' => $versionner('BOUSSOLE', 'version')));
					$x=$page;
				}
				else {
					spip_log("ACTION INFORMER BOUSSOLE : alias = ". $alias, 'boussole' . _LOG_ERREUR);
				}
				spip_log("ACTION INFORMER BOUSSOLE : alias = ". $alias, 'boussole' . _LOG_INFO);
			}
		}
	}
}

?>
