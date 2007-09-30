<?php
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

// ne sert plus ??

function exec_autocron(){

	include_spip('inc/spiplistes_api');

	spiplistes_log("DEBUG: exec_autocron() <<", LOG_DEBUG); 	

	$rsult_pile = spip_query("SELECT * FROM spip_courriers AS messages WHERE statut='encour' LIMIT 0,1");
	$mssage_pile = spip_num_rows($rsult_pile);
	$mess=spip_fetch_array($rsult_pile);	

	if($mssage_pile > 0 ){
		// Les valeurs sont deja initialises
		// Compter le nombre de mails a envoyer
		
		$id_mess = $mess["id_courrier"];
		$nb_inscrits = $mess["total_abonnes"];
		$nb_messages_envoyes = $mess["nb_emails_envoyes"];
		
		if($nb_inscrits > 0)
			echo "<p align='center'> <strong>".round($nb_messages_envoyes/$nb_inscrits *100)." %</strong> (".$nb_messages_envoyes."/".$nb_inscrits.") </p>";
	
	}
	else
		echo "fin";
	
	// ??
	$action = generer_url_action('cron','&var='.time());
	echo ' <div style="background-image: url(\''. $action . '\');"> </div> ';

	spiplistes_log("exec_autocron ACTION: $action", LOG_DEBUG);	
	spiplistes_log("exec_autocron() >>", LOG_DEBUG);	
 
}

?>