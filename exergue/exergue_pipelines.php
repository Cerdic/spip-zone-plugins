<?php

function exergue_pre_propre($letexte) {
	
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

function exergue_insert_head($flux) {

			$flux .= <<<EOF
<script type="text/javascript">
<!--
(function($){
	$(document).ready(function(){
		/* a t'on des ancres de exergues <exergue /> ? */
		var exergue_tab = new Array();	
		$('exergue').each(function(i){
			exergue_tab[i] = $(this);
		});
		
		$('.spip_exergue').each(function(i){
			var content = $(this).html();
			/* soit il y a une ancre et on fout l'exergue suivant là, soit on fout l'exergue avant le paragraphe parent*/
			if(exergue_tab[i]){
				exergue_tab[i].before('<div class="exergue">« '+ guillemets_check(capitaliseFirstLetter(content)) +' »</div>');
			}else{
				$(this).parent().before('<div class="exergue">« '+ guillemets_check(capitaliseFirstLetter(content)) +' »</div>');
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
    return string.replace('«','"').replace('»','"');
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