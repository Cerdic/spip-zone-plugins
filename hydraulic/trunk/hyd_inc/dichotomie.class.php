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


/**
 * Dichotomie
 */
class cDichotomie {

	const DBG = false; /// Pour loguer les messages de debug de cette classe

	private $oLog; ///< Journal de calcul

	//~ const IDEFINT = 100; /// Pas de parcours de l'intervalle pour initialisation dichotomie
	//~ const IDICMAX = 100; /// Itérations maximum de la dichotomie
	// ***** DEBUG *****
	const IDEFINT = 10; /// Pas de parcours de l'intervalle pour initialisation dichotomie
	const IDICMAX = 20; /// Itérations maximum de la dichotomie
	// ***** FIN DEBUG *****

	private $objet; ///< Objet contenant la méthode de calcul du débit
	private $sFnCalculQ; /// Nom de la méthode de calcul du débit
	private $bLogError; /// true pour afficher les messages d'erreur en cas de non convergence

	/**
	* Construction de la classe.
	* @param $oLog Journal de calcul
	* @param $objet Objet contenant la méthode de calcul du débit et la
	*      propriété VarCal pointeur vers la variable à calculer
	* @param $sFnCalculQ Nom de la méthode de calcul du débit
	*/
	public function __construct(&$oLog,&$objet,$sFnCalculQ, $bLogError=true) {
		$this->oLog = &$oLog;
		$this->objet = &$objet;
		$this->sFnCalculQ = $sFnCalculQ;
		$this->bLogError = $bLogError;
	}

	private function CalculQ() {
		$sFnCalculQ = $this->sFnCalculQ;
		$res = $this->objet->$sFnCalculQ();
		if(!is_array($res)) {
			$res = array($res,0);
		}
		if(self::DBG) spip_log('CalculQ('.$this->objet->VarCal.')='.$res[0],'hydraulic',_LOG_DEBUG);
		return $res;
	}

