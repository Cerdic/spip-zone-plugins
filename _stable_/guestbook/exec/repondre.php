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
	function exec_repondre(){
	global $connect_statut, $connect_toutes_rubriques;
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('guestbook:lelivre'), "", "");
	if (isset($_GET['id_message'])) {
		echo "<br /><br />";
	echo '<div style="margin:auto; width :50%;">';
	$date = date('Y-m-d H:i:s');
	echo"<br />";
		$rep=$_GET['id_message'];
		
		
		$sqlask = sql_select('nom, message', 'spip_guestbook', 'id_message='.$rep);
		$ask= spip_fetch_array($sqlask);
		$asp= $ask['nom'];
		$tex=$ask['message'];

$texte='';
$nom='';

		
	$texte=$_POST['message'];
	$nom=$_POST['nom'];
	$texte = addslashes($texte);
	$texte=nl2br($texte); 
		
		if($nom == '' OR $texte=''){
			
		echo "<form action='?exec=repondre&id_message=".$rep."' method='post'>";
		echo '<input type="hidden" name="id_rep" value="'.$rep.'" />';
	echo $asp.' a &eacute;crit :<br /> <i>"'.$tex.'"</i><br /><br />';
	$sql2= spip_query("SELECT auteur, message FROM  spip_guestbook_reponses WHERE id_message='".$rep."'");
	while ($ask2= mysql_fetch_array($sql2))
	{
		$name= $ask2['auteur'];
		$repons=$ask2['message'];
	echo $name.' a répondu  : <i>"'.$repons.'"</i><br />';
	}
	echo 'R&eacute;ponse: <textarea name="reponses" cols="50" rows="5" >'.$texte.'</textarea><br />';
	$nom=$GLOBALS['visiteur_session']['login'];
	echo 'Votre nom:  '.$nom.' <input name="nom" type="hidden" value="'.$nom.'"><br /><br />';
	echo "Remplissez tous les champs! <br /><br />";
	echo ' <input name="submit" type="submit" value="Envoyer"><br />';
	echo '</form>';
		
	}
	
	else {

$text=$_POST['reponses'];
		sql_insertq('spip_guestbook_reponses', array('id_message' => $rep, 'date' => $date, 'message' => $text, 'auteur' => $nom));     
		echo $asp.' a &eacute;crit : <i>"'.$tex.'"</i><br />';
		echo $nom.' a r&eacute;pondu : <i>"'.$text.'"</i><br />';
		
	}
	echo '</div>';
	}
	else{ 
		echo '<br /><br /> '.gros_titre(Warning);
				debut_boite_info();
				
				echo '<a href="?exec=livre">Vous n\'avez pas s&eacute;l&eacute;ctionn&eacute; d\'utilisateur. Retourner &agrave; la page des commenatires</a>'; }
	
		fin_cadre_relief();
	fin_page();
	exit;
	}
?>