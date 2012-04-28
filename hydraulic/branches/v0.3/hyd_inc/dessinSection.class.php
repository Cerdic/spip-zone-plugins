<?php
/*
 * hydraulic/inc_hyd/dessinSection.class.php
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
 * Classe pour l'affichage du dessin des section
 *
 * @date 10/04/2012
 * @author Médéric Dulondel
 *
 */
class dessinSection {
	private $hauteurDessin; // Hauteur du dessin en px
	private $largeurDessin; // Largeur du dessin en px
	private $mesCouleurs = array('red', 'blue', 'orange', 'green', 'grey', 'black');  // Couleur des différentes lignes
	private $tSection; // Choix de la section
	private $sectionClass; 
	private $donnees = array();
	
    function __construct($hauteur, $largeur, $typeSection, $section, $lib_data) {
        $this->hauteurDessin = $hauteur;
        $this->largeurDessin = $largeur;
        $this->tSection = $typeSection;
        $this->sectionClass = $section;
		$this->donnees = $lib_data;
    } 

	/*
	 * Rajoute une ligne à notre dessin. 
	 * $color correspond à la couleur de la ligne
	 * $val correspond à l'ordonnée exprimée en pixel de la ligne
	 */
	function AddRow($color, $val){		
		$ligneDessin = '$("#dessinSection").drawLine(0,'.$val.','.$this->largeurDessin.','.$val.', {color: "'.$color.'"});';
		return $ligneDessin;
	}
	
	/*
	 * Transforme des valeurs de tirants d'eau en leur valeur en pixel
	 */ 
	function transformeValeur($tabDonnees){
		// On détermine la valeur la plus grande dans le tableau
		$ValMax = 0;
		foreach($tabDonnees as $val){
			if($val > $ValMax){
				$ValMax = $val;
			}
		}
		
		// La valeur maximum de l'échelle correspondant à 10% de la hauteur afin de faire plus propre
		$valEchelle = $this->hauteurDessin - ($this->hauteurDessin * 0.1);
		
		// On transforme nos valeurs en leur attribuant la valeur en pixel et une couleur qui leur est associé
		$result = array();
		$couleur = 0;
		foreach($tabDonnees as $cle=>$valeur){
			$result[$cle][] = round($this->hauteurDessin - (($valeur*$valEchelle)/$ValMax), 1);
			$result[$cle][] = $this->mesCouleurs[$couleur];
			$couleur++;
		}

		asort($result);

		return $result;
	}
	
	// Retourne le dessin de la section
	function GetDessinSection(){
		// On transforme nos valeurs en pixels
		$mesDonnees = $this->transformeValeur($this->donnees); 
		
		// Hauteur dessin - Hauteur de berge, en format pixels 
		$diffHautBerge = $mesDonnees['rYB'][0];

		// On définit le style de notre dessin 
		$dessin = '<style type="text/css">
					.canvas{
						position: relative;
						width:'.$this->largeurDessin.'px;
						height:'.$this->hauteurDessin.'px;
					}
					</style>';
		
		// On créé la base de notre dessin de section
		$dessin.= '<script type="text/javascript">
					$(document).ready(function(){';
			
		$dessin.= '$("#dessinSection").drawLine(0, 0, 0,'.$diffHautBerge.', {stroke: 1});
				   $("#dessinSection").drawLine('.$this->largeurDessin.', 0,'.$this->largeurDessin.','.$diffHautBerge.', {stroke: 1});';
			
		switch($this->tSection){
			case 'FT':
				$dessin.= '$("#dessinSection").drawPolyline(
							[0,'.($this->largeurDessin*0.25).','.($this->largeurDessin*0.75).','.$this->largeurDessin.'],
							['.$diffHautBerge.','.$this->hauteurDessin.','.$this->hauteurDessin.','.$diffHautBerge.'], {stroke: 4});';
                break;

            case 'FR':
				$dessin.= '$("#dessinSection").drawPolyline(
							[0,0,'.$this->largeurDessin.','.$this->largeurDessin.'],
							['.$diffHautBerge.','.$this->hauteurDessin.','. $this->hauteurDessin.','.$diffHautBerge.'], {stroke: 4});';
	
                break;

            case 'FC':
					// Trouver une méthode de dessin pour les sections circulaires
                break;

            case 'FP':
				$dessin.= '$("#dessinSection").drawPolyline([0,0,'.$this->largeurDessin.','.$this->largeurDessin.'], [0,'.$this->hauteurDessin.','. $this->hauteurDessin.', 0]);';
	
				break;

            default:
                
		}			

		// On ajoute les différentes lignes avec couleur + valeur
		foreach($mesDonnees as $cle=>$valeur){
			if($cle != 'rYB'){
				$dessin.= $this->AddRow($valeur[1], $valeur[0]);
			}
		}
		
		$dessin.= '});
			</script>';

		//Div qui va contenir notre dessin de section
		$dessin.='<div id="dessinSection" class="canvas">';
		
		// Pour alterner le placement des libellés
		$droiteGauche = 0;
		// On rajoute les différents libelles avec la couleur qui va bien
		foreach($mesDonnees as $cle=>$valeur){
			if($cle != 'rYB'){
				$placement = ($droiteGauche%2==0)?'left: -80px':'right: -80px;';
				$dessin.= '<p style="position: absolute; top:'.($valeur[0]-8).'px;'.$placement.'; width: auto; display: inline-block; color:'.$valeur[1].'">'.$cle.' = '.round($this->donnees[$cle], $this->sectionClass->oP->iPrec).'</p>';
				$droiteGauche++;		
			}	
		}
		
		$dessin.= '</div>';
	
		return $dessin;
	}
}
?>
