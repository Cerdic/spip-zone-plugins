<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function forumsectorise_pre_insertion($flux) {
	include_spip('inc/plugin');
	include_spip('inc/config');

	$conf_forumsectorise = lire_config('forumsectorise');

	if (($flux['args']['table'] == table_objet_sql('article')) &&
		 in_array($flux['data']['id_secteur'], $conf_forumsectorise['ident_secteur'])) {
		$flux['data']['accepter_forum'] = $conf_forumsectorise['type'];
	}
	return $flux;
}


// pour ne pas afficher les options de forum sur les objets/articles
function forumsectorise_afficher_config_objet($flux) {
	if (($type = $flux['args']['type'])
		and $id = $flux['args']['id']
		and lire_config('forumsectorise/masqueroptions') == 'on'
	) {
		$aremplacer = recuperer_fond("prive/objets/configurer/moderation",
				array('id_objet' => $id, 'objet' => objet_type(table_objet($type))));
		$flux['data'] = str_replace($aremplacer,'',$flux['data']);
	}
	return $flux;
}