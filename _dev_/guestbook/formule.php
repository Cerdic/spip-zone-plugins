<?php
/**
	 * Livre d'or
	 *
	 * Copyright (c) 2006
	 * Bernard Blazin  http://www.libertyweb.info
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
$maj    = date('Y-m-d H:i:s');
$nom	= $_POST['nom'];
$ville	= $_POST['ville'];
$email	= $_POST['email'];
$note	= $_POST['note'];
$texte	= $_POST['texte'];
$texte = addslashes($texte);
include_spip('base/abstract_sql');
include_spip('ecrire/inc_connect');
		$sql="INSERT INTO spip_livre(email, nom, ville, maj, note, texte)  VALUES ('$email', '$nom', '$ville', '$maj', '$note', '$texte')";
									 	 
          
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

mysql_close();
		
		echo "Merci<BR>";
		echo "<a href='spip.php?page=livredor2'>retour</a>";
?>
