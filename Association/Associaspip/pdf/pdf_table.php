<?php
////////////////////////////////////////////////////
// PDF_Table
//
// Classe afin d'editer des tableaux au format PDF
//
// Base sur les fonctions d'Olivier : oliver@fpdf.org
//
////////////////////////////////////////////////////

if (!defined('_ECRIRE_INC_VERSION'))
	return;

define('FPDF_FONTPATH', 'font/');
include_spip('fpdf');
include_spip('inc/charsets');

class PDF_Table extends FPDF{
	var $ProcessingTable = false;
	var $aCols = array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;

	function Header(){ 	//Imprime l'en-tete du tableau si necessaire
		if($this->ProcessingTable)
			$this->TableHeader();
	}

	// modifier les caracteristique de la police en cours d'usage
	function AdaptFont($size, $attr='', $familly=$GLOBALS['association_metas']['fpdf_font']) {
		$this->SetFont($familly?$familly:'Arial', $attr, $size);
	}

	function TableHeader() {
		$this->AdaptFont(10,'B'); //Police gras 10pt
		$this->SetX($this->TableX);
		$fill = !empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,utf8_decode($col['c']),1,0,'C',$fill);
		$this->Ln();
	}

	function Footer(){
		$this->SetY(-15); //Positionnement a 1,5 cm du bas
		$this->AdaptFont(8); //Police 8pt
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');	//Numero de page a droite
	}

	function Row($data){
		$this->SetX($this->TableX);
		$ci = $this->ColorIndex;
		$fill = !empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],5,utf8_decode($data[$col['f']]),1,0,$col['a'],$fill);
		$this->Ln();
		$this->ColorIndex = 1-$ci;
	}

	// Calcule les largeurs des colonnes
	function CalcWidths($width, $align) {
		$TableWidth = 0;
		foreach($this->aCols as $i=>$col) {
			$w = $col['w'];
			if($w==-1)
				$w = $width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w = $w/100*$width;
			$this->aCols[$i]['w'] = $w;
			$TableWidth += $w;
		}
		//Calcule l'abscisse du tableau
		if($align=='C')
			$this->TableX = max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX = max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX = $this->lMargin;
	}

	// Ajoute une colonne au tableau
	function AddCol($field=-1, $width=-1, $caption='', $align='L') {
		if($field==-1)
			$field=count($this->aCols);
		$this->aCols[] = array('f'=>$field, 'c'=>html_entity_decode($caption), 'w'=>$width, 'a'=>$align);
	}

	function Table($query, $prop=array()){
		$this->Query(spip_query($query), $prop); // execute la requete
	}

	function Query($res, $prop=array()){
		//Traite les proprietes
		if(!isset($prop['width']))
			$prop['width'] = 0;
		if($prop['width']==0)
			$prop['width'] = $this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align'] = 'C';
		if(!isset($prop['padding']))
			$prop['padding'] = $this->cMargin;
		$cMargin = $this->cMargin;
		$this->cMargin = $prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor'] = array();
		$this->HeaderColor = $prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1'] = array();
		if(!isset($prop['color2']))
			$prop['color2'] = array();
		// Traite les donnees
		$this->RowColors = array($prop['color1'], $prop['color2']);
		$this->CalcWidths($prop['width'], $prop['align']); //Calcule les largeurs des colonnes
		$this->TableHeader(); //Imprime l'en-tete
		//Imprime les lignes
		$this->AdaptFont(8);
		$this->ColorIndex = 0;
		$this->ProcessingTable = true;
		while($row=sql_fetch($res))
			$this->Row($row);
		$this->ProcessingTable = false;
		$this->cMargin = $cMargin;
		$this->aCols = array();
	}

}

?>