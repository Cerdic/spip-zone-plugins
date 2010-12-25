<?php

function ajouter_jours($date,$jours) {
	if ($date = affdate($date,'U'))
		return date('Y-m-d', $date+intval($jours)*24*3600);
}

