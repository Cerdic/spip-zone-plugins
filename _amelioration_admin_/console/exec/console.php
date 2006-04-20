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
	
	if ($connect_statut != "0minirezo" OR !$connect_toutes_rubriques) {
		echo "<B>Vous n'avez pas acc&egrave;s &agrave; cette page.</B>";
		exit;
	}
	
	echo bouton_block_invisible("spiplog");
	echo "spip.log <br />";
	echo debut_block_invisible("spiplog");
	echo "<span style='font-size:medium;font:Georgia,Garamond,Times,serif;'>";
	//
	// Lire et afficher les fichiers logs
	//
	
	$files = preg_files(_DIR_SESSIONS,"spip\.log(\.[0-9])?");
	krsort($files);

	$log = "";
	foreach($files as $nom){
		if (lire_fichier($nom,$contenu))
			$log.=$contenu;
	}
	$contenu = explode("<br />",nl2br($contenu));
	
	$maxlines = 40;
	while ($contenu && $maxlines--){
		echo "<tt>".array_pop($contenu)."</tt><br />\n";
	}
	
	echo "</span>";
	echo fin_block();

	echo bouton_block_invisible("mysqllog");
	echo "mysql.log <br />";
	echo debut_block_invisible("mysqllog");
	echo "<span style='font-size:medium;font:Georgia,Garamond,Times,serif;'>";
	//
	// Lire et afficher les fichiers logs
	//
	
	$files = preg_files(_DIR_SESSIONS,"mysql\.log(\.[0-9])?");
	krsort($files);

	$log = "";
	foreach($files as $nom){
		if (lire_fichier($nom,$contenu))
			$log.=$contenu;
	}
	$contenu = explode("<br />",nl2br($contenu));
	
	$maxlines = 40;
	while ($contenu && $maxlines--){
		echo "<tt>".array_pop($contenu)."</tt><br />\n";
	}
	
	echo "</span>";
	echo fin_block();
	
	fin_page();

}

?>