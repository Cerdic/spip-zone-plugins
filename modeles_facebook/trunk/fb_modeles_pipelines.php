<?php
/**
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function fb_modeles_insert_head($flux) {
	$config = fbmod_config();
	if ($config && $config['include_metas']=='oui') {

	} else {
		if (isset($config['appid']) && strlen($config['appid'])) {
			$flux .= "\n<meta property=\"fb:app_id\" content=\"".$config['appid']."\" />";
		}
		elseif (isset($config['pageid']) && strlen($config['pageid'])) {
			$flux .= "\n<meta property=\"fb:page_id\" content=\"".$config['pageid']."\" />";
		}
		elseif (isset($config['userid']) && strlen($config['userid'])) {
			$flux .= "\n<meta property=\"fb:admins\" content=\"".$config['userid']."\" />";
		}
		else {
			$flux .= "\n<!-- FB Modeles vide -->";
		}
	}
	$flux .= "\n";

	return $flux;	
}

?>