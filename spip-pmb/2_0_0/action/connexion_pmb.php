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
	$session_id = $ws->pmbesOPACEmpr_login(_request('login'),_request('password'));
	if ($session_id) {
		//$compte = $ws->pmbesOPACEmpr_get_account_info($session_id);
		$result = $ws->pmbesOPACEmpr_get_account_info($session_id);
		    
		session_set('pmb_firstname',$result->personal_information->firstname);
		session_set('pmb_lastname',$result->personal_information->lastname);
		session_set('pmb_address_part1',$result->personal_information->address_part1);
		session_set('pmb_address_part2',$result->personal_information->address_part2);
		session_set('pmb_address_cp',$result->personal_information->address_cp);
		session_set('pmb_address_city',$result->personal_information->address_city);
		session_set('pmb_phone_number1',$result->personal_information->phone_number1);
		session_set('pmb_phone_number2',$result->personal_information->phone_number2);
		session_set('pmb_email',$result->personal_information->email);
		session_set('pmb_birthyear',$result->personal_information->birthyear);
		session_set('pmb_location_id',$result->location_id);
		session_set('pmb_location_caption',$result->location_caption);
		session_set('pmb_adhesion_date',$result->adhesion_date);
		session_set('pmb_expiration_date',$result->expiration_date);
		session_set('pmb_session',$session_id);
		redirige_par_entete(_request('redirect'));
	}
} catch (SoapFault $fault) {
        print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
}
	  
}

?>