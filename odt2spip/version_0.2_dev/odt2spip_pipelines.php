<?php

function odt2spip_affiche_droite($flux){
	$id_rubrique = $flux['args']['id_rubrique'];
	if ($flux['args']['exec'] == 'naviguer' AND $id_rubrique > 0) {
		$icone = icone_horizontale(_T("odtspip:importer_fichier"), "#", "",
					_DIR_PLUGIN_ODT2SPIP . "images/odt-24.png", false, 
					"onclick='$(\"#boite_odt2spip\").slideToggle(\"fast\");return false;'");
		$out = recuperer_fond('formulaires/odt2spip', 
					array('id_rubrique' => $id_rubrique, 'icone' => $icone));
		$flux['data'] .= $out;
	}
	return $flux;
}

?>
