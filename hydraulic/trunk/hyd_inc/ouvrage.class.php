<?php
/**
 *      @file ouvrage.class.php
 *      Gestion des calculs au niveau des Ouvrages en travers
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
/*
include_spip('hyd_inc/newton.class');
*/


/**
 * Calculs sur un ouvrage
 */
class cOuvrage {
    private $oLog;  /// Journal des calculs
    /**
     * Loi de débit pour l'ouvrage. Valeurs possibles :
     * - 1 - Déversoir/Orifice Cemagref 88 : Type 1,2,3 + Surverse
     * - 2 - Vanne de fond/Seuil Cemagref 88 : Type 1,2,3 + Surverse
     * - 3 - Seuil dénoyé : Type 3 + Surverse
     * - 4 - Seuil noyé : Type 3 + Surverse
     * - 5 - Vanne dénoyé : Type 1,2
     * - 6 - Vanne noyé : Type 1,2
     * - 7 - Cunge 1980 : Type 1,2,3 + Surverse
     * - 8 - Déversoir/Orifice Cemagref 02 : Type 4,5
     * - 9 - Vanne de fond/Seuil Cemagref 02 : Type 4,5
     */
    private $nL;
    /**
     * Loi de débit pour la surverse. Valeurs possibles :
     * - 1 - Déversoir/Orifice Cemagref 88
     * - 2 - Vanne de fond/Seuil Cemagref 88
     * - 3 - Seuil dénoyé
     * - 4 - Seuil noyé
     * - 7 - Cunge 1980
     */
    private $nLS;
    /**
     * Tableau contenant les paramètres de l'ouvrage.
     *
     * Liste des clés possibles du tableau  :
     * - Q : le débit de l'ouvrage
     * - ZM : la cote de l'eau à l'amont par rapport au radier
     * - ZV : la cote de l'eau à l'aval par rapport au radier
     * - L : largeur
     * - Z : cote de radier
     * - W : ouverture de vanne
     * - A : Angle des ouvrages triangulaires
     * - H : Hauteur de la vanne pour la surverse
     * - C : Coefficient de débit pour tous types sauf trapézoïdal
     * - CR : Coefficient de débit partie rectangulaire pour les trapézoïdales
     * - CT : Coefficient de débit partie triangulaire pour les trapézoïdales
     * - CS : Coefficient de débit de la surverse
     * - P : Précision du calcul
     */
    private $tP = array();

    const G = 9.81; /// Constante de gravité terrestre
    const R2G = 4.42944; /// sqrt(2*self::gP);
    const R32 = 2.59807; /// 3*sqrt(3)/2;
    const IDEFINT = 100; /// Pas de parcours de l'intervalle pour initialisation dichotomie
    const IDICMAX = 100; /// Itérations maximum de la dichotomie

    /**
     * Construction de la classe.
     * Calcul des ouvrages
     * @param $oLog Objet gérant le journal de calcul
     * @param $nLoi Loi de débit à l'ouvrage
     * @param $tP Tableaux des caractéristiques à l'ouvrage (largeur...)
     * @param $nLoiSurverse Loi de débit de la surverse
     */
    public function __construct(&$oLog, $tP) {
        $this->oLog = &$oLog;
        if(isset($tP['OuvrageLoi'])) {
            $this->nL = $tP['OuvrageLoi'];
        }
        if(isset($tP['SurverseLoi'])) {
            $this->nLS = $tP['SurverseLoi'];
        }
        else {
            $this->nLS = 0;
        }
        $this->tP = $tP;
        if(!isset($this->tP['C'])) {$this->tP['C']=0;} // Pour les lois trapézoïdales CEM02
        spip_log($this,'hydraulic');
    }


    /**
     * Mise à jour d'un paramètre de l'ouvrage
     * @param $sMaj Variable à modifier (indice du tableau tP)
     * @param $rmaj Valeur de la variable à mettre à jour
     */
    public function Set($sMaj,$rMaj) {
        $this->tP[$sMaj] = $rMaj;
        spip_log("cOuvrage->Set($sMaj,$rMaj)",'hydraulic');
    }


