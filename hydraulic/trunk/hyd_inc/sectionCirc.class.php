<?php
/**
 *      @file inc_hyd/sectionCirc.class.php
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
 * Calculs de la section circulaire
 */
class cSnCirc extends acSection {
    public $rD;      /// Diamètre du cercle
    private $rAlpha;    /// Angle de la surface libre par rapport au fond
    protected $nbDessinPoints=50;

    function __construct(&$oLog,&$oP,$rD) {
        $this->rD=(real) $rD;
        if($oP->rYB > $rD) {$oP->rYB = $rD;} // On place la berge au sommet du cercle
        parent::__construct($oLog,$oP);
    }

    /**
     * Calcul de l'angle Alpha de la surface libre par rapport au fond.
     * @return Alpha
     */
    protected function CalcAlpha() {
        if($this->rY > $this->oP->rYB) {
            $rY = $this->oP->rYB;
        }
        else {
            $rY = $this->rY;
        }
        if($rY <= 0) {
            return 0;
        }
        elseif($rY > $this->rD) {
            return pi();
        }
        else {
            $alpha = acos(1.-$rY/($this->rD/2.));
            if($alpha > pi()) {
                return pi();
            }
            else {
                return $alpha;
            }
        }
    }

    /**
     * Calcul de dérivée de l'angle Alpha de la surface libre par rapport au fond.
     * @return dAlpha
     */
    protected function CalcAlphaDer() {
        if($this->rY <= 0 or $this->rY >= $this->rD or $this->rY > $this->oP->rYB) {
            return 0;
        }
        else {
            return 2. / $this->rD / sqrt(1. - pow(1. - 2. * $this->rY / $this->rD,2));
        }
    }

    /**
     * Calcul de la largeur au miroir.
     * @return B
     */
    protected function CalcB() {
        if($this->rY > $this->oP->rYB) {
            return parent::CalcB();
        }
        else {
            return $this->rD * sin($this->Calc('Alpha'));
        }
    }

    /**
     * Calcul du périmètre mouillé.
     * @return B
     */
     protected function CalcP() {
        if($this->rY > $this->oP->rYB and !$this->bSnFermee) {
            // On n'ajoute pas le périmètre dans le cas d'une fente de Preissmann
            return $this->CalcGeo('P') + parent::CalcP($this->rY-$this->oP->rYB);
        }
        else {
            return $this->rD * $this->Calc('Alpha');
        }
    }

    /**
     * Calcul de la surface mouillée.
     * @return S
     */
    protected function CalcS() {
        if($this->rY > $this->oP->rYB) {
            return $this->CalcGeo('S') + parent::CalcS($this->rY-$this->oP->rYB);
        }
        else {
            return pow($this->rD,2) / 4 * ($this->Calc('Alpha') - sin($this->Calc('Alpha')) * cos($this->Calc('Alpha')));
        }
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        if($this->rY > $this->oP->rYB) {
            return parent::CalcSder();
        }
        else {
            return pow($this->rD,2) / 4 * $this->Calc('dAlpha') * (1 - cos(2 * $this->Calc('Alpha')));
        }
    }

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        if($this->rY > $this->oP->rYB && !$this->bSnFermee) {
            return parent::CalcPder();
        }
        else {
            return $this->rD * $this->Calc('dAlpha');
        }
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        if($this->rY > $this->oP->rYB) {
            return parent::CalcBder();
        }
        else {
            return $this->rD * $this->Calc('dAlpha') * cos($this->Calc('Alpha'));
        }
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre
     * multiplié par la surface hydraulique
     * @return S x Yg
     */
    protected function CalcSYg() {
        $SYg = sin($this->Calc('Alpha'))-pow(sin($this->Calc('Alpha')),3) / 3 - $this->Calc('Alpha') * cos($this->Calc('Alpha'));
        $SYg = pow($this->rD,3) / 8 * $SYg;
        return $SYg;
    }

    /**
     * Calcul de la dérivée de la distance du centre de gravité de la section à la surface libre
     * multiplié par la surface hydraulique
     * @return S x Yg
     */
    protected function CalcSYgder() {
        $cos = cos($this->Calc('Alpha'));
        $sin = sin($this->Calc('Alpha'));
        $SYg = $this->Calc('dAlpha') * $cos;
        $SYg += - $this->Calc('dAlpha') * $cos * pow($sin,2);
        $SYg += - $this->Calc('dAlpha') * $cos + $this->Calc('Alpha') * $this->Calc('dAlpha') * $sin;
        $SYg = 3 *pow($this->rD,3) / 8 * $SYg;
        return $SYg;
    }

}
?>
