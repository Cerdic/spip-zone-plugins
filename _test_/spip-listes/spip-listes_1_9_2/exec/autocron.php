<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_autocron()
{

global $table_prefix;
$qery_message = "SELECT * FROM ".$table_prefix."_courriers AS messages WHERE statut='encour' LIMIT 0,1";
$rsult_pile = spip_query($qery_message);
$mssage_pile = spip_num_rows($rsult_pile);
$mess=spip_fetch_array($rsult_pile);	

if($mssage_pile > 0 ){
	// Les valeurs sont deja initialisés
	// Compter le nombre de mails à envoyer
	
	$id_mess = $mess["id_courrier"];
	$nb_inscrits = $mess["total_abonnes"];
	$nb_messages_envoyes = $mess["nb_emails_envoyes"];
	
		if($nb_inscrits > 0)
		echo "<p align='center'> <strong>".round($nb_messages_envoyes/$nb_inscrits *100)." %</strong> (".$nb_messages_envoyes."/".$nb_inscrits.") </p>";	

}else{
echo "fin";
}
	
echo ' <div style="background-image: url(\''. generer_url_action('cron'). '&var='.time().'\');"> </div> ';
spip_log("spip_listes :  autocron");	
 
} 

?>