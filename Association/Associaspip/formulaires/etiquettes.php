<?php

function formulaires_etiquettes_charger_dist()
{
	include_spip('base/abstract_sql');
	$valeurs = array(
		'statut_interne'=>_request('statut_interne'),
		'filtre_email'=>true
	);

	// on peut faire beaucoup mais comment ??
	if(!isset($GLOBALS['association_metas']['etiquette_nb_colonne'])) {
		$tab_meta_eti = array(
			'etiquette_nb_colonne'=>3,
			'etiquette_nb_ligne'=>7,
			'etiquette_largeur_page'=>210,
			'etiquette_hauteur_page'=>297,
			'etiquette_marge_haut_etiquette'=>10,
			'etiquette_marge_gauche_etiquette'=>3,
			'etiquette_marge_droite_etiquette'=>10,
			'etiquette_marge_haut_page'=>10,
			'etiquette_marge_bas_page'=>10,
			'etiquette_marge_gauche_page'=>10,
			'etiquette_marge_droite_page'=>10,
			'etiquette_espace_etiquettesh'=>0,
			'etiquette_espace_etiquettesl'=>5,
			);
		foreach($tab_meta_eti as $key=>$value) {
			ecrire_meta($key, $value, null, 'association_metas');
		}
	}

	return $valeurs;
}

function formulaires_etiquettes_verifier_dist()
{
	$erreurs = array();

	// Verifier si il a au moins une selection
	$etiquette = _request('statut_interne') ;
	if($etiquette=='') {
		$erreurs['etiquette'] = _T('asso:etiquette_aucun_choix');
		$message = $erreurs['etiquette'] ;
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !<BR/>'.$message;
    }
    return $erreurs;
}

