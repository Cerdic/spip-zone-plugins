<?php


	/**
	 * SPIP-Mémos
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function memos_autoriser() {}
	
	
	function autoriser_memos_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'onglet':
			case 'configurer':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


	function memos_affiche_droite($flux) {
		$args = $flux['args'];
		$exec = $args['exec'];
		$data = $flux['data'];

		$alertes = memos_recuperer_memo('alertes');
		$alertes_sans_espaces = trim($alertes);
		if (!empty($alertes_sans_espaces)) {
			$data.= debut_cadre_relief(_DIR_PLUGIN_MEMOS."/prive/images/alertes.gif", true, '', _T('memos:titre_boite_alertes'));
			$data.= $alertes;
			$data.= fin_cadre_relief(true);
			$date.= '<br />';
		}

		$memo = memos_recuperer_memo($exec);
		$memo_sans_espaces = trim($memo);
		if (!empty($memo_sans_espaces)) {
			$data.= debut_cadre_relief(_DIR_PLUGIN_MEMOS."/prive/images/memo-24.png", true, '', _T('memos:titre_boite_memos'));
			$data.= $memo;
			$data.= fin_cadre_relief(true);
		}

		return array(
					'args' => $args,
					'data' => $data
					);
	}


	function memos_recuperer_memo($exec) {
		include_spip('inc/distant');
		$texte_commun		= recuperer_page($GLOBALS['meta']['spip_memos_serveur'].'/spip.php?page='.$GLOBALS['meta']['spip_memos_fond'].'&rubrique='.$exec.'&article=article_commun');
		$texte_specifique	= recuperer_page($GLOBALS['meta']['spip_memos_serveur'].'/spip.php?page='.$GLOBALS['meta']['spip_memos_fond'].'&rubrique='.$exec.'&article='.$GLOBALS['meta']['spip_memos_client']);
		$texte = $texte_commun;
		if (!empty($texte_specifique) AND !empty($texte_commun)) $texte.= '<br />';
		$texte.= $texte_specifique;
		return $texte;
	}


	function memos_install($action) {
		include_spip('inc/plugin');
		$info_plugin = plugin_get_infos(_NOM_PLUGIN_MEMOS);
		$version_plugin = $info_plugin['version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spip_memos_version']) AND ($GLOBALS['meta']['spip_memos_version'] >= $version_plugin));
				break;
			case 'install':
				if (!isset($GLOBALS['meta']['spip_memos_version'])) {
					ecrire_meta('spip_memos_version', $version_plugin);
					ecrire_meta('spip_memos_fond', 'memo');
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_memos_version'];
					if ($version_base < 2.0) {
						ecrire_meta('spip_memos_fond', 'memo');
						ecrire_meta('spip_memos_version', $version_base = 2.0);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				effacer_meta('spip_memos_client');
				effacer_meta('spip_memos_serveur');
				effacer_meta('spip_memos_fond');
				effacer_meta('spip_memos_version');
				break;
		}
	}


?>