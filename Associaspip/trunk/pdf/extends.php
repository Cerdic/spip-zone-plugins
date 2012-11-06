<?php
////////////////////////////////////////////////////
// PDF_Table
//
// Methodes pour editer des tableaux au format PDF
// bases sur les fonctions d'Olivier : oliver@fpdf.org
// http://www.fpdf.cz/skripty/tabulky-s-mysql/
//
// Methodes d'initialisation propres a Associaspip
//
////////////////////////////////////////////////////

if (!defined('_ECRIRE_INC_VERSION'))
	return;

define('FPDF_FONTPATH', 'font/');
include_spip('fpdf');
include_spip('inc/charsets');

class PDF extends FPDF {

	var $ajoute_titre;
	var $titre;

	var $ProcessingTable = FALSE;
	var $aCols = array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;

	function PDF($avec_titre=TRUE, $format='', $unite='', $orientation='') {
		$this->ajoute_titre = $avec_titre;
		$this->FPDF(
			($orientation?$orientation:($GLOBALS['association_metas']['fpdf_orientation']?$GLOBALS['association_metas']['fpdf_orientation']:'P')),
			($unite?$unite:($GLOBALS['association_metas']['fpdf_unit']?$GLOBALS['association_metas']['fpdf_unit']:'mm')),
			($format?$format:($GLOBALS['association_metas']['fpdf_format']?$GLOBALS['association_metas']['fpdf_format']:( ($GLOBALS['association_metas']['fpdf_widht'] AND $GLOBALS['association_metas']['fpdf_height'])?array($GLOBALS['association_metas']['fpdf_widht'],$GLOBALS['association_metas']['fpdf_height']):'A4')))
		);
		// reinitialiser les marges
		if ( is_numeric($GLOBALS['association_metas']['fpdf_marginl']) AND is_numeric($GLOBALS['association_metas']['fpdf_margint']) )
			$this->SetMargins($GLOBALS['association_metas']['fpdf_marginl'], $GLOBALS['association_metas']['fpdf_margint'] );
		// meta pour le fichier PDF
		$this->SetAuthor('Associaspip');
		$this->SetCreator('FPDF');
		$this->SetTitle(utf8_decode($GLOBALS['association_metas']['nom']));
		$this->SetSubject(utf8_decode(html_entity_decode($this->titre)));
		// typo par defaut
		$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), '', 12);
	}

	// modifier les caracteristique de la police en cours d'usage
	function AdaptFont($size, $attr='', $name='') {
		if (!$attr AND !$name)
			$this->SetFontSize($size);
		else
			$this->SetFont(($name?$name:($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial')), $attr, $size);
	}

	// conversion des couleurs sous forme hexadecimal-compate (web) en tableau decimales
	// adapte de http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
	// plus complet que http://logiciels.meteo-mc.fr/php-convertir-couleur.php
	// car prend en compte la forme courte : http://en.wikipedia.org/wiki/Web_colors#Web-safe_colors
	function hex2rgb($hex, $tostring=FALSE) {
		$hex = str_replace("#", "", $hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}

#		$rgb = array($r, $g, $b); return $tostring?implode(',', $rgb):$rgb;
		return $tostring?"$r,$g,$b":array($r, $g, $b);
	}

    // Haut de pages : redefinition de FPDF qui est directement appele par FPDF::AddPage()
    //@ http://www.id.uzh.ch/cl/zinfo/fpdf/doc/header.htm
    //!\ Adapter la marge haute (et la hauteur utile) des pages en consequence
	function Header() {
		if($this->ajoute_titre) { //Titre
			$this->AdaptFont(10); // Police 10pt
			$this->Cell(0,6,utf8_decode($GLOBALS['association_metas']['nom']),0,1,'L'); // Nom de l'association a gauche
			$this->AdaptFont(14,'B'); // Police 14pt gras
			$this->Cell(0,6,utf8_decode(html_entity_decode($this->titre)),0,1,'C'); // Titre du document au centre
			$this->Ln(10); // Saut de ligne : 10pt de haut
		}
		if($this->ProcessingTable)
			$this->TableHeader(); // Imprime l'en-tete du tableau si necessaire
	}

    // Pied de pages : redefinition de FPDF::Footer() qui est automatiquement appele par FPDF::AddPage() et FPDF::Close() !
    //@ http://www.id.uzh.ch/cl/zinfo/fpdf/doc/footer.htm
    //!\ Adapter la marge basse (et la hauteur utile) des pages en consequence
	function Footer() {
		$this->SetY(-15); // Positionnement a 1,5 cm du bas
		$this->AdaptFont(8); // Police 8pt
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R'); // Numero de page a droite
	}

	// Ligne de titre du tableau
	function TableHeader() {
		$this->AdaptFont(10,'B'); //Police gras 10pt
		$this->SetX($this->TableX);
		$fill = !empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,utf8_decode(html_entity_decode($col['c'])),1,0,'C',$fill);
		$this->Ln();
	}

	// Ligne standard du tableau
	function Row($data) {
		$this->SetX($this->TableX);
		$ci = $this->ColorIndex;
		$fill = !empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],5,utf8_decode(html_entity_decode($data[$col['f']])),1,0,$col['a'],$fill);
		$this->Ln();
		$this->ColorIndex = 1-$ci;
	}

	// Permet d'utiliser des multicells et non cells pour avoir du texte sur plusieurs lignes dans les cases
	function RowMultiCell($data) {
		// on commence par calculer le nombre de lignes de chaque cellule et le max
		$max_nb_lignes = 1;
		$nb_lignes = array();
		foreach($this->aCols as $col) {
			$lignes = explode("\n",$data[$col['f']]);
			$nb_lignes[$col['f']] = count($lignes);
			foreach ($lignes as $ligne) {
				$nb_lignes[$col['f']] += floor(($this->GetStringWidth($ligne))/($col['w']-4)); // le -4 c'est le padding a 2 dans pdf_adherents
			}
			$max_nb_lignes = ($nb_lignes[$col['f']]>$max_nb_lignes)?$nb_lignes[$col['f']]:$max_nb_lignes;
		}

		$x = $this->TableX;
		$y = $this->GetY();
		$this->SetX($x);
		$ci = $this->ColorIndex;
		$fill = !empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		$y = $this->GetY();
		foreach($this->aCols as $col) {
			$this->SetX($x);
			$this->MultiCell($col['w'],5*$max_nb_lignes/$nb_lignes[$col['f']],utf8_decode(html_entity_decode($data[$col['f']])),1,$col['a'],$fill);
			$this->SetY($this->GetY()-5*$max_nb_lignes);
			$x += $col['w'];
		}
		$this->ColorIndex = 1-$ci;
		$this->Ln(5*$max_nb_lignes);
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
			$field = count($this->aCols);
		$this->aCols[] = array('f'=>$field, 'c'=>utf8_decode(html_entity_decode($caption)), 'w'=>$width, 'a'=>$align);
	}

	/* Fonction modifiee pour Associaspip
	 *
	 * Alors que la fonction Query de base prend uniquement une requete sql, celle-ci permet d'ajouter un tableau et le nom d'un champs sur lequel
	 * faire la jointure avec la requete existente. Cela permet d'ajouter a la requete (par un array_merge qui peut ecraser des champs si ils sont
	 * nommes comme ceux des tables) un tableau qui sera donc ajoute a chaque ligne de data retournee par le fetch de la requete sql.
	 * L'interet est de pouvoir faire des traitement et afficher les donnees traites apres une query (dans notre cas un concat fait en php et qui
	 * fonctionne donc aussi bien en mysql qu'en postgresql
	 *
	 * Le code reprend donc l'integralite du code de Query mais ajoute un merge_array sur chaque ligne de donnees retournee par le fetch
	 * le parametre data doit donc etre un tableau de la forme: valeur_champ_jointure => array(champs1=>valeur, champs2=>valeur, ..)  afin d'inserer
	 * dans le resultat de la requete les champs champs1 et champ2 en jointure = sur le champs fourni dans l'autre parametre
	**/
	function Query($res, $data=array(), $champ_jointure='', $prop=array() ) {
		// Traite les proprietes
		if(!isset($prop['width']))
			$prop['width'] = 0;
		if($prop['width']==0)
			$prop['width'] = $this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align'] = $GLOBALS['association_metas']['fpdf_tablealign']?$GLOBALS['association_metas']['fpdf_tablealign']:'C';
		if(!isset($prop['padding']))
			$prop['padding'] = $GLOBALS['association_metas']['fpdf_marginc']?$GLOBALS['association_metas']['fpdf_marginc']:2;
		$cMargin = $this->cMargin;
		$this->cMargin = $prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor'] = $this->hex2rgb($GLOBALS['association_metas']['fpdf_rowhead']);
		$this->HeaderColor = $prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1'] = $this->hex2rgb($GLOBALS['association_metas']['fpdf_roweven']);
		if(!isset($prop['color2']))
			$prop['color2'] = $this->hex2rgb($GLOBALS['association_metas']['fpdf_rowodd']);
		// Initialiser l'affichage des donnees
		$this->RowColors = array($prop['color1'],$prop['color2']); // Indique l'alternance des couleurs de fond
		$this->CalcWidths($prop['width'],$prop['align']); // Calcule les largeurs des colonnes
		$this->TableHeader(); // Imprime l'en-tete
		$this->AdaptFont(8); // Police 8pt
		// Imprime les lignes
		$this->ColorIndex = 0;
		$this->ProcessingTable = TRUE;
		// partie du code modifiee pour etendre la fonction Query
		while($row = sql_fetch($res)) {
			if (is_array($data[$row[$champ_jointure]]))
				$row = array_merge($row,$data[$row[$champ_jointure]]);
			$this->RowMulticell($row);
		}
		// fin de partie du code modifie pour etendre la fonction Query
		$this->ProcessingTable = FALSE;
		$this->cMargin = $cMargin;
		$this->aCols = array();
	}

	// idem que Query sauf qu'on lui passe le texte de la requete SQL et non la ressource du resultat de la requete
	function Table($query, $data=array(), $champ_jointure='', $prop=array() ) {
		$this->Query(spip_query($query), $data, $champ_jointure, $prop); // execute la requete
	}

}

?>