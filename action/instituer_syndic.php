<?php

/**
*
 * Plugin « Puce active pour les articles syndiqués»
 * Licence GNU/GPL
 * 
  */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Surcharge de la fonction action_instituer_syndic_dist
// Copié en trés grande partie sur la fonction action_instituer_site_dist du fichier action/instituer_site
//  Evite les requêtes vers la base de données quand on demande de remplacer un statut par lui-même ( publié -> publié par exemple )
function action_instituer_syndic() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_syndic_article, $statut) = preg_split('/\W/', $arg);
	
	$cond = "id_syndic_article=" . intval($id_syndic_article);
	$row = sql_fetsel("statut, id_syndic", "spip_syndic_articles", $cond);
	if (!$row OR ($row['statut'] == $statut)) return;

	sql_updateq("spip_syndic_articles", array("statut" => $statut), $cond);

}
?>
