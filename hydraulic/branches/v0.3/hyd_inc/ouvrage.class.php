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
    private $nL;      /// Loi d'ouvrage
    private $nLS;     /// Loi pour la surverse
    private $Cd;    /// Coefficients de débit de l'ouvrage
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
    public function __construct(&$oLog,$nLoi, $tP, $nLoiSurverse = 0) {
        $this->oLog = &$oLog;
        $this->nL = $nLoi;
        $this->nLS = $nLoiSurverse;
        $this->tP = $tP;
        if(isset($tP['C'])) {
            // Lois avec un coef de débit
            $this->Cd = $tP['C'];
        }
        elseif(isset($tP['CR']) and isset($tP['CR'])) {
            // Lois des vannes et seuils trapézoïdaux
            $this->Cd = array($tP['CR'],$tP['CT']);
        }
        else {
            echo 'absence de coefficient de débit';
        }
        //spip_log($this,'hydraulic');
    }


    /**
     * Mise à jour d'un paramètre de l'ouvrage
     * @param $sMaj Variable à modifier (indice du tableau tP)
     * @param $rmaj Valeur de lavariable à mettre à jour
     */
    public function Set($sMaj,$rMaj) {
        $this->tP[$sMaj] = $rMaj;
    }


    /**
     * Calcul à l'ouvrage
     * @param $sCalc Variable à calculer (indice du tableau tP)
     * @param $rInit Valeur initiale pour le calcul
     * @return array(0=> donnée calculée, 1=> Flag d'écoulement)
     */
    public function Calc($sCalc,$rInit=0.) {
        //print_r($this->tP);
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
            //echo "\n".'nIterMax='.$nIterMax.'  XMinInit='.$XMinInit.'  XMaxInit='.$XMaxInit.'  DX='.$DX;


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
                    if(abs($Q/$QT-1.) <= $this->tP['P']) {break;}
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
        return array($rVarC,$nFlag);
    }


    /**
     * Calcul du débit à l'ouvrage
     * @param $sCalc Variable à calculer (indice du tableau tP)
     * @return array(0=> débit, 1=> Flag d'écoulement)
     */
    private function OuvrageQ() {
        /**
         * Flag d'écoulement $nFlag :
         * - -1 : erreur de calcul
         * - 0 : débit nul
         * - 1 : surface libre dénoyé
         * - 2 : surface libre noyé
         * - 3 : charge denoyé
         * - 4 : charge noyé partiel
         * - 5 : charge noyé total
         * - 11 : surverse dénoyé
         * - 12 : surverse noyé
         */
        $nFlag=-1; // Initialisé à -1 pour détecter les modifications
        // Gestion des sens de l'écoulement
        if($this->tP['ZM'] == $this->tP['ZV']){
            // Ecoulement nul
            return array(0,0);
        }
        elseif($this->tP['ZM']>$this->tP['ZV']){
            // Ecoulement amont -> aval
            $bSensAmAv = true;
        }
        else {
            // Ecoulement Aval -> amont
            $bSensAmAv = false;
            $ZV = $this->tP['ZV'];
            $this->tP['ZV'] = $this->tP['ZM'];
            $this->tP['ZM'] = $ZV;
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
            list($rQ,$nFlag)=$this->CalculQ($this->nL,$this->Cd);
            if($this->nLS and isset($this->tP['H']) and $this->tP['W']+$this->tP['H'] < $this->tP['ZM']) {
                // Vanne avec surverse autorisée et la cote amont est supérieure à la cote de surverse
                list($rQS,$nFlagS)=$this->CalculQ($this->nLS,$this->CdS,$this->tP['ZM']-$this->tP['W']-$this->tP['H']);
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
     * @return array(0=> débit, 1=> Flag d'écoulement)
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
     * @return array(0=> débit, 1=> Flag d'écoulement)
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
     * @return array(0=> débit, 1=> Flag d'écoulement)
     */
    private function SeuilDen($rC,$rZ=0) {
        $rQ=$rC*$this->tP['L']*self::R2G*pow($this->tP['ZM']-$rZ,1.5);
        $nFlag=1;
        return array($rQ,$nFlag);
    }

    /**
     * Loi seuil noyé classique
     * @param $rC Coefficient de débit
     * @param $rZ Cote de radier à retrancher pour la surverse
     * @return array(0=> débit, 1=> Flag d'écoulement)
     */
    private function SeuilNoy($rC,$rZ=0) {
        $rQ=$rC*self::R32*$this->tP['L']*self::R2G*sqrt($this->tP['ZM']-$rZ-$this->tP['ZV'])*$this->tP['ZV'];
        $nFlag=2;
        return array($rQ,$nFlag);
    }

    /**
     * Calcul du débit à partir d'une loi
     * @param $nLoi Loi de débit
     * @param $rC Coefficient de débit
     * @param $rZ Cote de radier à retrancher pour la surverse
     * @return array(0=> débit, 1=> Flag d'écoulement)
     */
    private function CalculQ($nLoi,$rC,$rZ=0) {
        $rQ=0; // Débit par défaut
        $nFlag=0; // Flag par défaut
        $tP = &$this->tP;
        switch($nLoi) {
        case 1 : // Equation seuil orifice Cemagref
            $bSurfacelibre=($tP['ZM']<=$tP['W']);
            $bDenoye=($tP['ZV']<=2/3*$tP['ZM']);
            $bPartiel=true;
            if(!$bDenoye) $bPartiel=($tP['ZV']<=2/3*$tP['ZM']+$tP['W']/3);
            if($bDenoye) {
                $Res=$this->SeuilDen($rC);
            }
            elseif($bPartiel or $bSurfacelibre) {
                $Res=$this->SeuilNoy($rC);
            }
            else {
                // Ennoyement total
            }
            if(!$bSurfacelibre and $bPartiel) {
                // Ecoulement en charge : on soustrait la partie en contact avec la pelle
                $Res2=$this->SeuilDen($rC,$tP['ZM']-$tP['W']);
                $Res[0]-= $Res2[0];
                $Res[1]=($Res[1]==1)?3:4;
            }

        case 3 : // Equation classique du seuil dénoyé
            return $this->SeuilDen($rC,$rZ);
        case 4 : // Equation classique de la vanne en charge dénoyée
            return $this->VanneDen($rC);
        case 5 : // Equation classique de la vanne en charge totalement noyée
            return $this->VanneNoy($rC);
        }
    }
}

?>
