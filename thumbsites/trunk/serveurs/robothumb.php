<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

    /*! \brief url_thumbsite_serveur() pour le serveur robothumb
     *
     *  Surcharge de la fonction thumbs() exploitant le serveur d'aperçu de robothumb
     *  
     * \param $url_site url du site à consulter
     * \return url de l'image générée par le serveur
     */
    function url_thumbsite_serveur($url_site) {
		include_spip('inc/config');
        $taille = lire_config('thumbsites/robothumb_taille', '120x90');

        //retourne l'url de la vignette
        return "http://www.robothumb.com/src/?url=${url_site}&size=${taille}";
    }        
?>
