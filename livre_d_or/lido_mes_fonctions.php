<?php 

	// lido_mes_fonctions.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$


/* filtres */

function lido_inserer_date ($texte, $date) {
	$texte = preg_replace('~<span class=\'signature\'>(.*)</span>~'
		, '<span class=\'signature\'>${1} - <span class=\'date\'>'.affdate($date).'</span></span>'
		, $texte
		);
	return($texte);
}

?>