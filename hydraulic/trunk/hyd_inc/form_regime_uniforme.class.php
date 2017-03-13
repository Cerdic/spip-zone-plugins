<?php
include_spip('hyd_inc/form_section.abstract.class');

class form_regime_uniforme extends form_section {

	/// Définition du nombre de colonnes du formulaire
	protected $nb_col = 5;

	/// Définition de la variable à calculer par défaut
	protected $sVarCal = 'rQ';

	/*
	* Initialisation du tableau de description du formulaire
	*/
	function __construct() {
		$this->saisies['c_hyd'] = array(
			'caract_hydraulique',
			array(
				'rQ'       =>array('debit', 1.2, 'op'),
				'rY'       =>array('tirant_eau', 0.8, 'op')
			),
			'cal'
		);
		$this->saisies['param_calcul'] = array(
			'param_calcul',
			array(
				'rPrec' => array('precision',0.001,'fop')
			),
			'fix'
		);
		parent::__construct();
		// On passe toutes les variables de section en cal
		foreach($this->saisies as $cle=>&$fs) {
			if(substr($cle,0,1)=="F") {
				$fs[2] = 'cal';
			}
		}
		$this->saisies['c_bief'][2] = 'cal';
	}

	/*
	* Variables supplémentaires passées au formulaire
	*/
	public function charger() {
		$valeurs = parent::charger();
		$valeurs['choix_section'] = 'FT';
		return $valeurs;
	}

	/**
	* Calcul du débit en régime uniforme.
	* @return Débit en régime uniforme
	*/
	public function Calc_Qn() {
		$this->oSn->Reset(true);
		if($this->oP->rIf <= 0) {
			$Qn = false;
			$this->oLog->Add(_T('hydraulic:h_normale_pente_neg_nul'),true);
		} else {
			$Qn = $this->oP->rKs*pow($this->oSn->Calc('R',$this->oSn->rY),2/3)*$this->oSn->Calc('S',$this->oSn->rY)*sqrt($this->oP->rIf);
			spip_log('Calc_Qn('.$this->VarCal.')='.$Qn,'hydraulic',_LOG_DEBUG);
		}
		return $Qn;
	}

	protected function calculer() {
		$this->creer_section_param();

		// On transforme les champs du tableau des données du formulaire en variables
		extract($this->data, EXTR_OVERWRITE|EXTR_REFS);

		if(isset($ValVar) && $ValVar != ''){
			// Pointage de la variable qui varie sur le bon attribut
			if($ValVar == 'rY' or in_array($ValVar, $this->get_champs_section($choix_section))){
				$this->oSn->{$ValVar} = &$i;
			}
			else{
				$this->oP->{$ValVar} = &$i;
			}
		}

		if(!in_array($ValCal,array('rY','rQ'))) {
			// Le calcul se fera par dichotomie
			include_spip('hyd_inc/dichotomie.class');
			$oDicho = new cDichotomie($this->oLog,$this,'Calc_Qn');
			// Pointage de la variable à calculer sur le bon attribut
			spip_log($ValCal,'hydraulic',_LOG_DEBUG);
			if(in_array($ValCal, $this->get_champs_section($choix_section))){
				$this->VarCal = &$this->oSn->{$ValCal};
			}
			else{
				$this->VarCal = &$this->oP->{$ValCal};
			}
		}

		$tRes = array(); // Tableau des résultats (ordonnées)
		$tAbs = array(); // Tableau des abscisses
		for($i = $min; $i < $max; $i+= $pas){
			spip_log("min=$min max=$max i=$i",'hydraulic',_LOG_DEBUG);
			$tAbs[] = $i;
			switch($ValCal) {
				case 'rY':
                $this->oSn->Reset();
				$tRes[] = $this->oSn->Calc('Yn');
				break;
				case 'rQ':
				$tRes[] = $this->Calc_Qn();
				break;
				default :
				if(end($tRes)!==false) {
					// Solution initiale = dernière solution trouvée
					$rInit = end($tRes);
				} else {
					// Solution initiale = Valeur saisie pour la variable à calculer
					$rInit = $$ValCal;
				}
				list($tRes[],$flag) = $oDicho->calculer($rQ,$rPrec,$rInit);
			}
		}
		return array('abs'=>$tAbs,'res'=>$tRes);
	}
}
?>
