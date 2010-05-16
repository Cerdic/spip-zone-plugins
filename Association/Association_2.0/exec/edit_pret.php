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
if (!defined("_ECRIRE_INC_VERSION")) return;
	
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
function exec_edit_pret(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		

		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$action=$_REQUEST['agir'];
		$id_pret= intval(_request('id_pret'));
		$data = !$id_pret ? '' : sql_fetsel('*', 'spip_asso_prets', "id_pret=$id_pret");
		if ($data) {
			$id_ressource=intval($data['id_ressource']);
			$duree=$data['duree'];
			$id_emprunteur=$data['id_emprunteur'];
			$commentaire_sortie=$data['commentaire_sortie'];
			$commentaire_retour=$data['commentaire_retour'];
			$date_retour=$data['date_retour'];
			$date_sortie=$data['date_sortie'];
			$action = 'modifier';
			$texte = _T('asso:bouton_modifie');
		} else {
			$action = 'ajouter';
			$id_ressource= $id_pret;
			$texte = _T('asso:bouton_ajoute');
			$date_retour=$date_sortie=date('Y-m-d');
			$id_emprunteur=$commentaire_sortie=$commentaire_retour='';
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_edition_prets')) ;
		
		association_onglets();
		
		echo debut_gauche('',true);
		
		echo debut_boite_info(true);
		$query = sql_select("*", "spip_asso_ressources", "id_ressource=$id_ressource" ) ;
		while ($data = sql_fetch($query)) {
			$statut=$data['statut'];
			echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
			echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
			echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
			echo $data['intitule'];
			echo '</p>';
		}
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite('', true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:prets_titre_edition_prets'));
		
		$data = sql_fetsel("pu,statut", "spip_asso_ressources", "id_ressource=$id_ressource");
		$statut=$data['statut']; 
		$pu=$data['pu'];
		
		$query = sql_select("*", "spip_asso_comptes", "id_journal=$id_pret ");
		while($data = sql_fetch($query)) {
			$journal=$data['journal']; 
			$montant=$data['recette'];
		}
		
		if( $action=="ajouter" ){ 
			$montant=$pu; 
			$date_sortie=date('Y-m-d');
		} 
		
		// Cadre R�servation
		$res = '<fieldset>'
		. '<legend>'._T('asso:prets_entete_reservation').'</legend>'
		. '<label for="date_sortie"><strong>'
		. _T('asso:prets_libelle_date_sortie')." :</strong></label>\n"
		. '<input name="date_sortie" type="text" value="'
		. $date_sortie.'" id="date_sortie" class="formo" />'
		. '<label for="duree"><strong>'
		. _T('asso:prets_libelle_duree')." :</strong></label>\n"
		. '<input name="duree" type="text" value="'
		. $duree.'" id="duree" class="formo" />'
		. '<label for="id_emprunteur"><strong>'
		. _T('asso:prets_libelle_num_emprunteur')
		. " :</strong></label>\n"
		. '<input name="id_emprunteur" type="text" value="'
		. $id_emprunteur.'" id="id_emprunteur" class="formo" />'
		. '<label for="commentaire_sortie"><strong>'
		. _T('asso:prets_libelle_commentaires')." :</strong></label>\n"
		. '<textarea name="commentaire_sortie" id="commentaire_sortie" class="formo">'
		. $commentaire_sortie.'</textarea>'
		. '</fieldset>';
		
		//Cadre retour
		$res .= '<fieldset>'
		. '<legend>'. _T('asso:prets_entete_retour').'</legend>'
		. '<label for="date_retour"><strong>'
		. _T('asso:prets_libelle_date_retour')." :</strong></label>\n"
		. '<input name="date_retour" type="text" value="'
		. $date_retour
		. '" id="date_retour" class="formo" />'
		. '<label for="montant"><strong>'
		. _T('asso:prets_libelle_montant')." :</strong></label>\n"
		. '<input name="montant" type="text" value="'
		. $montant.'" id="montant" class="formo" />';

		$sel = '';
		$sql = sql_select("*", "spip_asso_plan", "classe=".sql_quote($GLOBALS['asso_metas']['classe_banques']), '', "code") ;
		while ($banque = sql_fetch($sql)) {
			$c = $banque['code'];
			$sel .= "<option value='$c'" .
			  (($journal==$c) ? ' selected="selected"' : '')
			  . '>' . $banque['intitule'] ."</option>\n";
		}

		if ($sel) {
			$res .= '<label for="journal"><strong>'
			  . _T('asso:prets_libelle_mode_paiement')." :</strong></label>\n"
			  . '<select name="journal" id="journal" class="formo">'
			  . $sel
			  . "</select>\n";
		}

		$res .= '<label for="commentaire_retour"><strong>'
		. _T('asso:prets_libelle_commentaires')." :</strong></label>\n"
		. '<textarea name="commentaire_retour" id="commentaire_retour" class="formo">'
		. $commentaire_retour."</textarea>\n"
		. '</fieldset>'
		. '<input name="id_pret" type="hidden" value="'.$id_pret.'" />'
		. '<input name="id_ressource" type="hidden" value="'.$id_ressource.'" />'
		. '<input name="agir" type="hidden" value="'.$action.'" />'
		. '<div style="float:right;"><input type="submit" value="'
		. $texte
		. '" class="fondo" /></div>';

		echo redirige_action_post($action .'_prets', $id_pret, 'prets', "id=$id_ressource", $res);
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
?>
