<?php
/*
 * hydraulic/inc_hyd/graph.class.php
 *
 *
 *
 * Copyright 2012 David Dorchies <dorch@dorch.fr>
 *
 *
 *
 * This program is free software; you can redistribute it and/or modify
 *
 * it under the terms of the GNU General Public License as published by
 *
 * the Free Software Foundation; either version 2 of the License, or
 *
 * (at your option) any later version.
 *
 *
 *
 * This program is distributed in the hope that it will be useful,
 *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *
 * GNU General Public License for more details.
 *
 *
 *
 * You should have received a copy of the GNU General Public License
 *
 * along with this program; if not, write to the Free Software
 *
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *
 * MA 02110-1301, USA.
 *
 */

/**
 * Classe pour l'affichage des graphiques
 *
 * @date 09/01/2012
 * @author David Dorchies
 *
 */
class cGraph {

	const DBG = false; /// Activation des messages de débuggage de la classe

	private $tSeries;   //!< Tableau des séries
	private $echo;  //!< Chaine contenant le script jqPlot
	private $tLabels; /// Respectivement les titres du graphique, des abscisses, des ordonnées
	///@todo Transférer les deux constantes de graduation dans la configuration du plugin
	const nbTickXmax = 10; // Nbre max de graduation sur l'axe des abscisses
	const nbTickYmax = 10; // Nbre max de graduation sur l'axe des ordonnées

	function __construct($Title = '', $Xlabel='', $Ylabel='') {
		$this->tSeries = array();
		$this->tLabels = array(
			'title' =>  $Title,
			'X'     =>  $Xlabel,
			'Y'     =>  $Ylabel
		);
	}

	/**
	 * Ajout d'une série de données dans le graph
	 *
	 * @param $sNom Nom de la série dans la légende
	 * @param $tY Tableau des ordonnées de la série
	 * @param $tX Tableau des abscisses de la série (facultatif à partir de la 2ème série)
	 */
	function AddSerie($sNom, $tX, $tY, $sCouleur, $tOptions = array()) {
		$num = count($this->tSeries) + 1;
		$this->tSeries[$num] = new cSerie($num, $sNom, $tX, $tY, $sCouleur, $tOptions);
	}

	function GetAxesOptions($rDecal = 0, $rPente = 0, $rXFin = 0) {
		// Tableau des Abscisses et des ordonnées
		$tX = array(); $tY = array();
		foreach($this->tSeries as $oSeries) {
			$tX = array_merge($tX,array_keys($oSeries->tXY));
			$tY = array_merge($tY, array_values($oSeries->tXY));
		}
		// Options pour chacun
		$XOptions = $this->Get1AxeOptions($tX, self::nbTickXmax);
		$YOptions = $this->Get1AxeOptions($tY, self::nbTickYmax);
		return array_merge($XOptions, $YOptions);
	}

	function Get1AxeOptions($tX, $Tmax) {
		$Xmin = min($tX);
		$Xmax = max($tX);

		if($Xmin == $Xmax){
			$Xmin = $Xmin * 0.9;
			$Xmax = $Xmax * 1.1;
		}

		if($Xmin == 0 && $Xmax == 0){
			$Xmin = -1;
			$Xmax = 1;
		}

		$r1 = ($Xmax - $Xmin) / floatval($Tmax);
		$r2 = floor($r1 * pow(10,(-floor(log10($r1))))*10)/10;

		if($r2 > 5) {
			$XTick = 10;
		}
		elseif($r2 > 2.5) {
			$XTick = 5;
		}
		elseif($r2 > 2) {
			$XTick = 2.5;
		}
		elseif($r2 > 1) {
			$XTick = 2;
		}
		else {
			$XTick = 1;
		}

		// Ecart entre chaque graduation
		$XTick = $XTick * pow(10, floor(log10($r1)));

		// Minimum et maximum arrondis par rapport à $XTick
		$Xmin = floor($Xmin / $XTick) * $XTick;
		$Xmax = ceil($Xmax / $XTick) * $XTick;

		return array($Xmin, $Xmax, $XTick);
	}

	/**
	 * Décale les ordonnées selon un offset et une pente
	 * @param $rDecal Offset pour décaler l'affichage des ordonnées
	 * @param $rPente Pente pour décaler l'affichage des ordonnées
	 * @param $rXFin Si différent de zéro, abscisse à partir de laquelle calculer la pente
	 */
	function Decal($rDecal = 0, $rPente = 0, $rXFin = 0) {
		foreach($this->tSeries as $oSerie) {
			$oSerie->RecalOrdonnees($rDecal, $rPente, $rXFin);
		}
	}

