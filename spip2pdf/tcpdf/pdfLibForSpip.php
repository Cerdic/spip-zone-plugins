<?php

/**
 * This lib is a simplification of tcpdf targeted to ouput 
 * content from the Spip CMS.
 * 
 * @author Michael Courcy michael.courcy@netapsys.fr
 */


require_once(dirname(__FILE__).'/config/lang/eng.php');
require_once(dirname(__FILE__).'/tcpdf.php');

class PDF_FOR_SPIP extends TCPDF {
	
	//////////////////////////////////////////////
	///////////////// OutputFile /////////////////
	//////////////////////////////////////////////
	/*
	 * The file in which we must output the pdf.
	 */
	var $outputFile;
	
	//////////////////////////////////////////////
	////////////// Columning rules ///////////////
	//////////////////////////////////////////////  
			
	/*
	 * Current column number.
	 */
	var $col=0;
	/*
	 * Max index of the columns.
	 * 
	 * For instance 2 means we have the column 0, 1 and 2. 
	 */
	var $numCol=2;
	/*
	 * Width of a column.
	 */
	var $colWidth = 50;
	/*
	 * Distance between each column.
	 */
	var $colDistance = 5;
	

	
	/**
	 * Debug activation, when enabled all current position,
	 * x and y position are output on the pdf to help debuggiing.
	 *
	 * @var boolean
	 */
	var $debug = false;

	///////////////////////////////////////
	/////  Logo header definition /////////
	/////////////////////////////////////// 
	
	/**
	 * how to align the logo
	 */
	var	$header_logo_align = "left";

	////////////////////////////////////////
	//////  Title Header definition ////////
	////////////////////////////////////////
	/**
	* font for the title
	*/
	var $header_title_font = 'times';
	/**
	 * title style
	 *
	 * TODO list possible value
	 */
	var $header_title_style = '';
	/**
	 * title color
	 *
	 * TODO list possible value
	 */
	var $header_title_color = '#000000';
	/**
	 * The size of title header
	 *
	 * @var number
	 */
	var $header_title_size = 0;
	/**
	 * How should we align the title
	 *
	 * @var string
	 */
	var $header_title_align = 'left';

	/////////////////////////////////////////
	//////  String header definition ////////
	/////////////////////////////////////////
	/**
	* font for the title
	*/
	var $header_string_font = 'tahoma';
	/**
	 * title style
	 *
	 * TODO list possible value
	 */
	var $header_string_style = '';
	/**
	 * title color
	 *
	 * TODO list possible value
	 */
	var $header_string_color = '#000000';
	/**
	 * The size of title header
	 *
	 * @var number
	 */
	var $header_string_size = 0;
	/**
	 * How should we align the title
	 *
	 * @var string
	 */
	var $header_string_align = 'left';

	/**
	 * the lowest part of the header, mainly
	 * set by the bottom logo position.
	 */
	var $header_bottom = 0;
	/**
	 * TODO
	 *
	 * @var unknown_type
	 */
	var $header_cell_height = 0;
	/*
	 * Every link we meet during treatment is put in a buffer.
	 */
	var $linkBuffer = array();
	/*
	 * Total number of footNotes, used for adding link index on footNoteIndex
	 */
	var $totalFootNotes = 0;
	
	/**
	 * Initialise the columning layout.
	 *
	 * The colWidth is directly calculated depending of the 
	 * width of the document, the nuber of column and the distance between each column.
	 * 
	 * @param int $numCol the max index of the column 2 fo instance mean 0, 1 and 2 thus three columns.
	 * @param number $colDistance distance between each column
	 */
	public function multiColumn($numCol, $colDistance){
		//reinitialisation phase		
		$this->lMargin = $this->original_lMargin;
		$this->rMargin = $this->original_rMargin;
		$this->SetCol(0);
		
		$this->numCol = $numCol;		
		$this->colDistance = $colDistance;
		$this->colWidth = ($this->fw - $this->numCol * $this->colDistance  - $this->lMargin - $this->rMargin) / ($this->numCol+1);
						
	}
		
