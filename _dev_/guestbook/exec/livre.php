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
	function exec_livre() {
	if ($admin AND $connect_statut != "0minirezo") {
		echo _T('avis_non_acces_page');
		exit;
	}
			$commencer_page = charger_fonction('commencer_page', 'inc');
	echo	$commencer_page(_T('guestbook:lelivre'), "", "");
	debut_javascript();
	
	echo '<br />';
	echo '<div style="margin:auto; width :70%;">';
	echo debut_boite_info();
	echo gros_titre(_T('guestbook:lelivre'),"","");
	//ici les messages sans réponse avec appel du formulaire de réponse
	echo '<table width="70%"  style="margin:auto;" border="" cellspacing="0" cellpadding="2">';
	echo '<tr>';
	echo _T('guestbook:numero');
	echo _T('guestbook:texte');
	echo _T('guestbook:nom');
	echo _T('guestbook:date');
	echo _T('guestbook:repondre');
	echo '</tr>';
	
	$query = "SELECT * FROM spip_guestbook ORDER BY date DESC";
	$res = spip_query($query);
	while ($row =spip_fetch_array($res)){
	$id_messages= $row['id_messages'];
	$texte= $row['texte'];
	$nom= $row['nom'];
	$email= $row['email'];
	sscanf($row['maj'], "%4s-%2s-%2s", $annee, $mois, $jour);
	
	echo '<tr>';
	echo '<td>'.$id_messages.'</td>';
	echo '<td>'.$texte.'</td>';
	echo "<td><a href='mailto:".$email."'>".$nom."</a></td>";
	echo '<td>'.$jour.'/'.$mois.'/'.$annee.'</td>';
	echo "<td>";
	$sql2= spip_query("SELECT * FROM  spip_guestbook_reponses WHERE id_message='".$id_messages."'");
	while ($ask2= mysql_fetch_array($sql2))
	{
		$name= $ask2['nom'];
		$repons=$ask2['reponses'];
	echo $name.' a répondu  : <i>"'.$repons.'"</i><br />';
	}
	echo " <a href='?exec=repondre&id_message=".$id_messages."'>R&eacute;pondre</a></td>";
	echo '</tr>';		
	}
	//fin
	echo '</table>';
fin_boite_info();
echo '</div>';
	fin_page();
	}
?>