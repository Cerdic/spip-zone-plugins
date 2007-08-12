<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr" dir="ltr">
<head>
<title>SPIP mutualis&eacute;s</title>
</head>
<body>
<?php
// Création du tableau qui va contenir les fichiers et dossiers
$files = array();

// Ouverture du répetoire courant
// Pour le changer utiliser chdir() avant opendir()
$handle = opendir(".");

// Parcours des fichiers et dossiers du répertoire courant
while($file = readdir($handle)) {
    if($file != "." && $file != ".." && is_dir($file)) {
        $files[] = $file;
    }
}

// Fermeture du répertoire courant
closedir($handle);

// Tri du tableaunat
sort($files);

// Affichage des fichiers et dossiers triés
echo "<h1>Liste des sites install&eacute;s (".count($files).")</h1>\n<ul>";
foreach($files as $v) {
    echo "<li><a href='http://$v'>$v</a> — <a href='http://$v/ecrire/'>/ecrire/</a> — <a href='http://$v/ecrire/?exec=statistiques_visites'>Statistiques</a></li>\n";
}
echo "</ul>";
?>
</body>
</html>