<?php
/*
 
+-------------------------------+
 Nom de l'outil : sommaire
+-------------------------------+
 Date : mardi 03 avril 2007
 Auteur :  Patrice Vanneufville
+-------------------------------+
 Presente un petit sommaire 
 en haut de l'article base sur 
 les balises {{{}}}
+-------------------------------+

*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function sommaire_raccourcis() {
	return _T('couteauprive:sommaire_'.(defined('_sommaire_AUTOMATIQUE')?'sans':'avec'));
}

// pipeline 'nettoyer_raccourcis'
function sommaire_nettoyer_raccourcis($texte) {
	return str_replace(array(_sommaire_SANS_FOND, _CS_SANS_SOMMAIRE, _CS_AVEC_SOMMAIRE), '', $texte);
}

// informer dans la description de l'outil de la balise utilisee par SPIP
function sommaire_description_outil($flux) {
	if($flux['outil']=='sommaire' && preg_match(',<h(\d),', $GLOBALS['debut_intertitre'], $r))
		$flux['texte'] = str_replace(array('@h3@','@h4@'), array('h'.$r[1],'h'.($r[1]+1)), $flux['texte']);
	return $flux;
}

?>