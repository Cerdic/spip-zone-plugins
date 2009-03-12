<?php


	function traduire_mois($mois) {
		$nom_mois = _T('date_mois_'.intval($mois));
		return $nom_mois;
	}


?>