<?php
/**
 *      @file class.section.php
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
    public $rYCL;   /// Condition limite en cote à l'amont ou à l'aval
    public $rKs;    /// Strickler
    public $rQ;     /// Débit
    public $rLong;  /// Longueur du bief
    public $rIf;    /// Pente du fond
    public $rDx;    /// Pas d'espace (positif en partant de l'aval, négatif en partant de l'amont)
    public $rPrec;  /// Précision de calcul et d'affichage
    public $rG=9.81;/// Constante de gravité

    function __construct($rYCL,$rKs, $rQ, $rLong, $rIf, $rDx, $rPrec) {
        $this->rYCL=(real) $rYCL;
        $this->rKs=(real) $rKs;
        $this->rQ=(real) $rQ;
        $this->rLong=(real) $rLong;
        $this->rIf=(real) $rIf;
        $this->rDx=(real) $rDx;
        $this->rPrec=(real) $rPrec;
    }
}

/**
 * Gestion commune pour les différents types de section
 */
abstract class acSection {
    //~ public $rS;             /// Surface hydraulique
    //~ public $rP;             /// Périmètre hydraulique
    //~ public $rR;             /// Rayon hydraulique
    //~ public $rB;             /// Largeur au miroir
    //~ public $rJ;             /// Perte de charge
    //~ public $rFr;                /// Froude
    public $rY;          /// Tirant d'eau
    public $rHautCritique;  /// Tirant d'eau critique
    public $rHautNormale;   /// Tirant d'eau normal
    protected $oP;   /// Paramètres du système canal (classe oParam)
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
     * - dS : la dérivée de S par rapport Y
     * - dP : la dérivée de P par rapport Y
     * - dR : la dérivée de R par rapport Y
     * - dB : la dérivée de B par rapport Y
     */
    private $arCalc = array();

    /**
     * Construction de la classe.
     * Calcul des hauteurs normale et critique
     */
    public function __construct($oP) {
      //spip_log($this,'hydraulic');
      $this->oP = $oP;
      $oHautCritique = new cHautCritique($this, $oP);
      $this->rHautCritique = $oHautCritique->Newton($oP->rPrec);
      $oHautNormale= new cHautNormale($this, $oP);
      $this->rHautNormale = $oHautNormale->Newton($this->rHautCritique);
    }

    /**
     * Efface toutes les données calculées pour forcer le recalcul
     */
    public function Reset() {
        $this->arCalc = array();
    }

    /**
     * Calcul des données à la section
     * @param $sDonnee Clé de la donnée à calculer (voir $this->$arCalc)
     * @param $bRecalc Pour forcer le recalcul de la donnée
     * @return la donnée calculée
     */
    public function Calc($sDonnee, $rY = false) {
        if($rY!==false) {
            //~ spip_log('Calc('.$sDonnee.') rY='.$rY,'hydraulic');
            $this->rY = $rY;
            if(isset($this->arCalc[$sDonnee])) {
                // On efface toutes les données calculées pour forcer le calcul
                $this->Reset();
            }
        }

        if(!isset($this->arCalc[$sDonnee])) {
            // La donnée a besoin d'être calculée
            switch($sDonnee) {
                case 'S' :
                    $this->arCalc[$sDonnee] = $this->CalcS();
                    break;
                case 'P' :
                    $this->arCalc[$sDonnee] = $this->CalcP();
                    break;
                case 'R' :
                    $this->arCalc[$sDonnee] = $this->CalcR();
                    break;
                case 'B' :
                    $this->arCalc[$sDonnee] = $this->CalcB();
                    break;
                case 'J' :
                    $this->arCalc[$sDonnee] = $this->CalcJ();
                    break;
                case 'Fr' :
                    $this->arCalc[$sDonnee] = $this->CalcFr();
                    break;
                case 'dS' :
                    $this->arCalc[$sDonnee] = $this->CalcSder();
                    break;
                case 'dP' :
                    $this->arCalc[$sDonnee] = $this->CalcPder();
                    break;
                case 'dR' :
                    $this->arCalc[$sDonnee] = $this->CalcRder();
                    break;
                case 'dB' :
                    $this->arCalc[$sDonnee] = $this->CalcBder();
            }
        }
        //~ spip_log('Calc('.$sDonnee.')='.$this->arCalc[$sDonnee],'hydraulic');
        return $this->arCalc[$sDonnee];
    }

    /**
     * Calcul de la surface hydraulique.
     * Le résultat doit être stocké dans $this->rS
     * @return La surface hydraulique
     */
    abstract protected function CalcS();

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    abstract protected function CalcSder();

   /**
    * Calcul du périmètre hydraulique.
    * @return Le périmètre hydraulique
    */
    abstract protected function CalcP();

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    abstract protected function CalcPder();

   /**
    * Calcul du rayon hydraulique.
    * @return Le rayon hydraulique
    */
    protected function CalcR() {
        return $this->Calc('S')/$this->Calc('P');
    }

    /**
     * Calcul de dérivée du rayon hydraulique par rapport au tirant d'eau.
     * @return dR
     */
    protected function CalcRder() {
        return ($this->Calc('dS')*$this->Calc('P')-$this->Calc('S')*$this->Calc('dP'))/pow($this->Calc('P'),2);
    }

   /**
    * Calcul de la largeur au miroir.
    * @return La largeur au miroir
    */
    abstract protected function CalcB();

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    abstract protected function CalcBder();

