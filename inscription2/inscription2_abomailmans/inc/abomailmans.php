<?php
	
	// a utiliser dans le form de son choix ...
	function abomailman_traiter_abonnement($id_liste){
	        $liste_data = sql_fetsel("*","spip_abomailmans","id_abomailman = $id_liste");
	        $sujet=null;
	        $dowhat=null;
	        $titre=$liste_data['titre'];
	        //Si on a l'email d'inscription
	        if($liste_data['email_subscribe']){
	                spip_log("Liste defini par mails","abomailmans");
	                $liste_email = $liste_data['email_subscribe'];
	        }else{
	                //sinon comme avant
	                // 1er cas : c'est une liste MAILMAN ? join etrange  !!!
	                //ne serait-ce pas plutot subscribe et unsubscribe ?
	                if($liste_data['email_sympa'] == '') {
	                        spip_log("Liste -join","abomailmans");
	                        $liste_email = explode ("@", $liste_data['email']);
	                        // abonnement ou desabonement : on rajoute -join dans l'email de la liste
	                        $dowhat = "-join@";
	                        $liste_email = $liste_email[0]."$dowhat".$liste_email[1];
	                }
	                // 2eme cas : c'est une liste SYMPA (presence de deux @ à suivre)
	                else {
	                        spip_log("Liste sympa","abomailmans");
	                        $proprio_email = $liste_data['email_sympa'];
	                        $sujet = 'SUBSCRIBE ';
	                        $sujet .= $liste_data['email'].' ';
	                        $liste_email = $liste_data['titre'];
	                }
	        }
	        $sujet=isset($sujet)?$sujet:$liste_email;
	        $quoifait="i2_abomailmans:veut_s_abonner";
	        $body="$quoifait"."\n ".$titre."(".$liste_data['email'].") \n "._T("i2_abomailmans:envoi_vers")." $liste_email";
	
	
	        return array($titre,$proprio_email,$liste_email, $sujet, $body,$headers);
	}
	
	
	//* Envoi de mail via facteur
	function abomailman_mail($nom, $email, $to_email,$liste_email, $sujet="", $body="", $html="", $headers="") {
	        // si $to_mail est plein, c'est Sympa, s'il est vide c'est Mailman et il faut alors utiliser $liste_email
	        if (!$to_email)
	                $to_email = $liste_email;
	        $envoyer_mail = charger_fonction('envoyer_mail','inc/');
	        if($envoyer_mail($to_email, $sujet, $body, $email, $headers))
	                return true;
	        else
	                return false;
	}
	
	
?> 
