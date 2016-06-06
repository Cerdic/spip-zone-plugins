<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function alias_affiche_droite($flux) {
	if ($flux['args']['exec'] == 'articles' or ($flux['args']['exec'] == 'article' and isset($flux['args']['id_article']))) {
		global $spip_lang_right;
		$out = "";
		$id_article = isset($flux['args']['id_article']) ? $flux['args']['id_article'] : 0;
		if (version_compare($GLOBALS['spip_version_code'], '3', '<')) {
			$out .= debut_cadre_relief('', true);
		} else {
			$out .= "<div class='box infos'>";
		}

		$securiser_action = charger_fonction('securiser_action', 'inc');
		$url = $securiser_action('aliaser', "article-$id_article", str_replace('&amp;', '&', self(true)));
		$out .= "<span class='icone s24 horizontale article-alias-24'>";
		$out .= "<a href='$url'><img src='" . _DIR_PLUGIN_ALIAS . "img_pack/article-alias.gif' width='24' height='24' alt='Alias' /> <strong>" . _T('alias:create_alias') . "</strong></a>";
		$out .= "</span>";
		if (version_compare($GLOBALS['spip_version_code'], '3', '<')) {
			$out .= fin_cadre_relief(true);
		} else {
			$out .= "</div>";
		}
		$flux['data'] .= $out;
	}

	return $flux;
}

?>
