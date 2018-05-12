<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_NOISETTE_PREVIEW_dist($p) {

	// On passe dans le contexte toujours les deux identifiants d'une noisette, à savoir, l'id_noisette et le couple
	// (id_conteneur, rang).
	// -- L'id de la noisette qui est passé en argument.
	$id_noisette = interprete_argument_balise(1, $p);
	$id_noisette = isset($id_noisette) ? str_replace('\'', '"', $id_noisette) : '0';
	// -- L'autre identifiant de la noisette, à savoir, le couple (id_conteneur, rang) qui est lu dans la pile.
	$id_conteneur = champ_sql('id_conteneur', $p);
	$rang_noisette = champ_sql('rang_noisette', $p);
	// -- Le type et les paramètres qui sont lus dans la pile.
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);
	$noisette = "array(
		'id_noisette' => $id_noisette,
		'id_conteneur' => $id_conteneur,
		'rang_noisette' => $rang_noisette,
		'type_noisette' => $type_noisette,
	)";

	// On identifie si le type de noisette est actif ou pas afin de faire appel au bon fond.
	$type_noisette_actif = interprete_argument_balise(2, $p);
	$type_noisette_actif = isset($type_noisette_actif) ? str_replace('\'', '"', $type_noisette_actif) : '"oui"';

	// On récupère aussi le plugin appelant qui fait partie du stocakge de la noisette.
	$plugin = champ_sql('plugin', $p);

	// On appelle la fonction de calcul de la prévisualisation.
	$p->code = "calculer_preview_noisette($plugin, array_merge($noisette, unserialize($parametres)), $type_noisette_actif)";
	$p->interdire_scripts = false;

	return $p;
}

function calculer_preview_noisette($plugin, $noisette, $type_noisette_actif) {

	// Initialisation de la sortie.
	$preview = '';

	if ($type_noisette_actif == 'oui') {
		// On récupère le dossier de stockage des type de noisette afin de vérifier si un squelette de prévisualisation
		// existe pour le type de noisette concerné.
		include_spip('inc/utils');
		$squelette_preview = type_noisette_localiser($plugin, $noisette['type_noisette']) . '-preview';
		if (trouver_fond($squelette_preview)) {
			$preview = recuperer_fond($squelette_preview, $noisette);
		}
	} else {
		$preview = recuperer_fond('preview_erreur', $noisette);
	}

	return $preview;
}
