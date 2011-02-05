<?php

function retoursimple_pre_propre($texte){
	return echappe_html(post_autobr($texte));	
}
?>