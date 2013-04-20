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

	include_spip('pdf/extends');
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
	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2
	);
	$order = 'id_auteur';
	if ($sent['nom_famille']=='on')
	  $order = 'nom_famille' . ",$order";
	$pdf->Query(sql_select('*','spip_asso_membres', request_statut_interne(), '', $order), $prop);
	$pdf->Output();
	}
}
?>
