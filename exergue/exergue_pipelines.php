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
	/* nettoyer les ancres  <p><a name="exergue"></a></p> */
	$letexte = str_replace('<p><a name="exergue"></a></p>','<a name="exergue"></a>',$letexte);

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
		var nb_ancres = 0 ;

		$("a[name^='exergue'], .spip_exergue").each(function(i){
			/* si c'est une ancre on sait que le prochain exergue a cette ancre */
			if(!$(this).hasClass('spip_exergue')){
				// a[name^='exergue']
				var ancre_numerotee = $(this).attr('name').match(/[0-9]+/g) ;
 // var res = str.match(/ain/g); 
				if(ancre_numerotee){
					exergue_tab['exergue' + ancre_numerotee] = $(this);			
				}else{	
					var index_exergue = i + 1 - nb_ancres;
					exergue_tab['exergue' + index_exergue] = $(this);
				}
				nb_ancres ++ ;
			}
		});
		
		$('.spip_exergue').each(function(i){
			var content = $(this).html();
			/* Soit il y a une ancre [exergue<-] dans le texte et on place l'exergue suivant à cet endroit, soit il n'y en a pas et on place l'exergue avant la balise */
			i ++ ;
			if(exergue_tab['exergue' + i]){
				exergue_tab['exergue' + i].before('<span class="exergue">«&nbsp;'+ guillemets_check(capitaliseFirstLetter(content)) +'&nbsp;»</span>');
				exergue_tab['exergue' + i].remove();
				console.log(exergue_tab);

			}else{
				$(this).before('<span class="exergue">«&nbsp;'+ guillemets_check(capitaliseFirstLetter(content)) +'&nbsp;»</span>');
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
margin:20px 20px 20px 0;
display:block;
}
-->
</style>
EOF;



		return $flux;
}


?>