function formulaires_etiquettes_traiter_dist()
{
	include_spip('base/abstract_sql');
	include_spip('inc/acces');
	include_spip('pdf/extends');
	$ok = false;
	$nb_colonne = $GLOBALS['association_metas']['etiquette_nb_colonne'];
	$nb_ligne = $GLOBALS['association_metas']['etiquette_nb_ligne'];
	$largeur_page = $GLOBALS['association_metas']['etiquette_largeur_page'];
	$hauteur_page = $GLOBALS['association_metas']['etiquette_hauteur_page'];
	$marge_haut_etiquette = $GLOBALS['association_metas']['etiquette_marge_haut_etiquette'];
	$marge_gauche_etiquette = $GLOBALS['association_metas']['etiquette_marge_gauche_etiquette'];
	$marge_droite_etiquette = $GLOBALS['association_metas']['etiquette_marge_droite_etiquette'];
	$marge_haut_page = $GLOBALS['association_metas']['etiquette_marge_haut_page'];
	$marge_bas_page = $GLOBALS['association_metas']['etiquette_marge_bas_page'];
	$marge_gauche_page = $GLOBALS['association_metas']['etiquette_marge_gauche_page'];
	$marge_droite_page = $GLOBALS['association_metas']['etiquette_marge_droite_page'];
	$espace_etiquettesh = $GLOBALS['association_metas']['etiquette_espace_etiquettesh'];
	$espace_etiquettesl = $GLOBALS['association_metas']['etiquette_espace_etiquettesl'];
	$pas_horizontal = (($largeur_page-$marge_gauche_page-$marge_droite_page-($nb_colonne-1)*$espace_étiquettesl)/$nb_colonne)+$espace_etiquettesl;
	$pas_vertical=($hauteur_page-$marge_haut_page-$marge_bas_page-($nb_ligne-1)*$espace_etiquettesh)/$nb_ligne+$espace_etiquettesh;
	$tab_etiquette = array();
	$indice_colonne = 0;
	$indice_ligne = 0;
	$num_page = 1;

	$pdf=new PDF('P','mm','A4',false);
	$pdf->titre = _T('asso:adherent_titre_liste_actifs');
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(0 ,0);
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',8);

	$affiche_civilite = $GLOBALS['association_metas']['etiquette_avec_civilite'];

	$table = array('m'=>'spip_asso_membres', 'al'=>'spip_adresses_liens','a'=>'spip_adresses');
	$where = 'al.objet=\'auteur\' AND al.id_objet=m.id_auteur AND al.id_adresse=a.id_adresse AND ( (code_postal<>\'\' AND ville<>\'\') OR (boite_postale<>\'\') )';
	$statut_interne =_request('statut_interne');
	if($statut_interne!='tous'){
		$where .= ' AND statut_interne = '.sql_quote($statut_interne);
	}
	$filtre_email = _request('filtre_email');
	if($filtre_email) {
		$table['auteur'] ='spip_auteurs';
		$where .=' AND m.id_auteur=auteur.id_auteur AND auteur.email=\'\'';
	}
	$res = sql_select('*',$table, $where,'','nom_famille,prenom');
	$indice = 0;
	include_spip('ifltres','inc'); // http://doc.spip.org/@extraire_multi
	while($val = sql_fetch($res)) {
		if ($GLOBALS['association_metas']['etiquette_avec_civilite']) {
			$vnom = trim($val['sexe'].' '.$val['nom_famille'].' '.$val['prenom']);
		} else {
			$vnom = $val['prenom'].' '.$val['nom_famille'];
		}
		// cf. : http://fr.wikipedia.org/wiki/Adresse_postale#Exemples
		$etiquette = array(
			'ligne1'=>$val['id_auteur'],
			'ligne2'=>$vnom,
			'ligne4'=>$val['voie'],
			'ligne3'=>$val['complement'],
			'ligne5'=>trim($val['boite_postale']),
			'ligne6'=>trim($val['code_postal']).' '.$val['ville'],
			'ligne7'=>($val['pays']==$GLOBALS['association_metas']['pays'] ? '' : extraire_multi(sql_getfetsel('nom','spip_pays', (is_numeric($val['pays'])?'id_pays':'code').'='.sql_quote($val['pays']) ,'','')) ), // pas terrible de devoir faire une requete separee pour une adresse, mais ceci ne devrait pas se produire souvent (en general)
		);
		if((fmod($indice,$nb_colonne*$nb_ligne)==0)and ($indice>0)) {
			$pdf->AddPage();
			$num_page++;
		}
		$indice_colonne = $indice%$nb_colonne;
		$indice_ligne = floor($indice/$nb_colonne)%$nb_ligne;
		$posx = $marge_gauche_page+$indice_colonne*$pas_horizontal+$marge_gauche_etiquette;
		$posy = $indice_ligne*$pas_vertical+$marge_haut_etiquette+$marge_haut_page;
		$imp_droite = ($posx+$pas_horizontal-$marge_droite_etiquette-$espace_etiquettesl-$marge_gauche_etiquette);
		$pdf->SetrightMargin($imp_droite);
		$pdf->SetLeftMargin($posx);
		//$pdf->setX($posx);
		$pdf->setY($posy);
		$pdf->SetFont('Arial','',7);
		/* la ligne ci-dessous active l'affichage des  id_adresse-id_auteur au dessus de l'etiquette d'adresse : utile en deboguage */
#		$pdf->Cell(0,5,($indice+1).' -'.$etiquette['ligne1'],0,2);
		/* la ligne ci-dessous cree une petite ligne vide : a mettre en lieu et place de la precedente en phase finale */
		$pdf->Cell(0,5,' ',0,2);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,utf8_decode($etiquette['ligne2']),0,2);
		$pdf->SetFont('Arial','',8);
		if ($etiquette['ligne3'] >'') {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne3']),0,2);
		}
		if ($etiquette['ligne4'] >'') {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne4']),0,2);
		}
		$pdf->SetFont('Arial','B',9);
		if ($etiquette['ligne5'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne5']),0,2);
		}
		if ($etiquette['ligne6'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne6']),0,2);
		}
		$pdf->SetFont('Arial','',8);
		if ($etiquette['ligne7'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne7']),0,2);
		}
#		$pdf->Rect($indice_colonne*$pas_horizontal, $indice_ligne*$pas_vertical, $pas_horizontal, $pas_vertical );
		$indice++;
	}
	if ($indice==0) {
		$message .= 'Aucune étiquette à imprimer<br />';
	} else {
		$nom_fic = 'etiquette.pdf';
		$pdf->Output('etiquettes.pdf', 'D');
		$message .='<a href="'.$nom_fic.'">Telecharger le fichier</a>';
	}

	return array('editable' => $ok, 'message_ok'=> $message );
}

?>