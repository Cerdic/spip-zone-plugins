<?php
/**
 *      @file inc_hyd/sectionPuiss.class.php
 *      Gestion des calculs au niveau des Sections
 */

/*      Copyright 2012 Dorch <dorch@dorch.fr>
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
 * Calculs de la section parabolique ou "puissance"
 */
class cSnPuiss extends acSection {
    protected $rk;      /// Coefficient de forme compris entre 0 et 1
    //$LargeurBerge => La largeur des berges est déjà présente dans acSection

    function __construct(&$oLog,&$oP,$rk,$rLargeurBerge) {
        $this->rk = (real) $rk;
        $this->rLargeurBerge = (real) $rLargeurBerge;
        parent::__construct($oLog,$oP);
    }

    /**
     * Calcul de de la variable intermédiaire u.
     * On va le stocker dans le calcul d'alpha initialement prévu pour le circulaire
     * @return u
     */
    protected function CalcAlpha() {
        if($this->rY > $this->oP->rYB) {
            $rY = $this->oP->rYB;
        }
        else {
            $rY = $this->rY;
        }
        return $rY/$this->CalcGeo('B');
    }

    /**
     * Calcul de dérivée de l'angle Alpha de la surface libre par rapport au fond.
     * @return du
     */
    protected function CalcAlphaDer() {
        return 1./$this->CalcGeo('B');
    }

    /**
     * Calcul de L* à partir de la largeur et de la hauteur des berges
     * @return L*
     */
    private function CalcLstar() {
        if(!isset($this->arCalcGeo['L*'])) {
            $this->arCalcGeo['L*'] = $this->oP->rYB * pow($this->rLargeurBerge/$this->oP->rYB,1/(1-$this->rk));
        }
        return $this->arCalcGeo['L*'];
    }

    /**
     * Calcul de la largeur au miroir.
     * @return B
     */
    protected function CalcB() {
        if($this->rY >= $this->oP->rYB) {
            return $this->rLargeurBerge;
        }
        else {
            return $this->CalcLstar()*pow($this->Calc('Alpha'),$this->rk);
        }
    }

    /**
     * Calcul du périmètre mouillé.
     * @return B
     */
     protected function CalcP() {
        $n=20; /// Le nombre de partie pour le calcul de l'intégrale
        $rP=0; /// Le périmètre à calculer
        for($i=1;$i<=$n;$i++) {
            $rP += sqrt(1+pow($this->rk,2)/4*pow((2*$i-1)/(2*$n)*$this->Calc('Alpha'),2*($this->rk-1)));
        }
        $rP *= 2 * $this->CalcLstar() / $n;
    }

    /**
     * Calcul de la surface mouillée.
     * @return S
     */
    protected function CalcS() {
        return pow($this->CalcLstar(),2)*pow($this->Calc('Alpha'), $this->rk+1)/($this->rk+1);
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        return $this->CalcLstar()*$this->Calc('dAlpha')*pow($this->Calc('Alpha'),$this->rk);
    }

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return $this->CalcLstar()*2*$this->Calc('dAlpha')*sqrt(1+pow($this->rk,2)/4*pow($this->Calc('Alpha'),2*($this->rk-1)));
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return $this->CalcLstar()*$this->rk*$this->Calc('dAlpha')*pow($this->Calc('Alpha'),$this->rk-1);
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre.
     * @return Distance du centre de gravité de la section à la surface libre
     */
    protected function CalcSYg() {
        return $SYg;
    }

}
?>
