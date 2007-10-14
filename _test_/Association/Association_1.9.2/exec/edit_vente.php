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

	function exec_edit_vente() {
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_ventes = generer_url_ecrire('action_ventes');
		
		$action=$_REQUEST['action'];
		$id_vente= $_REQUEST['id'];		
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$query = spip_query ("SELECT * FROM spip_asso_ventes INNER JOIN spip_asso_comptes ON id_vente=id_journal WHERE id_vente=$id_vente AND imputation='vente' " );	
		while ($data = spip_fetch_array($query)) {
			$date_vente=$data['date_vente'];
			$article=$data['article'];
			$code=$data['code'];
			$acheteur=$data['acheteur'];
			$quantite=$data['quantite'];
			$prix_vente=$data['prix_vente'];
			$journal=$data['journal'];
			$don=$data['don'];
			$date_envoi=$data['date_envoi'];
			$frais_envoi=$data['frais_envoi'];
			$commentaire=$data['commentaire'];
		}	
		
		debut_page();
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des ventes'));
		
		echo '<form method="post" action="'.$url_action_ventes.'">';	
		echo '<label for="date_vente"><strong>Date (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date_vente" type="text" value="'.$date_vente.'" id="date_vente" class="formo" />';
		echo '<label for="article"><strong>Article :</strong></label>';
		echo '<input name="article"  type="text" size="40" value="'.$article.'" id="article" class="formo" />';
		echo '<label for="code"><strong>Code de l\'article :</strong></label>';
		echo '<input name="code"  type="text" value="'.$code.'" id="code" class="formo" />';
		echo '<label for="acheteur"><strong>Nom de l\'acheteur :</strong></label>';
		echo '<input name="acheteur" type="text" size="40" value="'.$acheteur.'" id="acheteur" class="formo" />';
		echo '<label for="quantite"><strong>Quantit&eacute; achet&eacute;e :</strong></label>';
		echo '<input name="quantite"  type="text" value="'.$quantite.'" id="quantite" class="formo" />';
		echo '<label for="prix_vente"><strong>Prix de vente(en &euro;) :</strong></label>';
		echo '<input name="prix_vente"  type="text" value="'.$prix_vente.'" id="prix_vente" class="formo" />';
		echo '<label for="journal"><strong>Mode de paiement :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe=".lire_config('association/classe_banques')." ORDER BY code") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="don"><strong>Don :</strong></label>';
		echo '<input name="don" type="text" value="'.$don.'" id="don" class="formo" />';
		echo '<label for="date_envoi"><strong>Envoy&eacute; le (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date_envoi"  type="text" value="'.$date_envoi.'" id="date_envoi" class="formo" />';
		echo '<label for="frais_envoi"><strong>Frais d\'envoi (en &euro;) :</strong></label>';
		echo '<input name="frais_envoi" type="text" value="'.$frais_envoi.'" id="frais_envoi" class="formo" />';
		echo '<label for="commentaire"><strong>Commentaires :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		
		echo '<input name="id" type="hidden" value="'.$id_vente.'" >';		
		echo '<input name="action" type="hidden" value="'.$action.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}  
?>
