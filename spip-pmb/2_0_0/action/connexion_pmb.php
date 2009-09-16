<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');
include_spip('inc/session');

function action_connexion_pmb_dist()
{
	  
	  
	  error_reporting(E_ALL);
$ws=new SoapClient("http://test3.bibli.fr/ostudio/PMBWsSOAP_1?wsdl");
try {
	$session_id = $ws->pmbesOPACEmpr_login("apachot","Dark-star");
	if ($session_id) {
		$compte = $ws->pmbesOPACEmpr_get_account_info($session_id);
		session_set('pmb_session',$session_id);
		redirige_par_entete(_request('redirect'));
	}
} catch (SoapFault $fault) {
        print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
}
	  
}

?>