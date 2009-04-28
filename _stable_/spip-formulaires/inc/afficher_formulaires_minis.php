<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function inc_afficher_formulaires_minis($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial2'), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_formulaire_mini_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png');
	}


	function afficher_formulaire_mini_boucle($row, $own) {
	
		global $dir_lang, $spip_lang_right;
		
		$vals = '';

		$id_formulaire	= $row['id_formulaire'];
		$titre			= $row['titre'];
		$statut			= $row['statut'];

		switch ($statut) {
			case 'hors_ligne':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'en_ligne':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s .= "<a href='" . generer_url_ecrire("formulaires","id_formulaire=$id_formulaire") .
			"'$dir_lang style=\"display:block;\">";
		$s .= typo($titre);
		$s .= "</a>";
		$s .= "</div>";
	
		$vals[] = $s;

		$vals[] = "<b>N°".$id_formulaire."</b>";
	
		return $vals;
	}


?>