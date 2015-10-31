<?php
/**
 * Ce fichier contient l'action `client_retirer_serveur` utilisée par un site client pour
 * retirer un serveur donné de la liste des serveurs consultables.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet au site client de retirer un serveur donné de sa liste des serveurs
 * qu'il est autorisé à interroger (variable de configuration).
 *
 * Cette action est réservée aux webmestres. Elle nécessite un seul argument, le nom du serveur
 * à retirer.
 *
 * @return void
 */
function action_inserer_wikipedia_dist(){

	// Securisation et autorisation car c'est une action auteur:
	// -> argument attendu est l'alias du serveur
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Insertion pour le taxon donné du texte Wikipedia récupéré.
	// Le texte Wikipédia est inséré dans le champ précisé.
	// Si le champ n'est pas vide, son contenu est écrasé.
	if ($arguments) {
		list($id_taxon, $nom_scientifique, $champ) = explode(':', $arguments);
		if (intval($id_taxon)) {
			include_spip('taxonomie_fonctions');
			$texte = taxonomie_informer($nom_scientifique);
			if ($texte) {
				sql_updateq('spip_taxons', array($champ => $texte), 'id_taxon='. sql_quote($id_taxon));
			}
		}
	}
}

?>