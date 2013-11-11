<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_AM_PATTERN_TR'))
	define('_AM_PATTERN_TR', '%(<tr[^>]*>)(.*?)</tr>%s');

function am_insert_head_css($flux) {
//	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/qr.css').'" type="text/css" media="all" />';
	return $flux;
}

function am_header_prive($flux) {
//	$flux .= '<script src="'.find_in_path('js/qr.js').'" type="text/javascript"></script>';
	return $flux;
}

$GLOBALS['am_listes'] = array(
	'prive/objets/liste/articles',
);

function am_recuperer_fond($flux) {
	include_spip('inc/filtres');

	if (in_array($flux['args']['fond'], $GLOBALS['am_listes'])) {
		$fond = $flux['args']['fond'];
		$texte = $flux['data']['texte'];
		$objet = array_pop(explode('/', $fond));

		if ($fond == 'prive/objets/liste/articles') {
			// TODO : il faut entourer la table d'un formulaire pour utiliser les checkbox
			// -- peut etre que le plus facile serait d'inclure le texte de la table dans le formulaire
			//    via un recuperer_fond

			// Ajout d'une colonne en première position dans le thead
			$thead = extraire_balise($texte, 'thead');
			if (preg_match(_AM_PATTERN_TR, $thead, $balises_tr)) {
				$choix = recuperer_fond('inclure/am_th_choix', $contexte);
				$texte = str_replace($balises_tr[2], $choix . $balises_tr[2], $texte);
			}

			// Ajout d'une ligne de boutons d'action en fin du thead de la table
			$contexte = array();
			$actions = recuperer_fond("inclure/am_$objet", $contexte);
			$texte = str_replace('</thead>', $actions . '</thead>', $texte);

			// Ajout pour chaque ligne du body d'une colonne en première position afin de faire
			// les choix des objets.
			// TODO : le problème est de récupérer l'id de l'objet pour le passer au fond
			$tbody = extraire_balise($texte, 'tbody');
			if (preg_match_all(_AM_PATTERN_TR, $tbody, $balises_tr)) {
				foreach($balises_tr[2] as $_balise) {
					$choix = recuperer_fond('inclure/am_td_choix', $contexte);
					$texte = str_replace($_balise, $choix . $_balise, $texte);
				}
			}
		}

		$flux['data']['texte'] = $texte;
	}

	return $flux;
}
?>
