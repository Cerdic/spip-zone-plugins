<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Filtre media_generer_vignette pour générer une vignette automatique à partir du fichier
// Recherche l'existence d'un filtre media_generer_vignette_ext et renvoie le résultat de ce filtre, sinon rien
// media_generer_vignette_ext doit renvoyer l'url de la vignette
function filtre_media_generer_vignette_dist($fichier,$ext) {
	$f = charger_fonction('media_generer_vignette_'.$ext,'filtre',true);
	if ($f)
		return $f($fichier);
	else
		return '';
}

// Pour les images jpg, png et gif, on renvoie simplement $fichier
// Le redimensionnement est assuré par le paramètre taille transmis aux modèles <media>
function filtre_media_generer_vignette_jpg_dist($fichier) {return $fichier;}
function filtre_media_generer_vignette_png_dist($fichier) {return $fichier;}
function filtre_media_generer_vignette_gif_dist($fichier) {return $fichier;}

// Extrait le groupe du mime_type
// utilisation [(#MIME_TYPE|groupe_mime)]
function filtre_groupe_mime_dist($m) {
	return substr($m,0,strpos($m,'/'));
}

?>