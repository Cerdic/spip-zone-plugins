<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 12/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format Pdf
function exec_pdf_comptesresultat()
{
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_comptabilite');
		$ids = association_passe_parametres_comptables();
		$pdf = new ExportComptesResultat();
		$pdf->init($ids);
		$pdf->association_cartouche_pdf('cpte_resultat_titre_general');
		$lesProduits = $pdf->association_liste_totaux_comptes_classes_pdf($GLOBALS['association_metas']['classe_produits'], 'cpte_resultat', +1, $pdf_>exercice, $pdf->destination);
		$lesCharges = $pdf->association_liste_totaux_comptes_classes_pdf($GLOBALS['association_metas']['classe_charges'], 'cpte_resultat', -1, $pdf_>exercice, $pdf->destination);
		$pdf->leResultat($lesProduits, $lesCharges);
		$pdf->association_liste_totaux_comptes_classes_pdf($GLOBALS['association_metas']['classe_contributions_volontaires'], 'cpte_benevolat', 0, $pdf_>exercice, $pdf->destination);
		$pdf->File('comptes_resultats');
	}
}

include_spip('inc/association_comptabilite');

class ExportComptesResultat extends ExportComptes_PDF {

	function leResultat($lesProduits, $lesCharges) {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$y_orig = $this->yy+$this->space_v;
		$yc = $y_orig+$this->space_v;
		// typo
		$this->SetFont('Arial', 'B', 14); // police : Arial gras 14px
		$this->SetFillColor(235); // Couleur du fond : gris-92.2%
		$this->SetTextColor(0); // Couleur du texte : noir
		//Titre centre
		$this->SetXY($xc, $yc);
		$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_resultat')), 0, 0, 'C');
		$yc += 10;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		//Couleur de fond
		$this->SetFillColor(215);

		$res = $lesProduits-$lesCharges;
		$this->SetXY($xc, $yc);
		if ($res<0) {
			$this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_perte')), 1, 0, 'R', true);
		} else {
			$this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_benefice')), 1, 0, 'R', true);
		}
		$this->Cell(30, 6, association_nbrefr($res), 1, 0, 'R', true);
		$yc += 6;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

}

?>