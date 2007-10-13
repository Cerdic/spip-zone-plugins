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
	include_spip ('inc/navigation_modules');

	function exec_edit_don(){
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_dons = generer_url_ecrire('action_dons');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$action=$_REQUEST['action'];
		$id_don= $_REQUEST['id'];		
		
		$query = spip_query (" SELECT * FROM spip_asso_dons WHERE id_don=$id_don ");
		while ($data = spip_fetch_array($query)) {
			$date_don=$data['date_don'];
			$bienfaiteur=$data['bienfaiteur'];
			$id_adherent=$data['id_adherent'];
			$argent=$data['argent'];
			$colis=$data['colis'];
			$valeur=$data['valeur'];
			$journal=$data['journal'];
			$contrepartie=$data['contrepartie'];
			$commentaire=$data['commentaire'];
		}
		
		debut_page();
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">DON<br><span class="spip_xx-large">'.$id_don.'</span></div>';
		print association_date_du_jour();
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des dons'));
		
		echo '<form method="post" action="'.$url_action_dons.'">';
		echo '<input name="id" type="hidden" value="'.$id_don.'">';
		echo '<input name="action" type="hidden" value="'.$action.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<label for="date_don"><strong>Date (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date_don" type="text" value="'.$date_don.'" id="date_don" class="formo" />';
		echo '<label for="bienfaiteur"><strong>Nom du bienfaiteur :</strong></label>';
		echo '<input name="bienfaiteur" type="text" value="'.$bienfaiteur.'" id="bienfaiteur" class="formo" />';
		echo '<label for="id_adherent"><strong>N&deg; de membre :</strong></label>';
		echo '<input name="id_adherent" type="text" value="'.$id_adherent.'" id="id_adherent" class="formo" />';
		echo '<label for="argent"><strong>Don financier (en &euro;) :</strong></label>';
		echo '<input name="argent" type="text" value="'.$argent.'" id="argent" class="formo" />';
		echo '<label for="journal"><strong>Mode de paiement :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe=".lire_config('association/classe_banques')." ORDER BY code") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="colis"><strong>Colis :</strong></label>';
		echo '<input name="colis" type="text" value="'.$colis.'" id="colis" class="formo" />';
		echo '<label for="valeur"><strong>Contre-valeur (en &euro;) :</strong></label>';
		echo '<input name="valeur" type="text" value="'.$valeur.'" id="valeur" class="formo" />';
		echo '<label for="contrepartie"><strong>Geste de l\'association :</strong></label>';
		echo '<input name="contrepartie" type="text" size="50" value="'.$contrepartie.'" id="contrepartie" class="formo" />';
		echo '<label for="commentaire"><strong>Remarques :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}  
?>
