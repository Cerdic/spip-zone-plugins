<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function formatspip_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'articles':
				$id_article = $flux['args']['id_article'];
				// le formulaire qu'on ajoute
				$flux['data'] .= formatspip_affiche($id_article);
				break;
			default:
				break;
		}
		return $flux;
	}

	function formatspip_affiche($id_article){
		include_spip('inc/presentation');
		
		$q = spip_query("SELECT descriptif, chapo, texte, ps FROM spip_articles WHERE id_article=$id_article");
		$row = spip_fetch_array($q);
		$txt = '';
		if (strlen($row['descriptif'])>0) {
			$txt .= "----- "._T('texte_descriptif_rapide')." -----\n\n";
			$txt .= $row['descriptif']."\n\n";
		}
		if (strlen($row['chapo'])>0) {
			$txt .= "----- "._T('info_chapeau')." -----\n\n";
			$txt .= $row['chapo']."\n\n";
		}
		if (strlen($row['texte'])>0) {
			$txt .= "----- "._T('info_texte')." -----\n\n";
			$txt .= $row['texte']."\n\n";
		}
		if (strlen($row['ps'])>0) {
			$txt .= "----- "._T('info_post_scriptum')." -----\n\n";
			$txt .= $row['ps']."\n\n";
		}
		
		$flux = '';
		$bouton = bouton_block_invisible("formatspip");
		$flux .= debut_cadre_enfonce("../"._DIR_PLUGIN_FORMATSPIP."/images/formatspip-24.png", true, '', $bouton._T('formatspip:texte_formatspip'));
		$flux .= debut_block_invisible("formatspip");
		$flux .= '<textarea cols="55" rows="20">'.$txt.'</textarea>';
		$flux .= fin_block();
		$flux .= fin_cadre_enfonce(true);
		
		return $flux;
	}

?>