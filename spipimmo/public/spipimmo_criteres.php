<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function critere_spipimmo_image_dist($idb, &$boucles, $crit)
	{
		$boucle = &$boucles[$idb];
		$boucle->where[]=array("'='", "type", "1");
	}

	function critere_spipimmo_document_dist($idb, &$boucles, $crit)
	{
		$boucle = &$boucles[$idb];
		$boucle->where[]=array("'='", "type", "0");
	}

?>
