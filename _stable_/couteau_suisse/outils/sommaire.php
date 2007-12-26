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
 les balises <h3> ou {{{}}}
+-------------------------------+

*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function sommaire_raccourcis() {
	return _T('desc:sommaire:aide', array('racc' => '<b>'
		. (defined('_sommaire_AUTOMATIQUE')?_sommaire_SANS_SOMMAIRE:_sommaire_AVEC_SOMMAIRE)
		. '</b>'));
}

function sommaire_nettoyer_raccourcis($texte) {
	return str_replace(array(_sommaire_SANS_FOND, _sommaire_SANS_SOMMAIRE, _sommaire_AVEC_SOMMAIRE), '', $texte);
}

?>