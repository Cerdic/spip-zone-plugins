<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Yohann Prigent (potter64) repris des travaux de Bernard Blazin (http://www.plugandspip.com )
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

	include_spip('inc/presentation');
	function exec_livre() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('guestbook:lelivre'), "", "");
	echo '<div style="margin:auto; width :70%;">';
	echo gros_titre(_T('guestbook:lelivre'),"","");
	//Légende
	echo '<table width="20%" style="margin:auto;" border="" cellspacing="0" cellpadding="2"><tr><td style="background:#cce7b9">Message publi&eacute;</td><td style="background:#e7a1a1">Message Hors Ligne</td><td style="background:#f1dec0">Message en attente de validation</td></tr></table><br /> ';
	//ici les messages sans réponse avec appel du formulaire de réponse
	echo '<table width="70%"  style="margin:auto;" border="" cellspacing="0" cellpadding="2">';
	echo '<tr>';
	echo _T('guestbook:numero');
	echo '<td>Note</td>';
	echo _T('guestbook:texte');
	echo _T('guestbook:nom');
	echo _T('guestbook:date');
	echo _T('guestbook:repondre');
	echo '<td>Statut</td>';
	echo '</tr>';
	if($_GET['statut'])
	{
		$statut=$_GET['statut'];
		$idmessage=$_GET['id_message'];
		sql_updateq("spip_guestbook", array("statut" => $statut), "id_message=$idmessage");
		}
	$query = "SELECT * FROM spip_guestbook ORDER BY date DESC LIMIT 0,60";
	$res = spip_query($query);
	while ($row =spip_fetch_array($res)){
	$id_message= $row['id_message'];
	$post_ip = $row['ip'];
	if($post_ip)  $post_ip= "(".$post_ip.")";
	else $post_ip="";
	$texte= $row['message'];
	$nom= $row['nom'];
	$email= $row['email'];
	$statut=$row['statut'];
	$note=$row['note'];
	sscanf($row['date'], "%4s-%2s-%2s", $annee, $mois, $jour);
	if ($statut == 'publie')
	{
		$faire= "<a href='?exec=livre&id_message=".$id_message."&statut=HL#message".$id_message."''>Mettre Hors-ligne</a>";
		$font= "#cce7b9";
		}
	elseif ($statut == 'HL')
	{
		$faire= "<a href='?exec=livre&id_message=".$id_message."&statut=publie#message".$id_message."'>Publier</a><br />$post_ip";
		$font="#e7a1a1";
		}
	else{
		$faire= "<a href='?exec=livre&id_message=".$id_message."&statut=publie#message".$id_message."''>Publier</a><br />";
		$faire .= "<a href='?exec=livre&id_message=".$id_message."&statut=HL#message".$id_message."''>Mettre Hors-ligne</a>";
		$font="#f1dec0";
		}
	echo '<tr style="background-color: '.$font.';">';
	echo '<td>'.$id_message.'</td>';
	echo '<td>'.$note.'</td>';
	echo '<td style="width: 100px;" id=message'.$id_message.'>'.$texte.'</td>';
	echo "<td><a href='mailto:".$email."'>".$nom."</a></td>";
	echo '<td>'.$jour.'/'.$mois.'/'.$annee.'</td>';
	echo "<td>";
	$sql2= spip_query("SELECT * FROM  spip_guestbook_reponses WHERE id_message='".$id_message."'");
	while ($ask2= mysql_fetch_array($sql2))
	{
		$name= $ask2['auteur'];
		$repons=$ask2['message'];
		
	echo $name.' a r&eacute;pondu  : <i>"'.$repons.'"</i><br />';
	}
	echo " <a href='?exec=repondre&id_message=".$id_message."'>R&eacute;pondre</a></td>";
	echo "<td>";
	echo $faire;
	echo '</td></tr>';		
	}
	//fin
	echo '</table>';
	echo '</div>';
	}
?>