<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & FranÃ§ois de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
include_spip('inc/presentation');

function exec_adherents(){

global $connect_statut, $connect_toutes_rubriques, $table_prefix;

if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
debut_page(_T('Gestion pour  Association'), "", "");

$url_adherents = generer_url_ecrire('adherents');
$url_ajout_cotisation = generer_url_ecrire('ajout_cotisation');
$url_edit_adherent = generer_url_ecrire('edit_adherent');
$url_voir_adherent = generer_url_ecrire('voir_adherent');
$url_action_adherents = generer_url_ecrire('action_adherents');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les membres actifs'));
debut_boite_info();

print('Nous sommes le '.date('d/m/Y').'');

//Bricolage?
if ( isset ($_POST['filtre'] )) {
	$filtre = $_POST['filtre'];}
	elseif ( isset ($_GET['filtre'] )) {
		$filtre =  $_GET['filtre'];}
		else { $filtre = 'defaut';}
		
			switch($filtre)
    {
	case "defaut": 
        $critere= "statut <> 'sorti'";
        break;
	case "ok":
        $critere="statut='ok'";
	   break;
	case "echu":
        $critere="statut='echu'";  
        break;
	case "relance":
        $critere="statut='relance'";
        break;
	case "sorti":
        $critere="statut='sorti'";
        break;	   
	case "prospect":
        $critere="statut='prospect'"; 
        break;
    case "tous":
        $critere="statut LIKE '%'";
        break;	
     }

echo '<table width="70%">';
echo '<tr>';

// PAGINATION ALPHABETIQUE
echo '<td>';

$lettre=$_GET['lettre'];
if ( empty ( $lettre ) ) { $lettre = "%"; }

$query = "SELECT upper( substring( nom, 1, 1 ) )  AS init FROM spip_asso_adherents WHERE $critere GROUP BY init ORDER by nom, id_adherent ";
$val = spip_query ($query) ;

while ($data = mysql_fetch_assoc($val))
   {
 	if($data['init']==$lettre)
	{echo ' <strong>'.$data['init'].'</strong>';}
	else {echo ' <a href="'.$url_adherents.'&lettre='.$data['init'].'&filtre='.$filtre.'">'.$data['init'].'</a>';}
	}
	if( $lettre == "%")
	{echo ' <strong>Tous</strong>';}
	else {echo ' <a href="'.$url_adherents.'&filtre='.$filtre.'">Tous</a>';}

// FILTRES
echo '<td style="text-align:right;">';
	// ID
if ( isset ($_POST['id'])) {
$id=$_POST['id'];
$critere="id_adherent='$id'";}
//$critere="id_asso='$id'";}

echo '<form method="post" action="'.$url_adherent.'">';
echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10" value="ID" onchange="form.submit()">';
//echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10" value="'._T('asso:ref_int').'" onchange="form.submit()">';
echo '</form>';

echo '<td style="text-align:right;">';
	//STATUT
echo '<form method="post" action="'.$url_adherent.'">';
echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
echo '<select name ="filtre" class="fondl" onchange="form.submit()">';
echo '<option value="defaut"';
	if ($filtre=="defaut") {echo ' selected="selected"';}
	echo '> Actifs';
echo '<option value="ok"';
	if ( $filtre=="ok" ) {echo ' selected="selected"';}
	echo '> A jour';
echo '<option value="echu"';
	if ( $filtre=="echu" ) {echo ' selected="selected"';}
	echo '> A relancer';
echo '<option value="relance"';
	if ( $filtre=="relance" ) {echo ' selected="selected"';}
	echo '> Relanc&eacute;s';
echo '<option value="sorti"';
	if ( $filtre=="sorti" ) {echo ' selected="selected"';}
	echo '> DÃ©sactiv&eacute;s';
echo '<option value="prospect"';
	if ( $filtre=="prospect" ) {echo ' selected="selected"';}
	echo '> Prospects';
echo '<option value="tous"';
	if ( $filtre=="tous" ) {echo ' selected="selected"';}
	echo '> Tous';
echo '</select>';
echo '</form>';
echo '</table>';

//Affichage de la liste
echo '<table width="70%">';
echo '<form method="post" action="'.$url_action_adherents.'">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Photo</strong></td>';
echo '<td><strong>NOM</strong></td>';
echo '<td><strong>Pr&eacute;nom</strong></td>';
//echo '<td><strong>Fonction</strong></td>';
//echo '<td><strong>Email</strong></td>';
//echo '<td><strong>N&deg;</strong></td>';
//echo '<td><strong>Rue</strong></td>';
//echo '<td><strong>Code Postal</strong></td>';
echo '<td><strong>Ville</strong></td>';
//echo '<td><strong>Portable</strong></td>';
//echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
echo '<td><strong>'._T('asso:ref_int').'</strong></td>';
echo '<td><strong>Cat&eacute;gorie</strong></td>';
echo '<td><strong>Validit&eacute;</strong></td>';
//echo '<td><strong>A jour</strong></td>';
//echo '<td><strong>Notes</strong></td>';
echo '<td colspan="4" style="text-align:center;"><strong>Action</strong></td>';
echo '<td><strong>Sup</strong></td>';
echo '</tr>';


$max_par_page=30;
$debut=$_GET['debut'];

if (empty($debut))
{$debut=0;}

if (empty($lettre))
{$query = spip_query ( "SELECT spip_asso_adherents.*, spip_asso_categories.libelle AS libelle_categorie FROM spip_asso_adherents LEFT JOIN spip_asso_categories ON (spip_asso_categories.id_categorie=spip_asso_adherents.categorie) WHERE $critere ORDER BY nom LIMIT $debut,$max_par_page" );}
else
{$query = spip_query ( "SELECT spip_asso_adherents.*, spip_asso_categories.libelle AS libelle_categorie FROM spip_asso_adherents LEFT JOIN spip_asso_categories ON (spip_asso_categories.id_categorie=spip_asso_adherents.categorie) WHERE upper( substring( nom, 1, 1 ) ) like '$lettre' AND $critere ORDER BY nom LIMIT $debut,$max_par_page" );}

$i=0;

while ($data = mysql_fetch_assoc($query))
    {	
    	$i++;
	$id_adherent=$data['id_adherent'];
	
	switch($data['statut'])
    {
    case "echu":
        $class= "impair";
        break;
    case "ok":
        $class="valide";
	   break;
    case "relance":
        $class="pair";	   
        break;
    case "sorti":
		$class="sortie";	   
        break;
    case "prospect":
		$class="prospect";	   
        break;	   
     }

echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right;">'.$data["id_adherent"].'</td>';
echo '<td class ="'.$class.'">';
if (empty ($data['id_auteur']))
{echo'';}
else {
echo'<img src="/IMG/auton'.$data['id_auteur'].'.jpg" width="60" eight= "60" title="'.$data["nom"].' '.$data["prenom"].'">';
}
/*
if (empty ($data['vignette']))
{echo'';}
else {echo'<img src="/IMG/assologo'.$data['id_adherent'].'" width="60" eight= "60" title="'.$data["nom"].' '.$data["prenom"].'">';}
*/
echo '</td>';
echo '<td class ='.$class.'>';
if (empty($data["email"])) 
{ echo $data["nom"].'</td>'; }
else
{ echo '<a href="mailto:'.$data["email"].'">'.$data["nom"].'</a></td>'; }
echo '<td class ='.$class.'>'.$data["prenom"].'</td>';
//echo '<td class ='.$class.'>'.$data["fonction"].'</td>';
//echo '<td class ='.$class.' style="text-align:right;">'.$data["numero_ad"].'</td>';
//echo '<td class ='.$class.'>'.$data["rue_ad"].'</td>';
//echo '<td class ='.$class.'>'.$data["cp_ad"].'</td>';
echo '<td class ='.$class.'>'.$data["ville"].'</td>';
//echo '<td class ='.$class.'>'.$data["portable"].'</td>';
//echo '<td class ='.$class.'>'.$data["telephone"].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data["id_asso"].'</td>'; //référence interne
echo '<td class ='.$class.'>'.$data["categorie"].'</td>';
echo '<td class ='.$class.'>'.association_datefr($data['validite']).'</td>';
//echo '<td class ='.$class.' style="text-align:center;"><img src="/ecrire/img_pack/'.$puce.'" title="'.$title.'"></td>';
//echo '<td class ='.$class.'>'.$data["remarques"].'</td>';
echo '<td class ='.$class.'>';

if (isset($data["id_auteur"])) {
$id_auteur= $data["id_auteur"];
$sql = "SELECT * FROM spip_auteurs WHERE id_auteur='$id_auteur' ";
$req = spip_query ($sql) ;
while ($auteur = mysql_fetch_assoc($req))
    {
	switch($auteur['statut'])
    {
    case "0minirezo":
        $logo= "admin-12.gif";
        break;
    case "1comite":
        $logo="redac-12.gif";
	   break;
    case "6forum":
        $logo="visit-12.gif";	      
     }
echo '<a href="/ecrire/?exec=auteurs_edit&id_auteur='.$data["id_auteur"].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/'.$logo.'" title="Modifier le visiteur"></a>';
}
}
else
{ echo '&nbsp;</td>'; }
//echo '<td class ='.$class.'>';
//if (empty($data["email"])) 
//{ echo '&nbsp;</td>'; }
//else
//echo '<a href="mailto:'.$data["email"].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/mail-12.png" title="envoyer un courrier"></a>';
//echo '<td class ='.$class.'><input name="cotisation[]" type="checkbox" value='.$id_adherent.'></td>';
echo '<td class ='.$class.'><a href="'.$url_ajout_cotisation.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/cotis-12.gif" title="Ajouter une cotisation"></a>';
echo '<td class ='.$class.'><a href="'.$url_edit_adherent.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier le membre"></a>';
echo '<td class ='.$class.'><a href="'.$url_voir_adherent.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="Voir le membre"></a>';
echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_adherent'].'></td>';

echo '</tr>';
}

echo '</table>';

echo '<table width="70%">';
echo '<tr>';

//SOUS-PAGINATION
echo '<td>';
if (empty($lettre))
{$query = "SELECT * FROM spip_asso_adherents WHERE $critere";}
else
{$query = "SELECT * FROM spip_asso_adherents WHERE upper( substring( nom, 1, 1 ) ) like '$lettre'  AND $critere";}
$val= spip_query($query);
$nombre_selection=spip_num_rows($val);
$pages=intval($nombre_selection/$max_par_page) + 1;

if ($pages == 1)	
{ echo '';}
else {
	for ($i=0;$i<$pages;$i++)
	{ 
	$position= $i * $max_par_page;
	if ($position == $debut)
	{ echo '<strong>'.$position.' </strong>'; }
	else 
	{ echo '<a href="'.$url_adherents.'&lettre='.$lettre.'&debut='.$position.'&filtre='.$filtre.'">'.$position.'</a> '; }
	}	
}
echo '<td  style="text-align:right;">';
echo '<input type="submit" name="Submit" value="Envoyer" class="fondo">';
echo '</table>';


echo '</form>';

echo '<p>En bleu : Relanc&eacute; | En rose : A &eacute;ch&eacute;ance | En vert : A jour<br> En brun : D&eacute;sactiv&eacute; | En jaune paille : Prospect</p>'; 

// TOTAUX
$query = "SELECT montant FROM spip_asso_adherents WHERE statut ='ok'";
$val = spip_query ($query) ;
$nombre_membres=spip_num_rows($val);
$query = "SELECT sum(montant) AS somme FROM spip_asso_adherents WHERE statut ='ok'";
$val = spip_query($query) ;
$caisse = mysql_fetch_assoc($val);

echo '<p><font color="#9F1C30"><strong>Total des cotisations : ', $caisse['somme'], ' &euro;<br /> </strong></font><br/>';
echo '<font color="blue"><strong>Nombre d\'adh&eacute;rents : ',$nombre_membres,'</strong></font></p>';

fin_boite_info();  
fin_cadre_relief();  
fin_page();}
?>
