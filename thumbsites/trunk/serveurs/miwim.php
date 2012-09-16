<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
    /*! \file miwim.php
     *  \brief Fichier de fonctions personnalisées au serveur miwim
     *         
     *  Défini la surcharge de thumb
     *  Le nom du fichier doit être obligatoirement celui déclaré dans le fond pour le paramétre thumbsites/serveur     
     */


    /*! \brief thumbsite_serveur() pour le serveur miwim
     *
     *  Surcharge de la fonction thumbs() exploitant le serveur d'aperçu de miwim
     *  
     * \param $url_site url du site à consulter
     * \return url de l'image générée par le serveur
     */
    function url_thumbsite_serveur($url_site) {
		include_spip('inc/config');
        $taille = lire_config('thumbsites/miwim_taille', '120x90');

        //retourne l'url de la vignette
        return "http://thumbs.miwim.fr/img.php?url=${url_site}&ext=jpg&size=${taille}&remplace=http://www.miwim.fr/templates/miwim/img/no-preview.gif";
    }        
?>
