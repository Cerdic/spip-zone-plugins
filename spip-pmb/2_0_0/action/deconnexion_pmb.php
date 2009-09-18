<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');
include_spip('inc/session');

function action_deconnexion_pmb_dist()
{
	  
	  
	  error_reporting(E_ALL);
$ws=new SoapClient("http://test3.bibli.fr/ostudio/PMBWsSOAP_1?wsdl");
try {
	$ws->pmbesOPACEmpr_logout(session_get('pmb_session'));
		    
		session_set('pmb_firstname','');
		session_set('pmb_lastname','');
		session_set('pmb_address_part1','');
		session_set('pmb_address_part2','');
		session_set('pmb_address_cp','');
		session_set('pmb_address_city','');
		session_set('pmb_phone_number1','');
		session_set('pmb_phone_number2','');
		session_set('pmb_email','');
		session_set('pmb_birthyear','');
		session_set('pmb_location_id','');
		session_set('pmb_location_caption','');
		session_set('pmb_adhesion_date','');
		session_set('pmb_expiration_date','');
		session_set('pmb_session','');
		redirige_par_entete(_request('redirect'));
	
} catch (SoapFault $fault) {
        print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
}
	  
}

?>