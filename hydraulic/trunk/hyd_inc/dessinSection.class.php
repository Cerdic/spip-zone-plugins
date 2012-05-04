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
    private $mesCouleurs = array('red', 'blue', 'orange', 'green', 'grey', 'black');  // Couleur des différentes lignes
    private $sectionClass;
    private $donnees = array();
    private $rValMax = 0; // Hauteur maxi en m à figurer dans le dessin
    private $rSnXmax = 0; // Largeur maximum en m à figurer dans le dessin

    function __construct($hauteur, $largeur, &$section, $lib_data) {
        $this->hauteurDessin = (real) $hauteur;
        $this->largeurDessin = (real) $largeur;
        $this->sectionClass = &$section;
        $this->donnees = $lib_data;
        // On détermine la valeur la plus grande dans le tableau
        foreach($this->donnees as $val){
            if($val > $this->ValMax){
                $this->ValMax = $val;
            }
        }
        //spip_log($this,'hydraulic');
    }

    /**
     * Rajoute une ligne à notre dessin.
     * $color correspond à la couleur de la ligne
     * $val correspond à l'ordonnée exprimée en pixel de la ligne
     */
    function AddRow($color, $val){
        $ligneDessin = '$("#dessinSection").drawLine(0,'.$val.','.$this->largeurDessin.','.$val.', {color: "'.$color.'"});';
        return $ligneDessin;
    }

    /**
     * Convertit un tirant d'eau en mètre en une ordonnée en pixels
     */
    private function GetDessinY($val) {
        // La valeur maximum de l'échelle  en px correspondant à 10% de la hauteur afin de faire plus propre
        return round($this->hauteurDessin * (1- 0.9*$val/$this->ValMax), 1)-2;
    }

    /**
     * Convertit une largeur en mètre en une abscisse en pixels
     * @param $Axe détermine si le pixel est à droite (1) ou à gauche (-1) de l'axe de symétrie
     * @return Abscisse en pixel à dessiner
     */
    private function GetDessinX($val,$Axe) {
        return round(($this->largeurDessin-14) * (1/2 + $Axe*$val/$this->SnXmax), 1)+7;
    }

    /**
     * Transforme le tableau de tirants d"eau et charges à afficher en pixel + attribution des couleurs
     */
    function transformeValeur($tabDonnees){
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
        // Récupération des coordonnées de la section à dessiner
        $tCoordSn = $this->sectionClass->DessinCoordonnees();

        // Détermination de la largeur max de la section
        $this->SnXmax = max($tCoordSn['x'])*2;

        // Dessin des verticales au dessus des berges
        $LargeurBerge = $this->sectionClass->CalcGeo('B')/2;
        $xBergeGauche = $this->GetDessinX($LargeurBerge,-1);
        $xBergeDroite = $this->GetDessinX($LargeurBerge,1);
        $dessin.= '$("#dessinSection").drawLine('.$xBergeGauche.', 0, '.$xBergeGauche.','.$diffHautBerge.', {stroke: 1});
                   $("#dessinSection").drawLine('.$xBergeDroite.', 0,'.$xBergeDroite.','.$diffHautBerge.', {stroke: 1});';

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
        $dessin.=   '$("#dessinSection").drawPolyline(
                        ['.implode(',',$tSnX).'],
                        ['.implode(',',$tSnY).'], {stroke: 4});';

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
