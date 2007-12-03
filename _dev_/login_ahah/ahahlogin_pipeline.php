<?php
/* Login AHAH
 *
 * (c) 2007 Cedric Morin
 * Licence GPL
 */

function ahahlogin_affichage_final($flux){
	if (strpos($flux,'login_modal')===FALSE) 
		return $flux;
	$url_site_spip = $GLOBALS['meta']['adresse_site'];
	$url_ahah = generer_url_public('ahah-login','',true);
	$x = find_in_path('x.png');
	$plus = <<<plus
<script type='text/javascript'>
$('document').ready(function(){ $('a.login_modal').click(login_frame)});
function ajax_login_form(c){
	if (c.match(/^OK$/)) {
		document.location="$url_site_spip/ecrire";
		return;
	}
	if ($('#login_box').length==0)
		$.modal('<div id="login_box" class="formulaire_spip"></div>)');
	$('#login_box')
	.html(c/*.replace(/ahah-login/,'login')*/)
	.find('a').click(function(){ $.get($(this).attr('href'),ajax_login_form);return false;})
	.end()
	.find('form').submit(function(){ $(this).ajaxSubmit({success:ajax_login_form });return false;});
	$('#login_securise').show();
	$('#login_non_securise').remove();	
}
function login_frame(){
	/*$('body').append('<div id="login_box"></div>');*/
	$.get('$url_ahah',ajax_login_form);
	return false;
}
</script>
<style>
#login_box {height:18em;}
/* Overlay */
#modalOverlay {height:100%; width:100%; position:fixed; left:0; top:0; z-index:3000; background-color:#000; cursor:wait;}
/* Container */
#modalContainer {height:20em; width:400px; position:fixed; left:50%; top:15%; margin-left:-200px; z-index:3100; background-color:#fff; border:3px solid #ccc;}
#modalContainer a.modalCloseImg {background:url($x) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-14px; left:388px; cursor:pointer;}
#modalContainer #basicModalContent {padding:8px;}</style>
<!-- IE 6 hacks -->
<!--[if lt IE 7]>
<style>
#modalContainer {position: absolute; top:expression((document.documentElement.scrollTop || document.body.scrollTop) + Math.round(15 * (document.documentElement.offsetHeight || document.body.clientHeight) / 100) + 'px');}
#modalContainer a.modalCloseImg {background:none; width:22px; height:26px; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, src='img/basic/x.png',sizingMethod='scale');}
#modalIframe {z-index:1000; position:absolute; width:100%; height:100%; top:0; left:0;}</style>
<![endif]-->
plus;

	$flux = preg_replace(',</body>,i',"$plus</body>",$flux);
	return $flux;
}

?>