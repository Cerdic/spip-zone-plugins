<?php
////////////////////////////////////////////////////
// PDF_Table
//
// Classe afin d'éditer des tableaux au format PDF 
//
// Basé sur les fonctions d'Olivier : oliver@fpdf.org
//
////////////////////////////////////////////////////

require('fpdf.php');

class PDF_Table extends FPDF{
	var $ProcessingTable=false;
	var $aCols=array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;

	function Header(){ 	//Imprime l'en-tête du tableau si nécessaire
		if($this->ProcessingTable)
			$this->TableHeader();
	}

	function TableHeader() {
		$this->SetFont('Arial','B',10); 								//Police Arial gras 10
		$this->SetX($this->TableX);
		$fill=!empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
		$this->Ln();
	}
	
	function Footer(){
		$this->SetY(-15);										//Positionnement à 1,5 cm du bas
		$this->SetFont('Arial','',8); 								//Police Arial 8
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');	//Numéro de page à droite
	}
	
	function Row($data){
		$this->SetX($this->TableX);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],5,$data[$col['f']],1,0,$col['a'],$fill);
		$this->Ln();
		$this->ColorIndex=1-$ci;
	}

	function CalcWidths($width,$align){    
		//Calcule les largeurs des colonnes
		$TableWidth=0;
		foreach($this->aCols as $i=>$col){
			$w=$col['w'];
			if($w==-1)
				$w=$width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w=$w/100*$width;
			$this->aCols[$i]['w']=$w;
			$TableWidth+=$w;
		}
		//Calcule l'abscisse du tableau
		if($align=='C')
			$this->TableX=max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX=$this->lMargin;
	}

	function AddCol($field=-1,$width=-1,$caption='',$align='L'){
		//Ajoute une colonne au tableau
		if($field==-1)
			$field=count($this->aCols);
		$this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align);
	}

	function Table($query,$prop=array()){
		//Exécute la requête
		$res=spip_query($query);
		//Traite les propriétés
		if(!isset($prop['width']))
			$prop['width']=0;
		if($prop['width']==0)
			$prop['width']=$this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align']='C';
		if(!isset($prop['padding']))
			$prop['padding']=$this->cMargin;
		$cMargin=$this->cMargin;
		$this->cMargin=$prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor']=array();
		$this->HeaderColor=$prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1']=array();
		if(!isset($prop['color2']))
			$prop['color2']=array();
		$this->RowColors=array($prop['color1'],$prop['color2']);
		//Calcule les largeurs des colonnes
		$this->CalcWidths($prop['width'],$prop['align']);
		//Imprime l'en-tête
		$this->TableHeader();
		//Imprime les lignes
		$this->SetFont('Arial','',8);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;
		while($row=spip_fetch_array($res))
			$this->Row($row);
		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		$this->aCols=array();
	}
}
?>
