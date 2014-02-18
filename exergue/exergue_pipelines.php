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

		$('.spip_exergue').each(function(){
			var content = $(this).html();
			/* soit il y a une ancre, soit on fout l'exergue avnt le paragraphe */
			
			if('lol' == 'wesh'){
			
			}else{
				$(this).parent().before('<div class="exergue">« '+ capitaliseFirstLetter(content) +' »</div>');
			}
	
	
		});

	});
})(jQuery);

function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
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