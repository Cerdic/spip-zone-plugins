<?php

/**
 * gestion d'une boucle sur la listes des langues configurées dans spip
 */

$fichiers = array(
	"chemin" => "varchar(200)"
);
$fichiers_key = array(
	"PRIMARY KEY"	=> "chemin"
);

$GLOBALS['tables_principales']['fichiers'] =
	array('field' => &$fichiers, 'key' => &$fichiers_key);

function boucle_FICHIERS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$pattern='*';
	$flags=GLOB_NOSORT;
	$racine='';
	$codeFichier='$fichier';
	foreach($boucle->param as $param) {
		if(preg_match('/masque=(.*)/', $param, $regs)) {
			$pattern= $regs[1];
		} elseif(preg_match('/racine=(.*)/', $param, $regs)) {
			$racine= $regs[1];
DEBUG("boucle_FICHIERS : ".calculer_texte($regs[1], $id_boucle, $boucles, null) );
			$codeFichier="substr(\$fichier, ".strlen($racine).")";
		} elseif($param=='dir') {
			$flags|= GLOB_ONLYDIR;
		} elseif($param=='par chemin') {
			$flags&= ~GLOB_NOSORT;
		} else {
			erreur_squelette(_T('zbug_info_erreur_squelette'), $param);
		}
	}
	$code=<<<CODE
	\$SP++;
	\$code=array();
	foreach(glob('$racine$pattern', $flags) as \$fichier) {
		\$Pile[\$SP] = array('chemin' => $codeFichier);
		\$code[]=$boucle->return;
	}
	\$t0= join('$boucle->separateur', \$code);
CODE;

	return $code;
}

?>