    /**
     * Calcul à l'ouvrage
     * @param $sCalc Variable à calculer (indice du tableau tP)
     * @param $rInit Valeur initiale pour le calcul
     * @return array(0=> donnée calculée, 1=> Flag d'écoulement)
     * Signification du Flag d'écoulement :
     * - -1 : erreur de calcul
     * -  0 : débit nul
     * -  1 : surface libre dénoyé
     * -  2 : surface libre noyé
     * -  3 : charge denoyé
     * -  4 : charge noyé partiel
     * -  5 : charge noyé total
     * - 11 : surverse dénoyé
     * - 12 : surverse noyé
     */
    public function Calc($sCalc,$rInit=0.) {
        // Calcul du débit (facile !)
        if($sCalc=='Q') {
            return $this->OuvrageQ();
        }
        else {
            // Sinon calcul d'une autre donnée par dichotomie
            $rVarC = &$this->tP[$sCalc];
            $QT = $this->tP['Q']; // Débit recherché (Target)
            $XMinInit = 0;
            $rVarC = $XMinInit;
            list($Q1,$nFlag) = $this->OuvrageQ();
            $XMaxInit = $rInit*10; /// @todo Boucler la valeur max sur 10,100,1000,10000
            $rVarC = $XMaxInit;
            list($Q2,$nFlag) = $this->OuvrageQ();
            $DX = ($XMaxInit - $XMinInit) / floatval(self::IDEFINT);
            $nIterMax = floor(max($XMaxInit - $rInit,$rInit - $XMinInit) / $DX + 1);
            $Xmin = $rInit;
            $Xmax = $rInit;
            $X1 = $rInit;
            $X2 = $rInit;
            $rVarC = $rInit;
            list($Q,$nFlag) = $this->OuvrageQ();
            $Q1 = $Q;
            $Q2 = $Q;
            //echo "\nQT=$QT nIterMax=$nIterMax XMinInit=$XMinInit XMaxInit=$XMaxInit DX=$DX";


            for($nIter=1;$nIter<=$nIterMax;$nIter++) {
                //Ouverture de l'intervalle des deux côtés puis à droite et à gauche
                $Xmax = $Xmax + $DX;
                if($Xmax > $XMaxInit xor $DX <= 0) $Xmax = $XMaxInit;
                $rVarC = $Xmax;
                list($Q,$nFlag) = $this->OuvrageQ();
                if($Q1 < $Q2 xor $Q <= $Q2) {
                    $Q2 = $Q;
                    $X2 = $Xmax;
                }
                if($Q1 < $Q2 xor $Q >= $Q1) {
                    $Q1 = $Q;
                    $X1 = $Xmax;
                }
                $Xmin = $Xmin - $DX;
                if($Xmin < $XMinInit xor $DX <= 0) {
                    $Xmin = $XMinInit;
                }
                $rVarC = $Xmin;
                list($Q,$nFlag) = $this->OuvrageQ();
                if($Q1 < $Q2 xor $Q <= $Q2) {
                    $Q2 = $Q;
                    $X2 = $Xmin;
                }
                if($Q1 < $Q2 xor $Q >= $Q1) {
                    $Q1 = $Q;
                    $X1 = $Xmin;
                }

/*
                echo "\n".'nIter='.$nIter.' Xmin='.$Xmin.' Xmax='.$Xmax;
                echo "\n".'X1='.$X1.' Q1='.$Q1.' X2='.$X2.' Q2='.$Q2;
                echo "\n".'$QT > $Q1 xor $QT >= $Q2 = '.($QT > $Q1 xor $QT >= $Q2);
*/

                if($QT > $Q1 xor $QT >= $Q2) {break;}
            }

            if($nIter >= self::IDEFINT) {
                // Pas d'intervalle trouvé avec au moins une solution
                if($Q2 < $QT and $Q1 < $QT) {
                    // Cote de l'eau trop basse pour passer le débit il faut ouvrir un autre ouvrage
                    $rVarC = $XmaxInit;
                }
                else {
                    // Cote de l'eau trop grande il faut fermer l'ouvrage
                    $rVarC = $XminInit;

                }
                list($Q,$nFlag) = $this->OuvrageQ();
                $nFlag = -1;
            }
            else {
                // Dichotomie
                $X = $rInit;
                for($nIter = 1; $nIter<=self::IDICMAX;$nIter++) {
                    $rVarC=$X;
                    list($Q,$nFlag) = $this->OuvrageQ();
                    if($QT!=0 && abs($Q/$QT-1.) <= $this->tP['P']) {break;}
                    if($QT < $Q xor $Q1 <= $Q2) {
                        // QT < IQ et Q(X1) > Q(X2) ou pareil en inversant les inégalités
                        $X1=$rVarC;
                    }
                    else {
                        // QT < IQ et Q(X1) < Q(X2) ou pareil en inversant les inégalités
                        $X2=$rVarC;
                    }
                    $X=($X2+$X1)*0.5;
                }
                if($nIter == self::IDICMAX) {
                    //IF1 <-- -10 anomalie: la dichotomie n'a pas abouti en ITER iterations
                    $nFlag = -1;
                }
            }
        }
        //echo "\nCalc rVarC=$rVarC nFlag=$nFlag";
        return array($rVarC,$nFlag);
    }


