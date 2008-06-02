<?php
    /*! \file thumbshots.php 
     *  \brief Fichier de fonctions personnalisées au serveur thumbshots
     *         
     *  Défini la surcharge de thumb
     *  Le nom du fichier doit être obligatoirement celui déclaré dans le fond pour le paramétre thumbsites/serveur
     */


    /*! \brief thumbsite_serveur() pour le serveur thumbshots
     *
     *  Surcharge de la fonction thumbs() exploitant le serveur d'aperçu de thumbshots
     *  
     * \param $url url du site à consulter
     * \return url de l'image générée par le serveur
     */
    function thumbsite_serveur($url) {
        //retourne l'url de la vignette
        return "http://www.thumbshots.de/cgi-bin/show.cgi?url=".$url;
    }        
?>
