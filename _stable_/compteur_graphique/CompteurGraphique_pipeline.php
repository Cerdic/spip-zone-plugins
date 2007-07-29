<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMPTEURGRAPHIQUE',(_DIR_PLUGINS.end($p)));

	function CompteurGraphique_AjouterBouton($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['statistiques_visites']->sousmenu['compteur_graphique']= new Bouton(
			"../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."/img_pack/CompteurGraphique.gif",  // icone
			"Compteurs graphiques"	// titre
			);
		}
		return $boutons_admin;
	}

function CompteurGraphique_AfficheGauche($flux) {
    $exec = $flux['args']['exec'];
	$test_configuration = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut = 9");
	$tab_configuration = spip_fetch_array($test_configuration);
	$res_configuration = $tab_configuration['id_compteur'];
	if (!isset($res_configuration) OR ($GLOBALS['connect_statut'] == "0minirezo")) {
		if ((($exec == 'articles_edit') OR ($exec == 'articles')) AND (isset($_GET['id_article']))) {
			include_spip('inc/CompteurGraphique_GestionArticle');
			$flux['data'] .= CompteurGraphique_ArticleGauche($exec);
		}
		if (($exec == 'rubriques_edit') OR ($exec == 'naviguer') AND (isset($_GET['id_rubrique']))) {
			include_spip('inc/CompteurGraphique_GestionRubrique');
			$flux['data'] .= CompteurGraphique_rubriquedroite($exec);
		}
	}
	return $flux;
}

?>