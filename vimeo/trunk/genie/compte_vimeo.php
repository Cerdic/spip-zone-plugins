<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function genie_compte_vimeo($t){

		$vimeo = charger_fonction('vimeo', 'action');
		$vimeo(true);

		return 1;

	}

?>
