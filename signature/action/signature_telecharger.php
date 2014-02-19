<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_signature_telecharger_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		  spip_log("signature_telecharger_dist $arg pas compris","signature"._LOG_ERREUR);
	} else {
	    $id_auteur = intval($r[1]);
      $html = recuperer_fond('auteur_signature', array('id_auteur'=>$id_auteur));
      $date = date("Y-m-d");
      
      header("Content-Type: text/html");
      header("Content-disposition: attachment; filename=signature-$date.html");
      header("Pragma: no-cache");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
      header("Expires: 0");     
      echo($html); 
      die();
	}
}


?>