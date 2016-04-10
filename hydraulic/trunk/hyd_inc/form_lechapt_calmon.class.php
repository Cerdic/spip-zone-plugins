<?php
include_spip('hyd_inc/formulaire.abstract.class');

class form_lechapt_calmon extends formulaire {

	// Tableau des caractéristiques des champs de saisie
	public $saisies = array(
		'fs_materiau' => array(
			'type_materiau',
			array(
				'typeMateriau' => array('choix_materiau','form_lechapt_calmont_materiau',''),
				'L' =>  array('L',1.863,'fop'),
				'M' =>  array('M',2.,'fop'),
				'N' =>  array('N',5.33,'fop')
			),
			'fix'
		),
		'fs_hydraulique' => array(
			'caract_hydraulique',
			array(
				'Q' => array('param_Q', 3., 'fvcop'),
				'D' => array('param_D', 1.2, 'fvcop'),
				'J' => array('param_J', 0.6, 'fvcop'),
				'Lg' => array('param_Lg', 100., 'fvcop')
			),
			'cal'
		),
		'fs_param_calc' => array(
			'param_calcul',
			array(
				'rPrec' => array('precision',0.001,'fop')
			),
			'fix'
		)
	);

	// Définition de la variable à calculer par défaut
	protected $sVarCal = 'Q';

	// Définition du nombre de colonnes du formulaire
	protected $nb_col = 5;


	/*
	* Tableau des données pour chaque type de tuyau. Ces valeurs sont associées
	* aux numéros des options du select (voir page lechapt_calmon.php)
	*/
	private function saisies_materiau() {
		$type_materiaux = array(

			'1'          => array(
				'L' =>1.863,
				'M' =>2,
				'N' =>5.33
			),

			'2'          => array(
				'L' =>1.601,
				'M' =>1.975,
				'N' =>5.25
			),

			'3'          => array(
				'L' =>1.40,
				'M' =>1.96,
				'N' =>5.19
			),

			'4'          => array(
				'L' =>1.16,
				'M' =>1.93,
				'N' =>5.11
			),

			'5'          => array(
				'L' =>1.1,
				'M' =>1.89,
				'N' =>5.01
			),

			'6'          => array(
				'L' =>1.049,
				'M' =>1.86,
				'N' =>4.93
			),

			'7'          => array(
				'L' =>1.01,
				'M' =>1.84,
				'N' =>4.88
			),

			'8'          => array(
				'L' =>0.916,
				'M' =>1.78,
				'N' =>4.78
			),

			'9'          => array(
				'L' =>0.971,
				'M' =>1.81,
				'N' =>4.81
			),
		);

		return $type_materiaux;

	}

	public function charger() {
		$valeurs = parent::charger();
		$valeurs = array_merge($valeurs,
			array(
				'mes_saisies_materiaux' => $this->saisies_materiau(),
				'tableau_caract' => array('caract_hydraulique'=>$this->champs_fvc),
				'typeMateriau' => 1
			)
		);
		return $valeurs;
	}

	protected function calculer() {
		// On transforme les champs du tableau des données du formulaire en variables
		extract($this->data, EXTR_OVERWRITE|EXTR_REFS);
		/*
		* Selon la variable à calculer, on gère les valeurs = à 0  et les valeurs infinies
		* et on fait le valcul correspondant.
		*/
		$tDiv0 = array('Q'=>'Lg', 'D'=>'J', 'J'=>'D', 'Lg'=>'Q');
		$Div0 = $tDiv0[$ValCal];

		if(${$Div0} == 0 && _request("choix_champs_$Div0") != "var_$Div0"){
			$tRes[] = 0;
		}
		else{
			$tRes = array(); // Tableau des résultats (ordonnées)
			$tAbs = array(); // Tableau des abscisses
			for($i = $min; $i <= $max; $i+= $pas){
				$tAbs[] = $i;
				if($i == 0 && _request("choix_champs_$Div0") == "var_$Div0"){
					$tRes[] = INF;
				}
				else{
					switch($ValCal){
						case 'Q':
						$tRes[] = pow(((($J*pow($D, $N))/$L)*(1000/$Lg)), 1/$M);
						break;
						case 'D':
						$tRes[] = pow(((($L*pow($Q, $M))/$J)*($Lg/1000)), 1/$N);
						break;
						case 'J':
						$tRes[] = (($L*pow($Q, $M))/pow($D, $N))*($Lg/1000);
						break;
						case 'Lg':
						$tRes[] = (($J*pow($D, $N))/($L*pow($Q,$M)))*1000;
					}
				}
			}
		}
		return array('abs'=>$tAbs,'res'=>$tRes);
	}
}
?>