<?php

// utiliser le pipeline 'styliser' pour
// définir le squelette a utiliser si on est dans le cas
// d'une rubrique de spipClear

include_spip("inc/config");
function spipclear_styliser($flux){
	// si article, rubrique ou sommaire,
	// on cherche si spip clear doit s'activer
	if (($fond = $flux['args']['fond'])
	AND in_array($fond, array('article','rubrique','sommaire'))) {
		
		$ext = $flux['args']['ext'];

		// cas du sommaire
		if ($fond == 'sommaire') {
			// uniquement si configuration de spipClear pour le sommaire
			if (lire_config('spipclear/sommaire_spipclear') == 'on') {
				if ($squelette = test_squelette_spipclear($fond, $ext)) {
					$flux['data'] = $squelette;
				}
			}
		}
		
		// cas dans une rubrique
		// uniquement si configuration de spipClear pour le secteur en question
		elseif ($id_rubrique = $flux['args']['id_rubrique']) {
			$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique));
			if (in_array($id_secteur, lire_config('spipclear/secteurs', array(0,-1)))) {
				if ($squelette = test_squelette_spipclear($fond, $ext)) {
					$flux['data'] = $squelette;
				}
			}
		}
	}
	return $flux;
}

function test_squelette_spipclear($fond, $ext) {
	if ($squelette = find_in_path($fond."_spipclear.$ext")) {
		return substr($squelette, 0, -strlen(".$ext"));
	}
	return false;
}
?>
