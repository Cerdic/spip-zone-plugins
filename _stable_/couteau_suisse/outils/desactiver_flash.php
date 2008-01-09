<?php

function InhibeFlash_affichage_final($flux){
	$code = "";
	// regarder si jquery deja la ou non
	if (strpos($flux,"jquery.js")===FALSE)
		$code .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
	$code .=<<<jscode
<script type="text/javascript"><!--
var code;
if (window.jQuery) jQuery(document).ready(function(){
  $('object').each(function(){
  	$('param',this).remove();
  }).wrap("<div class='noflash'></div>");
  $('div.noflash').each(function(){
  	var code = this.innerHTML;
  	// ajouter les attributs juste avant la fermeture de la balise object
  	var reg=new RegExp("(<object [^>]*>)", "i");
  	code = code.replace(reg,"");
  	var reg=new RegExp("(<\/object>)", "i");
  	code = code.replace(reg,"");
  	this.innerHTML=code;
  })
});
//--></script>
jscode;
	$flux = str_replace("</body>",$code."</body>",$flux);
	return $flux;
}

?>