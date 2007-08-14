<?php
 /*
 *   +----------------------------------+
 *    Nom du Filtre : sommaire_article
 *   +----------------------------------+
 *    Date : mardi 03 avril 2007
 *    Auteur :  Patrice Vanneufville
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Presenter un petit sommaire en haut
 *     de l'article base sur les balises <h3>
 *   +-------------------------------------+ 
 *
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par l'outil
// si cette fonction n'existe pas, le plugin cherche alors  _T('cout:un_outil:aide');
function sommaire_raccourcis() {
	return defined('_sommaire_AUTOMATIQUE')
		?_T('cout:sommaire:aide', array('racc' => '<strong>'._sommaire_SANS_SOMMAIRE.'</strong>'))
		:_T('cout:sommaire:aide', array('racc' => '<strong>'._sommaire_AVEC_SOMMAIRE.'</strong>'));
}

?>