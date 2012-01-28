<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 01/2012                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format Csv

function exec_export_compte_resultat_csv() {
	if (!autoriser('associer', 'export_compte_resultat_csv')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {
		include_spip('inc/charsets');
		include_spip('inc/association_plan_comptable');
		$var = _request('var');
		$csv = new CSV($var);
		$csv->EnTete();
		foreach (array('charges', 'produits', 'contributions_volontaires') as $key) {
			$csv->LesEcritures($key);
		}
		$csv->Enregistre('cpte_resultat.csv');
	}
}

/**
 *  Utilisation d'une classe tres tres tres simple !!!
 */
class CSV {

	var $exercice;
	var $join;
	var $sel;
	var $where;
	var $having;
	var $order;
	var $out;

	function  __construct($var) {
		$tableau = @unserialize($var);
		$this->exercice = $tableau[0];
		$this->join = $tableau[1];
		$this->sel = $tableau[2];
		$this->where = $tableau[3];
		$this->having = $tableau[4];
		$this->order = $tableau[5];
		$this->out = '';
	}

	function EnTete() {
		$this->out .= '"' . html_entity_decode(_T('asso:cpte_resultat_titre_general')) . '",';
		$this->out .= '"' . utf8_decode('Association - '. $GLOBALS['association_metas']['nom']) . '",';
		$this->out .= '"' . utf8_decode('Exercice : '. exercice_intitule($this->exercice)) . '",';
		$this->out .= "\n";
		$this->out .= '"' . utf8_decode('code') . '",';
		$this->out .= '"' . utf8_decode('intitule') . '",';
		$this->out .= '"' . utf8_decode('montant') . '"';
		$this->out .= "\n";
	}

	function LesEcritures($key) {
		switch ($key) {
			case 'charges' :
				$quoi = "sum(depense) AS valeurs";
				break;
			case 'produits' :
				$quoi = "sum(recette) AS valeurs";
				break;
			case 'contributions_volontaires' :
				$quoi = "sum(depense) AS charge_evaluee, sum(recette) AS produit_evalue";
				break;
		}
		$query = sql_select(
				"imputation, $quo, DATE_FORMAT(date, '%Y') AS annee" . $this->sel,
				'spip_asso_comptes '. $this->join,
				$this->where, $this->order, 'code ASC', '',
				$this->having . $GLOBALS['association_metas']['classe_' . $key]);
		$chapitre = '';
		$i = 0;
		while ($data = sql_fetch($query)) {
			if ($key==='contributions_volontaires') {
				$charge_evaluee = $data['charge_evaluee'];
				$produit_evalue = $data['produit_evalue'];
			}
			else {
				$valeurs = $data['valeurs'];
			}
			$new_chapitre = substr($data['code'], 0, 2);

			if ($chapitre!=$new_chapitre) {
				$this->out .= '"' . utf8_decode($new_chapitre) . '",';
				$this->out .= '"' . utf8_decode(association_plan_comptable_complet($new_chapitre)) . '",';
				$this->out .= '"' . utf8_decode("") . '"';
				$this->out .= "\n";
				$chapitre = $new_chapitre;
			}
			$this->out .= '"' . utf8_decode($data['code']) . '",';
			$this->out .= '"' . utf8_decode($data['intitule']) . '",';
			if ($key==='contributions_volontaires') {
				if ($charge_evaluee>0) {
					$this->out .= '"' . number_format($charge_evaluee, 2, ',', ' ') . '"';
				}
				else {
					$this->out .= '"' . number_format($produit_evalue, 2, ',', ' ') . '"';
				}
			}
			else {
				$this->out .= '"' . number_format($valeurs, 2, ',', ' ') . '"';
			}
			$this->out .= "\n";
		}
	}

	function Enregistre($fichier) {
		$f = fopen($fichier, 'w');
		fputs($f, $this->out);
		fclose($f);
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="' . $fichier . '"');
		readfile($fichier);
	}

}

?>