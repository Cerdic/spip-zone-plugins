<?php

function exergue_pre_propre($letexte) {

	// trouver des balises exergue
	if (preg_match_all(",<(exergue)>(.*)<\/(exergue)>,Uims",
	$letexte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {							
			// ne pas mettre le <div...> s'il n'y a qu'une ligne
			if (is_int(strpos($reg[0],"\n"))) {
				$letexte = str_replace($reg[0], "<div class=\"spip_exergue\">"."\n\n" . $reg[2] . "</div>", $letexte);
			} else {
				$letexte = str_replace($reg[0], "<span class=\"spip_exergue\">" . $reg[2] . "</span>", $letexte);		
			}
		}
	}
	return $letexte;
	
}

function exergue_post_propre($letexte) {
	/* nettoyer les ancres <p><exergue /></p> */
	$letexte = preg_replace(",<p><exergue /></p>,","<exergue />",$letexte);

	return $letexte;
}


function exergue_insert_head($flux) {

			$flux .= <<<EOF
<script type="text/javascript">
<!--
(function($){
	$(document).ready(function(){
		/* a t'on des ancres de exergues [exergue<-] dans le texte ? */
		var exergue_tab = new Array();	
		$('a[name=exergue]').each(function(i){
			exergue_tab[i] = $(this);
		});
		
		$('.spip_exergue').each(function(i){
			var content = $(this).html();
			/* Soit il y a une ancre [exergue<-] dans le texte et on place l'exergue suivant à cet endroit, soit il n'y en a pas et on place l'exergue avant le paragraphe parent */
			if(exergue_tab[i]){
				exergue_tab[i].before('<div class="exergue">«&nbsp;'+ guillemets_check(capitaliseFirstLetter(content)) +'&nbsp;»</div>');
				exergue_tab[i].remove();
			}else{
				$(this).parent().before('<div class="exergue">«&nbsp;'+ guillemets_check(capitaliseFirstLetter(content)) +'&nbsp;»</div>');
			}
		});

	});
})(jQuery);

function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function guillemets_check(string)
{
    return string.replace('«','&ldquo;').replace('»','&rdquo;');
}

-->
</script>
EOF;


	$flux .= <<<EOF
<style type="text/css">
<!--
.exergue{
float:left;
width:200px;
font-weight:bold;
margin:20px 20px 20px 0
}
-->
</style>
EOF;



		return $flux;
}


?>