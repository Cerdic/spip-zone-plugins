<?php

/** BOUCLE LANGUE **/

$langues = array(
	"langue" => "varchar(2)"
);
$langues_key = array(
	"PRIMARY KEY"	=> "langue"
);
$GLOBALS['tables_principales']['spip_langues'] =
	array('field' => &$langues, 'key' => &$langues_key);
$GLOBALS['table_des_tables']['langues'] = 'langues';

function boucle_LANGUES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

	if(isset($boucle->modificateur['tout'])) {
	  $liste= "lire_meta('langues_proposees').lire_meta('langues_proposees2')";
	} else {
	  $liste= "lire_meta('langues_multilingue')";
	}

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= explode(',', $liste);
	foreach(\$l as \$k) {
		\$Pile[\$SP]['langue'] = \$k;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

?>