	/**
	 * Position the pdf cursor depending of the columning rule setup.
	 * 
	 * Call the first time by writeSpipContent.
	 *
	 * @param int $col
	 */
	public function SetCol($col) {			
	 		//Move position to a column
	 		$this->col=$col;
	 		$x= $this->original_lMargin + $col*($this->colWidth+$this->colDistance);
	 		if($this->rtl){
	 			$this->SetRightMargin($x);
	 		}else{
	 			$this->SetLeftMargin($x);
	 		}
	 		$this->SetX($x);
	 		//echo "SetCol page ".$this->page." x ".$x." y ".$this->getY()." col number ".$col." \n<br/>";
	}
	
	/**
	 * Call by every function that must decide if wether 
	 * or not they should change page it's the  
	 * opportunity place to decide to or to not break page 
	 * and put the content somewhere else. 
	 * 
	 * here in a column layout.
	 * 
	 * @return boolean whether or not we should change page.
	 */
	public function AcceptPageBreak() {
	 		if($this->col < $this->numCol) {
	 			//Go to next column
	 			$this->SetCol($this->col+1);
	 			$this->SetY($this->tMargin);
	 			return false;
	 		}
	 		else {
	 			//Go back to first column and issue page break
	 			$this->SetCol(0);
	 			return true;
	 		}
	}
	
	/**
	 * Fix the main property of the document.
	 *
	 * @param string $creator creator name
	 * @param string $author author name
	 * @param string $title the title of the pdf
	 * @param string $subject subject of the document
	 * @param string $keyword the keyword that qualifies this document
	 */
	function setProperties($creator, $author, $title, $subject, $keyword){
		$this->SetCreator($creator);
		$this->SetAuthor($author);
		$this->SetTitle($title);
		$this->SetSubject($subject);
		$this->SetKeywords($keyword);
	}

	/**
	 * We override this method to call HeaderTitle and HeaderString
	 * and HeaderLogo  because those functions allow us to draw the
	 * header with colors and images.
	 *
	 * header_margin
	 * lMargin
	 * rMargin
	 *
	 */
	function Header() {
		if ($this->print_header) {
				if (!isset($this->original_lMargin)) {
					$this->original_lMargin = $this->lMargin;
				}
				if (!isset($this->original_rMargin)) {
					$this->original_rMargin = $this->rMargin;
				}
				//set current position and draw the title string and logo
				$this->SetXY($this->original_lMargin, $this->header_margin);
				$this->HeaderLogo();
				//if the image overflow the bottom
				//adapt the header_bottom value to start writing after
				//the logo.
				if($this->header_bottom < $this->img_rb_y){
					$this->header_bottom = $this->img_rb_y;
				}
				$this->HeaderTitle();
				//if the title overflow the bottom
				//adapt the header_bottom value to start writing after
				//the title.
				if($this->header_bottom < $this->GetY()){
					$this->header_bottom = $this->GetY();
				}
				$this->HeaderString();
				//if the string overflow the bottom
				//adapt the header_bottom value to start writing after
				//the string.
				if($this->header_bottom<$this->GetY()){
					$this->header_bottom = $this->GetY();
				}
					
				$x = $this->original_lMargin;
				$y = $this->header_margin;
				$y2 = $this->header_bottom;
				$x2 = $this->GetX();
				$this->SetXY($x,max($y,$y2));
				$this->SetXY($this->original_lMargin, $this->tMargin);
			}
		}


	/**
	 * Set the property of the header
	 *
	 * @param number $header_margin the distance betwenn 
	 * @param unknown_type $header_border_color
	 */
	function setHeaderProperties($header_margin, $header_border_color){
		$this->header_margin = $header_margin;
	}

	/**
	 * Setter function
	 *
	 * @param string $header_logo
	 * @param string $header_logo_align
	 * @param number $header_logo_width
	 */
	function setHeaderLogo($header_logo, $header_logo_align, $header_logo_width){
		$this->header_logo 			= $header_logo;
		$this->header_logo_align	= $header_logo_align;
		$this->header_logo_width	= $header_logo_width;
	}

