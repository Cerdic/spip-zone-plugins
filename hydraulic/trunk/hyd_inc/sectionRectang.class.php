<?php
/**
 *      @file inc_hyd/sectionRectang.class.php
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
 * Calculs de la section rectangulaire
 */
class cSnRectang extends acSection {

    function __construct(&$oLog,&$oP,$rLargeurFond) {
        $this->rLargeurBerge = $rLargeurFond;
        parent::__construct($oLog,$oP);
    }

    protected function CalcP() {
        return $this->rLargeurBerge+parent::CalcP($this->rY);
    }

    protected function CalcS() {
        return parent::CalcS($this->rY);
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre
     * multiplié par la section
     * @return SYg
     */
    protected function CalcSYg() {
        return parent::CalcSYg($this->rY);
    }

    /**
     * Calcul de la dérivée de la distance du centre de gravité de la section à la surface libre
     * multiplié par la section
     * @return Dérivée de SYg par rapport à Y
     */
    protected function CalcSYgder() {
        return parent::CalcSYgder($this->rY);
    }

   /**
    * Calcul du tirant d'eau conjugué avec la formule analytique pour la section rectangulaire
    * @return tirant d'eau conjugué
    */
    protected function CalcYco() {
        return $this->rY*(sqrt(1 + 8 * pow($this->Calc('Fr'),2)) - 1) / 2;
    }

}
?>
