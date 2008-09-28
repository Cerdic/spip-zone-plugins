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
	 
	include_spip('inc/presentation');
	function exec_livre_edition(){
	debut_page(_T('livre:lelivre'), "", "");
	echo "<br /><br />";
	gros_titre(_T('Livre d&acute;or'));
	debut_cadre_relief(  "", false, "", $titre = _T('Les Messages'));
	debut_boite_info();
	echo '<br />';
	//ici les messages sans réponse avec appel du formulaire de réponse
	echo '<form method="post" action="?exec=repondre">';
	echo'<table width="100%" border="" cellspacing="0" cellpadding="2">';
	echo'  <tr>';
	echo'    <td>id_message</td>';
	echo'    <td>message</td>';
	echo'    <td>mail</td>';
	echo'    <td>date</td>';
	echo'    <td>r&eacute;pondre</td>';
	echo'  </tr>';
	$query = "SELECT id_messages,texte,nom,email,maj FROM spip_livre order by maj";
	$res = spip_query($query);
	while ($row =spip_fetch_array($res)){
	$id_messages= $row['id_messages'];
	$texte= $row['texte'];
	$nom= $row['nom'];
	$email= $row['email'];
	sscanf($row['maj'], "%4s-%2s-%2s", $annee, $mois, $jour);
	echo'  <tr>';
	echo'  <td>',$id_messages,'</td>';
	echo'    <td>',$texte,'</td>';
	echo"    <td><a href='mailto:".$email."'>",$nom,"</a></td>";
	echo'    <td>',$jour,'/',$mois,'/',$annee,'</td>';
	echo'    <td><input name="repond[]" type="checkbox" value='.$id_messages.'></td>';
	echo'  </tr>';		
	}
	//fin
	echo'</table>';
	echo '<div align="center"><BR />';
	echo' <input type="submit" name="Submit" value="R&eacute;pondre">';
	echo' </div>';
	echo'</form>';
	fin_boite_info();
	fin_cadre_relief();
	fin_page();
	exit;
	}
?>