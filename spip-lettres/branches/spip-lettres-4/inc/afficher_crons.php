<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function inc_afficher_crons($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('lettresprive:envois_recurrents');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 16), array('arial2'), array('', 12), array('arial2'), array('arial1', 150));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_cron_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/cron-24.png');
	}


	function afficher_cron_boucle($row, $own) {
		$vals = '';

		$vals[] = http_img_pack('../'._DIR_PLUGIN_LETTRES.'prive/images/cron-16.png', "case", '');

		$vals[] = typo($row['titre']);

		$vals[] = http_img_pack('../'._DIR_PLUGIN_LETTRES.'prive/images/rubrique-12.png', "rub", '');

		$vals[] = '<a href="'.generer_url_ecrire('naviguer', 'id_rubrique='.$row['id_rubrique']).'">'.typo($row['titre_rub']).'</a>';

		$vals[] = '<a href="'.generer_url_ecrire('config_lettres_cron', 'supprimer_cron='.$row['id_rubrique']).'">'._T('lettresprive:supprimer_cron').'</a>';

		return $vals;
	}
	
	
?>