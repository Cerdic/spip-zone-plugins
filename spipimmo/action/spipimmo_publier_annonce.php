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

	function action_spipimmo_publier_annonce()
	{
		$resPublier=sql_select("`publier`", "spip_annonces", "`id_annonce`=" . _request('arg'));
		$enrPublier=sql_fetch($resPublier);

		if($enrPublier["publier"]==1)
		{
			$modPublier=sql_update("spip_annonces", array("publier"=>"0"), "`id_annonce`=" . _request('arg'));
		}
		else
		{
			$modPublier=sql_update("spip_annonces", array("publier"=>"1"), "`id_annonce`=" . _request('arg'));
		}

		redirige_par_entete($_SERVER["HTTP_REFERER"]);
	}
?>
