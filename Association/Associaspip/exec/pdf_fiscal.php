<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Production du recu fiscal a partir du formulaire standard 
// "Recu Dons aux oeuvres", article 200-5 du Code General des Impots)

// Nouvelle version a prendre en compte:
//http://www.impots.gouv.fr/portal/deploiement/p1/fichedescriptiveformulaire_5184/fichedescriptiveformulaire_5184.pdf

if (!defined("_ECRIRE_INC_VERSION")) return;

define('RECU_FISCAL', find_in_path('recu_fiscal.pdf'));
if (!defined('SIGNATURE_PRES')) define('SIGNATURE_PRES', '');

include_spip('pdf/fpdi_pdf_parser');
include_spip('pdf/fpdf');
include_spip('pdf/fpdf_tpl');
include_spip('pdf/fpdi');
include_spip('pdf/chiffreEnLettre');

function exec_pdf_fiscal()
{
  $annee = intval(_request('annee'));
  $id_auteur = intval(_request('id'));

  $full = autoriser('associer', 'adherents');
  if (!$full AND ($id_auteur != $GLOBALS['visiteur_session']['id_auteur'])) {
		include_spip('inc/minipres');
		echo minipres();
  } elseif (!$data = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur")) {
		include_spip('inc/minipres');
		echo minipres(_T('public:aucun_auteur'));
  } else {
		if (!preg_match('/^\d{4}$/', $annee)) $annee = date('Y') - 1;
		$montants = sql_getfetsel('SUM(recette) AS montant', "spip_asso_comptes", "id_journal=$id_auteur AND vu AND date_format( date, '%Y' ) = $annee AND imputation=" . sql_quote($GLOBALS['association_metas']['pc_cotisations']));
		if (!$montants)
		  {echo "Versement en $annee pour l'adherent de mail $mail: $montants";}
		else {
		  $nom=$data['prenom'].' '.$data['nom_famille']; 
		  $adresse=$data['adresse'];
		  $cp=$data['code_postal'];
		  $ville=$data['ville'];

		  if (isset($_GET['var_profile']))
		    erreur_squelette();
		  else build_pdf($montants, $nom, $adresse, $cp, $annee, $ville, "$annee-$id_auteur");
		}
  }
}

function build_pdf($montants, $nom, $adresse, $cp, $annee, $ville, $code)
{
$pdf =& new FPDI();

$lettre=new ChiffreEnLettre();
$nombre=$lettre->Conversion($montants);
$pdf->setSourceFile(RECU_FISCAL);

$pdf->addPage();
$pdf->useTemplate($pdf->importPage(1, '/MediaBox'), 0, 0, 210);

$pdf->SetFont('Arial'); 
$pdf->SetFontSize(9); 
$pdf->SetTextColor(0,0,0); 
$pdf->SetXY(160, 25); 
$pdf->Write(0, $code);
#$pdf->SetXY(20, 55);
#$pdf->Write(0, $GLOBALS['association_metas']['nom']);
#$pdf->SetXY(20, 64);
#$pdf->Write(0, $GLOBALS['association_metas']['rue']);
#$pdf->SetXY(40, 69);
#$pdf->Write(0, $GLOBALS['association_metas']['cp']);
#$pdf->SetXY(70, 69);
#$pdf->Write(0, $GLOBALS['association_metas']['ville']);
$pdf->SetXY(25, 178); 
$pdf->Write(0, $nom);
$pdf->SetXY(45, 186); 
$pdf->Write(0, $adresse);
$pdf->SetXY(40, 191); 
$pdf->Write(0, $cp);
$pdf->SetXY(80, 191); 
$pdf->Write(0, $ville);
$pdf->SetXY(132, 209); 
$pdf->SetFontSize(12); 
$pdf->Write(0, $montants);
$pdf->SetFontSize(9); 
$pdf->SetXY(75, 223); 
$pdf->Write(0, $nombre);
if (SIGNATURE_PRES) $pdf->Image(SIGNATURE_PRES,160,247);
$pdf->SetFillColor(255,255,255);
$pdf->Rect(68,228,12,2,'F');
$pdf->Rect(135,247,25,4,'F');
$pdf->SetXY(68, 229); 
$pdf->Write(0, $annee);
$pdf->SetXY(135, 248); 
$pdf->Write(0, date('d/m/Y'));


// A finir: page 2.
#$pdf->addPage();
#$pdf->useTemplate($pdf->importPage(2, '/MediaBox'), 0, 0, 210);

$pdf->Output('test.pdf', 'I');
}
?>