	/**
	 * Renvoie le script jqplot du graphique
	 * @param $sId Attribut id de la balise DIV où sera créé le graphique
	 * @param $iHeight Hauteur du graphique en pixels
	 * @param $iWidth Largeur du graphique en pixels
	 */
	function GetGraph($sId, $iHeight, $iWidth) {
		if(self::DBG) spip_log($this->tSeries,'hydraulic',_LOG_DEBUG);
		$sId = 'jqplot_'.$sId;
		$this->echo = sprintf('
			<div id="%s" style="height:%spx;width:%spx; "></div>',
			$sId, $iHeight, $iWidth);
		$this->echo .= '
		<script language="javascript" type="text/javascript">';
			// On récupère les données de chaque série
			foreach($this->tSeries as $oSerie) {
				$this->echo .= $oSerie->GetPush();
			}
			$tS = array();
			for($i=1; $i<=count($this->tSeries); $i++) {
				$tS[]='tSerie'.$i;
			}
			$this->echo .= sprintf('
				chart=$.jqplot(\'%s\',
					[%s],',
					$sId,
				implode(', ',$tS));
				$this->echo .= '
				{';
					if($this->tLabels['title']) {
						$this->echo .= '
						title:\''.$this->tLabels['title'].'\',';
					}
					$this->echo .= '
					seriesDefaults: {showMarker:false},';
					$tS = array();
					foreach($this->tSeries as $oSerie) {
						$tS[] = $oSerie->GetConfig();
					}
					$this->echo .= sprintf('
						series:[
							%s
						],',
						implode(',
						',$tS)
					);
					// Options de légende et curseur
					$this->echo .= '
					legend: {show: true, location:\'ne\', fontSize:\'1em\'},
					cursor: {
						show:true,
						showVerticalLine: true,
						showHorizontalLine: true,
						showCursorLegend: true,
						showTooltip: false,
						zoom: true,
						dblClickReset: false,
						intersectionThreshold: 6
					},';
					// Options des axes
					list($Xmin, $Xmax, $XTick, $Ymin, $Ymax, $YTick) = $this->GetAxesOptions();
					$this->echo .= sprintf('
						axes:{
							xaxis:{
								label:\'%s\',
								labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
								min:%s,
								max:%s,
							tickInterval:%s},
							yaxis:{
								label:\'%s\',
								labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
								min:%s,
								max:%s,
								tickInterval:%s,
								tickOptions:{formatString:\'%%.3f\'}
							}
						}
				}
			);
		</script>',
					addslashes($this->tLabels['X']),$Xmin,$Xmax,$XTick,
					addslashes($this->tLabels['Y']),$Ymin,$Ymax,$YTick);

		return $this->echo;
	}
}

/**
 * Classe pour la gestion des séries dans le graph
 *
 * @date 09/01/2012
 * @author David Dorchies
 *
 */
class cSerie {
	private $num;       //!< Numéro d'ordre de la série
	private $sNom;      //!< Nom de la série (Balise de langue telle que définie dans lang/hydraulic_xx.php)
	public $tXY;       //!< Tableau $this->tXY[abscisse]=ordonnée
	private $sCouleur;  //!< Couleur de la courbe (Code HTML)
	private $sOptions;  //!< Options supplémentaires

	/**
	 * Construction de la classe
	 *
	 * @param $sNom Nom de la série (Balise de langue telle que définie dans lang/hydraulic_xx.php)
	 * @param $tX Tableau des abscisses
	 * @param $tY Tableau des ordonnées (Même taille que $tX) ou réel pour une valeur fixe
	 * @param $sCouleur Couleur de la courbe (Code HTML)
	 * @param $iLineWidth Epaisseur de la courbe
	 * @param $sOptions Options supplémentaires
	 */
	function __construct($num, $sNom, $tX, $tY, $sCouleur, $sOptions = '') {
		$this->num = $num;
		$this->sNom = $sNom;
		if(is_array($tY)) {
			$this->tXY = array_combine($tX, $tY);
		}
		else {
			$this->tXY = array_fill_keys($tX, $tY);
		}
		$this->sCouleur = $sCouleur;
		$this->sOptions = $sOptions;
	}

	/**
	 * Retourne la chaine à insérer dans l'option series de jqplot
	 */
	function GetConfig() {
		$ret = sprintf(
			'{label:\'%s\', color:\'%s\'',
			addslashes(_T('hydraulic:'.$this->sNom)),
		$this->sCouleur);
		if($this->sOptions) {
			$ret .= ', '.$this->sOptions;
		}
		$ret .= '}';
		return $ret;
	}

	/**
	 * Retourne la chaine des push du tableau de la série
	 */
	function GetPush() {
		$ret = sprintf("\nvar tSerie%s=[];",$this->num);
		foreach($this->tXY as $rX=>$rY) {
			$ret .= sprintf("\ntSerie%s.push([%s, %s]);", $this->num, $rX, $rY);
		}
		return $ret."\n";
	}

	/**
	 * Recalcule les ordonnées sur une base Y * pente + fond
	 * @param $rDecal Offset pour décaler l'affichage des ordonnées
	 * @param $rPente Pente pour décaler l'affichage des ordonnées
	 * @param $rXFin Si différent de zéro, abscisse à partir de laquelle calculer la pente
	 * @bug ne fonctionne pas si l'abscisse minimum est différente de 0
	 */
	function RecalOrdonnees($rDecal = 0, $rPente = 0, $rXFin = 0) {
		$rCoteFond = $rDecal;
		foreach($this->tXY as $rX=>&$rY) {
			if($rPente) {
				if($rXFin) {
					$rY = $rY + $rDecal + $rPente * ($rXFin - $rX);
				}
				else {
					$rY = $rY + $rDecal + $rPente * $rX;
				}
			}
		}
	}
}
?>
