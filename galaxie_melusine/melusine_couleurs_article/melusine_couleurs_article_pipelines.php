<?php
/**
 * Plugin Couleurs pour les articles de Mélusine
 * (c) 2012 Jean-Marc Labat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */



function melusine_couleurs_article_affiche_milieu ($flux) {
// On affiche le formulaire de choix des couleurs sous l'article
        $exec = $flux["args"]["exec"];
	if ($exec == "article") {
		$id_rubrique = $flux["args"]["id_article"];
		$contexte = array('id_article' => $id_article);
		$ret = "<div id='pave_couleurs'>";
		$ret .= recuperer_fond("prive/squelettes/contenu/configurer_article", $contexte);
		$ret .= "</div>";
		$flux["data"] .= $ret;
		
	}
    return $flux;
}


?>