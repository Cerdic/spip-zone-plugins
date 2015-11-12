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
		list($id_taxon, $nom_scientifique, $spip_langue, $champ, $section) = explode(':', $arguments);
		$section = ($section == '*') ? null : $section;
		if (intval($id_taxon)) {
			// Récupération des informations tsn, source et edite du taxon
			$taxon = sql_fetsel(array('tsn', 'sources', 'edite', $champ), 'spip_taxons', 'id_taxon='. sql_quote($id_taxon));

			// Appel du service query de Wikipedia
			include_spip('services/wikipedia/wikipedia_api');
			$langue = wikipedia_spipcode2language($spip_langue); // TODO : attention à gérer la langue en amont
			$information = wikipedia_get($taxon['tsn'], $nom_scientifique, $langue, $section);
			if ($information['texte']) {
				// Conversion du texte mediawiki vers SPIP et mise en format multi
				include_spip('convertisseur_fonctions');
				$texte_converti = '<multi>'
					. '[' . $spip_langue .']'
					. convertisseur_texte_spip($information['texte'], 'MediaWiki_SPIP')
					. '</multi>';

				// Mise à jour pour le taxon du descriptif et des champs connexes en base de données
				$maj = array();
				// - le texte du descriptif est inséré dans la langue choisie en mergeant avec l'existant
				//   si besoin
				include_spip('inc/taxonomer');
				$maj[$champ] = merger_multi($taxon[$champ], $texte_converti, true);
				// - l'indicateur d'édition est positionné à oui
				if ($taxon['edite']) {
					$maj['edite'] = 'oui';
				}
				// - la source wikipédia est ajoutée (ou écrasée si elle existe déjà)
				$maj['sources'] = array('wikipedia' => array('champs' => array($champ)));
				if ($sources = unserialize($taxon['sources'])) {
					$maj['sources'] = array_merge($maj['sources'], $sources);
				}
				$maj['sources'] = serialize($maj['sources']);
				// - Mise à jour
				sql_updateq('spip_taxons', $maj, 'id_taxon='. sql_quote($id_taxon));
			}
		}
	}
}

?>