	/**
	 * This method is called by Header() to draw a logo
	 *
	 * After the call to this method one important value will
	 * be set header_x, that will give Hint on where to start
	 * the title and the string
	 *
	 * TODO manage logo centering
	 *
	 * header_logo
	 * header_logo_align
	 * header_logo_width
	 */
	function HeaderLogo(){
		if (($this->header_logo) AND ($this->header_logo != K_BLANK_IMAGE)) {
			//left align
			if(
				empty($this->header_logo_align)
				| $this->header_logo_align=='left'
				| $this->header_logo_align==''
			  )
			{

				$this->Image(	
					K_PATH_IMAGES.$this->header_logo,
					$this->original_lMargin,
					$this->header_margin,
					$this->header_logo_width
				);
			}
			//right align
			else
			{
				$this->Image(	
					K_PATH_IMAGES.$this->header_logo,
					$this->fw - $this->original_rMargin - $this->header_logo_width,
					$this->header_margin,
					$this->header_logo_width
				);
			}
		}
	}

	/**
	 * Setter function
	 *
	 * @param unknown_type $header_title_font
	 * @param unknown_type $header_string_style
	 * @param unknown_type $header_title_color
	 * @param unknown_type $header_title_size
	 * @param unknown_type $header_title_align
	 */
	function setHeaderTitle(	
		$header_title,
		$header_title_font,
		$header_string_style,
		$header_title_color,
		$header_title_size,
		$header_title_align
	) {
			
		$this->header_title			= $header_title;
		$this->header_title_font 	= $header_title_font;
		$this->header_string_style 	= $header_string_style;
		$this->header_title_color 	= $header_title_color;
		$this->header_title_size 	= $header_title_size;
		$this->header_title_align 	= $header_title_align;
			
	}

	/**
	 * Print the header title on the header depending of
	 * the value passed in setHeaderTitle
	 *
	 */
	function HeaderTitle(){
		$cell_height = round((K_CELL_HEIGHT_RATIO * $this->header_title_size) / $this->k, 2);

		$this->SetFont($this->header_title_font, $this->header_title_style, $this->header_title_size);
		$this->SetX($this->calculateHeaderTextPosition());
		$array_rgb = $this->colorTxt2ArrayRGB($this->header_title_color);
		$this->SetTextColor($array_rgb['R'],$array_rgb['G'],$array_rgb['B'], true);
		$width = $this->calculateHeaderTextWidth();
		$this->MultiCell(
			$width,
			$cell_height,
			$this->header_title,
			0,
			$this->header_title_align,
			0
		);

		$this->SetTextColor($this->prevTextColor[0],$this->prevTextColor[1],$this->prevTextColor[2],true);

	}



	/**
	 * setter function for the string header.
	 *
	 * @param ustring $header_string_font
	 * @param string $header_string_style
	 * @param hexaColor $header_string_color
	 * @param number $header_string_size
	 * @param string $header_string_align
	 */
	function setHeaderString(	
		$header_string,
		$header_string_font,
		$header_string_style,
		$header_string_color,
		$header_string_size,
		$header_string_align) 
	{
			
		$this->header_string 		= $header_string;
		$this->header_string_font 	= $header_string_font;
		$this->header_string_style 	= $header_string_style;
		$this->header_string_color 	= $header_string_color;
		$this->header_string_size 	= $header_string_size;
		$this->header_string_align 	= $header_string_align;
			
	}

	/**
	 * Print the header String on the header depending 
	 * of value passed in setHeaderString.
	 *
	 */
	function HeaderString(){
		$cell_height = round((K_CELL_HEIGHT_RATIO * $this->header_string_size) / $this->k, 2);
		$this->SetFont($this->header_string_font, $this->header_string_style, $this->header_string_size);
		$this->SetX($this->calculateHeaderTextPosition());
		$array_rgb = $this->colorTxt2ArrayRGB($this->header_string_color);
		$this->SetTextColor($array_rgb['R'],$array_rgb['G'],$array_rgb['B'], true);
		$width = $this->calculateHeaderTextWidth();

		$this->MultiCell(
			$width,
			$cell_height,
			$this->header_string,
			0,
			$this->header_string_align,
			0
		);
			
		$this->SetTextColor($this->prevTextColor[0],$this->prevTextColor[1],$this->prevTextColor[2],true);
	}

