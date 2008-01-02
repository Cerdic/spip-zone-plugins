<?php

// Ceci est une surcharge de inc/autoriser.php

##
## autoriser_article_modifier
##
@define('_DEBUG_CRAYONS', true);
$GLOBALS['FormulaireArticle'] = @unserialize($GLOBALS['meta']['FormulaireArticle']);

if ($GLOBALS['FormulaireArticle']['modifier_article']) {
	if (!function_exists('autoriser_article_modifier')) {
		function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
			$s = spip_query(
			"SELECT id_rubrique,id_secteur,statut FROM spip_articles WHERE id_article="._q($id));
			$r = spip_fetch_array($s);
			include_spip('inc/auth');
			return
				autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
				OR (
					in_array($qui['statut'], $GLOBALS['FormulaireArticle']['modifier_article'])
					AND (
						$GLOBALS['FormulaireArticle']['auteur_mod_article']
						OR in_array($r['statut'], array('publie','prop','prepa', 'poubelle'))
					)
					AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur'])) //1.9.2
					//AND sql_count(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
				);
		}
	}
}
?>