<?php
/**
 * Plugin rss en article
 *
 * page cachée pour les gens préssés ne pouvant pas attendre le genie
 * permet de relancer manuellement la recopie	du flux en article
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function exec_rss_article_dist(){
	// si pas autorise : message d'erreur	... admin ... a affiner
	if (!autoriser('editer', 'article')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip("genie/rssarticle_copie");

	//
	// affichages
	// 

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('rssarticle:activer_recopie_intro'), 'editer', 'editer');
	// titre
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	echo gros_titre(_T('rssarticle:activer_recopie_intro'),'', false);

	// colonne gauche
	echo debut_gauche('', true);
	echo debut_droite('', true);
	
	// centre de la page	
	genie_rssarticle_copie_dist("manuel");
	echo '<div><small>'.date('Y/m/d H:i:s').'</small><br />'._T('rssarticle:maj_manuelle').'</div>';
	echo '<div style="margin:2em 0;"><a href="?exec=rss_article" style="border:1px solid;padding:0.5em;background:#fff;">'._T('rssarticle:maj_recharge').'</a></div>';

	// pied
	echo fin_gauche() . fin_page();
}

?>
