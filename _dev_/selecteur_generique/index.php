<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html dir="ltr" lang="fr">
<head>
<title>test de selecteur generique</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script src="http://www.jquery.info/spip.php?page=jquery.js" type="text/javascript"></script>

</head>

<body>

<?php

chdir('..');
include 'ecrire/inc_version.php';
include dirname(__FILE__).'/selecteur_generique.php';

echo "<hr />";
echo "Sur les mots : ";
echo selecteur_objets('mot');


echo "<hr />";
echo "Sur les auteurs : ";
echo selecteur_objets('auteur');
