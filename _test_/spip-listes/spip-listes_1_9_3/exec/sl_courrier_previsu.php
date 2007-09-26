<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/filtres');
include_spip('inc/lang');

// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com

function exec_sl_courrier_previsu(){

	$template 		= _request('template');
	$sujet 			= _request('sujet');
	$message 		= _request('message');
	$Confirmer  	= _request('Confirmer');
	$date 			= _request('date');
	$id_rubrique	= _request('id_rubrique');
	$id_mot			= _request('id_mot');
	$id_courrier 	= _request('id_courrier');
	$charset 		= lire_meta('charset');
	
/*	echo "<pre>";
	print_r($GLOBALS);
	echo "</pre>";*/
	include_spip('public/assembler');
	$contexte_template = array('date' => trim ($date),
							   'id_rubrique' => $id_rubrique,
							    'id_mot' => $id_mot,
							   'template'=>$template,
							   'lang'=>$lang, 
							   'sujet'=>$sujet,
							   'message'=>$message );
	
	if (find_in_path('patrons/'.$template.'_texte.html')){
		$patron_version_texte = true ;
		$message_texte =  recuperer_fond('patrons/'.$patron.'_texte', $contexte_template);
	}
	

	
	// Il faut utiliser recuperer_page et non recuperer_fond car sinon les url des articles
	// sont sous forme privee : spip.php?action=redirect&.... horrible !
	// pour utiliser recuperer_fond,il faudrait etre ici dans un script action
	//	$texte_patron = recuperer_fond('patrons/'.$template, $contexte_template);
	
	
	$url = generer_url_public('patron_switch','',true);
	foreach ($contexte_template as $k=>$v)
		$url = parametre_url($url,$k,$v,'&');
	
	// $texte_patron = recuperer_page($url) ;	

	$texte_patron = recuperer_fond('patrons/'.$template, $contexte_template);

	$titre_patron = _T('spiplistes:lettre_info')." ".$nomsite;
	
	$titre = $titre_patron;
	$texte = $texte_patron;

	if((strlen($texte) > 10))
		spip_query("UPDATE spip_courriers SET titre="._q($titre).", texte="._q($texte).", message_texte="._q($message_texte)." WHERE id_courrier="._q($id_courrier));
	else
		$message_erreur = _T('spiplistes:patron_erreur');


	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
	echo "<html lang='$lang' dir='ltr'>";
	echo "<head><meta http_equiv='Content-Type' content='text/html; charset=".$charset."'>\n<meta http-equiv=\"Pragma\" content=\"no-cache\">\n
	</script>\n
	
	</head><body>\n";

/*
 echo "<div style='text-align:left;border:1px solid #000;background: yellow;color: #000;margin-bottom: 10px;padding:10px;'>";  
  echo "<p><strong>$patron</strong><p>\n";
  if($patron_version_texte) echo _T('spiplistes:patron_detecte');
  echo _T('spiplistes:date_ref').": $date<br />";
  
  echo menu_langues('changer_lang', $lang , '<strong>Langue :</strong>&nbsp;','', generer_url_ecrire('import_patron','id_message='.$id_courrier.'&patron='.$patron.'&date='.$date ) );
	echo "</div>";
*/


		// si confirmation
	
	
	echo "<form id='choppe_patron-1' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_MODIF,"id_message=$id_courrier")."' method='post' name='choppe_patron-1'>";
	echo "<input type='hidden' name='modifier_message' value=\"oui\" />";
	echo "<input type='hidden' name='id_message' value=\"$id_message\" />";
	if(!intval($id_courrier))
		echo "<input type='hidden' name='new' value=\"oui\" />";
		
		echo "<input type=\"hidden\" name=\"titre\" value=\"".$sujet."\">";
		echo "<input type=\"hidden\" name=\"texte\" value=\"".htmlspecialchars($texte)."\">";
		echo "<input type=\"hidden\" name=\"date\" value=\"".$date."\">";

		echo "<div style='background-color:white;margin-top:5px;width:600px;margin:auto'>" ;
		
		echo liens_absolus($texte).$message_erreur."";
		
	
	//echo spiplistes_propre($texte_patron).$message_erreur;
	
	$contexte_pied = array('lang'=>$lang);
	$texte_pied = recuperer_fond('modeles/piedmail', $contexte_pied);

	echo $texte_pied;
		
		echo "</div><br/><br/>";

		debut_cadre_formulaire();
		/*
		echo "Envoyer ce courrier &agrave; cette liste de diffusion :<br />";
		$result = spip_query("SELECT email, titre FROM spip_abomailmans");
			echo "<select name='email_liste' class='formo'>";
			while ($row = spip_fetch_array($result)) {
				echo "<option value='".$row['email']."'>".$row['titre']." -> ".$row['email']."</option>\n";
			}
			echo "</select>";
		*/
		echo "<div id='cacher_confirmer'><br /><input name=\"Valider\" type=\"submit\" value=\""._T("Confirmer")."\" id=\"Confirmer\"></div>";
		echo "</form>";
		fin_cadre_formulaire();

	

	echo "</body></html>";
	unset ($_POST);

}	

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>