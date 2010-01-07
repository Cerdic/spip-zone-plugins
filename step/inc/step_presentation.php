<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// -------------- Icones/Boutons --------------------------------------

// retourne une icone 16px ayant un lien vers $url
function step_btn_action($url, $icone, $alt, $class='', $couleur='auto', $taille=16){
	// selection couleur (calculer les couleurs de l'auteur actuel)
	include_spip('inc/presentation'); // SPIP 2
	include_spip('inc/presentation_mini'); //_SPIP 2.1
	parametres_css_prive();
	
	include_spip('inc/filtres_images');
	switch ($couleur){
		case 'auto':
			$coul = couleur_foncer(substr($GLOBALS["couleur_foncee"], 1));
			break;
		case 'danger':
		case 'erreur':
			$coul = 'ff0000';
			break;
		case 'ok':
			$coul = '00cc00';
			break;
		case 'info':
			$coul = '0088cc';
			break;
		case 'encours':
			$coul = 'ff9900';
			break;
		default:
			$coul = $couleur;
			break;
	}
	
	$img = "<img src='" 
			. extraire_attribut(image_sepia(step_chemin_image($icone), $coul), 'src')
			. "' width='$taille' height='$taille'"
			. ($class ? "class='$class'" : '')
			. "alt='$alt' title='$alt' />";
	return 	$url ? "<a href='".$url."'>$img</a>\n" : $img;
}

// retourne un bouton vide... ie l'espacement d'un bouton d'action, mais sans lui !
function step_btn_action_vide(){
	return 	"<img src='" . step_chemin_image('rien.gif')
			. "' width='16' height='16' alt='$alt' title='$alt' />\n";		
}


?>
