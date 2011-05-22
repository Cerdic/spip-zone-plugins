<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_adherents()
{
	if (!autoriser('associer', 'adherents')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$pdf=new PDF();	

	$pdf->titre = _T('asso:adherent_titre_liste_actifs');
	$pdf->Open();
	$pdf->AddPage();

	//On définit les colonnes (champs,largeur,intitulé,alignement)
	$champs = $GLOBALS['association_tables_principales']['spip_asso_membres']['field'];
	$sent = _request('champs');
	foreach ($champs as $k => $v) {
	  if ($sent[$k]=='on') {
	    $type = strpos($v, 'text');
	    $p = ($type===false) ? 'R' : (($type==0) ? 'L' : 'C');
	    $n = ($type===false) ? 20 : (($type==0) ? 45 : 25);
	    $pdf->AddCol($k,$n,_T('asso:adherent_libelle_' . $k), $p);
	  }
	}
	// ainsi que les colonnes pour les champs hors table spip_asso_membres
	if ($sent['email']=='on') $pdf->AddCol('email',45 ,_T('asso:adherent_libelle_email'), 'C');
//	if ($sent['adresse']=='on') $pdf->AddCol('adresse',45 ,_T('asso:adherent_libelle_adresse'), 'L');
//	if ($sent['telephone']=='on') $pdf->AddCol('telephone',20 ,_T('asso:adherent_libelle_telephone'), 'C');

	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2
	);
	$order = 'm.id_auteur';
	if ($sent['nom_famille']=='on')
	  $order = 'm.nom_famille' . ",$order";
//* A FAIRE : AJOUTER LE MAIL, ADRESSE, TELEPHONE DANS LA QUERY ou trouver un autre moyen 
	$pdf->Query(sql_select('*','spip_asso_membres as m INNER JOIN spip_auteurs as a ON m.id_auteur=a.id_auteur', request_statut_interne(), '', $order), $prop);
	$pdf->Output();
	}
}
?>
