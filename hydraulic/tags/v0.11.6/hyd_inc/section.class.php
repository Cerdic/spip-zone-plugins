<?php
/**
 *      @file section.class.php
 *      Gestion des calculs au niveau des Sections
 */

/*      Copyright 2009-2012 Dorch <dorch@dorch.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

// Chargement de la classe pour la méthode de Newton
include_spip('hyd_inc/newton.class');

/**
 * Gestion des Paramètres du canal (hors section)
 */
class cParam {
	public $rKs;    /// Strickler
	public $rQ;     /// Débit
	public $rIf;    /// Pente du fond
	public $rPrec;  /// Précision de calcul et d'affichage
	public $rG=9.81;/// Constante de gravité
	public $iPrec;  /// Précision en nombre de décimales
	public $rYB;    /// Hauteur de berge

	function __construct($rKs, $rQ, $rIf, $rPrec, $rYB) {
		$this->rKs=(real) $rKs;
		$this->rQ=(real) $rQ;
		$this->rIf=(real) $rIf;
		$this->rPrec=(real) $rPrec;
		$this->rYB=(real) $rYB;
		$this->iPrec=(int)-log10($rPrec);
	}
}

/**
 * Gestion commune pour les différents types de section.
 * Comprend les formules pour la section rectangulaire pour gérer les débordements
 */
abstract class acSection {
	const DBG = false; /// Pour loguer les messages de debug de cette classe et ses filles

	public $rY=0;          /// Tirant d'eau
	public $rHautCritique;  /// Tirant d'eau critique
	public $rHautNormale;   /// Tirant d'eau normal
	public $oP;   /// Paramètres du système canal (classe oParam)
	protected $oLog; /// Pour l'affichage du journal de calcul
	public $rLargeurBerge; /// largeur au débordement
	protected $bSnFermee = false; /// true si la section est fermée (fente de Preissmann)
	/**
	 * Tableau contenant les données dépendantes du tirant d'eau $this->rY.
	 *
	 * Les clés du tableau peuvent être :
	 * - S : la surface hydraulique
	 * - P : le périmètre hydraulique
	 * - R : le rayon hydraulique
	 * - B : la largeur au miroir
	 * - J : la perte de charge
	 * - Fr : le nombre de Froude
	 * - dP : la dérivée de P par rapport Y
	 * - dR : la dérivée de R par rapport Y
	 * - dB : la dérivée de B par rapport Y
	 */
	private $arCalc = array();
	protected $arCalcGeo = array(); /// Données ne dépendant pas de la cote de l'eau

	private $rY_old ; /// Mémorisation du tirant d'eau pour calcul intermédiaire
	private $arCalc_old = array(); /// Mémorisation des données hydrauliques pour calcul intermédiaire
	/**
	 * Nombre de points nécessaires pour le dessin de la section (hors point de berge)
	 * Valeur de 1 par défaut pour les sections rectangulaires et trapézoïdales
	 */
	protected $nbDessinPoints=1;

	/**
	 * Construction de la classe.
	 * Calcul des hauteurs normale et critique
	 */
	public function __construct(&$oLog,&$oP) {
		$this->oP = &$oP;
		$this->oLog = &$oLog;
		$this->CalcGeo('B');
		//~ if(self::DBG) spip_log($this,'hydraulic.'._LOG_DEBUG);
	}


	/**
	 * Efface toutes les données calculées pour forcer le recalcul
	 * @param $bGeo Réinitialise les données de géométrie aussi
	 */
	public function Reset($bGeo=true) {
		$this->arCalc = array();
		if($bGeo) {
			$this->arCalcGeo = array();
		}
	}


	/**
	 * Mémorise les données hydraulique en cours ou les restitue
	 * @param bMem true pour mémorisation, false pour restitution
	 */
	public function Swap($bMem) {
		if($bMem) {
			$this->rY_old = $this->rY;
			$this->arCalc_old = $this->arCalc;
		}
		else {
			$this->rY = $this->rY_old;
			$this->arCalc = $this->arCalc_old;
			$this->arCalc_old = array();
		}
	}


