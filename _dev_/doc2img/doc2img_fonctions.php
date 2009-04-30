<?php

/*! \file doc2img_fonctions.php 
 *  \brief Liste des fonctions à chargée à chaque appel d'une page publique ou privée
 *         
 *  A chaque chargement d'une page, les fonctions suivantes sont accesibles. Ce sont entre autres les filtres, balises, ... utiles au plugin
 */



/*! \brief explode() pour spip
 *
 *  Surcharge de la fonction php explode(), pour être utilisé par php
 *  Remarque : L'appel de filtre passe toujours le texte source en premier 
 *  (c'est le compilateur qui fait ça tout seul)=> il faut inverser les paramètres d'explode qui elle attend le texte en second d'où spip_explode
 *  
 * \param $texte balise fournit par le compilateur
 * \param $separateur pattern 
 * \return tableau decomposé par $separateur      
 */   
function spip_explode($texte,$separateur){
    return explode($separateur,$texte);
}

/*! \brief d2c_convertir
 *
 *  Filtre pour lancer la conversion d'un document depuis un squelette
 *  
 * \param $id_document le document à convertir
 * \return un statut vrai/faux exploitable par un tableau
 */   
function d2c_convertir($id_document) {
    include_spip('inc/doc2img_convertir');
    if (can_doc2img($id_document)) {
        if (is_doc2img($id_document)) {
            return "deja converti";
        } else {
            return convertir_document($id_document);
        }    
    } else {
        return "non convertible";
    }
}

function d2c_is_convert($id_document) {
    include_spip('inc/doc2img_convertir');
    return is_doc2img($id_document) ? ' ' : '';
}

function d2c_can_convert($id_document) {
    include_spip('inc/doc2img_convertir');
    return can_doc2img($id_document) ? ' ' : '';
}

/*
 * @brief generer l'url absolue d'un doc2img en fonction de son id
 * @return string
 *          l'url absolue du fichier
 */
 
function generer_url_doc2img($id) {
    $fichier = sql_getfetsel('fichier','spip_doc2img','id_doc2img='.$id);
    $doc2img = get_spip_doc($fichier);
    return $GLOBALS['meta']['adresse_site'].'/'.$doc2img;
}

/*
 * @brief Fournir un #URL_DOC2IMG adapté au contexte ou bien #URL_DOC2IMG{unid} pour un id_doc2img donné
 * 
 */
function balise_URL_DOC2IMG_dist($p) {

    $type = 'doc2img';

    $_id = interprete_argument_balise(1,$p);
    if (!$_id) $_id = champ_sql('id_' . $type, $p);
    
    $p->code = "generer_url_doc2img($_id)";        
    $p->interdire_scripts = false;

    return $p;
}

?>
