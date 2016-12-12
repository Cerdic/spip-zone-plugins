<?php

function browser_update_insert_head($texte){
	$texte .= "\n".'<script>'."\n".'var $buoop = {c:2};'."\n".'function $buo_f(){'."\n".'var e = document.createElement("script");'."\n".'e.src = "//browser-update.org/update.min.js";'."\n".'document.body.appendChild(e);'."\n".'};'."\n".'try {document.addEventListener("DOMContentLoaded", $buo_f,false)}'."\n".'catch(e){window.attachEvent("onload", $buo_f)}'."\n".'</script>'."\n";
	return $texte;
}

?>