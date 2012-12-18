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
    public $rk;      /// Coefficient de forme compris entre 0 et 1
    //$LargeurBerge => La largeur des berges est déjà présente dans acSection
    protected $nbDessinPoints=50;

    function __construct(&$oLog,&$oP,$rk,$rLargeurBerge) {
        $this->rk = (real) $rk;
        $this->rLargeurBerge = (real) $rLargeurBerge;
        parent::__construct($oLog,$oP);
    }

    /**
     * Calcul de Lambda (mais on garde la routine Alpha commune avec la section circulaire)
     * @return Lambda
     */
    protected function CalcAlpha() {
        return $this->rLargeurBerge/pow($this->oP->rYB,$this->rk);
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
            return $this->Calc('Alpha')*pow($this->rY,$this->rk);
        }
    }

    /**
     * Calcul du périmètre mouillé.
     * @return B
     */
     protected function CalcP() {
        $n=100; /// Le nombre de partie pour le calcul de l'intégrale
        $rLambda2 = pow($this->Calc('Alpha'),2);
        $rP = 0; /// Le périmètre à calculer
        $rPrevious = 0;
        for($i=1;$i<=$n;$i++) {
            $rCurrent = pow($this->rY*$i/$n,$this->rk)/2;
            $rP += sqrt(pow($n,-2)+$rLambda2*pow($rCurrent-$rPrevious,2));
            $rPrevious = $rCurrent;
        }
        $rP *= 2 ;
        return $rP;
    }

    /**
     * Calcul de la surface mouillée.
     * @return S
     */
    protected function CalcS() {
        return $this->Calc('Alpha')*pow($this->rY, $this->rk+1)/($this->rk+1);
    }


    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return 2 * sqrt(1+pow($this->rk*$this->Calc('Alpha')/2,2)*pow($this->rY,2*($this->rk-1)));
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return $this->Calc('Alpha')*$this->rk*pow($this->rY,$this->rk-1);
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre
     * multiplié par la surface hydraulique
     * @return S x Yg
     */
    protected function CalcSYg() {
        return $this->Calc('Alpha')*pow($this->rY, $this->rk+2)/(($this->rk+1)*($this->rk+2));
    }
    /**
     * Calcul de la dérivée distance du centre de gravité de la section à la surface libre
     * multiplié par la surface hydraulique
     * @return S x Yg
     */
    protected function CalcSYgder() {
        $SYg = $this->Calc('dAlpha')*pow($this->rY, $this->rk+2) + $this->Calc('Alpha')*pow($this->rY, $this->rk+1)*($this->rk+2);
        return $SYg/(($this->rk+1)*($this->rk+2));
    }

}
?>
