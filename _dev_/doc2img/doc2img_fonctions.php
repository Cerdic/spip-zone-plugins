<?php

/*! \file doc2img_fonctions.php 
 *  \brief Liste des fonctions � charg�e � chaque appel d'une page publique ou priv�e
 *         
 *  A chaque chargement d'une page, les fonctions suivantes sont accesibles. Ce sont entre autres les filtres, balises, ... utiles au plugin
 */



/*! \brief explode() pour spip
 *
 *  Surcharge de la fonction php explode(), pour �tre utilis� par php
 *  Remarque : L'appel de filtre passe toujours le texte source en premier 
 *  (c'est le compilateur qui fait �a tout seul)=> il faut inverser les param�tres d'explode qui elle attend le texte en second d'o� spip_explode
 *  
 * \param $texte balise fournit par le compilateur
 * \param $separateur pattern 
 * \return tableau decompos� par $separateur      
 */   
function spip_explode($texte,$separateur){
    return explode($separateur,$texte);
}

/*! \brief explode() pour spip
 *
 *  Surcharge de la fonction php explode(), pour �tre utilis� par php
 *  Remarque : L'appel de filtre passe toujours le texte source en premier 
 *  (c'est le compilateur qui fait �a tout seul)=> il faut inverser les param�tres d'explode qui elle attend le texte en second d'o� spip_explode
 *  
 * \param $texte balise fournit par le compilateur
 * \param $separateur pattern 
 * \return tableau decompos� par $separateur      
 */   
function d2c_convertir($id_document) {
    include_spip('inc/convertir');
    if (controler_document($id_document)) {
        convertir_document($id_document);
        return " ";
    } else {
        return "";
    }
}

?>
