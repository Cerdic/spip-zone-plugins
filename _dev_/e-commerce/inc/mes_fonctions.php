<?php

function public_connect_db()
	{
	if (!$link = mysql_connect('ilsalone.free.fr', 'ilsalone', '0range'))
		{
		echo 'Connexion impossible � mysql';
		exit;
		}
	if (!mysql_select_db('ilsalone', $link))
		{
		echo 'S�lection de base de donn�es impossible';
		exit;
		}
	}

?>
