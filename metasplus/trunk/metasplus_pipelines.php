<?php
/**
 * Utilisations de pipelines par le plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2016-2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Effectuer des traitements juste avant l'envoi des pages publiques.
 *
 * Ajout des metas open graph, dublin core et twitter dans le <head> public.
 * 
 * @Note : on retrouve les informations du contexte au moyen d'un squelette pour pour bénéficier de la mise en cache. Capillotracté mais ça fontionne.
 *
 * @param $flux
 * @return mixed
 */
function metasplus_affichage_final($flux) {

	include_spip('inc/config');

	// Tests préliminaires avant d'inclure éventuellement les métas
	if (!test_espace_prive()
		// Il y a un <head>
		and $pos_head = strpos($flux, '</head>')
		// Les protocoles ne sont pas tous désactivés (improbable mais possible)
		and (
			!lire_config('metasplus')
			or count(lire_config('metasplus')) < 3
		)
		// Le contexte est retrouvé
		and $contexte = recuperer_fond('metasplus_trouver_contexte')
		and is_array($contexte = unserialize($contexte))
		// La page n'est pas en erreur
		and empty($contexte['erreur'])
		// La page n'est pas exclue
		and is_array($pages_exclues = (
			(defined('_METASPLUS_PAGES_EXCLUES') and _METASPLUS_PAGES_EXCLUES) ?
				explode(',', _METASPLUS_PAGES_EXCLUES) :
				array()
		))
		and (!in_array($contexte['type-page'], $pages_exclues))
		// Ce n'est pas une page d'un pseudo fichier (ex. robots.txt.html)
		and !strpos($contexte['type-page'], '.')
	) {

		// Trouver le squelette à utiliser : variante de la page si elle existe, sinon le squelette par défaut (dist.html)
		$fond_defaut   = 'inclure/metasplus/dist';
		$fond_variante = 'inclure/metasplus/' . $contexte['type-page'];
		if (find_in_path($fond_variante.'.html')) {
			$fond = $fond_variante;
		} elseif (find_in_path($fond_defaut.'.html')) {
			$fond = $fond_defaut;
		}

		// Si le squelette n'est pas vide, on ajoute son contenu à la fin du head
		if ($fond
			and $metas = recuperer_fond($fond, $contexte)
		) {
			$metas = "<!-- Plugin Métas + -->\n$metas\n";
			$flux = substr_replace($flux, $metas, $pos_head, 0);
		}
	}

	return $flux;
}
