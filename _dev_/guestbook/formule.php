<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
	include_spip('base/abstract_sql');
	$maj = date('Y-m-d H:i:s');
	$nom = $_POST['nom'];
	$ville = $_POST['ville'];
	$email = $_POST['email'];
	$note = $_POST['note'];
	$texte = $_POST['texte'];
	$texte = nl2br($texte); 
	if(isset ($nom AND $texte)){
		sql_insertq("spip_livre", array('email' => $email, 'nom' => $nom, 'ville' => $ville, 'maj' => $maj, 'note' => $note, 'texte' => $texte));
		echo _T('livre:mercivp');
	}    
	else {
		echo _T('livre:rienmis');
	}
?>
