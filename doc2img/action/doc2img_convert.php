<?php

/*! \file doc2img_convert.php
 *  \brief tout ce qui concerne le traitement des documents
 *
 */

/*! \brief ensemble des actions necessaires � la conversion d'un 
 *
 *  Traite juste l'action :
 *  - R�cupere l'id_document
 *  - Retourne vers la page demand�e ou � defaut la page appelante
 *  
 *  \param $redirect url de redirection (obetnue via _request())
 *  \param $id_document id_document fournit par le contexte (via _request())      
 */    
function action_doc2img_convert_dist(){

    //on charge les fonctions de conversion
    include_spip('inc/doc2img_convertir');

    //on lance la conversion du document 
    $id_document = intval(_request('id_document'));
    if ($id_document) {
        convertir_document($id_document);    
    }

    //charge la page don�ne par $redirect � defaut la page appelante
#    if (empty($redirect)) {
        $redirect = $_SERVER['HTTP_REFERER'];
#    } else {
#        $redirect = "ecrire/".rawurldecode(_request('redirect'));
#    }
    spip_log("redirection : ". $redirect,"doc2img");
    redirige_par_entete($redirect);
}
?>
