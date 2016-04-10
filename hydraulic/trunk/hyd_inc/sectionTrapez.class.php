<?php
/**
 *      @file inc_hyd/sectionTrapez.class.php
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

// Chargement de la classe abstraite acSection et ses classes associées
include_spip('hyd_inc/section.class');

/**
 * Calculs de la section trapézoïdale
 */
class cSnTrapez extends acSection {
	public $rLargeurFond;    /// Largeur au fond
	public $rFruit;          /// Fruit des berges


	function __construct(&$oLog,&$oP,$rLargeurFond, $rFruit) {
		$this->rLargeurFond=(real) $rLargeurFond;
		$this->rFruit=(real) $rFruit;
		parent::__construct($oLog,$oP);
	}

	protected function Calc_B($bBerge=false) {
		if(!$bBerge && $this->rY > $this->oP->rYB) {
			return $this->rLargeurBerge;
		}
		else {
			return $this->rLargeurFond+2*$this->rFruit*$this->rY;
		}
	}

	/**
	 * Calcul du périmètre mouillé
	 * @param $rY Uniquement présent car la méthode parent à cet argument
	 * @return Périmètre mouillé (m)
	 */
	protected function Calc_P($rY=0) {
		if($this->rY > $this->oP->rYB) {
			$P = $this->CalcGeo('P') + parent::Calc_P($this->rY-$this->oP->rYB);
		}
		else {
			$P = $this->rLargeurFond+2*sqrt(1+pow($this->rFruit,2))*$this->rY;
		}
		//~ spip_log('Trapez->CalcP(rY='.$this->rY.')='.$P,'hydraulic.'._LOG_DEBUG);
		return $P;
	}


	/**
	 * Calcul de la surface mouillée
	 * @param $rY Uniquement présent car la méthode parent à cet argument
	 * @return Surface mouillée (m2)
	 */
	protected function Calc_S($rY=0) {
		if($this->rY > $this->oP->rYB) {
			$S = $this->CalcGeo('S') + parent::Calc_S($this->rY-$this->oP->rYB);
		}
		else {
			$S = $this->rY*($this->rLargeurFond+$this->rFruit*$this->rY);
		}
		//~ spip_log('Trapez->CalcS(rY='.$this->rY.')='.$S,'hydraulic.'._LOG_DEBUG);
		return $S;
	}

	/**
	 * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
	 * @return dS
	 */
	protected function Calc_dS() {
		if($this->rY > $this->oP->rYB) {
			return parent::Calc_dS();
		}
		else {
			return $this->rLargeurFond + 2*$this->rFruit*$this->rY;
		}
	}

	/**
	 * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
	 * @return dP
	 */
	protected function Calc_dP() {
		if($this->rY > $this->oP->rYB) {
			return parent::Calc_dP();
		}
		else {
			return 2*sqrt(1+$this->rFruit*$this->rFruit);
		}
	}

	/**
	 * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
	 * @return dB
	 */
	protected function Calc_dB() {
		if($this->rY > $this->oP->rYB) {
			return parent::Calc_dB();
		}
		else {
			return 2*$this->rLargeurFond*$this->rFruit;
		}
	}

	/**
	 * Calcul de la distance du centre de gravité de la section à la surface libre
	 * multiplié par la surface hydraulique
	 * @param $rY Uniquement présent car la méthode parent à cet argument
	 * @return S x Yg
	 */
	protected function Calc_SYg($rY=0) {
		return ($this->rLargeurFond / 2 + $this->rFruit * $this->rY / 3) * pow($this->rY,2);
	}

	/**
	 * Calcul de la dérivée de la distance du centre de gravité de la section à la surface libre
	 * multiplié par la surface hydraulique
	 * @param $rY Uniquement présent car la méthode parent à cet argument
	 * @return S x Yg
	 */
	protected function Calc_dSYg($rY=0) {
		$SYg = $this->rFruit / 3 * pow($this->rY,2);
		$SYg += ($this->rLargeurFond / 2 + $this->rFruit * $this->rY / 3) * 2 * $this->rY;
		return $SYg;
	}

}
?>