<?php

function prefixer_nom_saisie($saisie, $prefixe) {

	$saisie['options']['nom'] = $prefixe . '[' . $saisie['options']['nom'] . ']';

	return $saisie;
}
