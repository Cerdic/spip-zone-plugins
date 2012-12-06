<?php

	if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

	function generer_url_annonce($id_annonce)
	{
		$resListeAnnonces=sql_select("*", "spip_annonces", "", $order, "", $limit);
		$nbAnnonces=sql_count($resListeAnnonces);
		
		$resUrl=sql_select("*", "spip_annonces", "id_annonce=" . $id_annonce);
		$enrAnnonce=sql_fetch($resUrl);
		return get_spip_script('./')."?page=annonce&id_annonce=" . $id_annonce . "&type=" . $enrAnnonce["type_offre"] . "&ville=" . $enrAnnonce["ville_bien"];
	}

?>
