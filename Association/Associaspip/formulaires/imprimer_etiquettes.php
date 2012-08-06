<?php

function formulaires_imprimer_etiquettes_charger_dist()
{
	include_spip('base/abstract_sql');
	$valeurs = array(
		'statut_interne'=>_request('statut_interne'),
		'filtre_email'=>true
	);

	// on peut faire beaucoup mieux mais comment ?
	if(!$GLOBALS['association_metas']['etiquette_nb_colonne'])
		$tab_meta_eti['etiquette_nb_colonne'] = 3;
	if(!$GLOBALS['association_metas']['etiquette_nb_ligne'])
		$tab_meta_eti['etiquette_nb_ligne'] = 7;
	if(!$GLOBALS['association_metas']['etiquette_largeur_page'])
		$tab_meta_eti['etiquette_largeur_page'] = 210;
	if(!$GLOBALS['association_metas']['etiquette_hauteur_page'])
		$tab_meta_eti['etiquette_hauteur_page'] = 297;
	if(!$GLOBALS['association_metas']['etiquette_marge_haut_etiquette'])
		$tab_meta_eti['etiquette_marge_haut_etiquette'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_marge_gauche_etiquette'])
		$tab_meta_eti['etiquette_marge_gauche_etiquette'] = 3;
	if(!$GLOBALS['association_metas']['etiquette_marge_droite_etiquette'])
		$tab_meta_eti['etiquette_marge_droite_etiquette'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_marge_haut_page'])
		$tab_meta_eti['etiquette_marge_haut_page'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_marge_bas_page'])
		$tab_meta_eti['etiquette_marge_bas_page'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_marge_gauche_page'])
		$tab_meta_eti['etiquette_marge_gauche_page'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_marge_droite_page'])
		$tab_meta_eti['etiquette_marge_droite_page'] = 10;
	if(!$GLOBALS['association_metas']['etiquette_espace_etiquettesh'])
		$tab_meta_eti['etiquette_espace_etiquettesh'] = 0;
	if(!$GLOBALS['association_metas']['etiquette_espace_etiquettesl'])
		$tab_meta_eti['etiquette_espace_etiquettesl'] = 5;
	foreach($tab_meta_eti as $key=>$value) {
		ecrire_meta($key, $value, null, 'association_metas');
	}

	return $valeurs;
}

function formulaires_imprimer_etiquettes_verifier_dist()
{
	$erreurs = array();

	// Verifier si il a au moins une selection
	if(_request('statut_interne')=='') {
		$erreurs['etiquette'] = _T('asso:etiquette_aucun_choix');
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
    }
    return $erreurs;
}

function formulaires_imprimer_etiquettes_traiter_dist()
{
	include_spip('base/abstract_sql');
	include_spip('inc/acces');
	include_spip('pdf/extends');
	$pas_horizontal = (($GLOBALS['association_metas']['etiquette_largeur_page']-$GLOBALS['association_metas']['etiquette_marge_gauche_page']-$GLOBALS['association_metas']['etiquette_marge_droite_page']-($GLOBALS['association_metas']['etiquette_nb_colonne']-1)*$GLOBALS['association_metas']['etiquette_espace_etiquettesl'])/$GLOBALS['association_metas']['etiquette_nb_colonne'])+$GLOBALS['association_metas']['etiquette_espace_etiquettesl'];
	$pas_vertical = ($GLOBALS['association_metas']['etiquette_hauteur_page']-$GLOBALS['association_metas']['etiquette_marge_haut_page']-$GLOBALS['association_metas']['etiquette_marge_bas_page']-($GLOBALS['association_metas']['etiquette_nb_ligne']-1)*$GLOBALS['association_metas']['etiquette_espace_etiquettesh'])/$GLOBALS['association_metas']['etiquette_nb_ligne']+$GLOBALS['association_metas']['etiquette_espace_etiquettesh'];
	$tab_etiquette = array();
	$indice_colonne = 0;
	$indice_ligne = 0;
	$num_page = 1;

	$pdf = new PDF(false, array($GLOBALS['association_metas']['etiquette_largeur_page'],$GLOBALS['association_metas']['etiquette_hauteur_page']), 'mm', 'P');
	$pdf->titre = _T('asso:adherent_titre_liste_actifs');
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(0 ,0);
	$pdf->AliasNbPages();
	$pdf->SetFontSize(8);

	$affiche_civilite = $GLOBALS['association_metas']['etiquette_avec_civilite'];

	$table = array('m'=>'spip_asso_membres', 'al'=>'spip_adresses_liens','a'=>'spip_adresses');
	$where = "al.objet='auteur' AND al.id_objet=m.id_auteur AND al.id_adresse=a.id_adresse AND ( (code_postal<>'' AND ville<>'') OR (boite_postale<>'') )";
	$statut_interne = _request('statut_interne');
	if($statut_interne!='tous'){
		$where .= ' AND statut_interne = '.sql_quote($statut_interne);
	}
	$filtre_email = _request('filtre_email');
	if($filtre_email) {
		$table['auteur'] = 'spip_auteurs';
		$where .= " AND m.id_auteur=auteur.id_auteur AND auteur.email=''";
	}
	$res = sql_select('*',$table, $where,'','nom_famille,prenom');
	$indice = 0;
	include_spip('filtres','inc'); // http://doc.spip.org/@extraire_multi
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
		if((fmod($indice,$GLOBALS['association_metas']['etiquette_nb_colonne']*$GLOBALS['association_metas']['etiquette_nb_ligne'])==0)and ($indice>0)) {
			$pdf->AddPage();
			$num_page++;
		}
		$indice_colonne = $indice%$GLOBALS['association_metas']['etiquette_nb_colonne'];
		$indice_ligne = floor($indice/$GLOBALS['association_metas']['etiquette_nb_colonne'])%$GLOBALS['association_metas']['etiquette_nb_ligne'];
		$posx = $GLOBALS['association_metas']['etiquette_marge_gauche_page']+$indice_colonne*$pas_horizontal+$GLOBALS['association_metas']['etiquette_marge_gauche_etiquette'];
		$posy = $indice_ligne*$pas_vertical+$GLOBALS['association_metas']['etiquette_marge_haut_etiquette']+$GLOBALS['association_metas']['etiquette_marge_haut_page'];
		$imp_droite = ($posx+$pas_horizontal-$GLOBALS['association_metas']['etiquette_marge_droite_etiquette']-$GLOBALS['association_metas']['etiquette_espace_etiquettesl']-$GLOBALS['association_metas']['etiquette_marge_gauche_etiquette']);
		$pdf->SetrightMargin($imp_droite);
		$pdf->SetLeftMargin($posx);
		//$pdf->setX($posx);
		$pdf->setY($posy);
		$pdf->SetFontSize(7);
#		$pdf->Cell(0,5,($indice+1).' -'.$etiquette['ligne1'],0,2); // active l'affichage des  id_adresse-id_auteur au dessus de l'etiquette d'adresse : utile en deboguage
		$pdf->Cell(0,5,' ',0,2); // cree une petite ligne vide : a mettre en lieu et place de la precedente en phase finale
		$pdf->AdaptFont(9,'B');
		$pdf->Cell(0,5,utf8_decode($etiquette['ligne2']),0,2);
		$pdf->SetFontSize(8);
		if ($etiquette['ligne3'] >'') {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne3']),0,2);
		}
		if ($etiquette['ligne4'] >'') {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne4']),0,2);
		}
		$pdf->AdaptFont(9,'B');
		if ($etiquette['ligne5'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne5']),0,2);
		}
		if ($etiquette['ligne6'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne6']),0,2);
		}
		$pdf->SetFontSize(8);
		if ($etiquette['ligne7'] >'') {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne7']),0,2);
		}
#		$pdf->Rect($indice_colonne*$pas_horizontal, $indice_ligne*$pas_vertical, $pas_horizontal, $pas_vertical );
		$indice++;
	}
	if ($indice==0) {
		$message .= _T('asso:etiquette_aucune_impression');
	} else {
		$nom_fic = 'etiquette.pdf';
		$pdf->Output($nom_fic, 'D');
		$message .= _T('asso:etiquette_fichier_telecharger', array('fichier'=>$nom_fic) );
	}

	return array('editable' => false, 'message_ok'=> $message );
}

?>