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
include_spip('inc/presentation');
function exec_efface(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('Le LIVRE'), "", "");
debut_gauche();
echo "<br /><br />";
gros_titre(_T('Destruction  des tables'));

debut_cadre_relief();
 $date = date("d-m-Y");
$heure = date("H:i");
Print("Nous sommes le $date et il est $heure");      
echo"<br>";
icone_horizontale(_T('livre:retour'), generer_url_ecrire("livre"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png');
echo "La suppression des tables supprimera toutes les données du livre d'or<br>";
echo"Confirmez !";
echo "<form action='' method='post'>";
//echo '<input  type="checkbox" value="oui">oui';
echo ' <input type="radio" name="reponse" value="1" />
oui ';
echo'<input type="radio" name="reponse" value="2" />
 non<br>';
echo '<input name="submit" type="submit" value="valider"><br><br>';
if($_POST["reponse"]==2){print "<meta http-equiv='refresh' content=\"0;URL=?exec=livre\">";}
elseif($_POST["reponse"]==1){
//fonction drop table à fabriquer
// $requete="DROP table if exists contenu"; 

//  
include_spip('base/abstract_sql');
include_spip('ecrire/inc_connect');
mysql_query("DROP TABLE spip_reponses_livre"); 
 mysql_query("DROP TABLE spip_livre"); 

//fin fonction
echo"Les 2 tables sont détruites";}
else{echo"Vous n'avez rien coché !";}

	
//$value=$_POST['0'];
//$values=$_POST['1'];
//if ($value[0]==0){
 //generer_url_ecrire("livre");}
 fin_cadre_relief();
fin_page();
                        exit;
                }
?>

