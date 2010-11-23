<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function mathjax_spip_insert_head($flux){
	$flux .= mathjax_spip_flux();
	return $flux;
}

function mathjax_spip_header_prive($flux){
	$flux .= mathjax_spip_flux();
	return $flux;
}
	
function mathjax_spip_flux(){
	
	$js = find_in_path('lib/mathjax-v1.0.1a/MathJax.js');
	$param = <<<EOF
		MathJax.Hub.Config({
    	    extensions: ["tex2jax.js"],
    	    jax: ["input/TeX", "output/HTML-CSS"],
    	    tex2jax: {
    	        inlineMath: [ ['$','$'], ["\\(","\\)"] ],
    	        displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
    	    },
    	    "HTML-CSS": { availableFonts: ["TeX"] }
    	});
	</script>
EOF;
	$flux = "\n<script type='text/javascript' src='$js'>
		$param
	</script>\n";
	return $flux;
}

?>