	/**
	 * Calcul des données à la section
	 * @param $sDonnee Clé de la donnée à calculer (voir $this->$arCalc)
	 * @param $bRecalc Pour forcer le recalcul de la donnée
	 * @return la donnée calculée
	 */
	public function Calc($sDonnee, $rY = false) {
		if($rY!==false && $rY!=$this->rY) {
			if(self::DBG) spip_log('Calc('.$sDonnee.') rY='.$rY.' => $this->rY','hydraulic.'._LOG_DEBUG);
			$this->rY = $rY;
			// On efface toutes les données dépendantes de Y pour forcer le calcul
			$this->Reset(false);
		}
		if(!isset($this->arCalc[$sDonnee]) | (isset($this->arCalc[$sDonnee]) && !$this->arCalc[$sDonnee])) {
			// La donnée a besoin d'être calculée
			switch($sDonnee) {
				case 'I-J' : // Variation linéaire de l'énergie spécifique (I-J) en m/m
				$this->arCalc[$sDonnee] = $this->oP->rIf-$this->Calc('J');
				break;
				default :
				$Methode = 'Calc_'.$sDonnee;
				$this->arCalc[$sDonnee] = $this->$Methode();
			}
			//if(self::DBG) spip_log($this->arCalc,'hydraulic.'._LOG_DEBUG);
		}
		if(self::DBG) spip_log('Calc('.$sDonnee.')='.$this->arCalc[$sDonnee],'hydraulic.'._LOG_DEBUG);
		return $this->arCalc[$sDonnee];
	}


	/**
	 * Calcul des données uniquement dépendantes de la géométrie de la section
	 * @param $sDonnee Clé de la donnée à calculer (voir $this->$arCalcGeo)
	 * @param $rY Hauteur d'eau
	 * @return la donnée calculée
	 */
	public function CalcGeo($sDonnee) {
		if($sDonnee != 'B' && !isset($this->arCalcGeo['B'])) {
			// Si la largeur aux berges n'a pas encore été calculée, on commence par ça
			$this->CalcGeo('B');
		}
		if(!isset($this->arCalcGeo[$sDonnee])) {
			// La donnée a besoin d'être calculée
			//if(self::DBG) spip_log('CalcGeo('.$sDonnee.') rY='.$this->oP->rYB,'hydraulic.'._LOG_DEBUG);
			$this->Swap(true); // On mémorise les données hydrauliques en cours
			$this->Reset(false);
			$this->rY = $this->oP->rYB;
			switch($sDonnee) {
				case 'B' : // Largeur aux berges

				$this->arCalcGeo[$sDonnee] = $this->Calc_B();
				if($this->arCalcGeo[$sDonnee] < $this->oP->rYB / 100) {
					// Section fermée
					$this->bSnFermee = true;
					// On propose une fente de Preissmann égale à 1/100 de la hauteur des berges
					$this->arCalcGeo[$sDonnee] = $this->oP->rYB / 100;
				}
				$this->rLargeurBerge = $this->arCalcGeo[$sDonnee];
				break;
				default :
				$Methode = 'Calc_'.$sDonnee;
				$this->arCalcGeo[$sDonnee] = $this->$Methode();
			}
			//~ if(self::DBG) spip_log('CalcGeo('.$sDonnee.',rY='.$this->oP->rYB.')='.$this->arCalcGeo[$sDonnee],'hydraulic.'._LOG_DEBUG);
			$this->Swap(false); // On restitue les données hydrauliques en cours
		}
		return $this->arCalcGeo[$sDonnee];
	}


	/**
	 * Calcul de la surface hydraulique.
	 * @return La surface hydraulique
	 */
	protected function Calc_S($rY) {
		//~ if(self::DBG) spip_log('section->CalcS(rY='.$rY.')='.($rY*$this->rLargeurBerge),'hydraulic.'._LOG_DEBUG);
		return $rY*$this->rLargeurBerge;
	}


	/**
	 * Calcul de la dérivée surface hydraulique.
	 * @return La surface hydraulique
	 */
	protected function Calc_dS() {
		return $this->rLargeurBerge;
	}


