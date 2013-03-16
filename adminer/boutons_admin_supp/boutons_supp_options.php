<?php
global $cacher_le_bouton;

/*
Pour placer la barre de boutons verticalement et a droite, decommenter
la ligne suivante en enlevant les deux barres qui la debutent.
*/

//$GLOBALS['style_barre'] = "a_droite";

/*
Pour que la barre soit translucide, décommenter les deux lignes suivantes
et choisir le taux de translucidite (de 0 a 1).
*/

$GLOBALS['style_barre'] = "translucide";
$GLOBALS['translucidite'] = "0.6";


/*
Pour annuler l'affichage d'un bouton precis, decommenter la ligne voulue en
enlevant les deux barres qui la debutent.

Noter qu'il est parfaitement possible d'utiliser des conditions en PHP pour
afficher ou non un bouton. Par exemple, si l'on veut que les boutons pour
editer et modifier une rubrique n'apparaissent pas dans une rubrique
particuliere (par exemple, la rubrique 100), il suffit d'utiliser la syntaxe
suivante :
*/
if($GLOBALS['id_rubrique']=='100'){
	$cacher_le_bouton["editer_rubrique"]=" style='display:none !important'";
	$cacher_le_bouton["modifier_rubrique"]=" style='display:none !important'";
}
/*
Par defaut, tous les boutons sont affiches.
*/

//$cacher_le_bouton["garder_au_premier_plan"]=" style='display:none !important'";

// $cacher_le_bouton["auteur"]=" style='display:none !important'";

// $cacher_le_bouton["creer_rubrique"]=" style='display:none !important'";
// $cacher_le_bouton["nouvel_article"]=" style='display:none !important'";
// $cacher_le_bouton["referencer_site"]=" style='display:none !important'";
// $cacher_le_bouton["nouvelle_breve"]=" style='display:none !important'";
 
// $cacher_le_bouton["editer_rubrique"]=" style='display:none !important'";
// $cacher_le_bouton["editer_article"]=" style='display:none !important'";
// $cacher_le_bouton["editer_breve"]=" style='display:none !important'";
// $cacher_le_bouton["editer_auteur"]=" style='display:none !important'";
 
// $cacher_le_bouton["modifier_breve"]=" style='display:none !important'";
// $cacher_le_bouton["modifier_rubrique"]=" style='display:none !important'";
// $cacher_le_bouton["modifier_article"]=" style='display:none !important'";
// $cacher_le_bouton["modifier_mot"]=" style='display:none !important'";
// $cacher_le_bouton["modifier_site"]=" style='display:none !important'";
// $cacher_le_bouton["modifier_auteur"]=" style='display:none !important'";
 
// $cacher_le_bouton["previsualisation"]=" style='display:none !important'";
// $cacher_le_bouton["statistiques"]=" style='display:none !important'";
// $cacher_le_bouton["espace_prive"]=" style='display:none !important'";
// $cacher_le_bouton["calculer"]=" style='display:none !important'";
// $cacher_le_bouton["deconnexion"]=" style='display:none !important'";
 
// $cacher_le_bouton["masquer"]=" style='display:none !important'";
?>