	///////////////////////////////////////
	////// HELPER FUNCTION FOR HEADER /////
	///////////////////////////////////////

	/**
	 * Calculate the remaining width for  header text.
	 */
	function calculateHeaderTextWidth(){
		//we have a logo
		if (($this->header_logo) AND ($this->header_logo != K_BLANK_IMAGE)){
			return
			$this->fw
			- $this->original_rMargin
			- $this->header_logo_width
			- $this->original_lMargin;
		}
		//we don't have a logo
		else{
			return
			$this->fw
			- $this->original_rMargin
			- $this->original_lMargin;
		}
	}

	/**
	 * Return the position of the cell where we must write the content.
	 *
	 * Depending of the logo presence logo position and logo size
	 *
	 */
	function calculateHeaderTextPosition(){
		//we have a logo
		if(($this->header_logo) AND ($this->header_logo != K_BLANK_IMAGE)){
			//if the logo is align on the left
			if($this->header_logo_align=='left'){
				return $this->original_lMargin + $this->header_logo_width;
			}else{
				return $this->original_lMargin;
			}
		}
		//we dont have logo
		else{
			return $this->original_lMargin;
		}
	}
	
		/**
	 	 * This method is used to render the page footer. 
	 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
		 */
		public function Footer() {
			if ($this->print_footer) {
				
				if (!isset($this->original_lMargin)) {
					$this->original_lMargin = $this->lMargin;
				}
				if (!isset($this->original_rMargin)) {
					$this->original_rMargin = $this->rMargin;
				}
				
				//set font
				$this->SetFont($this->footer_font[0], $this->footer_font[1] , $this->footer_font[2]);
				//set style for cell border
				$prevlinewidth = $this->GetLineWidth();
				$line_width = 0.3;
				$this->SetLineWidth($line_width);
				$this->SetDrawColor(0, 0, 0);
				
				$footer_height = round((K_CELL_HEIGHT_RATIO * $this->footer_font[2]) / $this->k, 2); //footer height
				//get footer y position
				$footer_y = $this->h - $this->footer_margin - $footer_height;
				//set current position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $footer_y);
				} else {
					$this->SetXY($this->original_lMargin, $footer_y);
				}
				
				//print document barcode
				if ($this->barcode) {
					$this->Ln();
					$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin)/3); //max width
					$this->writeBarcode($this->GetX(), $footer_y + $line_width, $barcode_width, $footer_height - $line_width, "C128B", false, false, 2, $this->barcode);
				}
				
				$pagenumtxt = $this->l['w_page']." ".$this->PageNo().' / {nb}';
				
				$this->SetY($footer_y); 
				
				//Print page number
				if ($this->rtl) {
					$this->SetX($this->original_lMargin);
					$this->Cell($this->fw - $this->original_lMargin - $this->original_rMargin, $footer_height, $pagenumtxt, 'T', 0, 'left');
				} else {
					$this->SetX($this->original_lMargin);
					$this->Cell($this->fw - $this->original_lMargin - $this->original_rMargin, $footer_height, $pagenumtxt, 'T', 0, 'right');
				}
				// restore line width
				$this->SetLineWidth($prevlinewidth);
			}
		}
	
	/**
	 * This function replace writeHtmlCell as writeHTMLCell does not support 
	 * the justification of content. Indeed it's a rather difficult algorithm
	 * to manage justification of text with html content inside. 
	 * 
	 * The goal of this function is much more modest than the capacity of 
	 * a browser to justify content with html inside. 
	 * 
	 * Things are processed this way, taking in account that the content come from
	 * the spip CMS : 
	 * 
	 * 1) Convert the "puces.gif" in regular plain <li> tag
	 * 2) Convert <br> and <p> in carriage return ie \n
	 * 3) Convert all the links and create a buffer of link that will 
	 * be outputted at the end of the document.
	 * 4) Clean all the unsupported tag ie : other than <li> or <img>
	 * 5) Split the content  through the supported tag <li> and <img>
	 * 6) For each token
	 * 	if it's a token li write a li like content
	 *  if it's an img pass the line write the img and pass the line again, the spip webmaster as the responsability 
	 * 		to handle the size of the image though a filter like "reduire_image" 
	 *  if it's plain text write the plain text using MultiCell with the given
	 * align value
	 * 
	 * Once this operation is over we ouput the link buffer and reinitialise it 
	 * for the next call.
	 * 
	 * Note that the width of content depend on the the width of the document
	 * and could be altered by the setCol and acceptPageBreak overriden function.
	 * 
	 * @param string $html the html content to output
	 * @param string $align how to align the content the different value are left, right or justify, default is justify.
	 */
	function writeSpipContent($html,$align="justify"){
		//clean entity
		$html = $this->convert_entity($html); 
		//convert the puces 
		$html = $this->convertPuce($html);
		//convert the br and p in carriage return
		$html = $this->convertBrAndP($html);
		//manage the footnotes
		$html = $this->createFootNoteBuffer($html);
		//create the link buffer
		$html = $this->createLinkBuffer($html);
		//clean the document from unwanted tag
		$html = $this->cleanUnwantedTag($html);
		//split the content allowed tag 
		$tokens = $this->tokenizeContent($html);
		if($this->debug){
			print_r($tokens);
		}else{
			foreach($tokens as $token){
				if($token['type']=="li"){
					if($this->rtl){
						$this->writeLi($token,"right");
					}else{
						$this->writeLi($token,"left");
					}					
				}elseif($token['type']=="h3"){
					$this->writeH3($token,"center");
				}elseif($token['type']=="img"){
					$this->writeImage($token);
				}elseif($token['type']=="plain"){
					$this->writePlain($token,$align);
				}
			}
		}
		//finally output the link buffer at 
		//the end of the text
		$this->writeLinkBuffer();			
	}
	/**
	 * Output a li in the pdf
	 *
	 * @param array $token describe the token that hold the li
	 * @param string $align align rule to apply (left,right,justify)
	 */
	function writeLi($token,$align){
		//TODO Manage RTL
		$text = " - ".trim($token['content']);
		$this->MultiCell($this->colWidth,5,$text,0,$align);
	}
	
	/**
	 * Output a h3 in the pdf
	 *
	 * @param array $token describe the token that hold the h3
	 * @param string $align align rule to apply (left,right,justify)
	 */
	function writeH3($token,$align){
		$text = $token['content'];
		$currentFontSize = $this->FontSize;
		$this->tempfontsize = $this->FontSizePt;
		$this->SetFontSize(PDF_INTERTITRE_SIZE);
		$this->setStyle('b', true);
		$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
		
		$this->MultiCell($this->colWidth,5,$text,0,$align);
		
		$currentFontSize = $this->FontSize;
		$this->SetFontSize($this->tempfontsize);
		$this->tempfontsize = $this->FontSizePt;
		$this->setStyle('b', false);
		$this->Ln();
		$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
	}
	
	/**
	 * Output an image in the pdf
	 *
	 * @param array $token describe the token that hold the image 
	 */
	function writeImage($token){		
		if(!isset($token['width'])) {
			$token['width'] = 0;
		}
		if(!isset($token['height'])) {
			$token['height'] = 0;
		}
		$width = $this->pixelsToMillimeters($token['width']);
		$height = $this->pixelsToMillimeters($token['height']);
		
		if($width > $this->colWidth){
			//apply ratio
			$ratio = $this->colWidth / $width;
			$width = $this->colWidth;
			$height = $height * $ratio;
		}
		
		$this->Image($token['src'], $this->GetX(),$this->GetY(), $width, $height, '', '', 'N');
	}
	
	/**
	 * Output plain text in the pdf
	 *
	 * @param array $token describe the token that hold the plain text
	 * @param string $align align rule to apply (left,right,justify)
	 */
	function writePlain($token,$align){
		//echo "writePlain page ". $this->page." margin ".$this->rMargin. " x ".$this->GetX(). " y ".$this->GetY()." token ".$token['content']." <br/>\n";
		$text = $token['content'];
		$this->MultiCell($this->colWidth,5,$text,0,$align);
	}
	
	/**
	 * Output all the link we met during text treatment.
	 *
	 */
	function writeLinkBuffer(){
		
		if(!empty($this->linkBuffer)){
		
			$headsize = -2;
			$currentFontSize = $this->FontSize;
			$this->tempfontsize = $this->FontSizePt;
			$this->SetFontSize($this->FontSizePt + $headsize);
			$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
				
		
			$text = "";
			foreach($this->linkBuffer as $index => $link){
				$text .= "[".$index."] ". $link['url'] . "\n";
			}
			$this->MultiCell($this->colWidth,5,$text,0,"left");
		
		
			$currentFontSize = $this->FontSize;
			$this->SetFontSize($this->tempfontsize);
			$this->tempfontsize = $this->FontSizePt;
			$this->Ln();
			$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
			
			//once the link buffer  has been outputed we empty it 
			$this->linkBuffer = array();
			
		}
	}
	
	
	/**
	 * Spip create ordered list using a puce image
	 * like <img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">
	 * we convert this kind of puce in classical li tag.
	 *
	 * @param string $html the html content containing the puce
	 * @return the html with puce replaced by li
	 */
	function convertPuce($html){
		//the pattern of a puce 
		$pattern = '/<[^>]+src="[^>]+puce[^>]+"[^>]+>/';
		$replacement = "<li>";
		return preg_replace($pattern,$replacement,$html);			
	}
	/**
	 * Replace all the p and br occurence by single carriage return.
	 *
	 * @param string $html the html content containing the 
	 * @return the html stripped
	 */
	function convertBrAndP($html){
		$pattern = array(		'/<p[^>]*>/',	'/<br[^>]*>/',	'/<\/p>/');
		$replacement = array(	"\n",			"\n",			"\n");
		return preg_replace($pattern,$replacement,$html);
	}
	
	
    /* Convert the footnote in simple text with a reference appended at the boottom 
     * of the page. We must treat them as link thus overrinding the use #NOTES
	 * Example : [<a href="#nb1" name="nh1" id="nh1" class="spip_note" title="[1] nbp 1">1</a>]
	 * Become  : [1]
	 * And the content inside the title attribut is stored in the link buffer 
	 * to the entry 1 to be outputed at the end of the text. 
	 *
	 * @param string $html
	 * @return the html content with link converted.
	 */	
	function createFootNoteBuffer($html){
		//extract the links
		$exploded = array();
		// capture pattern like [<a href="#nb1" name="nh1" id="nh1" class="spip_note" title="[1] nbp 1">1</a>]
		$pattern = '/\[<a[^>]+name="[^>]+"[^>]*>\d+<\/a>\]/';
		preg_match_all($pattern, $html, $exploded);
		//parse the anchor and fill the link buffer
		$pattern = '/<a.*title=([^>]+)>/';
		//-----------------------^ 		
		$link_index = 1;
		foreach($exploded[0] as $key=>$element){
			preg_match($pattern, $element, $link);
			//echo $element;
			//print_r($link);
			$link_index = $key + 1;
			$footNote = $link[1];
			$footNote = preg_replace('/\[\d+\]/',"",$footNote);			
			$this->linkBuffer[$link_index]['url'] = $footNote;
			$html = str_replace($element, " [".$link_index."]",$html);
		}
		$this->totalFootNotes = $link_index;
		return $html;
	}
	
	/**
	 * Convert the link in simple text with a reference appended between bracket [...]
	 * Example : <a href="http://mysite.com/myContent.html">about the content</a>
	 * Become  : about the content [1]
	 * But the link is stored in the link buffer to the entry 1 to be outputed
	 * at the end of the text. 
	 *
	 * @param string $html
	 * @return the html content with link converted.
	 */
	function createLinkBuffer($html){
		//extract the links
		$exploded = array();
		$pattern = '/<a[^>]+href="[^>]+"[^>]*>[^>]+<\/a>/';
		preg_match_all($pattern, $html, $exploded);
		//parse the link and fill the link buffer
		$pattern = '/<a[^>]+href="([^>]+?)"[^>]*>([^>]+)<\/a>/';
		foreach($exploded[0] as $key=>$element){
			preg_match($pattern, $element, $link);
			$link_index = $key + $this->totalFootNotes + 1;
			$link_url = $link[1];
			$link_content = $link[2];
			$this->linkBuffer[$link_index]['url'] = $link_url;
			$this->linkBuffer[$link_index]['content'] = $link_content;
			$html = str_replace($element, $link_content. " [".$link_index."]",$html);
		}
		return $html;
	}
	
	/**
	 * Clean all the html tag except li and img.
	 * As we only support <li> and <img> tag (given that p and br were already converted
	 * to carriage return) we need to work on html that only have those tag.
	 * 
	 * 
	 * @param string $html the content we clean
	 * @return the html cleaned of all the html tags exept <li> and <img> tag.
	 */
	function cleanUnWantedTag($html){
		return strip_tags($html,"<li><img><h3>");
	}
	
	/**
	 * Split the content in 4 kind of token
	 * li token to write li like element list
	 * img token to write an image
	 * h3 token for the "intertitre render"
	 * plain token to write plain text
	 * 
	 * When we start with a li the most challenging part is to 
	 * know how it end up it could be a closing li or a single line break. 
	 *
	 * @param string $html the content we tokenize
	 * @return a list of token 
	 */
	function tokenizeContent($html){
		$pattern = '/(<[^>]+>)/Uu';
		$html = str_replace("</li>","||",$html);
		$html = str_replace("</h3>","||",$html);
		$html = preg_replace('/(<[^>]*>)/Uu','||$1||',$html);
		$html = str_replace("\n","\n||",$html);
		$html = str_replace("\r","\r||",$html);
		$a = explode("||",$html);
		$tokens = array();
		$token_counter = 0;
		for($i=0; $i<count($a); $i++){
			if(trim($a[$i])==""){
				//ignore
				continue;
			}elseif($a[$i]=="<li>"){
				$token_counter++;
				$tokens[$token_counter]['type'] = "li";
				$tokens[$token_counter]['content'] = $a[$i+1];
				//pass the next
				$i++;
			}elseif(preg_match('/<h3[^>]*>/',$a[$i])){
				$token_counter++;
				$tokens[$token_counter]['type'] = "h3";
				$tokens[$token_counter]['content'] = $a[$i+1];
				//pass the next
				$i++;
			}elseif(preg_match('/<img[^>]+src="?([^>]+?)"? [^>]*>/',$a[$i],$src)){
				$token_counter++;
				$tokens[$token_counter]['type'] = "img";
				$tokens[$token_counter]['src'] = $src[1];
				//extract the width if applicable  
				if(preg_match('/<img[^>]+width="([^>]+?)"[^>]*>/',$a[$i],$width)){
					$tokens[$token_counter]['width'] = $width[1];						
				}
				//extract the height if applicable
				if(preg_match('/<img[^>]+height="([^>]+?)"[^>]*>/',$a[$i],$height)){
					$tokens[$token_counter]['height'] = $height[1];						
				}
			}else{
				$token_counter++;
				$tokens[$token_counter]['type'] = "plain";
				$tokens[$token_counter]['content'] = $a[$i];
			}
		}
		return $tokens;
	}
	
	

	/////////////////////////////////////////////////////////
	//////FUNCTION REWRITTEN TO RESPECT THE SPIP SEMENTIC////
	/////////////////////////////////////////////////////////

	/**
	 * Prints a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
	 * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
	 * @param float $w Cell width. If 0, the cell extends up to the right margin.
	 * @param float $h Cell height. Default value: 0.
	 * @param string $txt String to print. Default value: empty string.
	 * @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
	 * @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
	 Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
	 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
	 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
	 * @param mixed $link URL or identifier returned by AddLink().
	 * @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
	 * @since 1.0
	 * @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), AddLink(), Ln(), MultiCell(), Write(), SetAutoPageBreak()
	 */
	public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {
		if($align=='justify'){
			$align='J';
		}elseif($align=='left'){
			$align='L';
		}elseif($align=='right'){
			$align='R';
		}elseif($align=='center'){
			$align='C';
		}
		parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch);
	}

	/**
	 * This method allows printing text with line breaks. They can be automatic (as soon as the text reaches the right border of the cell) or explicit (via the \n character). As many cells as necessary are output, one below the other.<br />
	 * Text can be aligned, centered or justified. The cell block can be framed and the background painted.
	 * @param float $w Width of cells. If 0, they extend up to the right margin of the page.
	 * @param float $h Height of cells.
	 * @param string $txt String to print
	 * @param mixed $border Indicates if borders must be drawn around the cell block. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
	 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align</li><li>C: center</li><li>R: right align</li><li>J: justification (default value)</li></ul>
	 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
	 * @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right</li><li>1: to the beginning of the next line [DEFAULT]</li><li>2: below</li></ul>
	 * @return int number of cells (number of lines)
	 * @since 1.3
	 * @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), Cell(), Write(), SetAutoPageBreak()
	 */
	public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1) {
		if($align=='justify'){
			$align='J';
		}elseif($align=='left'){
			$align='L';
		}elseif($align=='right'){
			$align='R';
		}elseif($align=='center'){
			$align='C';
		}
		if($this->debug){
			//it's useful to have the border ouputted
			$border = 1;
			//we must follow the current position and width
			//of each cell
			$debug_info = "[Witdh: ".$w
			." X:".$this->GetX()
			." Y:".$this->GetY()."]";
			$txt .= " ".$debug_info;
		}
		parent::MultiCell($w, $h, $txt, $border, $align, $fill, $ln);
	}

	/**
	 * Take a string that could be a color in hexa or a rgb definition similar
	 * to the rgb definition in css style.
	 *
	 * @param string $couleur
	 * @return array an array containing the color description in rbg mode
	 */
	function colorTxt2ArrayRGB($couleur){
			if(substr($couleur,0,1)=='#' AND strlen($couleur)==7){
				//hexa color
				$array_rgb = $this->convertColorHexToDec($couleur);
			}else{
				$array_rgb = split(' ',$couleur);
			}
			return $array_rgb;
	}
	
	
	function convert_entity($texte){
		
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);//Remplace les clés par les valeurs, et les valeurs par les clés 
		$trans['&nbsp;'] = ' ';
		$trans['&#8220;'] = '&ldquo;';//Guillemet ouvrant deuxième niveau (France Amerique Anglais)
		$trans['&#8221;'] = '&rdquo;';//Guillemet fermant deuxième niveau (France Amerique Anglais)
		$trans['&#171;'] = '&laquo;';//Guillemet ouvrant premier niveau (France et Suisse)
		$trans['&#187;'] = '&raquo;';//Guillemet fermant premier niveau (France et Suisse)
		$trans['&#8216;'] = '&lsquo;';//Guillemet ouvrant troisième niveau (France Anglais)
		$trans['&#8217;'] = '’';//Guillemet fermant troisième niveau (France Anglais)
		$trans['&#8239;'] = '&lsaquo;';//Guillemet ouvrant suisse deuxième niveau
		$trans['&#8250;'] = '&rsaquo;';//Guillemet fermant suisse deuxième niveau
		$trans['&#8250;'] = '&rsaquo;';
		$trans['&#8222;'] = '&raquo;';
	
		$texte = strtr($texte, $trans);
		
		return $texte;
	}	
		

   /** Defines the left, top and right margins. By default, they equal 1 cm. Call this method to change them.
    * 
    * Note that instead of the SetLeftMargin or setRightMargin SetMargins is supposed
    * to be an initializer margin definition, thus we save the original value of the 
    * margin here. 
    * 
	* @param float $left Left margin.
	* @param float $top Top margin.
	* @param float $right Right margin. Default value is the left one.
	* @since 1.0
	* @see SetLeftMargin(), SetTopMargin(), SetRightMargin(), SetAutoPageBreak()
	*/
	public function SetMargins($left, $top, $right=-1) {
		//Set left, top and right margins
		$this->lMargin=$left;
		$this->tMargin=$top;
		if($right==-1) {
			$right=$left;
		}
		$this->rMargin=$right;
		//Save the original value
		$this->original_lMargin = $left;
		$this->original_rMargin = $right;
	}
	
	function AddPage($orientation=''){
		$this->SetCol(0);
		parent::AddPage($orientation);
	}


}

?>