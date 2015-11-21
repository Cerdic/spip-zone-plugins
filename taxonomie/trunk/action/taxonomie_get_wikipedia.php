<?php
/**
 * Ce fichier contient l'action `taxonomie_get_wikipedia` qui permet d'appeler l'api Wikipedia pour remplir
 * un champ de taxon.
 *
 * @package SPIP\TAXONOMIE\SERVICES\WIKIPEDIA
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à un utilisateur d'insérer dans le champ d'un taxon (principalement le descriptif)
 * le texte d'une section de page Wikipedia.
 *
 * Cette action est réservée aux utilisateurs ayant le droit de modifier un taxon.
 * Elle nécessite plusieurs arguments, à savoir, l'id du taxon, son nom scientifique, le code de langue SPIP,
 * le champ de taxon concerné par l'insertion et la section de page Wikipedia à insérer ou `*`
 * si toute la page est requise.
 *
 * @uses wikipedia_spipcode2language()
 * @uses wikipedia_get()
 * @uses taxon_merger_traductions()
 *
 * @return void
 */
function action_taxonomie_get_wikipedia_dist(){

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
				$maj[$champ] = taxon_merger_traductions($taxon[$champ], $texte_converti, true);
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