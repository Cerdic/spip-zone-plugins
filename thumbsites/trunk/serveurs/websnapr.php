<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
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
	function url_thumbsite_serveur($url_site) {
		//obtient les paramétres de connexion
		include_spip('inc/config');
		$clef = lire_config('thumbsites/websnapr_clef');
		$taille = lire_config('thumbsites/websnapr_taille', 'T');

		//retourne l'url de la vignette
		return "http://images.websnapr.com/?size=${taille}&key=${clef}&url=${url_site}";
	}
?>
