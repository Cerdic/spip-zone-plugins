<?php
    /*! \file girafa.php 
     *  \brief Fichier de fonctions personnalisées au serveur girafa
     *         
     *  Défini la surcharge de thumb
     *  Le nom du fichier doit être obligatoirement celui déclaré dans le fond pour le paramétre thumbsites/serveur     
     */


    /*! \brief thumbsite_serveur() pour le serveur girafa
     *
     *  Surcharge de la fonction thumbs() exploitant le serveur d'aperçu de girafa
     *  
     * \param $url url du site à consulter
     * \return url de l'image générée par le serveur
     */
    function thumbsite_serveur($url) {
        //obtient les paramétres de connexion
        $identifiant = lire_config('thumbsites/girafa_identifiant');
        $signature = lire_config('thumbsites/girafa_signature');

        //generer un md5sum spécifique pour girafa
        //cf : https://tserver.girafa.com/help/QuickStart.php
        $md = substr(md5($signature.$url), -16, 16);
    
        //retourne l'url de la vignette
        return "http://scst.srv.girafa.com/srv/i?i=".$identifiant."&r=".$url."&s=".$md;
    }        
?>
