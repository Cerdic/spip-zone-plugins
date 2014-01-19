notifications_archive
=====================

Ce plugin permet d'enregistrer les notifications envoyés par spip dans une table.

Afin qu'une notification spip soit prise en compte par ce plugin il faut:

/*Déclarer la notification via la pipeline notifications_archive*/

exemple :

function reservation_evenement_notifications_archive($flux){
    $flux=array_merge($flux,array(
    'reservation_client'=>array(
        'activer'=>'on', /*Configuration par défaut*/
        'duree'=>'180'  /*Configuration par défaut*/
        ),
    'reservation_vendeur'=>array(
        'duree'=>'180'  /*Configuration par défaut*/
        )        
    ));
       
    return $flux;   
}

/*Appeler la fonction archiver_notification() dans votre fichier notifications/nomdelanotification.php*/
    L'appel doit se faire après $envoyer_mail($email,$subject,$o);
    
    exemple

function notifications_reservation_client_dist($quoi,$id_reservation, $options) {
    include_spip('inc/config');
    $config = lire_config('reservation_evenement');

    $envoyer_mail = charger_fonction('envoyer_mail','inc');
    
    $options['id_reservation']=$id_reservation;  
    $options['qui']='client';     
    $subject=_T('reservation:votre_reservation_sur',array('nom'=>$GLOBALS['meta']['nom_site']));
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

