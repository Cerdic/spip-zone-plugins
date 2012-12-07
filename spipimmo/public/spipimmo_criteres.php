<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

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
