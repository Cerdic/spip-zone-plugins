<?php

/**
 * Plugin Mélusine
 * (c) 2012 Jean-Marc Labat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier de fonctions permet de definir des elements
 * systematiquement charges lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, ?
 * 
 */






// ===================================================
// Filtre : hauteur_majoree
// ===================================================
// Auteur: S. Bellégo
// Fonction : Retourne la hauteur d'une image + 20. Sert pour
//                   afficher correctemnt le logo d'une rubrique
// ===================================================
//



function hauteur_majoree($img) {
    if (!$img) return;
    include_spip('logos.php');
    list ($h,$l) = taille_image($img);
    $h+=20;
    return $h;
}
// FIN du Filtre : hauteur_majoree

// ===================================================
// Filtre : typo_couleur
// ===================================================
// Auteur : Smellup, inspiré du filtre original de A. Piérard
// Fonction : permettant de modifier la couleur du texte ou 
//                   de l'introduction d'un article
// Utilisation :                  
//  - pour le rédacteur : [rouge]xxxxxx[/rouge]
//  - pour le webmaster : [(#TEXTE|couleur)]
// ===================================================
//
function typo_couleur($texte) {

    // Variables personnalisables par l'utilisateur
    $typo_couleur_active = 'oui';
    // --> Activation ou désactivation de la fonction
    // --> Nuances personnalisables par l'utilisateur
    $couleur = array(
        'noir' => "#000000",
        'blanc' => "#FFFFFF",
        'rouge' => "#FF0000",
        'vert' => "#00FF00",
        'bleu' => "#0000FF",
        'jaune' => "#FFFF00",
        'gris' => "#808080",
        'marron' => "#800000",
        'violet' => "#800080",
        'rose' => "#FFC0CB",
        'orange' => "#FFA500"
    );

    $recherche = array(
        'noir' => "/(\[noir\])(.*?)(\[\/noir\])/",
        'blanc' => "/(\[blanc\])(.*?)(\[\/blanc\])/",
        'rouge' => "/(\[rouge\])(.*?)(\[\/rouge\])/",
        'vert' => "/(\[vert\])(.*?)(\[\/vert\])/",
        'bleu' => "/(\[bleu\])(.*?)(\[\/bleu\])/",
        'jaune' => "/(\[jaune\])(.*?)(\[\/jaune\])/",
        'gris' => "/(\[gris\])(.*?)(\[\/gris\])/",
        'marron' => "/(\[marron\])(.*?)(\[\/marron\])/",
        'violet' => "/(\[violet\])(.*?)(\[\/violet\])/",
        'rose' => "/(\[rose\])(.*?)(\[\/rose\])/",
        'orange' => "/(\[orange\])(.*?)(\[\/orange\])/"
    );

    $remplace = array(
        'noir' => "<span style=\"color:".$couleur['noir'].";\">\\2</span>",
        'blanc' => "<span style=\"color:".$couleur['blanc'].";\">\\2</span>",
        'rouge' => "<span style=\"color:".$couleur['rouge'].";\">\\2</span>",
        'vert' => "<span style=\"color:".$couleur['vert'].";\">\\2</span>",
        'bleu' => "<span style=\"color:".$couleur['bleu'].";\">\\2</span>",
        'jaune' => "<span style=\"color:".$couleur['jaune'].";\">\\2</span>",
        'gris' => "<span style=\"color:".$couleur['gris'].";\">\\2</span>",
        'marron' => "<span style=\"color:".$couleur['marron'].";\">\\2</span>",
        'violet' => "<span style=\"color:".$couleur['violet'].";\">\\2</span>",
        'rose' => "<span style=\"color:".$couleur['rose'].";\">\\2</span>",
        'orange' => "<span style=\"color:".$couleur['orange'].";\">\\2</span>"
    );

    $supprime = "\\2";


    if ($typo_couleur_active == 'non') {
        $texte = preg_replace($recherche, $supprime, $texte);
    }
    else {
        $texte = preg_replace($recherche, $remplace, $texte);
    }
    return $texte;
}

// ===================================================
// Balise : #INTRODUCTION (surcharge)
// ===================================================
// Auteur: Smellup
// Fonction : Surcharge de la fonction standard de calcul de la 
//                   balise #INTRODUCTION. Permet d'en definir la
//                   taille en nombre de caractère
// ===================================================
//
function introduction ($type, $texte, $chapo='', $descriptif='') {

    // Personnalisable par l'utilisateur
    $taille_intro_article = 600;
    $taille_intro_breve = 300;
    $taille_intro_message = 600;
    $taille_intro_rubrique = 600;
    
    switch ($type) {
        case 'articles':
            if ($descriptif)
                return propre($descriptif);
            else if (substr($chapo, 0, 1) == '=')   // article virtuel
                return '';
            else
                return PtoBR(propre(supprimer_tags(couper_intro($chapo."\n\n\n".$texte, $taille_intro_article))));
            break;
        case 'breves':
            return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_breve))));
            break;
        case 'forums':
            return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_message))));
            break;
        case 'rubriques':
            if ($descriptif)
                return propre($descriptif);
            else
                return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_rubrique))));
            break;
    }
}


