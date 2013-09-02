<?php
////////////////////////////////////////////////////
// PDF_Label 
//
// Classe afin d'éditer au format PDF des étiquettes
//
// Basé sur le  script deLaurent PASSEBECQ <lpasseb@numericable.fr>
////////////////////////////////////////////////////

require_once('fpdf.php');

class PDF_Label extends FPDF {

	// Propriétés privées
	var $_Avery_Name	= '';				// Nom du format de l'étiquette
	var $_Margin_Left	= 0;				// Marge de gauche de l'étiquette
	var $_Margin_Top	= 0;				// Marge en haut de la page avant la première étiquette
	var $_X_Space 		= 0;				// Espace horizontal entre 2 bandes d'étiquettes
	var $_Y_Space 		= 0;				// Espace vertical entre 2 bandes d'étiquettes
	var $_X_Number 		= 0;				// Nombre d'étiquettes sur la largeur de la page
	var $_Y_Number 	= 0;				// Nombre d'étiquettes sur la hauteur de la page
	var $_Width 			= 0;				// Largeur de chaque étiquette
	var $_Height 			= 0;				// Hauteur de chaque étiquette
	var $_Char_Size		= 10;				// Hauteur des caractères
	var $_Line_Height	= 10;				// Hauteur par défaut d'une ligne
	var $_Metric 			= 'mm';			// Unité de mesure des étiquettes. Aidera à calculer les bonnes valeurs
	var $_Metric_Doc 	= 'mm';			// Unité de mesure du document
	var $_Font_Name	= 'Arial';			// Nom de la police (voir dossier font/)

	var $_COUNTX = 1;
	var $_COUNTY = 1;

	// convertit les unites (in -> mm, mm -> in)
	// $src and $dest doivent etre 'in' ou 'mm'
	function _Convert_Metric ($value, $src, $dest) {
		if ($src != $dest) {
			$tab['in'] = 39.37008;
			$tab['mm'] = 1000;
			return $value * $tab[$dest] / $tab[$src];
		} else {
			return $value;
		}
	}

	// Give the height for a char size given.
	function _Get_Height_Chars($pt) {
		// Tableau de concordance entre la hauteur des caractères et de l'espacement entre les lignes
		$_Table_Hauteur_Chars = array(
			6=>2, 
			7=>2.5, 
			8=>3, 
			9=>4, 
			10=>5, 
			11=>6, 
			12=>7, 
			13=>8, 
			14=>9, 
			15=>10
		);
		if (in_array($pt, array_keys($_Table_Hauteur_Chars))) {
			return $_Table_Hauteur_Chars[$pt];
		} else {
			return 100; // There is a prob..
		}
	}

	function _Set_Format($format) {
		$this->_Metric 		= $format['metric'];
		$this->_Avery_Name= $format['name'];
		$this->_Margin_Left	= $this->_Convert_Metric ($format['marginLeft'], $this->_Metric, $this->_Metric_Doc);
		$this->_Margin_Top	= $this->_Convert_Metric ($format['marginTop'], $this->_Metric, $this->_Metric_Doc);
		$this->_X_Space 	= $this->_Convert_Metric ($format['SpaceX'], $this->_Metric, $this->_Metric_Doc);
		$this->_Y_Space 	= $this->_Convert_Metric ($format['SpaceY'], $this->_Metric, $this->_Metric_Doc);
		$this->_X_Number 	= $format['NX'];
		$this->_Y_Number 	= $format['NY'];
		$this->_Width 		= $this->_Convert_Metric ($format['width'], $this->_Metric, $this->_Metric_Doc);
		$this->_Height	 	= $this->_Convert_Metric ($format['height'], $this->_Metric, $this->_Metric_Doc);
		$this->Set_Font_Size($format['font-size']);
	}

	function PDF_Label ($format, $unit='mm', $posX=1, $posY=1) {
		
		$Tformat = $format;
		
		parent::FPDF('P', $Tformat['metric'], $Tformat['paper-size']);
		$this->_Set_Format($Tformat);
		$this->Set_Font_Name('Arial');
		$this->SetMargins(0,0); 
		$this->SetAutoPageBreak(false); 
		
		$this->_Metric_Doc = $unit;
		// Permet de commencer l'impression à l'étiquette désirée dans le cas où la page a déjà servi
		if ($posX > 1) $posX--; else $posX=0;
		if ($posY > 1) $posY--; else $posY=0;
		if ($posX >=  $this->_X_Number) $posX =  $this->_X_Number-1;
		if ($posY >=  $this->_Y_Number) $posY =  $this->_Y_Number-1;
		$this->_COUNTX = $posX;
		$this->_COUNTY = $posY;
	}

	// Méthode pour modifier la taille des caractères
	// Cela modifiera aussi l'espace entre chaque ligne
	function Set_Font_Size($pt) {
		if ($pt > 3) {
			$this->_Char_Size = $pt;
			$this->_Line_Height = $this->_Get_Height_Chars($pt);
			$this->SetFontSize($this->_Char_Size);
		}
	}

	// Methode pour changer le nom de la police
	function Set_Font_Name($fontname) {
		if ($fontname != '') {
			$this->_Font_Name = $fontname;
			$this->SetFont($this->_Font_Name);
		}
	}

	// On imprime une étiqette
	function Add_PDF_Label($texte) {
		// On est sur une nouvelle page, donc on doit ajouter une page
		if (($this->_COUNTX ==0) and ($this->_COUNTY==0)) {
			$this->AddPage();
		}

		$_PosX = $this->_Margin_Left+($this->_COUNTX*($this->_Width+$this->_X_Space));
		$_PosY = $this->_Margin_Top+($this->_COUNTY*($this->_Height+$this->_Y_Space));
		$this->SetXY($_PosX+3, $_PosY+3);
		$this->MultiCell($this->_Width, $this->_Line_Height, $texte);
		$this->_COUNTY++;

		if ($this->_COUNTY == $this->_Y_Number) {
			// Si on est en bas de page, on remonte le 'curseur' de position
			$this->_COUNTX++;
			$this->_COUNTY=0;
		}

		if ($this->_COUNTX == $this->_X_Number) {
			// Si on est en bout de page, alors on repart sur une nouvelle page
			$this->_COUNTX=0;
			$this->_COUNTY=0;
		}
	}

}
?>
