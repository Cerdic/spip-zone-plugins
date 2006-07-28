<?php

global $tables_auxiliaires;
include_spip('base/auxiliaires');
$tables_auxiliaires['spip_auteurs_articles']['field']['rang']='INT NOT NULL';

if(!function_exists('balise_RANG')) {
function balise_RANG($p) {
        //get the calling boucle
        $boucle = &$p->boucles[$p->id_boucle];
	//consider any automatic join as an explicit join to permit selecting joint table fields
	$boucle->jointures_explicites = $boucle->jointures;
	//generate field code

        $_rang = champ_sql('rang', $p);
	if($boucle->type_requete == 'article') {
	        $_titre = champ_sql('titre',$p);
	        $p->code = "(isset($_rang)?($_rang):recuperer_numero($_titre))";
	} else {
		$p->code = "$_rang";
	}
        $p->interdire_scripts = false;
        return $p;
}
}


?>