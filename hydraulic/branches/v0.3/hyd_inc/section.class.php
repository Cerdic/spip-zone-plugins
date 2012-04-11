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
    public $rYCL;   /// Condition limite en cote à l'amont ou à l'aval
    public $rKs;    /// Strickler
    public $rQ;     /// Débit
    public $rLong;  /// Longueur du bief
    public $rIf;    /// Pente du fond
    public $rDx;    /// Pas d'espace (positif en partant de l'aval, négatif en partant de l'amont)
    public $rPrec;  /// Précision de calcul et d'affichage
    public $rG=9.81;/// Constante de gravité
    public $iPrec;  /// Précision en nombre de décimales

    function __construct($rKs, $rQ, $rIf, $rPrec, $rYB, $rYCL = 0, $rDx = 0, $rLong = 0) {
        $this->rYCL=(real) $rYCL;
        $this->rKs=(real) $rKs;
        $this->rQ=(real) $rQ;
        $this->rLong=(real) $rLong;
        $this->rIf=(real) $rIf;
        $this->rDx=(real) $rDx;
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
    //~ public $rS;             /// Surface hydraulique
    //~ public $rP;             /// Périmètre hydraulique
    //~ public $rR;             /// Rayon hydraulique
    //~ public $rB;             /// Largeur au miroir
    //~ public $rJ;             /// Perte de charge
    //~ public $rFr;                /// Froude
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
     * - dS : la dérivée de S par rapport Y
     * - dP : la dérivée de P par rapport Y
     * - dR : la dérivée de R par rapport Y
     * - dB : la dérivée de B par rapport Y
     */
    private $arCalc = array();
    protected $arCalcGeo = array(); /// Données ne dépendant pas de la cote de l'eau

    /**
     * Construction de la classe.
     * Calcul des hauteurs normale et critique
     */
    public function __construct(&$oLog,&$oP) {
        $this->oP = &$oP;
        $this->oLog = &$oLog;
        $this->CalcGeo('B');
        spip_log($this,'hydraulic');
    }

    /**
     * Efface toutes les données calculées pour forcer le recalcul
     */
    public function Reset($bGeo=true) {
        $this->arCalc = array();
        if($bGeo) {
         $this->arCalcGeo = array();
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
            spip_log('Calc('.$sDonnee.') rY='.$rY,'hydraulic');
            $this->rY = $rY;
            // On efface toutes les données dépendantes de Y pour forcer le calcul
            $this->Reset(false);
        }

        if(!isset($this->arCalc[$sDonnee])) {
            // La donnée a besoin d'être calculée
            switch($sDonnee) {
                case 'S' : // Surface mouillée
                    $this->arCalc[$sDonnee] = $this->CalcS();
                    break;
                case 'P' : // Périmètre mouillé
                    $this->arCalc[$sDonnee] = $this->CalcP();
                    break;
                case 'R' : // Rayon hydraulique
                    $this->arCalc[$sDonnee] = $this->CalcR();
                    break;
                case 'B' : // Largeur au miroir
                    $this->arCalc[$sDonnee] = $this->CalcB();
                    break;
                case 'J' : // Perte de charge linéaire
                    $this->arCalc[$sDonnee] = $this->CalcJ();
                    break;
                case 'Fr' : // Froude
                    $this->arCalc[$sDonnee] = $this->CalcFr();
                    break;
                case 'dS' : // dS/dY
                    $this->arCalc[$sDonnee] = $this->CalcSder();
                    break;
                case 'dP' : // dP/dY
                    $this->arCalc[$sDonnee] = $this->CalcPder();
                    break;
                case 'dR' : // dR/dY
                    $this->arCalc[$sDonnee] = $this->CalcRder();
                    break;
                case 'dB' : // dB/dY
                    $this->arCalc[$sDonnee] = $this->CalcBder();
                    break;
                case 'V' : // Vitesse moyenne
                    $this->arCalc[$sDonnee] = $this->CalcV();
                    break;
                case 'Hs' : // Charge spécifique
                    $this->arCalc[$sDonnee] = $this->CalcHs();
                    break;
                case 'Yf' : // Tirant d'eau fluvial
                    $this->arCalc[$sDonnee] = $this->CalcYf();
                    break;
                case 'Yt' : // Tirant d'eau torrentiel
                    $this->arCalc[$sDonnee] = $this->CalcYt();
                    break;
                case 'Yco' : // Tirant d'eau conjugué
                    $this->arCalc[$sDonnee] = $this->CalcYco();
                    break;
                case 'Tau0' : // Force tractrice ou contrainte de cisaillement
                    $this->arCalc[$sDonnee] = $this->CalcTau0();
                    break;
                case 'SYg' : // Distance du centre de gravité de la section à la surface libre
                    $this->arCalc[$sDonnee] = $this->CalcSYg();
                    break;
                case 'Imp' : // Impulsion hydraulique
                    $this->arCalc[$sDonnee] = $this->CalcImp();
                    break;
                case 'Alpha' : // Angle Alpha de la surface libre par rapport au fond pour les sections circulaires
                    $this->arCalc[$sDonnee] = $this->CalcAlpha();
                    break;
                case 'dAlpha' : // Dérivée de l'angle Alpha de la surface libre par rapport au fond pour les sections circulaires
                    $this->arCalc[$sDonnee] = $this->CalcAlphaDer();
                    break;
            }
        }
        spip_log('Calc('.$sDonnee.')='.$this->arCalc[$sDonnee],'hydraulic');
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
            spip_log('CalcGeo('.$sDonnee.') rY='.$this->oP->rYB,'hydraulic');
            $this->Reset(false);
            $this->rY = $this->oP->rYB;
            switch($sDonnee) {
                case 'B' : // Largeur aux berges
                    $this->arCalcGeo[$sDonnee] = $this->CalcB();
                    if($this->arCalcGeo[$sDonnee] < $this->oP->rYB / 100) {
                        // Section fermée
                        $this->bSnFermee = true;
                        // On propose une fente de Preissmann égale à 1/100 de la hauteur des berges
                        $this->arCalcGeo[$sDonnee] = $this->oP->rYB / 100;
                    }
                    $this->rLargeurBerge = $this->arCalcGeo[$sDonnee];
                    break;
                case 'S' : // Surface mouillée au niveau des berges
                    $this->arCalcGeo[$sDonnee] = $this->CalcS();
                    break;
                case 'P' : // Périmètre mouillé au niveau des berges
                    $this->arCalcGeo[$sDonnee] = $this->CalcP();
                    break;
                case 'Yc' : // Tirant d'eau critique
                    $this->arCalcGeo[$sDonnee] = $this->CalcYc();
                    break;
                case 'Yn' : // Tirant d'eau normal
                    $this->arCalcGeo[$sDonnee] = $this->CalcYn();
                    break;
                case 'Hsc' : // Charge spécifique critique
                    $this->arCalcGeo[$sDonnee] = $this->CalcHsc();
            }
        }
        spip_log('CalcGeo('.$sDonnee.')='.$this->arCalcGeo[$sDonnee],'hydraulic');
        return $this->arCalcGeo[$sDonnee];
    }

    /**
     * Calcul de la surface hydraulique.
     * @return La surface hydraulique
     */
    protected function CalcS($rY) {
        return $rY*$this->rLargeurBerge;
    }

    /**
     * Calcul de dérivée de la surface hydraulique par rapport au tirant d'eau.
     * @return dS
     */
    protected function CalcSder() {
        return $this->rLargeurBerge;
    }

   /**
    * Calcul du périmètre hydraulique.
    * @return Le périmètre hydraulique
    */
    protected function CalcP($rY) {
        return 2*$rY;
    }
    /**
     * Calcul de dérivée du périmètre hydraulique par rapport au tirant d'eau.
     * @return dP
     */
    protected function CalcPder() {
        return 2;
    }

   /**
    * Calcul du rayon hydraulique.
    * @return Le rayon hydraulique
    */
    protected function CalcR() {
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
    protected function CalcRder() {
        if($this->Calc('P')!=0) {
            return ($this->Calc('dS')*$this->Calc('P')-$this->Calc('S')*$this->Calc('dP'))/pow($this->Calc('P'),2);
        }
        else {
            return 0;
        }
    }

   /**
    * Calcul de la largeur au miroir.
    * @return La largeur au miroir
    */
    protected function CalcB() {
        return $this->rLargeurBerge;
    }
    /**
     * Calcul de dérivée de la largeur au miroir par rapport au tirant d'eau.
     * @return dB
     */
    protected function CalcBder() {
        return 0;
    }

   /**
    * Calcul de la perte de charge par la formule de Manning-Strickler.
    * @return La perte de charge
    */
    private function CalcJ() {
        return pow($this->Calc('V')/$this->oP->rKs,2)/pow($this->Calc('R'),4/3);
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
        // L'appel à Calc('J') avec Y en paramètre réinitialise toutes les données dépendantes de la ligne d'eau
        return $Y-($this->oP->rDx*($this->oP->rIf-$this->Calc('J',$Y))/(1-pow($this->Calc('Fr',$Y),2)));
    }

   /**
    * Calcul de la vitesse moyenne.
    * @return Vitesse moyenne
    */
    private function CalcV() {
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
    private function CalcHs() {
        return $this->rY+pow($this->Calc('V'),2)/(2*$this->oP->rG);
    }


   /**
    * Calcul de la charge spécifique critique.
    * @return Charge spécifique critique
    */
    private function CalcHsc() {
        // On mémorise les calculs hydrauliques en cours
        $rY = $this->rY;
        $arCalc = $this->arCalc;
        // On calcule la charge avec la hauteur critique
        $rHsc = $this->Calc('Hs',$this->CalcGeo('Yc'));
        // On restitue les données initiales
        $this->rY = $rY;
        $this->arCalc = $arCalc;
        return $rHsc;
    }


   /**
    * Calcul du tirant d'eau critique.
    * @return tirant d'eau critique
    */
    private function CalcYc() {
        $oHautCritique = new cHautCritique($this, $this->oP);
        if(!$this->rHautCritique = $oHautCritique->Newton($this->oP->rPrec) or !$oHautCritique->HasConverged()) {
         $this->oLog->Add(_T('hydraulic:h_critique').' : '._T('hydraulic:newton_non_convergence'));
      }
      return $this->rHautCritique;
    }

   /**
    * Calcul du tirant d'eau normal.
    * @return tirant d'eau normal
    */
    private function CalcYn() {
        $oHautNormale= new cHautNormale($this, $this->oP);
        if(!$this->rHautNormale = $oHautNormale->Newton($this->CalcGeo('Yc')) or !$oHautNormale->HasConverged()) {
         $this->oLog->Add(_T('hydraulic:h_normale').' : '._T('hydraulic:newton_non_convergence'));
      }
        return $this->rHautNormale;
    }

   /**
    * Calcul du tirant d'eau fluvial.
    * @return tirant d'eau fluvial
    */
    private function CalcYf() {
        if($this->rY > $this->Calc('Yc')) {
            return $this->rY;
        }
        else {
            $oHautCorrespondante= new cHautCorrespondante($this, $this->oP);
            return $oHautCorrespondante->Newton($this->Calc('Yc'));
        }
    }

   /**
    * Calcul du tirant d'eau torrentiel.
    * @return tirant d'eau torrentiel
    */
    private function CalcYt() {
        if($this->rY < $this->Calc('Yc')) {
            return $this->rY;
        }
        else {
            $oHautCorrespondante= new cHautCorrespondante($this, $this->oP);
            return $oHautCorrespondante->Newton($this->Calc('Yc'));
        }
    }

   /**
    * Calcul du tirant d'eau conjugué.
    * @return tirant d'eau conjugué
    */
    private function CalcYco() {
        return $this->rY*(sqrt(1 + 8 * pow($this->Calc('Fr'),2)) - 1) / 2;
    }

   /**
    * Calcul de la contrainte de cisaillement.
    * @return contrainte de cisaillement
    */
    private function CalcTau0() {
        return 1000 * $this->oP->rG * $this->Calc('R') * $this->oP->rIf;
    }

    /**
     * Calcul de la distance du centre de gravité de la section à la surface libre.
     * @return Distance du centre de gravité de la section à la surface libre
     */
    protected function CalcSYg($rY) {
        return $rY / 2;
    }
    /**
     * Calcul de l'impulsion hydraulique.
     * @return Impulsion hydraulique
     */
    protected function CalcImp() {
        return 1000 * ($this->oP->rQ * $this->Calc('V') + $this->oP->rG * $this->Calc('SYg'));
    }

    /**
     * Calcul de l'angle Alpha entre la surface libre et le fond pour les sections circulaires.
     * @return Angle Alpha pour une section circulaire, 0 sinon.
     */
    protected function CalcAlpha(){
        return 0;
    }

    /**
     * Calcul de la dérivée de l'angle Alpha entre la surface libre et le fond pour les sections circulaires.
     * @return Dérivée de l'angle Alpha pour une section circulaire, 0 sinon.
     */
    protected function CalcAlphaDer(){
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
        // Calcul de la fonction
        $rFn = (pow($this->oP->rQ,2)/pow($this->oSn->Calc('S',$rX),2)*($this->oSn->Calc('B',$rX)/$this->oSn->Calc('S',$rX)/$this->oP->rG)-1);
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
    function __construct($oSn, cParam $oP) {
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
        // Calcul de la fonction
        $rFn = ($this->rQ-$this->rKs*pow($this->oSn->Calc('R',$rX),2/3)*$this->oSn->Calc('S',$rX)*sqrt($this->rIf));
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


/**
 * Calcul de la hauteur correspondante (charge égale)
 */
class cHautCorrespondante extends acNewton {
    private $rY; // Tirant d'eau connu
    private $rV2; // Vitesse moyenne au carré associée au tirant d'eau connu
    private $oSnCal; // Section contenant les données de la section avec la hauteur à calculer
    private $rG; // Constante de gravité

    /**
     * Constructeur de la classe
     * @param $oSn Section sur laquelle on fait le calcul
     * @param $oP Paramètres supplémentaires (Débit, précision...)
     */
    function __construct(acSection $oSn, cParam $oP) {
        $this->rY = $oSn->rY;
        $this->rV2 = pow($oSn->Calc('V'),2);
        $this->oSnCal = clone $oSn;
        $this->rQ = $oP->rQ;
        $this->rG = $oP->rG;
    }

    /**
     * Calcul de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcFn($rX) {
        // Calcul de la fonction
        $rFn = $this->rY - $rX + ($this->rV2+pow($this->oSnCal->Calc('V',$rX),2))/(2*$this->rG);
        spip_log('cHautCorrespondante:CalcFn('.$rX.')='.$rFn,'hydraulic');
        return $rFn;
    }

    /**
     * Calcul analytique de la dérivée de la fonction dont on cherche le zéro
     * @param $rX Variable dont dépend la fonction
     */
    protected function CalcDer($rX) {
        // L'initialisation a été faite lors de l'appel à CalcFn
        $rDer = - $this->rQ/ $this->rG * $this->oSnCal->Calc('dS') / pow($this->oSnCal->Calc('S'),3);
        spip_log('cHautCorrespondante:CalcDer('.$rX.')='.$rDer,'hydraulic');
        return $rDer;
    }
}

?>
