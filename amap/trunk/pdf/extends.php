<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


define('FPDF_FONTPATH','font/');
include_spip('pdf/pdf_table');
include_spip('inc/charsets');

class PDF extends PDF_Table {


var $ajoute_titre;
var $titre;

	function PDF($orientation='P',$unite='mm',$format='A4',$ajoute_titre=true){
		$this->ajoute_titre=$ajoute_titre;
		$this->FPDF($orientation, $unite, $format);
	}
	
	function Header(){
		//Titre
		if($this->ajoute_titre)
		{
			$this->SetFont('Arial','',10);
			$this->Cell(0,6,_T('amap:liste_paniers'),0,1,'L');
			$this->SetFont('Arial','B',14);
			$this->Cell(0,6,(html_entity_decode($this->titre)),0,1,'C');
			$this->Ln(10);
		//Imprime l'en-t�te du tableau si n�cessaire
		}
		parent::Header();
	}

	function TitreChapitre($num, $libelle){
		// Titre
		$this->SetFont('Arial','',10);
		//$this->SetFillColor(200,220,255);
		$this->Cell(0,6,html_entity_decode($libelle),0,1,'L');
		$this->Ln(10);
		// Sauvegarde de l'ordonnée
		$this->y0 = $this->GetY();
	}


	/* fonction ajoute pour Associaspip */
	/* alors que la fonction Query de base prend uniquement une query sql, celle-ci permet d'ajouter un tableau et le nom d'un champs sur lequel */
	/* faire la jointure avec la query existente. Cela permet d'ajouter a la query (par un array_merge qui peut ecraser des champs si ils sont   */
	/* nommes comme ceux des tables) un tableau qui sera donc ajoute a chaque ligne de data retournee par le fetch de la query sql.              */
	/* l'interet est de pouvoir faire des traitement et affichers de donnees traites apres une query (dans notre cas un concat fait en php et qui*/
	/* fonctionne donc aussi bien en mysql qu'en postgresql */
	
	/* le code reprend donc l'integralite du code de Query mais ajoute un merge_array sur chaque ligne de donnees retournee par le fetch */
	/* le parametre data doit donc etre un tableau de la forme: valeur_champ_jointure => array(champs1=>valeur, champs2=>valeur, ..)  afin d'inserer */
	/* dans le resultat de la requete les champs champs1 et champ2 en jointure = sur le champs fourni dans l'autre parametre */
	function Query_extended($res, $prop=array(), $data=array(), $champ_jointure) {
		//Traite les proprietes
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
		//Imprime l'en-t�te
		$this->TableHeader();
		//Imprime les lignes
		$this->SetFont('Arial','',8);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;
		/* partie du code modifiee pour etendre la fonction Query */
		while($row=sql_fetch($res)) {
			if (is_array($data[$row[$champ_jointure]])) $row = array_merge($row,$data[$row[$champ_jointure]]);
			$this->RowMulticell($row);
		}
		/* fin de partie du code modifie pour etendre la fonction Query */
		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		$this->aCols=array();
	}

	/* fonction qui permet d'utiliser des multicells et non cells pour avoir du texte sur plusieurs lignes dans les cases  */
	function RowMultiCell($data){
		/* on commence par calculer le nombre de lignes de chaque cellule et le max */
		$max_nb_lignes = 1;
		$nb_lignes = array();
		foreach($this->aCols as $col) {
			$lignes = explode("\n",$data[$col['f']]);
			$nb_lignes[$col['f']] = count($lignes);
			foreach ($lignes as $ligne) {
				$nb_lignes[$col['f']] += floor(($this->GetStringWidth($ligne)+4)/$col['w']); /* le +4 c'est le padding a 2 dans pdf_adherents */
			}
			$max_nb_lignes = ($nb_lignes[$col['f']]>$max_nb_lignes)?$nb_lignes[$col['f']]:$max_nb_lignes;
		}

		$x = $this->TableX;
		$y = $this->GetY();
		$this->SetX($x);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		$y = $this->GetY();
		
		foreach($this->aCols as $col) {
			$this->SetX($x);			
			$this->MultiCell($col['w'],5*$max_nb_lignes/$nb_lignes[$col['f']],utf8_decode($data[$col['f']]),1,$col['a'],$fill);
			$this->SetY($this->GetY()-5*$max_nb_lignes);
			$x += $col['w'];
			

		}
		$this->ColorIndex=1-$ci;
		$this->Ln(5*$max_nb_lignes);
	}
}
?>
