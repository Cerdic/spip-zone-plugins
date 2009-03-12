<?php


	function balise_MAINTENANT($p) {
		$p->code = "calcul_maintenant()";
		$p->interdire_scripts = false;
		return $p;
	}
	

	function calcul_maintenant() {
		return date('Y-m-d h:i:s');
	}


?>