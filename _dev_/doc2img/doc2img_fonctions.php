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


?>
