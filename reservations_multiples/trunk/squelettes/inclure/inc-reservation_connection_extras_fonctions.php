<?php

/**
 * Prépares les champs extras multiples
 *
 * @param array $champs_extras
 * @param integer $nombre
 * @return array
 */
function champs_extras_multiples($champs_extras, $nombre) {
	if (is_array($champs_extras)) {
		// Adapter les champs extras
		foreach ($champs_extras as $key => $value) {
			$nom_champ = $value['options']['nom'] . '_' . $nombre;
			set_request($nom_champ, '');#)
			$champs_extras[$key]['options']['nom'] = $nom_champ;
		}
	}
	return $champs_extras;
}
