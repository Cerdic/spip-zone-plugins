<?php
/**
 * Fonctions utiles au plugin QrCode
 *
 * @plugin     QrCode
 * @copyright  2014
 * @author     Frédéric Bonnaud
 * @licence    GNU/GPL
 * @package    SPIP\Qrcode\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


if ( !is_dir( _DIR_VAR."cache-qrcode/" ) ) {                                    
	if ( !mkdir ( _DIR_VAR."cache-qrcode/", 0777 ) ) {
		spip_log( "impossible de creer le repertoire", "qrcode" );
	}
}

function qrcode_hash($texte, $taille, $ecc) {
	return md5(serialize(array($texte, $taille, $ecc))) ;
}

function qrcode_getpng($texte, $taille, $ecc) {
	$filename = _DIR_VAR."cache-qrcode/qrcode-".qrcode_hash($texte, $taille, $ecc).".png";
	if (! file_exists($filename)) {
		require_once(find_in_path('lib/phpqrcode.php')) ;
		$errorCorrectionLevel = 'L' ;
		if (isset($ecc) && in_array($ecc, array('L','M','Q','H')))
			$errorCorrectionLevel = $ecc;
		$matrixPointSize = 4;
		if (isset($taille))
			$matrixPointSize = min(max((int)$taille, 1), 10);
		$data = 'http://www.spip.net' ;
		if (isset($texte))
			$data = $texte ;

		QRcode::png($data , $filename , $errorCorrectionLevel, $matrixPointSize ) ;
	}
	return $filename ;
}

function filtre_qrcode($texte,$taille=false,$ecc=false,$link=false) {
	$taille || ( $taille = lire_config('qrcode/taille') ) || ( $taille = 1 ) ;
	$ecc || ( $ecc = lire_config('qrcode/ecc') ) || ( $ecc = 'L' ) ;
	if ($class = lire_config('qrcode/css')) { $class = ' class="'.$class.'"' ; }
	if ($style = lire_config('qrcode/style')) { $style = ' style="'.$style.'"' ; }
	$filename = qrcode_getpng($texte, $taille, $ecc) ;
	$width = ' width="'.largeur($filename).'"';
	$height = ' height="'.hauteur($filename).'"';
	if ($link) {
		return "<a href=\"$texte\" title=\""._T('qrcode:aide')."\"><img$class$style src=\"$filename\"$width$height alt=\"qrcode:$texte\"/></a>" ;
	} else {
		return "<img$class$style src=\"$filename\"$width$height alt=\"qrcode:$texte\" title=\""._T('qrcode:aide')."\"/>" ;
	}
}
?>