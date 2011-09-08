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
		$champs = description_table('spip_asso_membres');

	$sent = _request('champs');
		foreach ($champs['field'] as $k => $v) {
	  if ($sent[$k]=='on') {
	    $type = strpos($v, 'text');
	    $p = ($type===false) ? 'R' : (($type==0) ? 'L' : 'C');
	    $n = ($type===false) ? 20 : (($type==0) ? 45 : 25);
	    $pdf->AddCol($k,$n,_T('asso:adherent_libelle_' . $k), $p);
	  }
	}
	// ainsi que les colonnes pour les champs hors table spip_asso_membres
	include_spip('inc/association_coordonnees');
	$liste_id_auteurs = unserialize(_request('liste_id_auteurs'));
	if ($sent['email']=='on') {
		$pdf->AddCol('email',45 ,_T('asso:adherent_libelle_email'), 'C');
		$emails =  association_recuperer_emails($liste_id_auteurs);
	}

	if ($sent['adresse']=='on') {
		$pdf->AddCol('adresse',45 ,_T('asso:adherent_libelle_adresse'), 'L');
		$adresses =  association_recuperer_adresses($liste_id_auteurs,"\n"," ");
	}
	if ($sent['telephone']=='on') {
		$pdf->AddCol('telephone',30 ,_T('asso:adherent_libelle_telephone'), 'C');
		$telephones = association_recuperer_telephones($liste_id_auteurs);
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

	$adresses_tels = array();
	foreach($liste_id_auteurs as $id_auteur) {
		$adresses_tels[$id_auteur] = array();
		if ($sent['email']=='on') $adresses_tels[$id_auteur]['email'] = implode("\n", $emails[$id_auteur]);
		if ($sent['adresse']=='on') $adresses_tels[$id_auteur]['adresse'] = preg_replace('/\&nbsp\;/', " ", preg_replace('/(\s*\<br\s*\/>\s*)+/i', "\n", implode("\n\n", $adresses[$id_auteur]))); /* recupere toutes les adresses dans un seul string separees par \n\n et remplace les <br/> par des \n et &nbsp; par des " " car la chaine est en HTML */
		if ($sent['telephone']=='on') {
			$first_tel = true;
			$telephones_string = '';
			foreach ($telephones[$id_auteur] as $telephone) {
				if (!$first_tel) {$telephones_string .= "\n";} else $first_tel = false;
				$telephones_string .=  recuperer_fond("modeles/coordonnees_telephoniques", array ('telephone' => $telephone));
			}
			$adresses_tels[$id_auteur]['telephone'] = $telephones_string;
		}
	}

	$pdf->Query_extended(sql_select('*','spip_asso_membres', sql_in('id_auteur', $liste_id_auteurs), '', $order), $prop, $adresses_tels, 'id_auteur');
	$pdf->Output();
	}
}
?>
