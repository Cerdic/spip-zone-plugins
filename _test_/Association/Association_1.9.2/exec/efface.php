<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
include_spip('inc/presentation');
function exec_efface(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('Association'), "", "");
debut_gauche();
echo "<br /><br />";
gros_titre(_T('Destruction  des tables'));

debut_cadre_relief();
print association_date_du_jour(true);

icone_horizontale(_T('retour'), generer_url_ecrire("association"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/annonce.gif');
echo "La suppression des tables supprimera toutes les donn&eacute;es de votre gestion d'association<br>";
echo"Confirmez !";
echo "<form action='' method='post'>";
//echo '<input  type="checkbox" value="oui">oui';
echo ' <input type="radio" name="reponse" value="1" />
oui ';
echo'<input type="radio" name="reponse" value="2" />
 non<br>';
echo '<input name="submit" type="submit" value="valider"><br><br>';
if($_POST["reponse"]==2){print "<meta http-equiv='refresh' content=\"0;URL=?exec=association\">";}
elseif($_POST["reponse"]==1){
//fonction drop table à fabriquer
// $requete="DROP table if exists contenu"; 

//  
include_spip('base/abstract_sql');
include_spip('ecrire/inc_connect');


echo $output;
spip_query("DROP TABLE spip_asso_adherents"); 
spip_query("DROP TABLE spip_asso_bienfaiteurs"); 
spip_query("DROP TABLE spip_asso_ventes"); 
spip_query("DROP TABLE spip_asso_comptes"); 
spip_query("DROP TABLE spip_asso_profil"); 
spip_query("DROP TABLE spip_asso_categories"); 
spip_query("DROP TABLE spip_asso_financiers"); 
spip_query("DROP TABLE spip_asso_dons"); 
spip_query("DROP TABLE spip_asso_livres"); 
spip_query("DELETE FROM spip_meta WHERE nom='asso_base_version'");

//fin fonction
echo"Toutes les  tables sont d&eacute;truites";}
else{echo"Vous n'avez rien coch&eacute; !";}

	
//$value=$_POST['0'];
//$values=$_POST['1'];
//if ($value[0]==0){
 //generer_url_ecrire("livre");}
 fin_cadre_relief();
fin_page();
                        exit;
                }
?>