    /**
     * Calcul du débit à l'ouvrage
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function OuvrageQ() {
        $nFlag=-1; // Initialisé à -1 pour détecter les modifications
        $bSensAmAv = true; // Par défaut on considère le sens d'écoulement amont -> aval
        if(!in_array($this->nL,array(3,5))) {
            // Pour les lois autres que seuil et vanne dénoyé,
            // On gère le sens de l'écoulement
            if($this->tP['ZM'] == $this->tP['ZV']){
                // Ecoulement nul
                return array(0,0);
            }
            elseif($this->tP['ZM']<$this->tP['ZV']){
                // Ecoulement Aval -> amont
                $bSensAmAv = false;
                $ZV = $this->tP['ZV'];
                $this->tP['ZV'] = $this->tP['ZM'];
                $this->tP['ZM'] = $ZV;
            }
        }

        // Gestion des écoulements nuls
        if((isset($this->tP['W']) and $this->tP['W'] == 0) // Vanne fermée
            and (!isset($this->tP['H']) // Pas de surverse
            or (isset($this->tP['H']) and ($this->tP['H']==0  // Pas de surverse
            or $this->tP['H']>$this->tP['ZM'])))){ // Cote amont inférieure à la surverse
            // Vanne fermée et pas de surverse
            $rQ = 0;
            $nFlag = 0;
        }

        if($nFlag < 0) {
            // On doit pouvoir calculer un débit sur l'ouvrage
            list($rQ,$nFlag)=$this->CalculQ($this->nL,$this->tP['C']);
            if($this->nLS and isset($this->tP['H']) and $this->tP['W']+$this->tP['H'] < $this->tP['ZM']) {
                // Vanne avec surverse autorisée et la cote amont est supérieure à la cote de surverse
                $W = $this->tP['W'];
                $this->tP['W'] = 99999;
                list($rQS,$nFlagS)=$this->CalculQ($this->nLS,$this->tP['CS'],$W+$this->tP['H']);
                $this->tP['W'] = $W;
                $rQ += $rQS;
                $nFlag = $nFlagS+10;
            }
        }

        if(!$bSensAmAv) {
            // Inversion de débit -> on remet tout à l'endroit
            $rQ = -$rQ;
            $ZM = $this->tP['ZV'];
            $this->tP['ZV'] = $this->tP['ZM'];
            $this->tP['ZM'] = $ZM;
        }
        //echo "\n".'OuvrageQ='.$rQ.' / '.$nFlag;
        return array($rQ,$nFlag);
    }


    /**
     * Loi de vanne de fond dénoyée classique
     * @param $rC Coefficient de débit
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function VanneDen($rC) {
        if($this->tP['ZM']>$this->tP['W']) {
            $rQ=$rC*$this->tP['W']*$this->tP['L']*self::R2G*sqrt($this->tP['ZM']-$this->tP['W']);
            $nFlag=3;
        }
        else {
            $this->oLog->Add(_T('hydraulic:debit_non_calcule').' : '
                ._T('hydraulic:surface_libre').' '._T('hydraulic:avec').' '
                ._T('hydraulic:loi_en_charge'));
            $rQ=0;
            $nFlag=-1;
        }
        return array($rQ,$nFlag);
    }


    /**
     * Loi de vanne de fond totalement noyée classique
     * @param $rC Coefficient de débit
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function VanneNoy($rC) {
        if($this->tP['ZM']>$this->tP['W']) {
            $rQ=$rC*$this->tP['W']*$this->tP['L']*self::R2G*sqrt($this->tP['ZM']-$this->tP['ZV']);
            $nFlag=5;
        }
        else {
            $this->oLog->Add(_T('hydraulic:debit_non_calcule').' : '
                ._T('hydraulic:surface_libre').' '._T('hydraulic:avec').' '
                ._T('hydraulic:loi_en_charge'));
            $rQ=0;
            $nFlag=-1;
        }
        return array($rQ,$nFlag);
    }


    /**
     * Loi seuil dénoyé classique
     * @param $rC Coefficient de débit
     * @param $rZ Cote de radier à retrancher pour la surverse
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function SeuilDen($rC,$rZ=0) {
        $rQ=$rC*$this->tP['L']*self::R2G*pow($this->tP['ZM']-$rZ,1.5);
        return array($rQ,1);
    }

    /**
     * Loi seuil noyé classique
     * @param $rC Coefficient de débit
     * @param $rZ Cote de radier à retrancher pour la surverse
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function SeuilNoy($rC,$rZ=0) {
        $rQ=$rC*self::R32*$this->tP['L']*self::R2G*sqrt($this->tP['ZM']-$rZ-$this->tP['ZV'])*$this->tP['ZV'];
        return array($rQ,2);
    }

    /**
     * Calcul du débit à partir d'une loi
     * @param $nLoi Loi de débit
     * @param $rC Coefficient de débit
     * @param $rZ Cote de radier à retrancher pour la surverse
     * @return array(0=> débit, 1=> Flag d'écoulement) (Voir Calc)
     */
    private function CalculQ($nLoi,$rC,$rZ=0) {
        $rQ=0; // Débit par défaut
        $nFlag=0; // Flag par défaut
        $tP = &$this->tP;
        switch($nLoi) {
        case 1 : // Equation seuil-orifice Cemagref
            $bDenoye=($tP['ZV']<=2/3*$tP['ZM']);
            if($tP['ZM']<=$tP['W']) {
                // Surface libre
                if($bDenoye) { // Seuil dénoyé
                    return $this->SeuilDen($rC);
                }
                else { // Seuil noyé
                    return $this->SeuilNoy($rC);
                }
            }
            else {
                // Ecoulements en charge
                if($bDenoye) { // Orifice dénoyé
                    $Q1 = $this->SeuilDen($rC);
                    $Q2 = $this->SeuilDen($rC,$tP['W']);
                    return array($Q1[0]-$Q2[0],3);
                }
                else { // Orifice noyé
                    if($tP['ZV']<=2/3*$tP['ZM']+$tP['W']/3) {
                        // Ennoyement partiel
                        $Q1 = $this->SeuilNoy($rC);
                        $Q2 = $this->SeuilDen($rC,$tP['W']);
                        return array($Q1[0]-$Q2[0],4);
                    }
                    else { // Ennoyement total
                        return $this->VanneNoy($rC*self::R32);
                    }
                }
            }
        case 2 : // Loi de seuil et vanne Cemagref
            $mu0 = 2 / 3 * $tP['C'];
            if($tP['ZM']<=$tP['W']) {
                // Surface libre
                $bSurfLibre = true;
                $alpha = 0.75;
            }
            else {
                // Ecoulements en charge
                $bSurfLibre = false;
                $alpha = 1 - 0.14 * $tP['ZV'] / $tP['W'];
                if($alpha < 0.4) {$alpha = 0.4;}
                if($alpha > 0.75) {$alpha = 0.75;}
            }
            if($tP['ZV'] <= $alpha * $tP['ZM']) {
                // Ecoulement dénoyé
                $bDenoye = true;
            }
            else { // Ecoulement noyé
                $bdenoye = false;
                $x = sqrt(1 - $tP['ZV'] / $tP['ZM']);
                $beta = -2 * $alpha + 2.6;
                if ($x > 0.2) {
                    $KF = 1 - pow(1 - $x / sqrt(1 - $alfa), $beta);
                }
                else {
                    $KF= 5 * $x * (1 - pow(1 - 0.2 / sqrt(1 - $alfa), $beta));
                }
            }
            if($bSurfLibre) { // Seuil
                $muf=$mu0-0.08;
                $Q = $muf * $tP['L'] * self::R2G * pow($tP['ZM'],1.5);
                if($bDenoye) { // Seuil dénoyé
                    return array($Q,1);
                }
                else { // Seuil noyé
                    $Q = $KF * $Q;
                    return array($Q,2);
                }
            }
            else { // Vanne
                $mu = $mu0 - 0.08 / ($tP['ZM'] / $tP['W']);
                $mu1 = $mu0 - 0.08 / ($tP['ZM'] / $tP['W'] - 1);
                if($bdenoye) { // Vanne dénoyée
                    $Q = $tP['L'] * self::R2G * ($mu * pow($tP['ZM'],1.5) - $mu1 * pow($tP['ZM'] - $tP['W'],1.5));
                    return array($Q,3);
                }
                else {
                    $alfa1 = 1 - 0.14 * ($tP['ZV'] - $tP['W']) / $tP['W'];
                    if ($alfa1<0.4) {$alfa1 = 0.4;}
                    if ($alfa1>0.75) {$alfa1 = 0.75;}
                    if($tP['ZV'] <= $alfa1 * $tP['ZM'] + (1 - $alfa1) * $tP['W']) {
                        // Vanne partiellement noyée
                        $Q = $tP['L'] * self::R2G * ($KF * $mu * pow($tP['ZM'],1.5) - $mu1 * pow($tP['ZM'] - $tP['W'],1.5));
                        return array($Q,4);
                    }
                    else { // Vanne totalement noyée
                        $x1 = sqrt(1 - ($tP['ZV'] - $tP['W']) / ($tP['ZM'] - $tP['W']));
                        $beta1 = -2 * $alfa1 + 2.6;
                        if ($x1 > 0.2) {
                            $KF1 = 1 - pow(1 - $x1 / sqrt(1 - $alfa1), $beta1);
                        }
                        else {
                            $KF1 = 5*  $x1 * (1 - pow(1 - 0.2 / sqrt(1 - $alfa1), $beta1));
                        }
                        $Q = $tP['L'] * self::R2G * ($KF * $mu * pow($tP['ZM'],1.5) - $KF1 * $mu1 * pow($tP['ZM'] - $tP['W'],1.5));
                        return array($Q,5);
                    }
                }
            }
        case 3 : // Equation classique du seuil dénoyé
            return $this->SeuilDen($rC,$rZ);
        case 4 : // Equation du seuil noyé
            return $this->SeuilNoy($rC,$rZ);
        case 5 : // Equation classique de la vanne en charge dénoyée
            return $this->VanneDen($rC);
        case 6 : // Equation classique de la vanne en charge totalement noyée
            return $this->VanneNoy($rC);
        }
    }
}

?>
