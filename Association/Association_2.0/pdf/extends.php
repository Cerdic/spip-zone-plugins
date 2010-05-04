<?

if (!defined("_ECRIRE_INC_VERSION")) return;


define('FPDF_FONTPATH','font/');
include_spip('pdf/pdf_table');
include_spip('inc/charsets');

class PDF extends PDF_Table {

	function PDF(){
		$this->FPDF('L', 'mm', 'A4');
	}
	
	function Header(){
		//Titre
		$this->SetFont('Arial','',10);
		$this->Cell(0,6,lire_config('association/nom'),0,1,'L');
		$this->SetFont('Arial','B',14);
		$this->Cell(0,6,unicode2charset(html2unicode($this->titre)),0,1,'C');
		$this->Ln(10);
		//Imprime l'en-t�te du tableau si n�cessaire
		parent::Header();
	}
}
?>
