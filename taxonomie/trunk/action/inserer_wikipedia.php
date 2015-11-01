<?php
/**
 * Ce fichier contient l'action `inserer_wikipedia` qui permet d'appeler l'api Wikipedia pour remplir
 * un champ de taxon.
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
	if (!autoriser('modifier', 'taxon')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Insertion pour le taxon donné du texte Wikipedia récupéré.
	// Le texte Wikipédia est inséré dans le champ précisé.
	// Si le champ n'est pas vide, son contenu est écrasé.
	if ($arguments) {
		// Détermination des arguments de l'action
		list($id_taxon, $nom_scientifique, $champ, $section) = explode(':', $arguments);
		$section = ($section == '*') ? null : $section;
		if (intval($id_taxon)) {
			include_spip('taxonomie_fonctions');
			$texte = taxonomie_informer($nom_scientifique, $section);
			if ($texte) {
				// Conversion du texte mediawiki vers SPIP
				include_spip('convertisseur_fonctions');
				$texte_converti = convertisseur_texte_spip($texte, 'MediaWiki_SPIP');
				// Mise à jour du descriptif en base de données
				// - le texte du descriptif est inséré dans la langue choisie
				// - l'indicateur d'édition est positionné à oui
				// - la source wikipédia est ajoutée
				sql_updateq('spip_taxons', array($champ => $texte_converti), 'id_taxon='. sql_quote($id_taxon));
			}
		}
	}
}

?>