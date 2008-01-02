<?php

function party_lieu($descriptif) {
	$infos = explode(' - ', $descriptif);
	return trim($infos[1]);
}

?>