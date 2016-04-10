<?php
include_spip('hyd_inc/formulaire.abstract.class');

class form_cond_distri extends formulaire {

	// Tableau des caractéristiques des champs de saisie
	public $saisies = array(
		'fs_hydraulique' => array(
			'caract_hydraulique',
			array(
				'Q' => array('param_Q', 3., 'op'),
				'D' => array('param_D', 1.2, 'op'),
				'J' => array('param_J', 0.6, 'op'),
				'Lg' => array('param_Lg', 100., 'op'),
				'nu' => array('param_nu', 1E-6, 'op')
			),
			'cal'
		),
		'fs_param_calc' => array(
			'param_calcul',
			array(
				'rPrec' => array('precision',0.001,'op')
			),
			'fix'
		)
	);

	// Définition de la variable à calculer par défaut
	protected $sVarCal = 'J';

	// Définition du nombre de colonnes du formulaire
	protected $nb_col = 5;

	protected function calculer() {
		// On transforme les champs du tableau des données du formulaire en variables
		extract($this->data, EXTR_OVERWRITE|EXTR_REFS);
		$tRes = array(); // Tableau des résultats (ordonnées)
		$tAbs = array(); // Tableau des abscisses

		$K = 0.3164 * pow(4,1.75)/(5.5*9.81*pow(3.1415,1.75)); // Constante de la formule

		for($i = $min; $i <= $max; $i+= $pas){
			$tAbs[] = $i;
			switch($ValCal){
				case 'Q':
				$tRes[] = pow($J/($K*pow($nu,0.25)*$Lg/pow($D,4.75)),1/1.75);
				break;
				case 'D':
				$tRes[] = pow($J/($K*pow($nu,0.25)*pow($Q,1.75)*$Lg),1/4.75);
				break;
				case 'J':
				$tRes[] = $K*pow($nu,0.25)*pow($Q,1.75)*$Lg/pow($D,4.75);
				break;
				case 'Lg':
				$tRes[] = $J/($K*pow($nu,0.25)*pow($Q,1.75)/pow($D,4.75));
				break;
				case 'nu':
				$tRes[] = pow($J/($K*pow($Q,1.75)*$Lg/pow($D,4.75)),1/0.25);
				break;
			}
		}
		return array('abs'=>$tAbs,'res'=>$tRes);
	}
}
?>