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
function exec_repondre(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('livre:lelivre'), "", "");
debut_gauche();
echo "<br /><br />";
gros_titre(_T('livre:repondremdl'));

debut_cadre_relief();
 $date1 = date("d-m-Y");
$heure = date("H:i");
Print("Nous sommes le $date1 et il est $heure");      
echo"<br>";
icone_horizontale(_T('livre:retour'), generer_url_ecrire("livre"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png');
$date= date('Y-m-d H:i:s');
if (isset($_POST['repond'])) {
    
    for ($i = 0, $c = count($_POST['repond']); $i < $c; $i++) {
	$rep=$_POST['repond'][$i];
        
		
		echo " Vous avez dej&agrave; r&eacute;pondu aux messages : ";
		$query = "SELECT id_messages FROM spip_reponses_livre order by date";
					$res = spip_query($query);
					while ($row =spip_fetch_array($res)){
					$id_message1=$row['id_messages'];
					echo $id_message1," ,";
					
					
    }}
}
echo "<form action='' method='post'>";
		echo 'Id: <input name="id_rep" type="text" value=',$rep,' readonly="true"><br>';
		echo 'R&eacute;ponse: <textarea name="reponses" cols="" rows="5" ></textarea><br>';
		echo 'Votre nom: <input name="nom" type="text"><br>';
		echo' <input name="submit" type="submit" value="Envoyer"><br>';
		echo '</form>';
		
		$rep=$_POST['id_rep'];
		$texte=$_POST['reponses'];
		$nom=$_POST['nom'];
	$texte = addslashes($texte);
$texte=nl2br($texte); 
if($nom == ''){
      echo '';
}    
else {
	$sql="INSERT INTO spip_reponses_livre(id_messages, date, reponses, nom)  VALUES ('$rep', '$date', '$texte', '$nom')";     
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
}
 fin_cadre_relief();
fin_page();
                        exit;
                }
?>