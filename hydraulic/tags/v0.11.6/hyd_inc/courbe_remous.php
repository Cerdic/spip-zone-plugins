<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 */


/**
 * Calcul d'une courbe de remous
 */
class cCourbeRemous {

	const DBG = false; /// Pour loguer les messages de debug de cette classe

	public $oP; /// Paramètres de la section
	public $oSect; /// Section du bief
	private $oLog; /// Journal de calcul

	private $rDx;    /// Pas d'espace (positif en partant de l'aval, négatif en partant de l'amont)

	public $VarCal; /// Variable calculée Y pour la dichotomie (intégration trapèze)


	/**
	* Construction de la classe.
	* @param $oLog Journal de calcul
	* @param $objet Objet contenant la méthode de calcul du débit et la
	*      propriété VarCal pointeur vers la variable à calculer
	* @param $sFnCalculQ Nom de la méthode de calcul du débit
	*/
	public function __construct(&$oLog, &$oParam, &$oSect, $rDx) {
		$this->oLog = &$oLog;
		$this->oP = &$oParam;
		$this->oSect = &$oSect;
		$this->rDx = (real) $rDx;
	}


	/**
	 * Calcul de dy/dx
	 */
	private function Calc_dYdX($Y) {
		// L'appel à Calc('J') avec Y en paramètre réinitialise toutes les données dépendantes de la ligne d'eau
		return - ($this->oP->rIf - $this->oSect->Calc('J',$Y)) / (1 - pow($this->oSect->Calc('Fr',$Y),2));
	}


	/**
	 * Calcul du point suivant de la courbe de remous par la méthode Euler explicite.
	 * @param $rY Tirant d'eau initial
	 * @return Tirant d'eau
	 */
	private function Calc_Y_Euler($Y) {
		// L'appel à Calc('J') avec Y en paramètre réinitialise toutes les données dépendantes de la ligne d'eau
		$Y2 = $Y+ $this->rDx * $this->Calc_dYdX($Y);
		if($this->rDx > 0 xor !($Y2 < $this->oSect->rHautCritique)) {
			return false;
		} else {
			return $Y2;
		}
	}


	/**
	 * Calcul du point suivant de la courbe de remous par la méthode RK4.
	 * @param $rY Tirant d'eau initial
	 * @return Tirant d'eau
	 */
	private function Calc_Y_RK4($Y) {
		// L'appel à Calc('J') avec Y en paramètre réinitialise toutes les données dépendantes de la ligne d'eau
		$rDx = $this->rDx;
		$rk1 = $this->Calc_dYdX($Y);
		if($this->rDx > 0 xor !($Y + $rDx / 2 * $rk1 < $this->oSect->rHautCritique)) {return false;}
		$rk2 = $this->Calc_dYdX($Y + $rDx / 2 * $rk1);
		if($this->rDx > 0 xor !($Y + $rDx / 2 * $rk2 < $this->oSect->rHautCritique)) {return false;}
		$rk3 = $this->Calc_dYdX($Y + $rDx / 2 * $rk2);
		if($this->rDx > 0 xor !($Y + $rDx / 2 * $rk3 < $this->oSect->rHautCritique)) {return false;}
		$rk4 = $this->Calc_dYdX($Y + $rDx * $rk3);
		$Yout = $Y + $rDx / 6 * ($rk1 + 2 * ($rk2 + $rk3) + $rk4);
		if($this->rDx > 0 xor !($Yout < $this->oSect->rHautCritique)) {return false;}
		return $Yout;
	}


	/**
	 * Equation de l'intégration par la méthode des trapèzes
	 */
	public function Calc_Y_Trapez_Fn() {
		return $this->oSect->Calc('Hs',$this->VarCal) - $this->oSect->Calc('J',$this->VarCal) / 2 * $this->rDx;
	}


	/**
	 * Calcul du point suivant de la courbe de remous par la méthode de l'intégration par trapèze
	 * @param $rY Tirant d'eau initial
	 * @return Tirant d'eau
	 */
	 private function Calc_Y_Trapez($Y) {
		include_spip('hyd_inc/dichotomie.class');
		$this->VarCal = &$Y;
		$oDicho = new cDichotomie($this->oLog, $this, 'Calc_Y_Trapez_Fn', false);
		// Calcul de H + J * \Delta x / 2
		$Trapez_Fn = $this->oSect->Calc('Hs',$this->VarCal) + $this->oSect->Calc('J',$this->VarCal) / 2 * $this->rDx;
		// H est la charge totale. On se place dans le référentiel ou Zf de la section à calculer = 0
		$Trapez_Fn = $Trapez_Fn - $this->rDx * $this->oP->rIf;
		list($Y2, $flag) = $oDicho->calculer($Trapez_Fn, $this->oP->rPrec, $this->oSect->rHautCritique);
		if($flag < 0) {
			return false;
		} elseif($this->rDx > 0 xor !($Y2 < $this->oSect->rHautCritique)) {
			return false;
		}
		return $Y2;
	}


	/**
	 * Calcul du point suivant d'une courbe de remous
	 * @param $rY Tirant d'eau initial
	 * @return Tirant d'eau
	 */
	public function Calc_Y($rY, $sResolution) {
		$funcCalcY = 'Calc_Y_'.$sResolution;
		if(method_exists($this,$funcCalcY)) {
			return $this->$funcCalcY($rY);
		} else {
			return false;
		}
	}

	/**
	 * Calcul d'une courbe de remous en fluvia ou torrentiel
	 * @param $rYCL Condition limite amont (torrentiel) ou aval (fluvial)
	 * @param $rLong Longueur du bief à calculer
	 * @param $sResolution Méthode numérique Euler, RK4 ou Trapez
	 */
	function calcul($rYCL, $rLong, $sResolution) {
		$trY = array();

		if($this->rDx > 0) {
			// Calcul depuis l'aval
			$xDeb = $rLong;
			$xFin = 0;
		}
		else {
			// Calcul depuis l'amont
			$xDeb = 0;
			$xFin = $rLong;
		}
		$dx = - $this->rDx;
		spip_log($this,'hydraulic',_LOG_DEBUG);

		$trY[sprintf('%1.'.round($this->oP->iPrec).'f',$xDeb)] = (real)$rYCL;

		// Boucle de calcul de la courbe de remous
		for($x = $xDeb + $dx; ($dx > 0 && $x <= $xFin) || ($dx < 0 && $x >= $xFin); $x += $dx) {
			$rY = (real)$this->Calc_Y(end($trY), $sResolution);
			if($rY) {
				if(end($trY) > $this->oSect->rHautNormale xor $rY > $this->oSect->rHautNormale) {
					$this->oLog->Add(_T('hydraulic:pente_forte').' '.$x. ' m ('._T('hydraulic:reduire_pas').')',true);
				}
				$trY[sprintf('%1.'.round($this->oP->iPrec).'f',$x)] = $rY;
			} else {
				$this->oLog->Add(_T('hydraulic:arret_calcul').' '.$x. ' m');
				break;
			}
		}
		return $trY;
	}
}
?>
