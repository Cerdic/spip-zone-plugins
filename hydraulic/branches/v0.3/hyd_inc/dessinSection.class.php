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
    private $donnees = array();   // Tableau des données
	private $hauteurDessin; // Hauteur du dessin en px
	private $largeurDessin; // Largeur du dessin en px
	private $precision; // Précision de calcul
	private $mesCouleurs = array('red', 'blue', 'orange', 'green', 'grey', 'black');  // Couleur des différentes lignes
	private $hautBerge;

    function __construct($result, $hauteur, $largeur, $prec, $hautB) {
		$this->donnees = $result;
        $this->hauteurDessin = $hauteur;
        $this->largeurDessin = $largeur;
        $this->precision = (int)-log10($prec);
        $this->hautBerge = $hautB;
    }

	/*
	 * Rajoute une ligne à notre dessin. 
	 * $color correspond à la couleur de la ligne
	 * $val correspond à l'ordonnée exprimée en pixel de la ligne
	 */
	function AddRow($color, $val){
		if($val > $this->hautBerge){
			$ligneDessin = '$("#dessinSection").drawLine(0,'.$val.','.$this->largeurDessin.','.$val.', {color: "'.$color.'", stroke: 0.5});';
		}
		else{
			$ligneDessin = '$("#dessinSection").drawLine(0,'.$val.','.$this->largeurDessin.','.$val.', {color: "'.$color.'", stroke: 2});';
		}
		
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
		return $result;
	}
	
	// Retourne le dessin de la section
	function GetDessinSection(){
		// On transforme nos valeur en pixels
		$mesDonnees = $this->transformeValeur($this->donnees); 
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
					$(document).ready(function(){
					$("#dessinSection").drawLine(0,'.$this->hauteurDessin.','.$this->largeurDessin.','.$this->hauteurDessin.');
					$("#dessinSection").drawLine(0,0,0,'.$this->hauteurDessin.');
					$("#dessinSection").drawLine('.$this->largeurDessin.',0,'.$this->largeurDessin.','.$this->hauteurDessin.');';
		
		// On ajoute les différentes lignes avec couleur + valeur
		foreach($mesDonnees as $cle=>$valeur){
			$dessin.= $this->AddRow($valeur[1], $valeur[0]);
		}
		
		$dessin.= '});
			</script>';

		//Div qui va contenir notre dessin de section
		$dessin.='<div id="dessinSection" class="canvas">';
		
		// Pour alterner le placement des libellés
		$droiteGauche = 0;
		// On rajoute les différents libelles avec la couleur qui va bien
		foreach($mesDonnees as $cle=>$valeur){
			$placement = ($droiteGauche%2 == 0)?'left: 0px':'right: 0px;';
			$dessin.= '<p style="position: absolute; top:'.($valeur[0]-8).'px;'.$placement.'; width: auto; display: inline-block; color:'.$valeur[1].'">'.$cle.' = '.round($this->donnees[$cle], $this->precision).'</p>';
			$droiteGauche++;			
		}
		
		$dessin.= '</div>';
	
		return $dessin;
	}
}
?>
