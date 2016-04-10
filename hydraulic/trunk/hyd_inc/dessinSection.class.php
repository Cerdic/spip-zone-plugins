<?php
/*
 * hydraulic/inc_hyd/dessinSection.class.php
 *
 *
 *
 * Copyright 2012 Médéric Dulondel, David Dorchies <dorch@dorch.fr>
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
 * Classe pour l'affichage du dessin des sections
 *
 * @date 10/04/2012
 * @author Médéric Dulondel, David Dorchies
 *
 */
class dessinSection {
	private $hauteurDessin; // Hauteur du dessin en px
	private $largeurDessin; // Largeur du dessin en px
	private $marges; // Marge à gauche et à droite du dessin pour le texte
	private $mesCouleurs = array('red', 'blue', 'orange', 'green', 'grey', 'black', 'DarkMagenta ', 'cyan');  // Couleur des différentes lignes
	private $sectionClass;
	private $donnees = array();
	private $rValMax = 0; // Hauteur maxi en m à figurer dans le dessin
	private $rSnXmax = 0; // Largeur maximum en m à figurer dans le dessin

	function __construct($hauteur, $largeur, $marges, &$section, $lib_data) {
		$this->hauteurDessin = (real) $hauteur;
		$this->largeurDessin = (real) $largeur - $marges*2;
		$this->marges = (real) $marges;
		$this->sectionClass = &$section;
		$this->donnees = $lib_data;
		// On détermine la valeur la plus grande dans le tableau
		foreach($this->donnees as $val){
			if($val > $this->rValMax){
				$this->rValMax = $val;
			}
		}
		//spip_log($this,'hydraulic.'._LOG_DEBUG);
	}

	/**
	* Rajoute une ligne à notre dessin.
	* $color correspond à la couleur de la ligne
	* $y correspond à l'ordonnée exprimée en pixel de la ligne
	*/
	function AddRow($color, $y){
		$gauche = $this->marges;
		$droite = round($gauche + $this->largeurDessin);
		$y = round($y);
		$ligneDessin = "
			cx.strokeStyle = \"$color\";
			cx.beginPath();
			cx.moveTo($gauche, $y);
			cx.lineTo($droite, $y);
			cx.stroke();";
		return $ligneDessin;
	}

	/**
	* Convertit un tirant d'eau en mètre en une ordonnée en pixels
	*/
	private function GetDessinY($val) {
		// La valeur maximum de l'échelle  en px correspondant à 10% de la hauteur afin de faire plus propre
		return round($this->hauteurDessin * (1- 0.9*$val/$this->rValMax), 1)-2;
	}

	/**
	* Convertit une largeur en mètre en une abscisse en pixels
	* @param $Axe détermine si le pixel est à droite (1) ou à gauche (-1) de l'axe de symétrie
	* @return Abscisse en pixel à dessiner
	*/
	private function GetDessinX($val,$Axe) {
		return $this->marges + round(($this->largeurDessin-14) * (1/2 + $Axe*$val/$this->SnXmax), 1)+7;
	}

	/**
	* Transforme le tableau de tirants d"eau et charges à afficher en pixel + attribution des couleurs
	*/
	function transformeValeur($tabDonnees) {
		// On transforme nos valeurs en leur attribuant la valeur en pixel et une couleur qui leur est associé
		$result = array();
		$couleur = 0;
		foreach($tabDonnees as $cle=>$valeur){
			$result[$cle][] = $this->GetDessinY($valeur);
			$result[$cle][] = $this->mesCouleurs[$couleur];
			$couleur++;
		}

		asort($result);

		return $result;
	}

	// Retourne le dessin de la section
	function GetDessinSection() {
		// On transforme nos valeurs en pixels
		$mesDonnees = $this->transformeValeur($this->donnees);

		// Hauteur dessin - Hauteur de berge, en format pixels
		$diffHautBerge = $mesDonnees['rYB'][0];

		// On définit le style de notre dessin
		$dessin = '<canvas id="cvsSection" width="'.($this->largeurDessin+2*$this->marges).'" height="'.$this->hauteurDessin.'"></canvas>';

		// On créé la base de notre dessin de section
		$dessin.= '
		<script type="text/javascript">
			var cx = document.getElementById("cvsSection").getContext("2d");
			cx.strokeStyle = "black";';
			// Récupération des coordonnées de la section à dessiner
			$tCoordSn = $this->sectionClass->DessinCoordonnees();

			// Détermination de la largeur max de la section
			$this->SnXmax = max($tCoordSn['x'])*2;

			// Dessin des verticales au dessus des berges
			$LargeurBerge = $this->sectionClass->CalcGeo('B')/2;
			$xBergeGauche = $this->GetDessinX($LargeurBerge,-1);
			$xBergeDroite = $this->GetDessinX($LargeurBerge,1);
			$dessin.= "
				cx.setLineDash([5]);
				cx.beginPath();
				cx.moveTo($xBergeGauche, 0);
				cx.lineTo($xBergeGauche, $diffHautBerge);
				cx.moveTo($xBergeDroite, 0);
				cx.lineTo($xBergeDroite, $diffHautBerge);
				cx.stroke();
				cx.setLineDash([]);";

			// Dessin de la section
			$tSnX = array();
			$tSnY = array();
			// Parcours des points à gauche
			for($i=count($tCoordSn['x'])-1; $i>=0; $i-=1) {
				$tSnX[] = $this->GetDessinX($tCoordSn['x'][$i],-1);
				$tSnY[] = $this->GetDessinY($tCoordSn['y'][$i]);
			}
			// Parcours des points à droite
			for($i=0; $i<count($tCoordSn['x']); $i++) {
				$tSnX[] = $this->GetDessinX($tCoordSn['x'][$i],1);
				$tSnY[] = $this->GetDessinY($tCoordSn['y'][$i]);
			}
			$dessin .= sprintf('
			cx.lineWidth = 4;
			cx.beginPath();
			cx.moveTo(%d,%d);',$tSnX[0],$tSnY[0]);
			for($i=1; $i<count($tSnX); $i++) {
				$dessin .= sprintf('
				cx.lineTo(%d,%d);',$tSnX[$i],$tSnY[$i]);
			}
			$dessin .= '
				cx.stroke();
				cx.lineWidth = 1.0;';

			// Affichage des valeurs pour chaque trait
			$dessin .= '
				cx.font = "12px sans-serif";';
			$bDroiteGauche = true; // Pour alterner le placement des libellés
			// On rajoute les différents libelles avec la couleur qui va bien
			foreach($mesDonnees as $cle=>$valeur){
				if($cle != 'rYB'){
					list($y,$color) = $valeur;
					// Ajout du trait
					$dessin .= $this->AddRow($color, $y);
					// Ajout du texte
					$dessin .= '
						cx.fillStyle = "'.$color.'";
						cx.textAlign="'.((!$bDroiteGauche)?'left':'right').'";';
					$x = ($bDroiteGauche)?($this->marges-5):($this->marges+$this->largeurDessin+5);
					$texte = $cle.' = '.round($this->donnees[$cle], $this->sectionClass->oP->iPrec);
					$y += 4;
					$dessin.= "
						cx.fillText(\"$texte\",$x,$y);";
					$bDroiteGauche = !$bDroiteGauche;
				}
			}

			$dessin.= '
		</script>';

		return $dessin;
	}
}
?>