<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function notifications_reservation_client_dist($quoi,$id_reservation, $options) {
    include_spip('inc/config');
    $config = lire_config('reservation_evenement');

    $envoyer_mail = charger_fonction('envoyer_mail','inc');
    
    $options['id_reservation']=$id_reservation;  
    $options['qui']='client';     
    $subject=_T('reservation:votre_reservation_sur',array('nom'=>$GLOBALS['meta']['nom_site']));

    $message=recuperer_fond('notifications/contenu_reservation_mail',$options);
     
    //
    // Envoyer les emails
    //
    //
    //

    $o=array('html'=>$message);

    $envoyer_mail($options['email'],$subject,$o);
}

?>