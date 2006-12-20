<?php

function public_connect_db()
	{
	if (!$link = mysql_connect('ilsalone.free.fr', 'ilsalone', '0range'))
		{
		echo 'Connexion impossible à mysql';
		exit;
		}
	if (!mysql_select_db('ilsalone', $link))
		{
		echo 'Sélection de base de données impossible';
		exit;
		}
	}

?>
