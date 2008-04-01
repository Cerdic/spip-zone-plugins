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

function exec_ajout_adherent() {
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:adherent_titre_ajout_adherent'), "", "");

	$url_asso = generer_url_ecrire('association');
	$url_action_adherents = generer_url_ecrire('action_adherents');
	$url_retour = $_SERVER['HTTP_REFERER'];

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_ajouter_membre_actif'));
	debut_boite_info();

	print  association_date_du_jour();

	echo' <p align="center"><form action="'.$url_action_adherents.'" method="POST">';

//FORMULAIRE MEMBRE
echo '<fieldset><legend>'._T('asso:adherent_titre_ajouter_membre').'</legend>';
#donées personnelles
echo '<strong>'._T('asso:donnees_perso').'</strong>';
echo '<table width="90%" class="noclass">';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_reference_interne').' :</td>';
echo '<td><input name="id_asso" type="text"></td></tr>';
echo '<tr> ';
echo '<td >'._T('asso:adherent_libelle_nom').' :</td>';
echo '<td><input name="nom" type="text" size="40"></td>';
echo '<td >'._T('asso:adherent_libelle_prenom').' :</td>';
echo '<td><input name="prenom" type="text" size="40"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_date_naissance').' (AAAA-MM-JJ) :</td>';
echo '<td><input name="naissance" type="text"></td>';
echo '<td>'._T('asso:adherent_libelle_sexe').' :</td>';
echo '<td><input name="sexe" type="radio" value="H"> H ';
echo '<input name="sexe" type="radio" value="F"> F ';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_email').' :</td>';
echo '<td colspan="3"><input name="email" type="text" size="40"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_rue').' :</td>';
echo '<td ><textarea name="rue" cols="30"></textarea></td></tr>';
//echo '<td>'._T('asso:adherent_libelle_num_rue').' :</td>';
//echo '<td ><input name="numero" type="text"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_ville').' :</td>';
echo '<td><input name="ville" type="text" size="40"></td>';
echo '<td>'._T('asso:adherent_libelle_codepostal').' :</td>';
echo '<td><input name="cp" type="text"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_portable').' :</td>';
echo '<td><input name="portable" type="text"></td>';
echo '<td>'._T('asso:adherent_libelle_telephone').' :</td>';
echo '<td><input name="telephone" type="text"></td></tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_profession').' :</td>';
echo '<td><input name="profession" type="text"  size= "40"></td>';
echo '<tr> ';	
echo '<td>'._T('asso:adherent_libelle_societe').' :</td>';
echo '<td><input name="societe" type="text" size= "40"></td></tr>';
echo '</table><HR>';
#données internes
echo '<strong>'._T('asso:donnees_internes').'</strong>';
echo '<table width="90%" class="noclass">';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_fonction').' :</td>';
echo '<td><input name="fonction" type="text" size="40" value=""></td>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_categorie').' :</td>';
echo '<td><select name="categorie" type="text">';
echo '<option value = ""> '._T('asso:adherent_libelle_categorie_choix').'</option>';
$query = spip_query( "SELECT * FROM spip_asso_categories");
while($categorie = spip_fetch_array($query)) 
{
echo '<option value = "'.$categorie["valeur"].'"> '.$categorie["libelle"].'</option>';
}
echo '</select></td>';
echo '<td>'._T('asso:adherent_libelle_date_validite').' (AAAA-MM-JJ) :</td>';
echo '<td><input name="validite" type="text"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';	
echo '<td>'._T('asso:adherent_libelle_secteur').' :</td>';
echo '<td><input name="secteur" type="text"></td>';
echo '<td>'._T('asso:adherent_libelle_accord').' :</td>';
echo '<td><input name="publication" type="radio" value="oui" checked>oui';
echo '<input name="publication" type="radio" value="non" unchecked>non</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_utilisateur1').' :</td>';
echo '<td><input name="utilisateur1" type="text"></td>';
echo '<td>'._T('asso:adherent_libelle_utilisateur2').' :</td>';
echo '<td><input name="utilisateur2" type="text"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:adherent_libelle_utilisateur3').' :</td>';
echo '<td><input name="utilisateur3" type="text"></td>';
echo '<td>'._T('asso:adherent_libelle_utilisateur4').' :</td>';
echo '<td><input name="utilisateur4" type="text"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';      
echo '<td>'._T('asso:adherent_libelle_remarques').' :</td>';
echo '<td colspan="3"><textarea name="remarques" cols="65" rows="3"></textarea></td></tr>';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input type="hidden" name="action" value="ajoute"></td></tr>';
echo '<td><input name="" type="submit" value="'._T('asso:adherent_bouton_envoyer').'" class="fondo"</tr>';
echo'</table>';
echo '</form>';

echo' </fieldset>';

	fin_boite_info();
	
	fin_cadre_relief();

	fin_page();
}

?>

