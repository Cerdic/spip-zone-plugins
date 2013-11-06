<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function notifications_reservation_vendeur_dist($quoi,$id_reservation, $options) {
    include_spip('inc/config');
    $config = lire_config('reservation_evenement');

    $envoyer_mail = charger_fonction('envoyer_mail','inc');
    
    $options['id_reservation']=$id_reservation; 
    $options['qui']='vendeur';   
    
    $sql=sql_select('email','spip_auteurs','id_auteur IN ('.implode(',',$config['vendeur_'.$config['vendeur']]).')');
    $email=array();
    while($data=sql_fetch($sql)){
        $email[]=$data['email'];        
        }
    
    $subject=_T('reservation:une_reservation_sur',array('nom'=>$GLOBALS['meta']['nom_site']));

    $message=recuperer_fond('notifications/contenu_reservation_mail',$options);
     
    //
    // Envoyer les emails
    //
    //
    //

    $envoyer_mail($email,$subject,array(
        'html'=>$message)
       );



}

?>