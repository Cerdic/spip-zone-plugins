<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function formatspip_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'article':
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
		
		$q = spip_query("SELECT titre, surtitre, soustitre, descriptif, chapo, texte, ps, virtuel, nom_site, url_site FROM spip_articles WHERE id_article=$id_article");
		$row = spip_fetch_array($q);
		$txt = '';
		if (strlen($row['titre'])>0) {
			$txt .= "----- "._T('info_titre')." -----\n";
			$txt .= interdire_scripts(entites_html($row['titre']))."\n\n";
		}
		if (strlen($row['surtitre'])>0) {
			$txt .= "----- "._T('info_surtitre')." -----\n";
			$txt .= interdire_scripts(entites_html($row['surtitre']))."\n\n";
		}
		if (strlen($row['soustitre'])>0) {
			$txt .= "----- "._T('texte_sous_titre')." -----\n";
			$txt .= interdire_scripts(entites_html($row['soustitre']))."\n\n";
		}
		if (strlen($row['descriptif'])>0) {
			$txt .= "----- "._T('texte_descriptif_rapide')." -----\n";
			$txt .= interdire_scripts(entites_html($row['descriptif']))."\n\n";
		}
		if (strlen($row['chapo'])>0) {
			$txt .= "----- "._T('info_chapeau')." -----\n";
			$txt .= interdire_scripts(entites_html($row['chapo']))."\n\n";
		}
		if (strlen($row['url_site'])>0) {
			$txt .= "----- "._T('entree_liens_sites')." -----\n";
			$txt .= _T('info_titre') . interdire_scripts(entites_html($row['nom_site']))."\n";
			$txt .= _T('info_url') . interdire_scripts(entites_html($row['url_site']))."\n\n";
		}
		if (strlen($row['texte'])>0) {
			$txt .= "----- "._T('info_texte')." -----\n";
			$txt .= interdire_scripts(entites_html($row['texte']))."\n\n";
		}
		if (strlen($row['ps'])>0) {
			$txt .= "----- "._T('info_post_scriptum')." -----\n";
			$txt .= interdire_scripts(entites_html($row['ps']))."\n\n";
		}
		if (strlen($row['virtuel'])>0) {
			$txt .= "----- "._T('bouton_redirection')." -----\n";
			$txt .= interdire_scripts(entites_html($row['virtuel']))."\n\n";
		}
		
		$txt = '<pre style="white-space: pre-wrap;       /* css-3 */
 white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
 white-space: -pre-wrap;      /* Opera 4-6 */
 white-space: -o-pre-wrap;    /* Opera 7 */
 word-wrap: break-word;       /* Internet Explorer 5.5+ */">'. $txt .'<pre>';
		
		// compatibilite avec SPIP 1.92
		$compat = function_exists('bouton_block_depliable');
		$bouton = $compat?bouton_block_depliable(_T('formatspip:texte_formatspip'), 'invisible', "formatspip")
			:bouton_block_invisible("formatspip")._T('formatspip:texte_formatspip');
		$bloc = $compat?debut_block_depliable(false, "formatspip")
			:debut_block_invisible("formatspip");
		return debut_cadre_enfonce("../"._DIR_PLUGIN_FORMATSPIP."/images/formatspip-24.png", true, '', $bouton)
			. $bloc	. $txt . fin_block()
			. fin_cadre_enfonce(true);
		
		return $flux;
	}
