<?php
// _SPIPLISTES_EXEC_AUTOCRON

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

// appelé par javascript/autocron.js ??

// ne semble plus être utilisé (CP-20071007)

function exec_spiplistes_autocron () {

	include_spip('inc/spiplistes_api');

	spiplistes_log("exec_autocron() <<", LOG_DEBUG); 	

	$sql_result = spip_query("SELECT id_courrier,total_abonnes,nb_emails_envoyes FROM spip_courriers WHERE statut='"._SPIPLISTES_STATUT_ENCOURS."' LIMIT 1");

	if(spip_num_rows($sql_result) > 0 ){

		$row = spip_fetch_array($sql_result);	

		// Compter le nombre de mails a envoyer
		
		$id_mess = $row['id_courrier'];
		$nb_inscrits = $row['total_abonnes'];
		$nb_messages_envoyes = $row['nb_emails_envoyes'];
		
		if($nb_inscrits > 0) {
			echo "<p align='center'> <strong>".round($nb_messages_envoyes/$nb_inscrits *100)." %</strong> (".$nb_messages_envoyes."/".$nb_inscrits.") </p>";
		}
	}
	else {
		echo "fin";
	}
	
	// ??
	$action = generer_url_action('cron','&var='.time());
	echo ' <div style="background-image: url(\''. $action . '\');"> </div> ';

	spiplistes_log("exec_autocron ACTION: $action", LOG_DEBUG);	
	spiplistes_log("exec_autocron() >>", LOG_DEBUG);	
 
}

?>