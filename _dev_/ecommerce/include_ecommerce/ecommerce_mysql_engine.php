<?php

if (!defined('_INCLUDE_ECOMMERCE')) 
	{
	# include necessaire a la boutique?
	@define('_INCLUDE_ECOMMERCE', '/include/ecommerce_mysql_engine');
	}


//
// DEBUGGING MODE
//
//	echo "<p><strong>".("panier.php [$retour]")."</strong> ";
//	exit;
//
// FIN
//


function boutique_mysql_connect ()
	{
	if (!$link = mysql_connect('', '', ''))
		{
		echo 'Connexion impossible � mysql';
		return 0;
		}
	if (!mysql_select_db('', $link))
		{
		echo 'S�lection de base de donn�es impossible';
		return 0;
		}
	return $link;
	}

function boutique_mysql_dysconnect ($link)
	{
	mysql_close($link);
	}

?>