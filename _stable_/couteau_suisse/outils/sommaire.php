<?php
/*
 
+-------------------------------+
 Nom de l'outil : sommaire
+-------------------------------+
 Date : mardi 03 avril 2007
 Auteur :  Patrice Vanneufville
+-------------------------------+
 Fonction :
 Presenter un petit sommaire 
 en haut de l'article base sur 
 les balises <h3> ou {{{}}}
+-------------------------------+

*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function sommaire_raccourcis() {
	return _T('cout:sommaire:aide', array('racc' => '<strong>'
		. (defined('_sommaire_AUTOMATIQUE')?_sommaire_SANS_SOMMAIRE:_sommaire_AVEC_SOMMAIRE)
		. '</strong>'));
}

?>