	/**
	 * Calcul du périmètre hydraulique.
	 * @return Le périmètre hydraulique
	 */
	protected function Calc_P($rY=0) {
		//~ if(self::DBG) spip_log('section->CalcP(rY='.$rY.')='.(2*$rY),'hydraulic.'._LOG_DEBUG);
		return 2*$rY;
	}


	/**
	 * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
	 * @return dP
	 */
	protected function Calc_dP() {
		return 2;
	}


	/**
	 * Calcul du rayon hydraulique.
	 * @return Le rayon hydraulique
	 */
	protected function Calc_R() {
		if($this->Calc('P')!=0) {
			return $this->Calc('S')/$this->Calc('P');
		}
		else {
			return INF;
		}
	}


	/**
	 * Calcul de dérivée du rayon hydraulique par rapport au tirant d'eau.
	 * @return dR
	 */
	protected function Calc_dR() {
		if($this->Calc('P')!=0) {
			return ($this->Calc('B')*$this->Calc('P')-$this->Calc('S')*$this->Calc('dP'))/pow($this->Calc('P'),2);
		}
		else {
			return 0;
		}
	}


	/**
	 * Calcul de la largeur au miroir.
	 * @return La largeur au miroir
	 */
	protected function Calc_B() {
		return $this->rLargeurBerge;
	}


	/**
	 * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
	 * @return dB
	 */
	protected function Calc_dB() {
		return 0;
	}


	/**
	 * Calcul de la perte de charge par la formule de Manning-Strickler.
	 * @return La perte de charge
	 */
	private function Calc_J() {
		if($this->Calc('R')!=0) {
			return pow($this->Calc('V')/$this->oP->rKs,2)/pow($this->Calc('R'),4/3);
		}
		else {
			return INF;
		}
	}


	/**
	 * Calcul du nombre de Froude.
	 * @return Le nombre de Froude
	 */
	private function Calc_Fr() {
		if($this->Calc('S')!=0) {
			return $this->oP->rQ/$this->Calc('S')*sqrt($this->Calc('B')/$this->Calc('S')/$this->oP->rG);
		}
		else {
			return INF;
		}
	}
	/**
	 * Calcul de la vitesse moyenne.
	 * @return Vitesse moyenne
	 */
	private function Calc_V() {
		if($this->Calc('S')!=0) {
			return $this->oP->rQ/$this->Calc('S');
		}
		else {
			return INF;
		}
	}


	/**
	 * Calcul de la charge spécifique.
	 * @return Charge spécifique
	 */
	private function Calc_Hs() {
		return $this->rY+pow($this->Calc('V'),2)/(2*$this->oP->rG);
	}


	/**
	 * Calcul de la charge spécifique critique.
	 * @return Charge spécifique critique
	 */
	private function Calc_Hsc() {
		$this->Swap(true); // On mémorise les données hydrauliques en cours
		// On calcule la charge avec la hauteur critique
		$rHsc = $this->Calc('Hs',$this->CalcGeo('Yc'));
		// On restitue les données initiales
		$this->Swap(false);
		return $rHsc;
	}


	/**
	 * Calcul du tirant d'eau critique.
	 * @return tirant d'eau critique
	 */
	private function Calc_Yc() {
		$oHautCritique = new cHautCritique($this, $this->oP);
		if(!$this->rHautCritique = $oHautCritique->Newton($this->oP->rYB) or !$oHautCritique->HasConverged()) {
			$this->oLog->Add(_T('hydraulic:h_critique').' : '._T('hydraulic:newton_non_convergence'),true);
		}
		return $this->rHautCritique;
	}

	/**
	 * Calcul du tirant d'eau normal.
	 * @return tirant d'eau normal
	 */
	private function Calc_Yn() {
		if($this->oP->rIf <= 0) {
			$this->rHautNormale = false;
			$this->oLog->Add(_T('hydraulic:h_normale_pente_neg_nul'),true);
		} else {
			$oHautNormale= new cHautNormale($this, $this->oP);
			if(!$this->rHautNormale = $oHautNormale->Newton($this->CalcGeo('Yc')) or !$oHautNormale->HasConverged()) {
				$this->oLog->Add(_T('hydraulic:h_normale').' : '._T('hydraulic:newton_non_convergence'),true);
			}
		}
		return $this->rHautNormale;
	}


