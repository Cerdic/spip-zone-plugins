<?php

function auteurs_multiples_champs($contexte) {

	$contexte = unserialize($contexte);

	// Si inscription de plusieurs personnes
	if ($contexte['multiple_personnes'] == 'on') {
		$champs_extras_auteurs_add = array();
		$nombre_auteurs = intval(_request('nr_auteurs')) ? _request('nr_auteurs') : (_request('nombre_auteurs') ? _request('nombre_auteurs') : '');
		if (_request('nr_auteurs') == 'nada')
			$nombre_auteurs = 0;
			$i = 1;
			while ($i <= $nombre_auteurs) {
				$i++;
				set_request('nom_' . $nr, '');
				set_request('email' . $nr, '');
				if ($flux['data']['champs_extras_auteurs']) {
					// Adapter les champs extras
					foreach ($flux['data']['champs_extras_auteurs'] as $key => $value) {
						$flux['data'][$value['options']['nom'] . '_' . $nr] = '';
						$champs_extras_auteurs_add[$nr][$key] = $value;
						$champs_extras_auteurs_add[$nr][$key]['options']['nom'] = $value['options']['nom'] . '_' . $nr;
					}
				}
			}

			set_request('champs_extras_auteurs_add', $champs_extras_auteurs_add);
			//$flux['data']['ajouter'] = $ajouter;
			$flux['data']['_hidden'] .= '<input type="hidden" name="nombre_auteurs" value="' . $flux['data']['nombre_auteurs'] . '">';

			return;
	}
}

