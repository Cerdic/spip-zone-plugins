<?php

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
		require_once(find_in_path('lib/phpqrcode/qrlib.php')) ;
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


function qrcode_insert_head($flux) {
	if (lire_config("qrcode/documents")) {
		( $taille = lire_config('qrcode/taille') ) || ( $taille = 1 ) ;
		( $ecc = lire_config('qrcode/ecc') ) || ( $ecc = 'L' ) ;
		( $cssid = lire_config('qrcode/remplacecssid') ) || ( $cssid = '.documents_joints' ) ;
		if ($class = lire_config('qrcode/css')) { $class = ' class="'.$class.'"' ; }
		if ($style = lire_config('qrcode/style')) { $style = ' style="'.$style.'"' ; }
		$flux .= "<script type='text/javascript'>
var url_site_spip = '".$GLOBALS['meta']['adresse_site']."' ;

$().ready(function() {
	$('$cssid a').each(function(ndx,item) {
		var re = new RegExp('^(https?|ftp)://') ;
		var url = $(this).attr('href') ;
		if (!re.test(url)) {
			url = url_site_spip + '/' + url ;
		}
		$(this).parent().prepend('<img$class$style src=\""._DIR_RACINE."?page=qrcode&data='+encodeURIComponent(url)+'&size=$taille&level=$ecc\" alt=\"qrcode:'+url+'\" title=\""._T('qrcode:aide')."\"/>') ;
	}) ;
}) ;

</script>
" ;
	}
	return $flux ;
}

function qrcode($texte,$taille=false,$ecc=false) {
	$taille || ( $taille = lire_config('qrcode/taille') ) || ( $taille = 1 ) ;
	$ecc || ( $ecc = lire_config('qrcode/ecc') ) || ( $ecc = 'L' ) ;
	if ($class = lire_config('qrcode/css')) { $class = ' class="'.$class.'"' ; }
	if ($style = lire_config('qrcode/style')) { $style = ' style="'.$style.'"' ; }
	$filename = qrcode_getpng($texte, $taille, $ecc) ;
	return "<img$class$style src=\"$filename\" alt=\"qrcode:$texte\" title=\""._T('qrcode:aide')."\"/>" ;
}

?>