	/**
	 * Calcul du tirant d'eau fluvial.
	 * @return tirant d'eau fluvial
	 */
	private function Calc_Yf() {
		if($this->rY > $this->CalcGeo('Yc')) {
			return $this->rY;
		}
		else {
			$oHautCorrespondante= new cHautCorrespondante($this, $this->oP);
			return $oHautCorrespondante->Newton($this->Calc('Yc')*2);
		}
	}


	/**
	 * Calcul du tirant d'eau torrentiel.
	 * @return tirant d'eau torrentiel
	 */
	private function Calc_Yt() {
		if($this->rY < $this->CalcGeo('Yc')) {
			return $this->rY;
		}
		else {
			$oHautCorrespondante= new cHautCorrespondante($this, $this->oP);
			return $oHautCorrespondante->Newton($this->CalcGeo('Yc')/2);
		}
	}


	/**
	 * Calcul du tirant d'eau conjugué.
	 * @return tirant d'eau conjugué
	 */
	protected function Calc_Yco() {
		// Mémorisation de Y avant l'appel au Newton
		$this->Swap(true);
		// Intanciation du Newton
		$oHautConj= new cHautConjuguee($this, $this->oP);
		// Choisir une valeur initiale du bon côté de la courbe
		if($this->Calc('Fr') < 1) {
			// Ecoulement fluvial, on cherche la conjuguée à partir du tirant d'eau torrentiel
			$rY0 = $this->Calc('Yt');
		}
		else {
			// Ecoulement torrentiel, on cherche la conjuguée à partir du tirant d'eau fluvial
			$rY0 = $this->Calc('Yf');
		}
		if(!$Yco = $oHautConj->Newton($rY0) or !$oHautConj->HasConverged()) {
			$this->oLog->Add(_T('hydraulic:h_conjuguee').' : '._T('hydraulic:newton_non_convergence'),true);
		}
		$this->Swap(false);
		return $Yco;
	}


	/**
	 * Calcul de la contrainte de cisaillement.
	 * @return contrainte de cisaillement
	 */
	private function Calc_Tau0() {
		return 1000 * $this->oP->rG * $this->Calc('R') * $this->Calc('J');
	}

	/**
	 * Calcul de la distance du centre de gravité de la section à la surface libre
	 * multiplié par la surface hydraulique
	 * @return S x Yg
	 */
	protected function Calc_SYg($rY) {
		return pow($rY,2) * $this->rLargeurBerge / 2;
	}


	/**
	 * Calcul de la dérivée distance du centre de gravité de la section à la surface libre
	 * multiplié par la surface hydraulique
	 * @return S x Yg
	 */
	protected function Calc_dSYg($rY) {
		return $rY * $this->rLargeurBerge;
	}


	/**
	 * Calcul de l'impulsion hydraulique.
	 * @return Impulsion hydraulique
	 */
	protected function Calc_Imp() {
		return 1000 * ($this->oP->rQ * $this->Calc('V') + $this->oP->rG * $this->Calc('SYg'));
	}


	/**
	 * Calcul de l'angle Alpha entre la surface libre et le fond pour les sections circulaires.
	 * @return Angle Alpha pour une section circulaire, 0 sinon.
	 */
	protected function Calc_Alpha(){
		return 0;
	}


	/**
	 * Calcul de la dérivée de l'angle Alpha entre la surface libre et le fond pour les sections circulaires.
	 * @return Dérivée de l'angle Alpha pour une section circulaire, 0 sinon.
	 */
	protected function Calc_dAlpha(){
		return 0;
	}


	/**
	 * Fournit les coordonnées des points d'une demi section pour le dessin
	 * @return tableau de couples de coordonnées (x,y)
	 */
	public function DessinCoordonnees() {
		$rPas = $this->oP->rYB / $this->nbDessinPoints;
		$tPoints = array();
		$this->Swap(true); // On mémorise les données hydrauliques en cours
		for($rY=0;$rY<$this->oP->rYB+$rPas/2;$rY+=$rPas) {
			//~ if(self::DBG) spip_log('DessinCoordonnees rY='.$rY,'hydraulic.'._LOG_DEBUG);
			$tPoints['x'][] = $this->Calc('B',$rY)/2;
			$tPoints['y'][] = $rY;
		}
		// On restitue les données initiales
		$this->Swap(false);
		return $tPoints;
	}
}


