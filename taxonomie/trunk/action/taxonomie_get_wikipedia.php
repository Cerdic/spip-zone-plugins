<?php
/**
 * Ce fichier contient l'action `taxonomie_get_wikipedia` qui permet d'appeler l'api Wikipedia pour remplir
 * un champ de taxon.
 *
 * @package SPIP\TAXONOMIE\SERVICES\WIKIPEDIA
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette action permet à un utilisateur d'insérer dans le champ d'un taxon (principalement le descriptif)
 * le texte d'une section de page Wikipedia.
 *
 * Cette action est réservée aux utilisateurs ayant le droit de modifier un taxon.
 * Elle nécessite plusieurs arguments, à savoir, l'id du taxon, son nom scientifique, le code de langue SPIP,
 * le champ de taxon concerné par l'insertion et la section de page Wikipedia à insérer ou `*`
 * si toute la page est requise.
 *
 * @uses wikipedia_find_language()
 * @uses wikipedia_get_page()
 * @uses taxon_merger_traductions()
 *
 * @return void
 */
function action_taxonomie_get_wikipedia_dist() {

	// Securisation et autorisation car c'est une action auteur:
	// -> argument attendu est l'alias du serveur
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	// Insertion pour le taxon donné du texte Wikipedia récupéré.
	// Le texte Wikipédia est inséré dans le champ précisé.
	// Si le champ n'est pas vide, son contenu est écrasé.
	if ($arguments) {
		// Détermination des arguments de l'action
		list($id_taxon, $nom_scientifique, $spip_langue, $champ, $section) = explode(':', $arguments);
		$section = ($section == '*') ? null : $section;

		// Verification des autorisations
		if (!autoriser('modifier', 'taxon', $id_taxon)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		if (intval($id_taxon)) {
			// Récupération des informations tsn, source et edite du taxon
			$taxon = sql_fetsel(array('tsn', 'sources', 'edite', $champ), 'spip_taxons', 'id_taxon=' . sql_quote($id_taxon));

			// Appel du service query de Wikipedia
			include_spip('services/wikipedia/wikipedia_api');
			$langue = wikipedia_find_language($spip_langue); // TODO : attention à gérer la langue en amont
			$options = array('language' => $langue, 'section' => $section);
			$information = wikipedia_get_page('text', $taxon['tsn'], $nom_scientifique, $options);
			if ($information['text']) {
				// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
				// Mise en format multi systématique.
				include_spip('inc/filtres');
				$convertir = chercher_filtre('convertisseur_texte_spip');
				$texte_converti = '<multi>'
								  . '[' . $spip_langue . ']'
								  . ($convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['texte'])
								  . '</multi>';

				// Mise à jour pour le taxon du descriptif et des champs connexes en base de données
				$maj = array();
				// - le texte du descriptif est inséré dans la langue choisie en mergeant avec l'existant
				//   si besoin. On limite la taille du descriptif pour éviter un problème lors de l'update
				include_spip('inc/taxonomer');
				$maj[$champ] = taxon_merger_traductions(substr($texte_converti, 0, 20000), $taxon[$champ]);
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
				sql_updateq('spip_taxons', $maj, 'id_taxon=' . sql_quote($id_taxon));
			}
		}
	}
}