   /**
    * Calcul de la perte de charge par la formule de Manning-Strickler.
    * @return La perte de charge
    */
    private function CalcJ() {
        return pow($this->oP->rQ/$this->Calc('S')/$this->oP->rKs,2)/pow($this->Calc('R'),4/3);
    }

   /**
    * Calcul du nombre de Froude.
    * @return Le nombre de Froude
    */
    private function CalcFr() {
        return $this->oP->rQ/$this->Calc('S')*sqrt($this->Calc('B')/$this->Calc('S')/$this->oP->rG);
   }

   /**
    * Calcul du point suivant de la courbe de remous par la méthode Euler explicite.
    * @return Tirant d'eau
    */
    public function CalcY_M1($Y) {
        $this->Reset(); // On réinitialise toutes les données dépendant de la ligne d'eau
        $this->rY = $Y; // Tirant d'eau initial pour le calcul du point suivant
        return $Y-($this->oP->rDx*($this->oP->rIf-$this->Calc('J'))/(1-pow($this->Calc('Fr'),2)));
   }
}


/**
 * Calculs de la section trapézoïdale
 */
class cSnTrapeze extends acSection {
   public $rLargeurFond;    /// Largeur au fond
   public $rFruit;          /// Fruit des berges


    function __construct($oP,$rLargeurFond, $rFruit) {
        $this->rLargeurFond=(real) $rLargeurFond;
        $this->rFruit=(real) $rFruit;
        parent::__construct($oP);
    }

    protected function CalcB() {
        return $this->rLargeurFond+2*$this->rFruit*$this->rY;
    }

    protected function CalcP() {
        return $this->rLargeurFond+2*sqrt(1+pow($this->rFruit,2))*$this->rY;
    }

    protected function CalcS() {
        return $this->rY*($this->rLargeurFond+$this->rFruit*$this->rY);
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        return $this->rLargeurFond + 2*$this->rFruit*$this->rY;
    }

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return 2*sqrt(1+$this->rFruit*$this->rFruit);
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return 2*$this->rLargeurFond*$this->rFruit;
    }
}

/**
 * Calculs de la section rectangulaire
 */
class cSnRectangulaire extends acSection {
   public $rLargeurFond; /// largeur au fond

    function __construct($oP,$rLargeurFond, $rFruit) {
        $this->rLargeurFond=(real) $rLargeurFond;
        parent::__construct($oP);
    }

    protected function CalcB() {
        return $this->rLargeurFond;
    }

    protected function CalcP() {
        return $this->rLargeurFond+2*$this->rY;
    }

    protected function CalcS() {
        return $this->rY*$this->rLargeurFond;
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        return $this->rLargeurFond;
    }

    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return 2;
    }

    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return 0;
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
        $this->oSn = $oSn;
        $this->oP = $oP;
        $this->rTol=$oP->rPrec;
        $this->rDx=$oP->rPrec/10;
    }

    /**
     * Calcul de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcFn($rX) {
        // Initialisation des données de la section
        $this->oSn->Reset();
        $this->oSn->rY = $rX;
        // Calcul de la fonction
        $rFn = (pow($this->oP->rQ,2)/pow($this->oSn->Calc('S'),2)*($this->oSn->Calc('B')/$this->oSn->Calc('S')/$this->oP->rG)-1);
        spip_log('cHautCritique:CalcFn('.$rX.')='.$rFn,'hydraulic');
        return $rFn;
    }

    /**
     * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcDer($rX) {
        // L'initialisation à partir de $rX a été faite lors de l'appel à CalcFn
        $rDer = ($this->oSn->Calc('dB')*$this->oSn->Calc('S')-3*$this->oSn->Calc('B')*$this->oSn->Calc('dS'));
        $rDer = pow($this->oP->rQ,2)/$this->oP->rG * $rDer / pow($this->oSn->Calc('S'),4);
        spip_log('cHautCritique:CalcDer('.$rX.')='.$rDer,'hydraulic');
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
    function __construct(acSection $oSn, cParam $oP) {
        $this->oSn=$oSn;
        $this->rQ=$oP->rQ;
        $this->rKs=$oP->rKs;
        $this->rIf=$oP->rIf;
        $this->rG=$oP->rG;
        $this->rTol=$oP->rPrec;
        $this->rDx=$oP->rPrec/10;
    }

    /**
     * Calcul de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcFn($rX) {
        // Initialisation des données de la section
        $this->oSn->Reset();
        $this->oSn->rY = $rX;
        // Calcul de la fonction
        $rFn = ($this->rQ-$this->rKs*pow($this->oSn->Calc('R'),2/3)*$this->oSn->Calc('S')*sqrt($this->rIf));
        spip_log('cHautNormale:CalcFn('.$rX.')='.$rFn,'hydraulic');
        return $rFn;
    }

    /**
     * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcDer($rX) {
        // L'initialisation a été faite lors de l'appel à CalcFn
        $rDer = 2/3*$this->oSn->Calc('dR')*pow($this->oSn->Calc('R'),-1/3)*$this->oSn->Calc('S');
        $rDer += pow($this->oSn->Calc('R'),2/3)*$this->oSn->Calc('dS');
        $rDer *= -$this->rKs * sqrt($this->rIf);
        spip_log('cHautNormale:CalcDer('.$rX.')='.$rDer,'hydraulic');
        return $rDer;
    }
}

?>