/**
 * Calcul de la hauteur critique
 */
class cHautCritique extends acNewton {
	private $oSn;
	private $oP;

	/**
	 * Constructeur de la classe
	 * @param $oSn Section sur laquelle on fait le calcul
	 * @param $oP Paramètres supplémentaires (Débit, précision...)
	 */
	function __construct($oSn,cParam $oP) {
		$this->oSn = clone $oSn;
		$this->oP = $oP;
		parent::__construct($oP);
	}

	/**
	 * Calcul de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcFn($rX) {
		// Calcul de la fonction
		if($this->oSn->Calc('S',$rX)!=0) {
			$rFn = (pow($this->oP->rQ,2)*$this->oSn->Calc('B',$rX)/pow($this->oSn->Calc('S',$rX),3)/$this->oP->rG-1);
		}
		else {
			$rFn = INF;
		}
		//~ if(self::DBG) spip_log('cHautCritique:CalcFn('.$rX.')='.$rFn,'hydraulic.'._LOG_DEBUG);
		return $rFn;
	}

	/**
	 * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcDer($rX) {
		if($this->oSn->Calc('S')!=0) {
			// L'initialisation à partir de $rX a été faite lors de l'appel à CalcFn
			$rDer = ($this->oSn->Calc('dB')*$this->oSn->Calc('S')-3*$this->oSn->Calc('B')*$this->oSn->Calc('B'));
			$rDer = pow($this->oP->rQ,2)/$this->oP->rG * $rDer / pow($this->oSn->Calc('S'),4);
		}
		else {
			$rDer = INF;
		}

		//if(self::DBG) spip_log('cHautCritique:CalcDer('.$rX.')='.$rDer,'hydraulic.'._LOG_DEBUG);
		return $rDer;
	}
}



/**
 * Calcul de la hauteur normale
 */
class cHautNormale extends acNewton {
	private $oSn;
	private $rQ;
	private $rKs;
	private $rIf;

	/**
	 * Constructeur de la classe
	 * @param $oSn Section sur laquelle on fait le calcul
	 * @param $oP Paramètres supplémentaires (Débit, précision...)
	 */
	function __construct($oSn, cParam $oP) {
		$this->oSn= clone $oSn;
		$this->rQ=$oP->rQ;
		$this->rKs=$oP->rKs;
		$this->rIf=$oP->rIf;
		$this->rG=$oP->rG;
		parent::__construct($oP);
	}

	/**
	 * Calcul de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcFn($rX) {
		// Calcul de la fonction
		$rFn = ($this->rQ-$this->rKs*pow($this->oSn->Calc('R',$rX),2/3)*$this->oSn->Calc('S',$rX)*sqrt($this->rIf));
		//if(self::DBG) spip_log('cHautNormale:CalcFn('.$rX.')='.$rFn,'hydraulic.'._LOG_DEBUG);
		return $rFn;
	}

	/**
	 * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcDer($rX) {
		// L'initialisation a été faite lors de l'appel à CalcFn
		$rDer = 2/3*$this->oSn->Calc('dR')*pow($this->oSn->Calc('R'),-1/3)*$this->oSn->Calc('S');
		$rDer += pow($this->oSn->Calc('R'),2/3)*$this->oSn->Calc('B');
		$rDer *= -$this->rKs * sqrt($this->rIf);
		//if(self::DBG) spip_log('cHautNormale:CalcDer('.$rX.')='.$rDer,'hydraulic.'._LOG_DEBUG);
		return $rDer;
	}
}


/**
 * Calcul de la hauteur correspondante (charge égale)
 */
class cHautCorrespondante extends acNewton {
	private $rY; // Tirant d'eau connu
	private $rS2; // 1/S^2 associé au tirant d'eau connu
	private $oSn; // Section contenant les données de la section avec la hauteur à calculer
	private $rQ2G; // Constante de gravité

