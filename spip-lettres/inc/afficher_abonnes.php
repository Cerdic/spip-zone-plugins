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


	include_spip('classes/lettre');


	function inc_afficher_abonnes($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('lettresprive:abonnes');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial2', 120), array('arial1', 40), array('arial1', 40), array('arial1', 60));
		$tableau = array();
		if (is_array($formater))
			$args = $formater;
		else
			$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_abonne_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/abonne.png');
	}


	function afficher_abonne_boucle($row, $own) {
		$vals = '';

		$id_abonne = $row['id_abonne'];
		$id_lettre = $own['id_lettre'];
		$id_rubrique = $own['id_rubrique'];
		$abonne = new abonne($id_abonne);
		$email	= $abonne->email;
		$nom	= $abonne->nom;
		$format	= $abonne->format;

		switch ($abonne->calculer_statut()) {
			case 'a_valider':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'valide':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
			case 'vide':
				$vals[] = http_img_pack('puce-poubelle.gif', 'puce-poubelle', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<a href='".generer_url_ecrire('abonnes', 'id_abonne='.$abonne->id_abonne)."'$dir_lang style=\"display:block;\">";
		$s.= typo($abonne->email);
		$s.= "</a>";
		$vals[] = $s;

		if (empty($abonne->nom)) {
			$s = '&nbsp;';
		} else {
			$s = "<a href='".generer_url_ecrire('abonnes', 'id_abonne='.$abonne->id_abonne)."'$dir_lang style=\"display:block;\">";
			$s.= $abonne->nom;
			$s.= "</a>";
		}
		$vals[] = $s;

		if ($id_lettre) {
			$clics = sql_count(sql_select('AC.id_clic', 'spip_abonnes_clics AS AC INNER JOIN spip_clics AS C ON C.id_clic=AC.id_clic', 'C.id_lettre='.intval($id_lettre).' AND AC.id_abonne='.intval($abonne->id_abonne)));
			if ($clics == 1)
				$vals[] = $clics.'&nbsp;'._T('lettresprive:clic_minuscules');
			else if ($clics > 1)
				$vals[] = $clics.'&nbsp;'._T('lettresprive:clics_minuscules');
			else
				$vals[] = '&nbsp;';
		} else {
			if ($id_rubrique) {
				$abonnement_direct = sql_countsel('spip_abonnes_rubriques', 'id_rubrique='.intval($id_rubrique).' AND id_abonne='.intval($abonne->id_abonne));
				if ($abonnement_direct)
					$vals[] = _T('lettresprive:direct');
				else
					$vals[] = _T('lettresprive:indirect');
			} else {
				$vals[] = '&nbsp;';
			}
		}

		$vals[] = $abonne->format;

		$vals[] = "<b>N°".$abonne->id_abonne."</b>";
	
		return $vals;
	}
	
	
?>