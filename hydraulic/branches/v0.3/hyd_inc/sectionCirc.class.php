<?php
/**
 *      @file inc_hyd/sectionCirc.class.php
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
 * Calculs de la section circulaire
 */
class cSnCirc extends acSection {
    protected $rD;      /// Diamètre du cercle
    private $rAlpha;    /// Angle de la surface libre par rapport au fond

    function __construct($oP,$rD) {
        $this->rD=(real) $rD;
        parent::__construct($oP);
    }

    /**
     * Calcul de l'angle Alpha de la surface libre par rapport au fond.
     * @return Alpha
     */
    protected function CalcAlpha() {
        if($this->rY <= 0) {
            spip_log('CalcAlpha('.$this->rY.')=0','hydraulic');
            return 0;
        }
        elseif($this->rY > $this->rD) {
            spip_log('CalcAlpha('.$this->rY.')='.pi(),'hydraulic');
            return pi();
        }
        else {
            $alpha = acos(1.-$this->rY/($this->rD/2.));
            if($alpha > pi()) {
                spip_log('CalcAlpha('.$this->rY.')='.pi(),'hydraulic');
                return pi();
            }
            else {
                spip_log('CalcAlpha('.$this->rY.')='.$alpha,'hydraulic');
                return $alpha;
            }
        }
    }

    /**
     * Calcul de dérivée de l'angle Alpha de la surface libre par rapport au fond.
     * @return dAlpha
     */
    protected function CalcAlphaDer() {
        if($this->rY <= 0 or $this->rY > $this->rD) {
            spip_log('CalcAlphaDer('.$this->rY.')=0','hydraulic');
            return 0;
        }
        else {
            spip_log('CalcAlphaDer('.$this->rY.')='.(2. / $this->rD / sqrt(1 - pow(1 - 2 * $this->rY / $this->rD,2))),'hydraulic');
            return 2. / $this->rD / sqrt(1. - pow(1. - 2. * $this->rY / $this->rD,2));
        }
    }

    /**
     * Calcul de la largeur au miroir.
     * @return B
     */
    protected function CalcB() {
        return $this->rD * sin($this->Calc('Alpha'));
    }

    /**
     * Calcul du périmètre mouillé.
     * @return B
     */
     protected function CalcP() {
        return $this->rD * $this->Calc('Alpha');
    }

    /**
     * Calcul de la surface mouillée.
     * @return S
     */
    protected function CalcS() {
        return pow($this->rD,2) / 4 * ($this->Calc('Alpha') - sin($this->Calc('Alpha')) * cos($this->Calc('Alpha')));
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        return $this->Calc('dAlpha') * $this->rD / 4 * cos(2 * $this->Calc('Alpha'));
    }

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return $this->rD * $this->Calc('dAlpha');
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return $this->rD * $this->Calc('dAlpha') * cos($this->Calc('Alpha'));
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre.
     * @return Distance du centre de gravité de la section à la surface libre
     */
    protected function CalcYg() {
        $SYg = sin($this->Calc('Alpha'))-pow(sin($this->Calc('Alpha')),3) / 3 - $this->Calc('Alpha') * cos($this->Calc('Alpha'));
        $SYg = pow($this->rD,3) / 8 * $SYg;
        return $SYg / $this->Calc('S');
    }

}
?>
