<?php

function alias_affiche_droite($flux){
	if ($flux['args']['exec']=='articles'){
		global $spip_lang_right;
		$out = "";
		$id_article = $flux['args']['id_article'];
		$out .= debut_cadre_relief('',true);
		$securiser_action = charger_fonction('securiser_action','inc');
		$url = $securiser_action('aliaser',"article-$id_article",str_replace('&amp;','&',self(true)));
		$out .= "<a href='$url'><img src='"._DIR_PLUGIN_ALIAS."img_pack/article-alias.gif' width='24' height='24' alt='Alias' /> "._T('alias:create_alias')."</a>";
		$out .= fin_cadre_relief(true);
		$flux['data'].= $out;
	}
	return $flux;
}

?>