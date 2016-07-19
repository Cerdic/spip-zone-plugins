<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function logo_auto_quete_logo_objet($flux) {
	// Si personne n'a trouvé de logo avant et que c'est pas pour le survol
	if (empty($flux['data']) and $flux['args']['mode'] !== 'off') {
		// On cherche la première image jointe au contenu
		include_spip('base/abstract_sql');
		if ($image = sql_fetsel(
			'fichier, extension',
			'spip_documents as d inner join spip_documents_liens as l on d.id_document = l.id_document',
			array(
				'l.objet = '.sql_quote($flux['args']['objet']),
				'l.id_objet = '.intval($flux['args']['id_objet']),
				sql_in('extension', array('png', 'jpg', 'gif')),
			),
			'', //group
			'0+titre, id_document'
		)) {
			$chemin_complet = _DIR_IMG . $image['fichier'];
			
			$flux['data'] = array(
				'chemin' => $chemin_complet,
				'timestamp' => @filemtime($chemin_complet),
			);
		}
	}
	
	return $flux;
}
