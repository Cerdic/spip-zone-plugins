<?php
  // script permattant d'importer un fichier d'actions explicite
  // ATTENTION : version simple sans recup de valeurs et sans callbacks
  // interessant en batch a la sieps

  // A MODIFIER SELON VOTRE CONFIG
$spipDir= "../..";
$spipDir= "/var/www/html/spalios";

// le bout de code qui va bien pour se retrouver dans un contexte "spip minimal"
define('_DIR_RACINE', "$spipDir/");
define('_DIR_RESTREINT_ABS', "$spipDir/ecrire/");
define('_DIR_RESTREINT', _DIR_RESTREINT_ABS);
include("$spipDir/ecrire/inc_version.php");
spip_connect();
include_spip("base/abstract_sql");

$r= spip_abstract_select(array("count(*)"), array("spip_articles"));
if (!$r || !spip_abstract_fetch($r)) {
  die("echec d'acces a la bdd (".mysql_error($r).")\n");
}
// arrivé là, on a les fonctions spip de base, et la bdd est accessible

// récupérer le xml2bdd du plugin editables
include_spip('inc/actionParser');

// pour chaque fichier passé en argument (avec un chemin en aaaa/ll/...txt)
array_shift($argv);
foreach($argv as $fichier) {
	$actions= file_get_contents($fichier);
	$parser = new actionParser(null);
	$parser->parse($actions);
	$code= $parser->getCode();

	echo "=> code = ".$code."\n";

// 	$cc= explode("\n && ", $code);
// 	array_shift($cc);
// 	foreach($cc as $c) {
// 		echo "CODE $c => ";
// 		$res= eval("return $c;");
// 		if($res===false) {
// 			die("erreur à l'exécution du code");
// 		}
// 		echo "$res\n";
// 	}
}

?>
