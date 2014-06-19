<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function notifications_reservation_client_dist($quoi,$id_reservation, $options) {
    include_spip('inc/config');
    $config = lire_config('reservation_evenement');

    $envoyer_mail = charger_fonction('envoyer_mail','inc');
    
    $options['id_reservation']=$id_reservation;  
    $options['qui']='client';     
    $subject=_T('reservation:votre_reservation_sur',array('nom'=>$GLOBALS['meta']['nom_site']));

    /*Chercher des chaines de langues spécifiques pour les différents statuts*/	
    $lang=$options['lang'];
   
 
    $var_reservation = 'i18n_reservation_'.$lang;
    $chaine_statut='sujet_votre_reservation_'.$options['statut'];

    if(isset($GLOBALS[$var_reservation][$chaine_statut]))$subject=_T('reservation:'.$chaine_statut,array('nom'=>$GLOBALS['meta']['nom_site']));  

    $email=$options['email'];
    $message=recuperer_fond('notifications/contenu_reservation_mail',$options);
     
    //
    // Envoyer les emails
    //
    //
    //

    $o=array('html'=>$message);

    $envoyer_mail($email,$subject,$o);
    

    // Si présent -  l'api de notifications_archive 
    if ($archiver = charger_fonction('archiver_notification','inc',true)) {
            $envoi='reussi';
            if(!$envoyer_mail)$envoi='echec';

            $o=array(
                'recipients'=>$email,                         
                'sujet'=>$subject,
                'texte'=>$message,
                'html'=>'oui',
                'id_objet'=>$id_reservation,
                'objet'=>'reservation',
                'envoi'=>$envoi,
                 'type'=>$quoi);           
            
        $archiver ($o);
    }    
}

?>
