<?php
function savecfg_afficher_tout($flux) {
	if($flux['args']['exec'] == 'cfg' AND _request('cfg')) {
		$flux['data'] = debut_boite_info(true) . recuperer_fond('prive/formulaires_savecfg') . '<hr />' . recuperer_fond('prive/formulaire_savecfg_import') . fin_boite_info(true);
	}
	return $flux;
}
?>