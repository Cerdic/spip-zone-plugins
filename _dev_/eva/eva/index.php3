<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="patch-securite1-19janv2007">
	<title>[EVA] Présentation ─ Documentation ─ Aide aux rédacteurs</title>
	<!-- feuilles de styles -->
	<link href="../css/eva_style_large.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="../css/eva_style_print.css" rel="stylesheet" type="text/css" media="print" />
<style type="text/css"><!--
h1 {
	font-size:18px;
	margin:0;
}
div#Contenu img {
	margin:4px;
	vertical-align:middle;
}
div#Contenu li {
	margin:4px;
}
--></style>
</head>
<body>
	
<div id="Page">

<?php 

define('REP','');

include "menu.php3";
echo '<div id="Contenu">';

//Si la variable GET est aide :--------------------------------------------
if (ereg('^[a-zA-Z0-9]{4,50}$', $_GET['aide']) && file_exists(REP.$_GET['aide'].'.html'))
{
include (REP.$_GET['aide'].'.html');
}
elseif (ereg('^[a-zA-Z0-9]{4,50}$', $_GET['perso']) && file_exists(REP.$_GET['perso'].'.html'))
{
include (REP.$_GET['perso'].'.html');
}
elseif (ereg('^[a-zA-Z0-9]{4,50}$', $_GET['presentation']) && file_exists(REP.$_GET['presentation'].'.html'))
{
include (REP.$_GET['presentation'].'.html');
}
else
{
include(REP.'page-404.html');
}

echo '</div>';

?>
	
</div>	
</body>
</html>
