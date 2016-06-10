<?php
/**
 * Plugin spip2spip
 * 
 * Licence GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_spip2spip_supprimer($arg=null) {
  
  include_spip('inc/autoriser');
	$err="";
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

  $id_spip2spip = intval($arg);

	if (autoriser('supprimer', 'spip2spip')) {          
    		$s= sql_delete("spip_spip2spips","id_spip2spip=$id_spip2spip");     		
    		 return array($id_spip2spip,$err); 
	} else {
	      die("erreur: acces interdit");
  } 
		
}
	

?>