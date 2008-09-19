<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// filtre de securisation des squelettes
// utilise avec [(#REM|AccesRestreint_securise_squelette)]
// evite divulgation d'info si plugin desactive
// par erreur fatale
function AccesRestreint_securise_squelette($letexte){
	return "";
}

// filtre de test pour savoir si l'acces a un article est restreint
function AccesRestreint_article_restreint($id_article){
	include_spip('inc/acces_restreint_autorisations');
	return
		@in_array($id_article,
			AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="")
		);
}
// filtre de test pour savoir si l'acces a une rubrique est restreinte
function AccesRestreint_rubrique_restreinte($id_rubrique){
	include_spip('inc/acces_restreint_autorisations');
	return
		@in_array($id_rubrique,
			AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="")
		);
}

?>