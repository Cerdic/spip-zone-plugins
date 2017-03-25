<?php
/**
 *      @file inc_hyd/newton.class.php
 *      Classe abstraite de résolution d'une équation par la méthode de Newton
 */

/*      Copyright 2009-2012 David Dorchies <dorch@dorch.fr>
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


abstract class acNewton {

	const DBG = false; /// Debuggage

	protected $rTol;
	protected $rDx;
	private $iCpt; /// n° itération Newton
	private $iCptMax=50; /// nb max itérations
	private $rRelax=1; /// Coefficient de relaxation
	private $rFnPrec=0; /// Mémorisation du Fn précédent pour détecter le changement de signe
	private $iOscil=0; /// Nombre de changement de signe de Delta
	private $oLog;


	/**
	 * Constructeur de la classe
	 * @param $oSn Section sur laquelle on fait le calcul
	 * @param $oP Paramètres supplémentaires (Débit, précision...)
	 */
	function __construct(cParam $oP) {
		$this->rTol = $oP->rPrec;
		$this->rDx = $oP->rPrec / 10;
		$this->iCpt = 0;
	}


	/**
	 * Calcul de la fonction f(x) dont on cherche le zéro.
	 * @param $rX x
	 * @return Calcul de la fonction
	 */
	abstract protected function CalcFn($rX);

	/**
	 * Calcul de la dérivée f'(x) (peut être redéfini pour calcul analytique)
	 * @param $rX x
	 * @return Calcul de la fonction
	 */
	protected function CalcDer($x) {
		//~ spip_log('Newton:CalcDer $rX='.$x,'hydraulic.'._LOG_DEBUG);
		return ($this->CalcFn($x+$this->rDx)-$this->CalcFn($x-$this->rDx))/(2*$this->rDx);
	}

	/**
	 * Test d'égalité à une tolérance près
	 * @param $rFn x
	 * @return True si égal, False sinon
	 */
	private function FuzzyEqual($rFn) {
		return (abs($rFn) < $this->rTol);
	}

	/**
	 * Fonction récursive de calcul de la suite du Newton
	 * @param $rX x
	 * @return Solution du zéro de la fonction
	 */
	public function Newton($rX) {
		$this->iCpt++;
		$rFn=$this->CalcFn($rX);
		if(self::DBG) spip_log('Newton '.$this->iCpt.' Relax='.$this->rRelax.'- f('.$rX.') = '.$rFn,'hydraulic.'._LOG_DEBUG);
		if($this->FuzzyEqual($rFn) || $this->iCpt >= $this->iCptMax) {
			return $rX;
		}
		else {
			$rDer=$this->CalcDer($rX);
			//~ echo(' - f\' = '.$rDer);
			if($rDer!=0) {
				if($rFn < 0 xor $this->rFnPrec < 0) {
					$this->nOscil++;
					if($this->rRelax > 1) {
						// Sur une forte relaxation, au changement de signe on réinitialise
						$this->rRelax = 1;
					}
					elseif($this->nOscil>2) {
						// On est dans le cas d'une oscillation autour de la solution
						// On réduit le coefficient de relaxation
						$this->rRelax *= 0.5;
					}
				}
				$this->rFnPrec = $rFn;
				$Delta = $rFn / $rDer;
				while(abs($Delta*$this->rRelax) < $this->rTol && $rFn > 10*$this->rTol && $this->rRelax < 2^8) {
					// On augmente le coefficicient de relaxation s'il est trop petit
					$this->rRelax *= 2;
				}
				$rRelax = $this->rRelax;
				while($rX - $Delta*$rRelax <= 0 && $rRelax > 1E-4) {
					// On diminue le coeficient de relaxation si on passe en négatif
					$rRelax *= 0.5; // Mais on ne le mémorise pas pour les itérations suivantes
				}
				$rX = $rX - $Delta*$rRelax;
				$this->rDelta = $Delta;
				if($rX<0) {$rX = $this->rTol;} // Aucune valeur recherchée ne peut être négative ou nulle
				return $this->Newton($rX);
			}
			else {
				// Echec de la résolution
				return false;
			}
		}
	}

	/**
	 * Pour savoir si le Newton a convergé
	 * @return true si oui, false sinon
	 */    public function HasConverged() {
		if($this->iCpt >= $this->iCptMax) {
			return false;
		}
		else {
			return true;
		}
	}
}

?>
