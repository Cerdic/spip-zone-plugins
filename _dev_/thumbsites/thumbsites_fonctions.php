<?php
    /*! \file thumbsites_fonctions.php 
     *  \brief Fichier mes_fonctions propre au plugin
     *         
     *  Défini le filtre thumbsite
     */

    //charge cfg
    include_spip('inc/cfg_config');

    /*! \brief filtre à utiliser dans les squelettes
     *
     *  Définition de la fonction de filtre
     *  Vérifie que le plugin est activé et qu'il n'existe pas ailleurs deja ce filtre
     *  Rappel : dans le cadre d'une utilisation SPIP, il n'y a pas de paramétre à donner. $url correspond à la balise appelant le filtre
     *  
     * \param $url url du site à consulter
     * \return url de l'image générée par le serveur
     */
    if ((lire_config('thumbsites/activer')=='on') AND !function_exists('thumbsite')) {
        function thumbsite($url) {
            //determine le serveur d'aperçu à utiliser
            $serveur = lire_config('thumbsites/serveur');
            //Charge le fichier de conf spécifique au serveur
            include_spip('serveurs/'.$serveur);
            //execute la surcharge
            return thumbsite_serveur($url);
        }
    }
?>
