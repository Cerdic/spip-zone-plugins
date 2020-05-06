<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cherche le logo d'un contenu précis
 *
 * Dans l'ordre, si aucun logo n'a été trouvé avant :
 * - image jointe au contenu, meilleure résolution
 * - vignette de document joint au contenu, meilleure résolution
 *
 * @param array $flux
 * @return array
 */
function logo_auto_quete_logo_objet($flux) {

	// Si personne n'a trouvé de logo avant et que c'est pas pour le survol
	if (empty($flux['data']) and $flux['args']['mode'] !== 'off') {

		// On cherche une image jointe au contenu avec la meilleure résolution.
		include_spip('base/abstract_sql');
		$image = sql_fetsel(
			array(
				'fichier',
				'media',
				'(largeur * hauteur) AS mpx',
				//'IF (largeur>hauteur, largeur/hauteur, hauteur/largeur) AS ratio',
			),
			'spip_documents AS docs' .
				' INNER JOIN spip_documents_liens AS liens' .
				' ON docs.id_document = liens.id_document',
			array(
				'liens.objet = ' . sql_quote($flux['args']['objet']),
				'liens.id_objet = ' . intval($flux['args']['id_objet']),
				'media = ' . sql_quote('image'),
			),
			'', // group
			'mpx DESC, 0+titre, docs.id_document'
		);

		// Sinon on cherche une vignette de document avec la meilleure résolution.
		if (!$image) {
			$image = sql_fetsel(
				array(
					'vignettes.fichier',
					'vignettes.media',
					'(vignettes.largeur * vignettes.hauteur) AS mpx',
					//'IF (vignettes.largeur>vignettes.hauteur, vignettes.largeur/vignettes.hauteur, vignettes.hauteur/vignettes.largeur) AS ratio',
				),
				'spip_documents_liens AS liens' .
					' JOIN spip_documents AS vignettes' .
					' INNER JOIN spip_documents as docs' .
						' ON docs.id_vignette = vignettes.id_document' .
						' AND docs.id_document = liens.id_document',
				array(
					'liens.objet = ' . sql_quote($flux['args']['objet']),
					'liens.id_objet = ' . intval($flux['args']['id_objet']),
					'vignettes.media = ' . sql_quote('image'),
				),
				'', // group
				'mpx DESC, 0+vignettes.titre, docs.id_document'
			);
		}

		// Si on a trouvé une image et qu'elle existe toujours
		if (!empty($image['fichier'])) {
			// Si c'est un URL on retourne le chemin directement
			if (filter_var($image['fichier'], FILTER_VALIDATE_URL)) {
				$chemin_complet = $image['fichier'];
			}
			// Sinon on va le chercher dans IMG
			elseif (file_exists(_DIR_IMG . $image['fichier'])) {
				$chemin_complet = _DIR_IMG . $image['fichier'];
			}
			
			// Est-ce qu'elle existe toujours ?
			if ($chemin_complet) {
				$flux['data'] = array(
					'chemin'    => $chemin_complet,
					'timestamp' => @filemtime($chemin_complet),
				);
			}
		}
		
		// Sinon on va chercher des fallbacks si quelqu'un en a défini
		if (
			empty($flux['data']['chemin'])
			and (
				$image = find_in_path("images/logo_auto_{$flux['args']['objet']}.jpg")
				or $image = find_in_path("images/logo_auto_{$flux['args']['objet']}.png")
				or $image = find_in_path('images/logo_auto.jpg')
				or $image = find_in_path('images/logo_auto.png')
			)
		) {
			$flux['data'] = array(
				'chemin'    => $image,
				'timestamp' => @filemtime($image),
			);
		}
	}

	return $flux;
}
