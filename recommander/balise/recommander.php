<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

/**
 * #RECOMMANDER{titre,url,texte,subject}
 * @param <type> $p
 * @return <type> 
 */
function balise_RECOMMANDER_dist ($p) {
	$_titre = interprete_argument_balise(1,$p);

	$_url = interprete_argument_balise(2,$p);
	$_url = ($_url ? $_url:"''");

	$_texte = interprete_argument_balise(3,$p);
	$_texte = ($_texte ? $_texte:"''");

	$_subject = interprete_argument_balise(4,$p);
	$_subject = ($_subject?$_subject:"''");

	$p->code = "recuperer_fond('modeles/recommander',array('titre'=>$_titre,'url'=>$_url,'texte'=>$_texte,'subject'=>$_subject))";
	$p->interdire_scripts = false;
	return $p;
}


?>