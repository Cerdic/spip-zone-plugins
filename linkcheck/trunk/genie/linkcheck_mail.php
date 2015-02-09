<?php
function genie_linkcheck_mail_dist(){
	
	include_spip('inc/config');
	
	if(lire_config('linkcheck/notifier_courriel')){
		
		$sql = sql_fetsel( 'COUNT(id_linkcheck) AS c, etat','spip_linkchecks', 'etat!="ok"', '', 'etat');
		
		if($sql>0){
			
			while($res = sql_fetch($sql)){
				
				$cont .= '<li>'.$res['c'].' lien(s) '.$res['etat'].'.</li>';
				
			}
			
			$cont = _T('linkcheck:mail_notification1');
			$cont .= '<ul>'.$cont.'</ul><br/>'
			$cont .= _T('linkcheck:mail_notification2');
					 
			$email = lire_config('email_webmaster');
			
			$nsite = lire_config('nom_site');
			
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
			
			$ok = $envoyer_mail($email, 'Liens cassés sur '.$nsite, array('html' => $cont, 'texte' => strip_tags($cont), 'nom_envoyeur' => 'Linkcheck'));
			
			if($ok) return 1;		
		}

	}
	return 0;	
	
}
?>
