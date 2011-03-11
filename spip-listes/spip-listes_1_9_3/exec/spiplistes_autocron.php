<?php
// _SPIPLISTES_EXEC_AUTOCRON

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

// appel� par javascript/autocron.js ??

// ne semble plus �tre utilis� (CP-20071007)

function exec_spiplistes_autocron () {

	include_spip('inc/spiplistes_api');

	spiplistes_debug_log ("exec_autocron()"); 	

	$sql_result = sql_select(
		"id_courrier,total_abonnes,nb_emails_envoyes"
		, 'spip_courriers'
		, "statut=".sql_quote(_SPIPLISTES_COURRIER_STATUT_ENCOURS), '', '', 1
	);

	if(sql_count($sql_result) > 0 ){

		$row = sql_fetch($sql_result);	

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

	spiplistes_debug_log ("exec_autocron ACTION: $action");	
	spiplistes_debug_log("fin exec_autocron()");	
 
}

