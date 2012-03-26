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
function exec_export_compteresultats_pdf()
{
	if (!autoriser('associer', 'export_compte_resultats')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$var = _request('var');
		$pdf = new EXPORT_PDF();
		$pdf->SetFont('Arial', '', 12);
		$pdf->AddPage();
		$pdf->init($var);
		$pdf->enTete();
		$pdf->lesCharges($GLOBALS['association_metas']['classe_charges']);
		$pdf->lesProduits($GLOBALS['association_metas']['classe_produits']);
		$pdf->leResultat();
		$pdf->lesContributionsVolontaires($GLOBALS['association_metas']['classe_contributions_volontaires']);
		$pdf->leFichier();
	}
}

define('FPDF_FONTPATH', 'font/');
include_spip('fpdf');
include_spip('inc/charsets');
include_spip('inc/association_plan_comptable');

class EXPORT_PDF extends FPDF {

	var $largeur = 210; // format A4
	var $hauteur = 297;
	var $xx = 0; // coin haut gauche 1ère boite
	var $yy = 0;
	var $marge_gauche = 10;
	var $marge_droite = 10;
	var $marge_haut = 10;
	var $marge_bas = 10;
	var $icone_h = 20;
	var $icone_v = 20;
	var $space_v = 2;
	var $space_h = 2;
	var $largeur_utile = 0; // largeur sans les marges droites et gauches
	var $largeur_pour_titre = 0; // largeur utile sans icone
	var $exercice;
	var $join;
	var $sel;
	var $where;
	var $having;
	var $order;
	var $total_charges;
	var $total_produits;

	function init($var) {
		$tableau = unserialize(rawurldecode($var));
		$this->exercice = $tableau[0];
		$this->join = $tableau[1];
		$this->sel = $tableau[2];
		$this->where = $tableau[3];
		$this->having = $tableau[4];
		$this->order = $tableau[5];

		$this->largeur_utile = $this->largeur-$this->marge_gauche-$this->marge_droite;
		$this->largeur_pour_titre = $this->largeur_utile-$this->icone_h-3*$this->space_h;

		$this->xx = $this->marge_gauche;
		$this->yy = $this->marge_haut;

		$this->total_charges = 0;
		$this->total_produits = 0;

		$this->SetAuthor('Marcel BOLLA');
		$this->SetCreator('Associaspip & Fpdf');
		$this->SetTitle('Module Comptabilite de Associaspip');
		$this->SetSubject(_T('asso:cpte_resultat_titre_general'));
	}

	function Footer() {
		//Positionnement a 2 fois la marge du bas
		$this->SetY(-2*$this->marge_bas);
		//Arial italique 8
		$this->SetFont('Arial', 'I', 8);
		//Couleur du texte en gris
		$this->SetTextColor(128);
		//Date et Numéro de page
		$this->Cell(0, 10, html_entity_decode(_T('asso:cpte_resultat_pied_page_export_pdf') .' -- '. affdate(date('Y-m-d')) .' -- '. _T('Page') .' '. $this->PageNo()), 0, 0, 'C');
	}

