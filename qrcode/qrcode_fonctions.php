<?php


function qrcode_insert_head($flux) {
	if (lire_config("qrcode/documents")) {
		( $taille = lire_config('qrcode/taille') ) || ( $taille = 1 ) ;
		( $ecc = lire_config('qrcode/ecc') ) || ( $ecc = 'L' ) ;
		if ($class = lire_config('qrcode/css')) { $class = ' class="'.$class.'"' ; }
		if ($style = lire_config('qrcode/style')) { $style = ' style="'.$style.'"' ; }
		$flux .= "<script type='text/javascript'>
var url_site_spip = '".$GLOBALS['meta']['adresse_site']."' ;

$().ready(function() {
	$('.documents_joints a').each(function(ndx,item) {
		var re = new RegExp('^(https?|ftp)://') ;
		var url = $(this).attr('href') ;
		if (!re.test(url)) {
			url = url_site_spip + url ;
		}
		$(this).parent().prepend('<img$class$style src=\""._DIR_RACINE."?page=qrcode&data='+encodeURIComponent(url)+'&size=$taille&level=$ecc\" alt=\"qrcode:'+url+'\" title=\""._T('qrcode:aide')."\"/>') ;
	}) ;
}) ;

</script>
" ;
	}
	return $flux ;
}

?>
