<?php
    /*! \file thumbshots.php 
     *  \brief Fichier de fonctions personnalisées au serveur robothumb
     *         
     *  Défini la surcharge de thumb
     *  Le nom du fichier doit être obligatoirement celui déclaré dans le fond pour le paramétre thumbsites/serveur
     */


    /*! \brief thumbsite_serveur() pour le serveur robothumb
     *
     *  Surcharge de la fonction thumbs() exploitant le serveur d'aperçu de robothumb
     *  
     * \param $url_site url du site à consulter
     * \return url de l'image générée par le serveur
     */
    function url_thumbsite_serveur($url_site) {
        $taille = lire_config('thumbsites/robothumb_taille');
        //retourne l'url de la vignette
        return "http://www.robothumb.com/src/?url=".$url_site."&size=".$taille;
    }        
?>
