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


	include_spip('inc/formulaires_classes');


	function inc_afficher_applicants($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('formulairesprive:applicants');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial2'), array('arial1', 100), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_applicant_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_FORMULAIRES.'/prive/images/applications.png');
	}


	function afficher_applicant_boucle($row, $own) {
		$vals = '';
		$id_applicant = $row['id_applicant'];
		$applicant = new applicant($id_applicant);
		$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
		$vals[] = '<a href="'.generer_url_ecrire('applicants','id_applicant='.$id_applicant).'">'.$applicant->email.'</a>';
		if ($applicant->nom)
			$vals[] = $applicant->nom;
		else
			$vals[] = '&nbsp;';
		$vals[] = "<b>N°".$id_applicant."</b>";
		return $vals;
	}


?>