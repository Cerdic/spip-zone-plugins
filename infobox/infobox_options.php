<?php
/**
 * Plugin Infobox pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */
 
function balise_POUET($p) {
	$message=interprete_argument_balise(1,$p);
	$pouet="'<h1>'.$message.'</h1>'";
	$p->code = "$pouet";
	$p->interdire_scripts = false;
	return $p;
}

function balise_INFOBOX($p) {

	// $class		= interprete_argument_balise(1,$p);
	// $message	= interprete_argument_balise(2,$p);

	$class = 'general';
	$message = 'Essai dans general';

	$boite = "'<script type=\"text/javascript\">$(function(){
	$(\'#infobox fieldset.infobox fieldset.$class\').append(\'$message\'); 
	});</script>'";
	
	$toto = "'
		<script type=\"text/javascript\">
		$(document).ready(function(){ 
			$(\'#infobox\').append(\'<h1>TOTO dans infobox</h1>\');
			$(\'#page\').append(\'<h1>TOTO dans page</h1>\'); 
		});
		</script>
	'";

	$p->code = "$toto";
	$p->interdire_scripts = false;
	return $p;
}

/*
	$_id = interprete_argument_balise(1, $p);
	if ($_id) {

		$compteur_critere	=	1; // variable pour récuperer le nombre de criteres
		$tab_critere		=	array() ; // variable tableau pour récuperer les criteres de la balise
		
		while(interprete_argument_balise($compteur_critere, $p)) {
			
			$tab_critere[$compteur_critere]	.=	interprete_argument_balise($compteur_critere, $p);
			
			$compteur_critere ++;
		}
	
		$tab_critere[1] = substr($tab_critere[1],1,strlen($tab_critere[1])-2); // On efface les simple quote!
		$tab_critere[2] = substr($tab_critere[2],1,strlen($tab_critere[2])-2); // On efface les simple quote!
		//echo $tab_critere[1].":".$tab_critere[2]."<br/>";
		
		$class			=	addslashes($tab_critere[1]);
		$message		=	"-	".addslashes($tab_critere[2])."<br/> ";
		
		$boite = "'<script type=\"text/javascript\">$(function(){
		$(\'.infobox fieldset.$class\').append(\'$message\'); 
		});</script>'";
		
		$p->code = "$boite";
		
		//echo $decoupe_tab[0].":".$decoupe_tab[1]."<br/>";
		
		
	} 
	else {
		
		$class			=	"divers";
		$message		=	"<br/>Veuillez mettre des paramètres à la balise!";
		
		$boite = "'<script type=\"text/javascript\">$(function(){
			$(\'.infobox fieldset.$class\').append(\'$message\');
			});</script>'";
	
		$p->code = "$boite";
	}

*/

?>