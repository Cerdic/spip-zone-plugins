<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_lettre_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_lettre_dist $arg pas compris");
	} else {
		action_supprimer_lettre_post($r[1]);
	}
}

function action_supprimer_lettre_post($id_malettre) {
  $id_malettre = intval($id_malettre);
                
  // recuperer les urls    pour les effacer
  $result = sql_select('*',"spip_meslettres", "id_malettre=" . intval($id_malettre),'','',1);
  while ($row = sql_fetch($result)){
        $url_html = $row['url_html'];
        spip_unlink(_DIR_IMG .$url_html);
        $url_txt = $row['url_txt'];
        spip_unlink(_DIR_IMG.$url_txt);         
  }

  // effacer base
	sql_delete("spip_meslettres", "id_malettre=" . intval($id_malettre));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_malettre/$id_malettre'");
  
}
?>