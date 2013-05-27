<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format LaTeX
// http://fr.wikipedia.org/wiki/LaTeX
function action_export_soldescomptes_latex() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\d+)-(\w+)-(\w+)(-(\w+))?$,', $arg, $r)) {
		spip_log("action_export_soldescomptes incompris: " . $arg);
	} else {
		include_spip('inc/association_comptabilite');
		$latex = new LaTeX(_request('var'));
		$latex->EnTete();
		foreach (array('charges', 'produits', 'contributions_volontaires') as $key) {
			$latex->LesEcritures($key);
		}
		$latex->Pied();
		$latex->leFichier('tex');
	}
}

include_spip('inc/association_comptabilite');
/**
 *  Utilisation d'une classe tres tres tres simple !!!
 */
class LaTeX extends ExportComptes_TXT {

	function EnTete() {
		$this->out .= '\\documentclass[a4paper]{article}'."\n";
		$this->out .= '\\usepackage['.$GLOBALS['meta']['charset'].']{inputenc}'."\n";
		$this->out .= '\\usepackage[french]{babel}'."\n";
		$this->out .= '\\usepackage[table]{xcolor}'."\n";
		$this->out .= '%generator: Associaspip'."\n";
		$t = sql_getfetsel('intitule', 'spip_asso_exercices', "id_exercice=" . intval($this->exercice));
		$g = html_entity_decode(_T('asso:cpte_resultat_titre_general'));
		$this->out .= '\\title{'. $g .'\\\\ '. _T('Exercice') . " : $t}\n";
		$this->out .= '\\author{'. $GLOBALS['association_metas']['nom'] .'}'."\n";
		$this->out .= '\\date{\\today}'."\n";
		$this->out .= '\\begin{document}'."\n";
		$this->out .= '\\maketitle{}'."\n";
	}

	function LesEcritures($key) {
		switch ($key) {
			case 'charges' :
				$quoi = "SUM(depense) AS valeurs";
				break;
			case 'produits' :
				$quoi = "SUM(recette) AS valeurs";
				break;
			case 'contributions_volontaires' :
				$quoi = "SUM(depense) AS charge_evaluee, SUM(recette) AS produit_evalue";
				break;
		}
		$this->out .= '\\section*{'. ucfirst($key) .'}'."\n";
		$this->out .= '\\begin{tabular}{|l p{.7562\\textwidth} r|}'."\n"; // 20/210=9.52381/100 30/210=14.8571/100 (210-20-30)/100=75.61909
		$query = sql_select(
			"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee ".$this->sel, // select
			'spip_asso_comptes'.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having .$GLOBALS['association_metas']['classe_'.$key] // having
		);
		$chapitre = '';
		$i = 0;
		while ($data = sql_fetch($query)) {
			if ($key==='contributions_volontaires') {
				if ($data['charge_evaluee']>0) {
					$valeurs = $data['charge_evaluee'];
				} else {
					$valeurs = $data['produit_evalue'];
				}
			} else {
				$valeurs = $data['valeurs'];
			}
			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				$this->out .= str_replace(array('\\','&'), array('\\backslash{}','\\&'), $new_chapitre) .' & ';
				$this->out .= '\multicolumn{2}{l|}{'. str_replace(array('\\','&'), array('\\backslash{}','\\&'), comptabilite_reference_intitule($new_chapitre) ) .'}\\\\'."\n";
				$chapitre = $new_chapitre;
			}
			$this->out .= str_replace(array('\\','&'), array('\\backslash{}','\\&'), $data['code']) .' & ';
			$this->out .= str_replace(array('\\','&'), array('\\backslash{}','\\&'), $data['intitule']) .' & ';
			$this->out .= $valeurs.'\\\\'."\n";
		}
		$this->out .= '\\end{tabular}'."\n";
	}

	function Pied() {
		$this->out .= '\end{document}'."\n";
	}

}

?>