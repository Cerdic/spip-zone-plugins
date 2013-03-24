<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_pdf_etiquettes() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// initialisation des parametres de mise en page
	$tab_meta_eti = array(
		'etiquette_espace_etiquettesl' => 5,
		'etiquette_nb_colonne' => 3,
		'etiquette_nb_ligne' => 7,
		'etiquette_largeur_page' => 210,
		'etiquette_hauteur_page' => 297,
		'etiquette_marge_haut_etiquette' => 10,
		'etiquette_marge_gauche_etiquette' => 3,
		'etiquette_marge_droite_etiquette' => 10,
		'etiquette_marge_haut_page' => 10,
		'etiquette_marge_bas_page' => 10,
		'etiquette_marge_gauche_page' => 10,
		'etiquette_marge_droite_page' => 10,
		'etiquette_espace_etiquettesh' => 0,
	);
	foreach($tab_meta_eti as $key=>$value) {
		if(!$GLOBALS['association_metas'][$key])
			ecrire_meta($key, $value, NULL, 'association_metas');
	}
	// initialisation de la mise en page
	$pas_horizontal = (($GLOBALS['association_metas']['etiquette_largeur_page']-$GLOBALS['association_metas']['etiquette_marge_gauche_page']-$GLOBALS['association_metas']['etiquette_marge_droite_page']-($GLOBALS['association_metas']['etiquette_nb_colonne']-1)*$GLOBALS['association_metas']['etiquette_espace_etiquettesl'])/$GLOBALS['association_metas']['etiquette_nb_colonne'])+$GLOBALS['association_metas']['etiquette_espace_etiquettesl'];
	$pas_vertical = ($GLOBALS['association_metas']['etiquette_hauteur_page']-$GLOBALS['association_metas']['etiquette_marge_haut_page']-$GLOBALS['association_metas']['etiquette_marge_bas_page']-($GLOBALS['association_metas']['etiquette_nb_ligne']-1)*$GLOBALS['association_metas']['etiquette_espace_etiquettesh'])/$GLOBALS['association_metas']['etiquette_nb_ligne']+$GLOBALS['association_metas']['etiquette_espace_etiquettesh'];
	// initialisation des compteurs
	$tab_etiquette = array();
	$indice_colonne = 0;
	$indice_ligne = 0;
	$num_page = 1;
	// on recupere les criteres des id_auteur
	$where = htmlspecialchars_decode(_request('where_adherents'));
	$jointure = _request('jointure_adherents');
	$filtre_email = _request('filtre_email');
	if($filtre_email) { // restreindre aux auteurs sans email principal
		$where .= " AND a.email='' ";
		$jointure .= " LEFT JOIN spip_auteurs a ON m.id_auteur=a.id_auteur ";
	}
	// on genere la requete des id_auteur
	$query = sql_select('m.id_auteur AS id_auteur', "spip_asso_membres m $jointure", $where, '', 'm.nom_famille,m.prenom');
	// on construit le tableau des id_auteur
	$liste_id_auteurs = array();
	while ($data = sql_fetch($query)) {
		$liste_id_auteurs[] = $data['id_auteur'];
	}

	include_spip('pdf/extends');
	$pdf = new PDF(FALSE, array($GLOBALS['association_metas']['etiquette_largeur_page'],$GLOBALS['association_metas']['etiquette_hauteur_page']), 'mm', 'P');
	$statut = _request('statut_interne');
	$pdf->titre = _T('asso:adherent_titre_liste_'.$statut);
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(0,0);
	$pdf->AliasNbPages();
	$pdf->SetFontSize(8);

	$affiche_civilite = $GLOBALS['association_metas']['etiquette_avec_civilite'];

	$where = "l.objet='auteur' AND ( (a.code_postal<>'' AND a.ville<>'') OR (a.boite_postale<>'') ) AND ". sql_in('l.id_objet', $liste_id_auteurs);
	$res = sql_select('l.id_objet, a.*','spip_adresses_liens l INNER JOIN spip_adresses a ON l.id_adresse=a.id_adresse', $where,'' );
	$indice = 0;
	include_spip('filtres','inc'); // http://doc.spip.org/@extraire_multi
	while($val = sql_fetch($res)) {
		$etiquette = array( // cf. : http://fr.wikipedia.org/wiki/Adresse_postale#Exemples
			'ligne1' => $val['id_objet'],
			'ligne2' => association_formater_idnom($val['id_objet'], array(), '', ''), // pas top : il faut refaire une requete... il faudra ameliorer la requete principale en faisant une jointure qui donne tout ca directement !!!
//			'ligne2' => association_formater_nom( ($GLOBALS['association_metas']['etiquette_avec_civilite']?$val['sexe']:''), $val['prenom'], $val['nom_famille'], ''),
			'ligne4' => $val['voie'],
			'ligne3' => $val['complement'],
			'ligne5' => trim($val['boite_postale']),
			'ligne6' => trim($val['code_postal']).' '.$val['ville'], // formatage a la francaise ; dans certains pays l'ordre est inverse (et en GB le code postal est carrement ecrit sur la ligne suivante !)
			'ligne7' => ($val['pays']==$GLOBALS['association_metas']['pays'] ? '' : extraire_multi(sql_getfetsel('nom','spip_pays', (is_numeric($val['pays'])?'id_pays':'code').'='.sql_quote($val['pays']) ,'','')) ), // pas terrible de devoir faire une requete separee pour une adresse, mais ceci ne devrait pas se produire souvent (en general)
		);
		if ( (fmod($indice,$GLOBALS['association_metas']['etiquette_nb_colonne']*$GLOBALS['association_metas']['etiquette_nb_ligne'])==0)and ($indice>0) ) {
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
		$pdf->setX($posx);
		$pdf->setY($posy);
		$pdf->SetFontSize(7);
#		$pdf->Cell(0,5,($indice+1).' -'.$etiquette['ligne1'],0,2); // active l'affichage des  id_adresse-id_auteur au dessus de l'etiquette d'adresse : utile en deboguage
		$pdf->Cell(0,5,' ',0,2); // cree une petite ligne vide : a mettre en lieu et place de la precedente en phase finale
		$pdf->AdaptFont(9,'B');
		$pdf->Cell(0,5,utf8_decode($etiquette['ligne2']),0,2);
		$pdf->SetFontSize(8);
		if ($etiquette['ligne3']) {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne3']),0,2);
		}
		if ($etiquette['ligne4']) {
			$pdf->Cell(0,5,utf8_decode($etiquette['ligne4']),0,2);
		}
		$pdf->AdaptFont(9,'B');
		if ($etiquette['ligne5']) {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne5']),0,2);
		}
		if ($etiquette['ligne6']) {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne6']),0,2);
		}
		$pdf->SetFontSize(8);
		if ($etiquette['ligne7']) {
			$pdf->cell(0,5,utf8_decode($etiquette['ligne7']),0,2);
		}
#		$pdf->Rect($indice_colonne*$pas_horizontal, $indice_ligne*$pas_vertical, $pas_horizontal, $pas_vertical );
		$indice++;
	}
//	if ($indice==0) {
//		$message .= _T('asso:etiquette_aucune_impression');
//	} else {
		$nom_fic = 'etiquettes_'. _request('suffixe') .'_'. ($filtre_email?'avec':'sans'). 'email.pdf';
		$pdf->Output($nom_fic, 'D');
//		$message .= _T('asso:etiquette_fichier_telecharger', array('fichier'=>$nom_fic) );
//	}

//	return array('editable' => FALSE, 'message_ok'=> $message );
}

?>