/**
 * Obtenir la liste des noisettes disponibles correspondant au type
 * passé en paramètre
 *
 * @param text $type est le type de noisette (articles, rurbiques, mobil, etc.)
 * le type correspond aussi au sous répertoire de module dans
 * lequel sont rangées les noisettes
 * @return 
 *
 * TODO changer le nom du répertoire en noisettes (et réperctuer les changements)
**/
function melusine_liste_noisettes_dispo($type=""){
	$type_casier = $type ? $type : "squelettes";
	effacer_config("melusine_".$type_casier."/skel");
	$sous_rep = $type ? $type."/" : "";
	$chemin="modules/".$sous_rep;

	// Ce qui suit est très inspiré des fonctions du noizetier
	// par Joseph, Marcimat et Kent1 (GPL3)
	$match = "[^-]*[.]html$";
	$liste = find_all_in_path($chemin, $match);
	$i=1;
	foreach($liste as $squelette=>$chemin) {
		$noisette = preg_replace(',[.]html$,i', '', $squelette);
		$cheminskels="melusine_".$type_casier."/skel/skels".$i;
        	ecrire_config($cheminskels,$noisette);
		$i++;
		
	}
}


/**
 * Vérifier qu'une colonne du squelette n'est pas vide
 *
 * @param text $colonne issu du caser de config
 * 
 * @return bool true si c'est vide 
 *
**/

function melusine_colonne_pasvide($colonne){
	foreach($colonne as $value){
		if($value!=''and $value!='aucun'){return true;};
	}
	return false;
}

/**
 * Sert à rassembler (ou à ordonner) les noisettes dans le casier
 * Est utilisé dans les formulaires de config du squelette melusine
 *
 * @param num $i ???
 * @param text $objet est le type d'objet concerné
 * @param text $zone est le casier concerné
 * (et donc le suffixe de la table meta où seront stockées les données)
 * 
 * @return
 *
**/

function melusine_rassembler($i,$objet="squelettes",$zone="effectifs"){
	$var=$i;
	$chemin='melusine_'.$objet.'/'.$zone.'/'.$var;
	$j=$i+1;
	$varplus=$j;
	$chemin_bas='melusine_'.$objet.'/'.$zone.'/'.$varplus;
	$pos_bas=lire_config($chemin_bas);
	ecrire_config($chemin,$pos_bas);
	ecrire_config($chemin_bas,'aucun');
	$i++;
	if($i<12){melusine_rassembler($i,$zone,$objet);};
}
/**
 * Retourne la liste des fichiers qui doivent être déplacés
 * à partir de la valeur des metas
 *
 * @param text $casier casier dans les meta qui contient les arrays
 * 
 * @return text la liste HTML des fichiers à déplacer.
 *
**/

function melusine_message_noisettes_a_deplacer($casier="melusine_perso_a_deplacer"){
	$return = "";

	// TODO à déplacer dans le fichier lang quand il y en aura un
	$message_info = "<p>Lors de son installation, M&eacute;lusine a r&eacute;cup&eacute;r&eacute; des fichiers de l'ancien plugin DATICE que vous aviez personnalis&eacute;s.</p>

<p>Pour que ces fichiers ne soient pas &eacute;cras&eacute;s lors de la prochaine mise &agrave; jour de M&eacute;lusine, il est n&eacute;cessaire que vous effectuiez par FTP les op&eacute;rations de copie ci-dessous&nbsp:</p>";

	// On détemine le dossier squelette
	$dossier_squelettes = $GLOBALS['dossier_squelettes'];
	if (!$dossier_squelettes) $dossier_squelettes = "squelettes";
	// On récupère les meta
	include_spip('inc/config');
	$tableaux = lire_config($casier);
	// Pour chaque URL
	if ($tableaux) {
		foreach($tableaux as $key1 => $sous_tableau) {
			foreach($sous_tableau as $value) {

				// Chemins pour le privé
				$chemin_fichier = substr($value,3);
				$chemin_copie = str_replace(_DIR_PLUGIN_MELUSINE,$dossier_squelettes."/",$value);
				$chemin_test_fichier = $value;
				$chemin_test_copie = "../".$chemin_copie;


				// Chemins pour le public
				if (!test_espace_prive()) {
					$chemin_test_fichier = $chemin_fichier;
					$chemin_copie = substr($chemin_copie,3);
					$chemin_test_copie = $chemin_copie;

				}

				// On vérifie que le fichier existe encore dans Mélusine
				// qu'il n'est pas vide
				// et qu'il n'existe pas encore dans le répertoire squelettes
				if (file_exists($chemin_test_fichier) AND filesize($chemin_test_fichier)>0 AND !file_exists($chemin_test_copie)) {
					$return .= wrap("<strong>Copier</strong> ".wrap($chemin_fichier,"<code>")." dans ".wrap($chemin_copie,"<code>"),"<li class='liste-item'>");
				}
			}
		}
		
	}
	if ($return) $return = $message_info.wrap($return,"<ul class='liste'>");
	return $return;

}
?>