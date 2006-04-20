<?php

function exec_console(){
  global $connect_statut;
  global $connect_toutes_rubriques;
  
  include_ecrire("inc_presentation");



debut_page("Suivi des logs", "", "");

echo "<br><br><br>";
gros_titre("Suivi des logs");

debut_gauche();

debut_droite();

echo "<FONT SIZE=2 FACE='Georgia,Garamond,Times,serif'>";

if ($connect_statut != "0minirezo" OR !$connect_toutes_rubriques) {
	echo "<B>Vous n'avez pas acc&egrave;s &agrave; cette page.</B>";
	exit;
}


//
// Lire et afficher les fichiers logs
//

$file  = Array();
$file1 = Array();
if (file_exists("data/spip.log.1"))
	$file1 = file("data/spip.log.1");
if (file_exists("data/spip.log"))
	$file = file("data/spip.log");

$s = sizeof($file);
$s1 = sizeof($file1); 
$n = min (40, $s+$s1);

for ($i = $n; $i--; $i > 0) {
	if ($i < $n-$s)
		echo "<tt>".$file1[$s1 + $i - ($n-$s)]."</tt><br />\n";
	else
		echo "<tt>".$file[$i + $s - $n]."</tt><br />\n";
}

echo "</FONT>";

fin_page();

	}

?>