	/**
	 * Constructeur de la classe
	 * @param $oSn Section sur laquelle on fait le calcul
	 * @param $oP Paramètres supplémentaires (Débit, précision...)
	 */
	function __construct(acSection $oSn, cParam $oP) {
		parent::__construct($oP);
		$this->rY = $oSn->rY;
		$this->rS2 = pow($oSn->Calc('S'),-2);
		$this->oSn = clone $oSn;
		$this->rQ2G = pow($oP->rQ,2)/(2*$oP->rG);
	}

	/**
	 * Calcul de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcFn($rX) {

		// Calcul de la fonction
		$rFn = $this->rY - $rX + ($this->rS2-pow($this->oSn->Calc('S',$rX),-2))*$this->rQ2G;
		//~ if(self::DBG) spip_log('cHautCorrespondante:CalcFn('.$rX.')='.$rFn,'hydraulic.'._LOG_DEBUG);
		return $rFn;
	}

	/**
	 * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcDer($rX) {
		// L'initialisation a été faite lors de l'appel à CalcFn
		if($this->oSn->Calc('S')!=0) {
			$rDer = -1 + 2 * $this->rQ2G * $this->oSn->Calc('B') / pow($this->oSn->Calc('S'),3);
		}
		else {
			$rDer = INF;
		}
		//~ if(self::DBG) spip_log('cHautCorrespondante:CalcDer('.$rX.')='.$rDer,'hydraulic.'._LOG_DEBUG);
		return $rDer;
	}

}


/**
 * Calcul de la hauteur conjuguée (Impulsion égale)
 */
class cHautConjuguee extends acNewton {
	/** Tirant d'eau connu */
	private $rY;
	/** 1/S^2 associé au tirant d'eau connu */
	private $rS2;
	/** Section contenant les données de la section avec la hauteur à calculer */
	private $oSn;
	/** Constante de gravité */
	private $rG;
	/** Carré du débit */
	private $rQ2;
	/** Surface hydraulique associée au tirant d'eau connu */
	private $rS;
	/** SYg associée au tirant d'eau connu */
	private $rSYg;

	/**
	 * Constructeur de la classe
	 * @param $oSn Section sur laquelle on fait le calcul
	 * @param $oP Paramètres supplémentaires (Débit, précision...)
	 */
	function __construct(acSection $oSn, cParam $oP) {
		parent::__construct($oP);
		$this->rY = $oSn->rY;
		$this->rQ2 = pow($oP->rQ,2);
		$this->oSn = clone $oSn;
		$this->rG = $oP->rG;
		$this->rS = $oSn->Calc('S');
		$this->rSYg = $oSn->Calc('SYg');
	}

	/**
	 * Calcul de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcFn($rX) {
		// Réinitialisation des paramètres hydrauliques de oSn avec l'appel $this->oSn->Calc('S',$rX)
		if($this->rS > 0 && $this->oSn->Calc('S',$rX) > 0) {
			$rFn = $this->rQ2 * (1 / $this->rS - 1 / $this->oSn->Calc('S'));
			$rFn += $this->rG * ($this->rSYg - $this->oSn->Calc('SYg'));
		}
		else {
			$rFn = -INF;
		}
		if(self::DBG) spip_log('cHautConjuguee:CalcFn('.$rX.')='.$rFn,'hydraulic.'._LOG_DEBUG);
		return $rFn;
	}

	/**
	 * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
	 * @param $rX Variable dont dépend la fonction
	 */
	protected function CalcDer($rX) {
		// L'initialisation a été faite lors de l'appel à CalcFn
		if($this->rS > 0 && $this->oSn->Calc('S') > 0) {
			$rDer = $this->rQ2 * $this->oSn->Calc('dS') * pow($this->oSn->Calc('S'),-2);
			$rDer += - $this->rG * $this->oSn->Calc('dSYg',$rX);
		}
		else {
			$rDer = -INF;
		}
		if(self::DBG) spip_log('cHautConjuguee:CalcDer('.$rX.')='.$rDer,'hydraulic.'._LOG_DEBUG);
		return $rDer;
	}

}

?>