	function enTete() {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$yc = $this->yy+$this->space_v;
		$this->SetDrawColor(128);

		// le logo du site
		$logo = find_in_path('IMG/siteon0.jpg');
//		$chercher_logo = charger_fonction('chercher_logo', 'inc');
//		$logo = $chercher_logo(0, 'id_site');
		if ($logo) {
			include_spip('/inc/filtres_images_mini');
			$this->Image(extraire_attribut(image_reduire($logo, $this->icone_h, $this->icone_v), 'src'), $xc, $yc, $this->icone_h);
//			$this->Image(extraire_attribut(image_reduire($logo[0], $this->icone_h, $this->icone_v), $xc, $yc, $this->icone_h), 'src'); // attention : que JPeG <http://forum.virtuemart.net/index.php?topic=75616.0>
		}
		//Arial gras 22
		$this->SetFont('Arial', 'B', 22);
		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);
		//Titre centre
		$xc += $this->space_h+($logo?$this->icone_h:0);
		$this->SetXY($xc, $yc);
		$this->Cell($logo?($this->largeur_pour_titre):($this->largeur_pour_titre+$this->icone_h-$this->space_h), 12, html_entity_decode(_T('asso:cpte_resultat_titre_general')), 0, 0, 'C', true);
		$yc += 12;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		//Arial 12
		$this->SetFont('Arial', '', 12);
		//Couleur de fond
		$this->SetFillColor(235);
		//Sous titre Nom de l'association
		$this->SetXY($xc, $yc);
		$this->Cell($logo?$this->largeur_pour_titre:$this->largeur_pour_titre+$this->icone_h-$this->space_h, 6, utf8_decode(_T('Association').' : '. $GLOBALS['association_metas']['nom']), 0, 0, 'C', true);
		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v/2);
		$yc += $this->space_v/2;

		//Arial 12
		$this->SetFont('Arial', '', 12);
		//Couleur de fond
		$this->SetFillColor(235);
		//Sous titre Date début et fin de l'exercice
		$this->SetXY($xc, $yc);
		$this->Cell($logo?$this->largeur_pour_titre:$this->largeur_pour_titre+$this->icone_h-$this->space_h, 6, utf8_decode(_T('Exercice').' : ' . sql_getfetsel('intitule','spip_asso_exercices', 'id_exercice='.$this->exercice) ), 0, 0, 'C', true);
		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour de l'entete
		$this->Rect($this->xx, $this->yy, $this->largeur_utile, $yc-$this->marge_haut);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function lesCharges($classe) {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$y_orig = $this->yy+$this->space_v;
		$yc = $y_orig+$this->space_v;

		//Arial gras 14
		$this->SetFont('Arial', 'B', 14);

		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);

		//Titre centre
		$this->SetXY($xc, $yc);
		$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_charges')), 0, 0, 'C');
		$yc += 10;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		$query = sql_select(
			"imputation, SUM(depense) AS valeurs, date_format(date, '%Y') AS annee".$this->sel, // select
			'spip_asso_comptes '.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having.$classe // having
		);
		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);

				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);

				$this->Cell(($this->largeur_utile)-(2*$this->space_h+20), 6, utf8_decode(($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))), 0, 0, 'L', true);

				$chapitre = $new_chapitre;

				//Saut de ligne
				$this->Ln();
				$yc += 6;
			}

			//Couleur de fond
			$this->SetFillColor(245);

			// positionne le curseur
			$this->SetXY($xc, $yc);
			$this->Cell(20, 6, utf8_decode($data['code']), 0, 0, 'R', true);

			$this->Cell(($this->largeur_utile)-(2*$this->space_h+50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);
			$this->Cell(30, 6, association_nbrefr($data['valeurs']), 0, 0, 'R', true);

			$this->total_charges += $data['valeurs'];

			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}
		// positionne le curseur
		$this->SetXY($xc, $yc);

		//Couleur de fond
		$this->SetFillColor(215);

		$this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_total_charges')), 1, 0, 'R', true);
		$this->Cell(30, 6, association_nbrefr($this->total_charges), 1, 0, 'R', true);
		$yc += 6;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function lesProduits($classe) {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$y_orig = $this->yy+$this->space_v;
		$yc = $y_orig+$this->space_v;

		//Arial gras 14
		$this->SetFont('Arial', 'B', 14);

		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);

		//Titre centre
		$this->SetXY($xc, $yc);
		$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_produits')), 0, 0, 'C');
		$yc += 10;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		$query = sql_select(
			"imputation, SUM(recette) AS valeurs, date_format(date, '%Y') AS annee".$this->sel, // select
			'spip_asso_comptes '.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having.$classe // having
		);
		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);

				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);

				$this->Cell(($this->largeur_utile)-(2*$this->space_h+20), 6, utf8_decode(($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))), 0, 0, 'L', true);

				$chapitre = $new_chapitre;

				//Saut de ligne
				$this->Ln();
				$yc += 6;
			}

			//Couleur de fond
			$this->SetFillColor(245);

			// positionne le curseur
			$this->SetXY($xc, $yc);
			$this->Cell(20, 6, utf8_decode($data['code']), 0, 0, 'R', true);

			$this->Cell(($this->largeur_utile)-(2*$this->space_h+50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);
			$this->Cell(30, 6, association_nbrefr($data['valeurs']), 0, 0, 'R', true);

			$this->total_produits += $data['valeurs'];

			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}
		// positionne le curseur
		$this->SetXY($xc, $yc);

		//Couleur de fond
		$this->SetFillColor(215);

		$this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_total_produits')), 1, 0, 'R', true);
		$this->Cell(30, 6, association_nbrefr($this->total_produits), 1, 0, 'R', true);
		$yc += 6;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function leResultat() {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$y_orig = $this->yy+$this->space_v;
		$yc = $y_orig+$this->space_v;

		//Arial gras 14
		$this->SetFont('Arial', 'B', 14);

		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);

		//Titre centre
		$this->SetXY($xc, $yc);

		$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_resultat')), 0, 0, 'C');
		$yc += 10;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		//Couleur de fond
		$this->SetFillColor(215);

		$res = $this->total_produits-$this->total_charges;
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

	function lesContributionsVolontaires($classe) {
		// Les coordonnees courantes
		$xc = $this->xx+$this->space_h;
		$y_orig = $this->yy+$this->space_v;
		$yc = $y_orig+$this->space_v;

		//Arial gras 14
		$this->SetFont('Arial', 'B', 14);

		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);

		//Titre centre
		$this->SetXY($xc, $yc);

		$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_benevolat')), 0, 0, 'C');
		$yc += 10;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		$charges_evaluees = $produits_evalues = 0;
		$query = sql_select(
			"imputation, SUM(depense) AS charge_evaluee, SUM(recette) AS produit_evalue, date_format(date, '%Y') AS annee".$this->sel, // select
			'spip_asso_comptes '.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having.$classe // having
		);
		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);
				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);
				$this->Cell(($this->largeur_utile)-(2*$this->space_h+20), 6, utf8_decode(($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))), 0, 0, 'L', true);
				$chapitre = $new_chapitre;
				//Saut de ligne
				$this->Ln();
				$yc += 6;
			}

			//Couleur de fond
			$this->SetFillColor(245);

			// positionne le curseur
			$this->SetXY($xc, $yc);
			$this->Cell(20, 6, utf8_decode($data['code']), 0, 0, 'R', true);
			$this->Cell(($this->largeur_utile)-(2*$this->space_h+50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);
			if ($data['charge_evaluee']>0) {
				$this->Cell(30, 6, association_nbrefr($data['charge_evaluee']), 0, 0, 'R', true);
				$charges_evaluees += $data['charge_evaluee'];
			} else {
				$this->Cell(30, 6, association_nbrefr($data['produit_evalue']), 0, 0, 'R', true);
				$produits_evalues += $data['produit_evalue'];
			}
			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}

		//Couleur de fond
		$this->SetFillColor(215);

		// positionne le curseur
		$this->SetXY($xc, $yc);

		$this->Cell(($this->largeur_utile)/2-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_total_charges_evaluees')), 1, 0, 'R', true);
		$this->Cell(30, 6, association_nbrefr($charges_evaluees), 1, 0, 'R', true);

		// positionne le curseur sur l'autre demi page
		$xc += ( $this->largeur_utile)/2;
		$this->SetXY($xc, $yc);
		$this->Cell(($this->largeur_utile)/2-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_total_produits_evalues')), 1, 0, 'R', true);
		$this->Cell(30, 6, association_nbrefr($produits_evalues), 1, 0, 'R', true);

		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function leFichier() {
		$this->Output('compte_resultats_'.$this->exercice.'.pdf', 'I');
	}

}


?>