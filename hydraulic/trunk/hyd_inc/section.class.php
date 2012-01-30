<?php
/**
 *      \file class.section.php
 *
 *      Copyright 2009 dorch <dorch@dorch-xps>
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

include_spip('hyd_inc/newton.class');
class cParam {
   public $rYCL; // Condition limite en cote à l'amont ou à l'aval
   public $rKs; // Strickler
   public $rQ; // Débit
   public $rLong; // Longueur du bief
   public $rIf; // Pente du fond
   public $rDx; // Pas d'espace (positif en partant de l'aval, négatif en partant de l'amont)
   public $rPrec; // Précision de calcul et d'affichage
   public $rG=9.81; // Constante de gravité

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
abstract class acSection {
   public $rS;
   public $rR;
   public $rB;
   public $rJ;
   public $rFr;
   public $rY;
   public $rHautCritique;
   public $rHautNormale;

   public function __construct($oP) {
      // Calcul des hauteurs normale et critique à la construction de la classe
      //spip_log($this,'hydraulic');
      $oHautCritique = new cHautCritique($this, $oP);
      $this->rHautCritique = $oHautCritique->Newton($oP->rPrec);
      $oHautNormale= new cHautNormale($this, $oP);
      $this->rHautNormale = $oHautNormale->Newton($this->rHautCritique);
   }

   abstract public function CalcS();

   abstract public function CalcP();

   public function CalcR() {
      $this->rR=$this->rS/$this->rP;
      return $this->rR;
   }

   abstract public function CalcB();

   public function CalcJ(cParam $oP) {
      $this->rJ= pow($oP->rQ/$this->rS/$oP->rKs,2)/pow($this->rR,4/3);
      return $this->rJ;
   }

   public function CalcFr(cParam $oP) {
      $this->rFr=$oP->rQ/$this->rS*sqrt($this->rB/$this->rS/$oP->rG);
      return $this->rFr;
   }

   public function ReCalcFr(cParam $oP,$rY) {
      $this->rY = $rY;
      $this->rS = $this->CalcS();
      $this->rFr=$oP->rQ/$this->rS*sqrt($this->CalcB()/$this->rS/$oP->rG);
      return $this->rFr;
   }

   public function CalcY(cParam $oP) {
      $dY=$oP->rDx*($oP->rIf-$this->rJ)/(1-pow($this->rFr,2));
      $this->rY=$this->rY-$dY;
      return $this->rY;
   }

   public function CalcPasX(cParam $oP, $rY) {
      $this->rY = $rY;
      $this->CalcB();
      $this->CalcP();
      $this->CalcS();
      $this->CalcR();
      $this->CalcJ($oP);
      $this->CalcFr($oP);
      return $this->CalcY($oP);
   }

}
class cSnTrapeze extends acSection {
   public $rLargeurFond;
   public $rFruit;


   function __construct($oP,$rLargeurFond, $rFruit) {
      $this->rLargeurFond=(real) $rLargeurFond;
      $this->rFruit=(real) $rFruit;
      parent::__construct($oP);
   }

   public function CalcB() {
      $this->rB=$this->rLargeurFond+2*$this->rFruit*$this->rY;
      return $this->rB;
   }

   public function CalcP() {
      $this->rP=$this->rLargeurFond+2*sqrt(1+pow($this->rFruit,2))*$this->rY;
      return $this->rP;
   }

   public function CalcS() {
      $this->rS=$this->rY*($this->rLargeurFond+$this->rFruit*$this->rY);
      return $this->rS;
   }
}
class cHautCritique extends acNewton {
   private $oSn;
   private $oP;

   function __construct($oSn,cParam $oP) {
      $this->oSn = $oSn;
      $this->oP = $oP;
      $this->rTol=$oP->rPrec;
      $this->rDx=$oP->rPrec/10;
   }
   public function CalcFn($rX) {
      $this->oSn->rY = $rX;
      $this->oSn->CalcS();
      $this->oSn->CalcB();
      return (pow($this->oP->rQ,2)/pow($this->oSn->rS,2)*($this->oSn->rB/$this->oSn->rS/$this->oP->rG)-1);
   }

}
class cHautNormale extends acNewton {
   private $oSn;
   private $rQ;
   private $rKs;
   private $rIf;

   function __construct($oSn, $oP) {
      $this->oSn=$oSn;
      $this->rQ=$oP->rQ;
      $this->rKs=$oP->rKs;
      $this->rIf=$oP->rIf;
      $this->rG=$oP->rG;
      $this->rTol=$oP->rPrec;
      $this->rDx=$oP->rPrec/10;
   }

   public function CalcFn($rX) {
      $this->oSn->rY = $rX;
      $this->oSn->CalcS();
      $this->oSn->CalcP();
      return ($this->rQ-$this->rKs*pow($this->oSn->CalcR(),2/3)*$this->oSn->rS*sqrt($this->rIf));
   }
}

?>
