<?php

/* * ***********************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************ */

if (!defined("_ECRIRE_INC_VERSION"))
	return;

define('FPDF_FONTPATH', 'font/');
include_spip('pdf/fpdf');
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
	var $annee;
	var $join;
	var $sel;
	var $having;
	var $order;
	var $total_charges;
	var $total_produits;

	function init($var) {
		$tableau = @unserialize($var);
		$this->annee = $tableau[0];
		$this->join = $tableau[1];
		$this->sel = $tableau[2];
		$this->having = $tableau[3];
		$this->order = $tableau[4];

		$this->largeur_utile = $this->largeur - $this->marge_gauche - $this->marge_droite;
		$this->largeur_pour_titre = $this->largeur_utile - $this->icone_h - 3 * $this->space_h;

		$this->xx = $this->marge_gauche;
		$this->yy = $this->marge_haut;

		$this->total_charges = 0;
		$this->total_produits = 0;
	}

	function Footer() {
		//Positionnement a 2 fois la marge du bas
		$this->SetY(-2 * $this->marge_bas);
		//$this->SetY($this->yy + $this->space_v);
		//Arial italique 8
		$this->SetFont('Arial', 'I', 8);
		//Couleur du texte en gris
		$this->SetTextColor(128);
		//Date et Numéro de page
		$this->Cell(0, 10, html_entity_decode(_T('asso:cpte_resultat_pied_page_export_pdf')) . " - " . date('d-m-Y') . " - Page " . $this->PageNo(), 0, 0, 'C');
	}

	function enTete() {
		// Les coordonnees courantes
		$xc = $this->xx + $this->space_h;
		$yc = $this->yy + $this->space_v;
		$this->SetDrawColor(128);
		
		// le logo du site
		// TODO : traiter le cas ou le site n'a pas de Logo
		$this->Image(find_in_path('IMG/siteon0.jpg'), $xc, $yc + 4, $this->icone_h);
		$xc += $this->icone_h;
		//Arial gras 22
		$this->SetFont('Arial', 'B', 22);
		//Couleurs du cadre, du fond et du texte
		$this->SetFillColor(235);
		$this->SetTextColor(0);
		//Titre centre
		$xc += $this->space_h;
		$this->SetXY($xc, $yc);
		$this->Cell($this->largeur_pour_titre, 12, html_entity_decode(_T('asso:cpte_resultat_titre_general')), 0, 0, 'C', true);
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
		$this->Cell($this->largeur_pour_titre, 6, utf8_decode("Association - " . $GLOBALS['association_metas']['nom']), 0, 0, 'C', true);
		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v / 2);
		$yc += $this->space_v / 2;

		//Arial 12
		$this->SetFont('Arial', '', 12);
		//Couleur de fond
		$this->SetFillColor(235);
		//Sous titre Date début et fin de l'exercice
		$this->SetXY($xc, $yc);
		$this->Cell($this->largeur_pour_titre, 6, utf8_decode("Exercice : " . $this->annee), 0, 0, 'C', true);
		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour de l'entete
		$this->Rect($this->xx, $this->yy, $this->largeur_utile, $yc - $this->marge_haut);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function lesCharges($classe) {
		// Les coordonnees courantes
		$xc = $this->xx + $this->space_h;
		$y_orig = $this->yy + $this->space_v;
		$yc = $y_orig + $this->space_v;

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

		$quoi = "sum(depense) AS valeurs";
		$query = sql_select("imputation, " . $quoi . ", date_format(date, '%Y') AS annee$this->sel",
				"spip_asso_comptes$this->join",
				"",
				$this->order . "annee",
				"code ASC",
				'',
				"annee=$this->annee$this->having$classe");

		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$valeurs = $data['valeurs'];
			$new_chapitre = substr($data['code'], 0, 2);

			if ($chapitre != $new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);

				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);

				$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 20), 6, utf8_decode(association_plan_comptable_complet($new_chapitre)), 0, 0, 'L', true);

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

			$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);

			$this->Cell(30, 6, number_format($valeurs, 2, ',', ' '), 0, 0, 'R', true);

			$this->total_charges += $valeurs;

			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}
		// positionne le curseur
		$this->SetXY($xc, $yc);

		//Couleur de fond
		$this->SetFillColor(215);

		$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_total_charges')), 1, 0, 'R', true);

		$this->Cell(30, 6, number_format($this->total_charges, 2, ',', ' '), 1, 0, 'R', true);

		$yc += 6;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc - $y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function lesProduits($classe) {
		// Les coordonnees courantes
		$xc = $this->xx + $this->space_h;
		$y_orig = $this->yy + $this->space_v;
		$yc = $y_orig + $this->space_v;

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

		$quoi = "sum(recette) AS valeurs";
		$query = sql_select("imputation, " . $quoi . ", date_format(date, '%Y') AS annee$this->sel",
				"spip_asso_comptes$this->join",
				"",
				$this->order . "annee",
				"code ASC",
				'',
				"annee=$this->annee$this->having$classe");

		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$valeurs = $data['valeurs'];
			$new_chapitre = substr($data['code'], 0, 2);

			if ($chapitre != $new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);

				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);

				$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 20), 6, utf8_decode(association_plan_comptable_complet($new_chapitre)), 0, 0, 'L', true);

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

			$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);

			$this->Cell(30, 6, number_format($valeurs, 2, ',', ' '), 0, 0, 'R', true);

			$this->total_produits += $valeurs;

			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}
		// positionne le curseur
		$this->SetXY($xc, $yc);

		//Couleur de fond
		$this->SetFillColor(215);

		$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_total_produits')), 1, 0, 'R', true);

		$this->Cell(30, 6, number_format($this->total_produits, 2, ',', ' '), 1, 0, 'R', true);

		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc - $y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function leResultat() {
		// Les coordonnees courantes
		$xc = $this->xx + $this->space_h;
		$y_orig = $this->yy + $this->space_v;
		$yc = $y_orig + $this->space_v;

		//Couleur de fond
		$this->SetFillColor(215);

		$res = $this->total_produits - $this->total_charges;

		$this->SetXY($xc, $yc);

		if ($res < 0) {
			$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_perte')), 1, 0, 'R', true);
		}
		else {
			$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_benefice')), 1, 0, 'R', true);
		}

		$this->Cell(30, 6, number_format($res, 2, ',', ' '), 1, 0, 'R', true);

		$yc += 6;

		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc - $y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

	function lesContributionsVolontaires($classe) {
		// Les coordonnees courantes
		$xc = $this->xx + $this->space_h;
		$y_orig = $this->yy + $this->space_v;
		$yc = $y_orig + $this->space_v;

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

		$quoi = "sum(depense) AS charge_evaluee, sum(recette) AS produit_evalue";
		$query = sql_select("imputation, " . $quoi . ", date_format(date, '%Y') AS annee$this->sel",
				"spip_asso_comptes$this->join",
				"",
				$this->order . "annee",
				"code ASC",
				'',
				"annee=$this->annee$this->having$classe");

		$chapitre = '';
		$i = 0;

		//Arial 12
		$this->SetFont('Arial', '', 12);

		while ($data = sql_fetch($query)) {
			// positionne le curseur
			$this->SetXY($xc, $yc);

			$charge_evaluee = $data['charge_evaluee'];
			$produit_evalue = $data['produit_evalue'];
			$new_chapitre = substr($data['code'], 0, 2);

			if ($chapitre != $new_chapitre) {
				//Couleur de fond
				$this->SetFillColor(225);

				$this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', true);

				$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 20), 6, utf8_decode(association_plan_comptable_complet($new_chapitre)), 0, 0, 'L', true);

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

			$this->Cell(($this->largeur_utile) - (2 * $this->space_h + 50), 6, utf8_decode($data['intitule']), 0, 0, 'L', true);

			if ($charge_evaluee > 0) {
				$this->Cell(30, 6, number_format($charge_evaluee, 2, ',', ' '), 0, 0, 'R', true);

				$charges_evaluees += $charge_evaluee;
			}
			else {
				$this->Cell(30, 6, number_format($produit_evalue, 2, ',', ' '), 0, 0, 'R', true);

				$produits_evalues += $produit_evalue;
			}
			//Saut de ligne
			$this->Ln();
			$yc += 6;
		}

		//Couleur de fond
		$this->SetFillColor(215);

		// positionne le curseur
		$this->SetXY($xc, $yc);

		$this->Cell(($this->largeur_utile) / 2 - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_total_charges_evaluees')), 1, 0, 'R', true);
		$this->Cell(30, 6, number_format($charges_evaluees, 2, ',', ' '), 1, 0, 'R', true);

		// positionne le curseur sur l'autre demi page
		$xc += ( $this->largeur_utile) / 2;
		$this->SetXY($xc, $yc);
		$this->Cell(($this->largeur_utile) / 2 - (2 * $this->space_h + 30), 6, html_entity_decode(_T('asso:cpte_resultat_total_produits_evalues')), 1, 0, 'R', true);
		$this->Cell(30, 6, number_format($produits_evalues, 2, ',', ' '), 1, 0, 'R', true);

		$yc += 6;
		//Saut de ligne
		$this->Ln($this->space_v);
		$yc += $this->space_v;

		// Rectangle tout autour
		$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc - $y_orig);

		// on sauve la position du curseur dans la page
		$this->yy = $yc;
	}

}

?>
