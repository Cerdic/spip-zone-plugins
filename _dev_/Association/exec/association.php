<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & Fran�ois de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
	include_spip('inc/presentation');
	include_spip('inc/gestion_base');

function exec_association() {
global $connect_statut, $connect_toutes_rubriques;

if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
asso_verifier_base();		
			
debut_page(_T('asso:association'), "naviguer", "association");

$url_edit_adherent = generer_url_ecrire('edit_adherent');

debut_gauche();
      	debut_boite_info();
      	echo propre(_T('asso:info_doc'));  	
      	fin_boite_info();

include_spip('inc/raccourcis');

debut_droite();	

	debut_cadre_formulaire();
		gros_titre(_T('asso:Votre association'));
		$query = "SELECT * FROM spip_asso_profil WHERE id_profil=1";

$val = spip_query (${query}) ;
$i=0;


while ($data = mysql_fetch_assoc($val))
    {	
    	$i++;
	echo '<br>';				
	echo '<strong>'.$data['nom'].'</strong><br>';
	//echo $data['numero'].'&nbsp;';
	echo $data['rue'].'<br>';
	echo $data['cp'].'&nbsp;';
	echo $data['ville'].'<br>';
	echo $data['telephone'].'<br>';
	echo $data['mail'].'<br>';
	echo $data['siret'].'<br>';
	echo $data['declaration'].'<br>';
	echo $data['prefet'].'<br>';
	echo _T('asso:President').' : '.$data['president'].'<br>';
	}
	
	fin_cadre_formulaire();
	
echo '<br />';
gros_titre(_T('asso:Votre &eacute;quipe'));		
echo '<br />';	
	
	debut_cadre_relief();

$query = "SELECT * FROM spip_asso_adherents WHERE fonction != '' AND statut != 'sorti' ORDER BY nom ";
$val = spip_query ($query) ;

echo '<table border=0 cellpadding=2 cellspacing=0 width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
echo '<tr bgcolor="#DBE1C5">';
echo '<td><strong>Nom</strong></td>';
echo '<td><strong>Email</strong></td>';
echo '<td><strong>Fonction</strong></td>';
echo '<td><strong>Portable</strong></td>';
echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
echo '</tr>';
while ($data = mysql_fetch_assoc($val))
    {	
echo '<tr style="background-color: #EEEEEE;">';
echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;"><a href="'.$url_edit_adherent.'&id='.$data['id_adherent'].'" title="Modifier l\'administrateur">'.$data['nom'].' '.$data['prenom'].'</a></td>';
echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;"><a href="mailto:'.$data['email'].'"title="Envoyer un email">email</a></td>';
echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['fonction'].'</td>';
echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['portable'].'</td>';
echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['telephone'].'</td>';
echo '</tr>';
}
echo '</table>';

	fin_cadre_relief();	
		fin_page();
		
//Petite routine pour mettre � jour les statuts de cotisation "�chu"
$sql = "UPDATE spip_asso_adherents SET statut='echu' WHERE statut = 'ok' AND validite < CURRENT_DATE() ";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

//ROUTINE ID_AUTEUR
//Enregistrement de l'id_auteur d'emails correspondants
$sql = "UPDATE spip_asso_adherents INNER JOIN spip_auteurs ON spip_asso_adherents.email=spip_auteurs.email SET spip_asso_adherents.id_auteur= spip_auteurs.id_auteur WHERE spip_asso_adherents.email<>'' ";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

	}

?>