	/**
	* Calcul à l'ouvrage
	* @param $sCalc Variable à calculer (Nom de la propriété de l'objet)
	* @param $QT Débit cible
	* @param $rTol Précision attendue
	* @param $rInit Valeur initiale
	*/
	public function calculer($QT,$rTol,$rInit=0.) {
		if(self::DBG) spip_log("Dichotomie->calculer($QT,$rTol,$rInit)",'hydraulic.'._LOG_DEBUG);
		$this->objet->VarCal = $rInit;
		list($Q,$nFlag) = $this->CalculQ();
		$XminInit = 1E-8;
		$this->objet->VarCal = $XminInit;
		list($Q1,$nFlag) = $this->CalculQ();
		if($Q1 < $Q xor $Q > $QT) $Q1 = $Q;
		$XmaxInit = max(1,$rInit)*100;
		$this->objet->VarCal = $XmaxInit;
		list($Q2,$nFlag) = $this->CalculQ();
		if($QT < $Q xor $Q > $Q2) $Q1 = $Q;
		$DX = ($XmaxInit - $XminInit) / floatval(self::IDEFINT);
		$nIterMax = floor(max($XmaxInit - $rInit,$rInit - $XminInit) / $DX + 1);
		if(self::DBG) spip_log("QT=$QT nIterMax=$nIterMax XminInit=$XminInit XmaxInit=$XmaxInit DX=$DX",'hydraulic',_LOG_DEBUG);
		$Xmin = $rInit;
		$Xmax = $rInit;
		$X1 = $rInit;
		$X2 = $rInit;
		$this->objet->VarCal = $rInit;
		list($Q,$nFlag) = $this->CalculQ();
		$Q1 = $Q;
		$Q2 = $Q;
		///< @todo : Chercher en dehors de l'intervalle en le décalant à droite ou à gauche en fonction de la valeur

		for($nIter=1;$nIter<=$nIterMax;$nIter++) {
			//Ouverture de l'intervalle des deux côtés : à droite puis à gauche
			$Xmax = $Xmax + $DX;
			if($Xmax > $XmaxInit xor $DX <= 0) $Xmax = $XmaxInit;
			$this->objet->VarCal = $Xmax;
			list($Q,$nFlag) = $this->CalculQ();
			if($Q1 < $Q2 xor $Q <= $Q2) {
				$Q2 = $Q;
				$X2 = $Xmax;
			}
			if($Q1 < $Q2 xor $Q >= $Q1) {
				$Q1 = $Q;
				$X1 = $Xmax;
			}
			$Xmin = $Xmin - $DX;
			if($Xmin < $XminInit xor $DX <= 0) {
				$Xmin = $XminInit;
			}
			$this->objet->VarCal = $Xmin;
			list($Q,$nFlag) = $this->CalculQ();
			if($Q1 < $Q2 xor $Q <= $Q2) {
				$Q2 = $Q;
				$X2 = $Xmin;
			}
			if($Q1 < $Q2 xor $Q >= $Q1) {
				$Q1 = $Q;
				$X1 = $Xmin;
			}

			if(self::DBG) spip_log("nIter=$nIter Xmin=$Xmin Xmax=$Xmax",'hydraulic',_LOG_DEBUG);
			if(self::DBG) spip_log("X1=$X1 Q1=$Q1 X2=$X2 Q2=$Q2",'hydraulic',_LOG_DEBUG);
			if(self::DBG) spip_log('$QT > $Q1 xor $QT >= $Q2 = '.($QT > $Q1 xor $QT >= $Q2),'hydraulic',_LOG_DEBUG);

			if($QT > $Q1 xor $QT >= $Q2) {break;}
		}

		if($nIter >= self::IDEFINT) {
			// Pas d'intervalle trouvé avec au moins une solution
			if($Q2 < $QT and $Q1 < $QT) {
				// Cote de l'eau trop basse pour passer le débit il faut ouvrir un autre ouvrage
				$this->objet->VarCal = $XmaxInit;
			}
			else {
				// Cote de l'eau trop grande il faut fermer l'ouvrage
				$this->objet->VarCal = $XminInit;
			}
			list($Q,$nFlag) = $this->CalculQ();
			$nFlag = -1;
			if($this->bLogError) {
				$sLog = ($Q1<$Q2)?"Q($X1)=$Q1 &lt; Q($X2)=$Q2":"Q($X2)=$Q2 &lt; Q($X1)=$Q1";
				$sLog = ($QT<$Q1)?"$QT &lt; $sLog":"$sLog &lt; $QT";
				$this->oLog->Add(_T('hydraulic:dichotomie_intervalle').' : '.
				$sLog,true);
			}
		}
		else {
			// Dichotomie
			$X = $rInit;
			$nFlag = 0;
			for($nIter = 1; $nIter<=self::IDICMAX;$nIter++) {
				$this->objet->VarCal=$X;
				if(self::DBG) spip_log("nIter=$nIter nFlag=$nFlag".' rVarC='.$this->objet->VarCal,'hydraulic',_LOG_DEBUG);
				list($Q,$nFlag) = $this->CalculQ();
				//~ if($QT!=0 && abs($Q/$QT-1.) <= $rTol) {break;}
				if($QT!=0 && abs($X1-$X2) <= $rTol) {break;}
				if($QT < $Q xor $Q1 <= $Q2) {
					// QT < IQ et Q(X1) > Q(X2) ou pareil en inversant les inégalités
					$X1=$this->objet->VarCal;
				}
				else {
					// QT < IQ et Q(X1) < Q(X2) ou pareil en inversant les inégalités
					$X2=$this->objet->VarCal;
				}
				$X=($X2+$X1)*0.5;
			}
			if($nIter == self::IDICMAX) {
				$this->oLog->Add(
					_T('hydraulic:dichotomie_non_convergence').' '.format_nombre($Q, $this->data['iPrec']),
				true);
				$nFlag = -1;
			}
		}
		if(self::DBG) spip_log('rVarC='.$this->objet->VarCal." nFlag=$nFlag",'hydraulic.'._LOG_DEBUG);
		return array($this->objet->VarCal,$nFlag);
	}

}

?>
