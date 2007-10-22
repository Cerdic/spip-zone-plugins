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

	function exec_ajout_cotisation(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$url_action_cotisations = generer_url_ecrire('action_cotisations');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$id_auteur=$_GET['id'];
		$query = spip_query( "SELECT * FROM spip_auteurs_elargis LEFT JOIN spip_asso_adherents ON spip_auteurs_elargis.id_auteur=spip_asso_adherents.id_auteur WHERE spip_auteurs_elargis.id_auteur='$id_auteur' " );
		while($data = spip_fetch_array($query)) {
			$nom_famille=$data['nom_famille'];
			$prenom=$data['prenom'];
			$categorie=$data['categorie'];
			$validite=$data['validite'];
			$split = split("-",$validite); 
			$annee = $split[0]; 
			$mois = $split[1]; 
			$jour = $split[2]; 
			
			debut_page(_T('Ajout de cotisation'), "", "");
			
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
				
			echo '<p>';
			echo 'Adh&eacute;rent :<strong>'.$nom_famille.' '.$prenom.'</strong><br />';
			echo 'Cat&eacute;gorie :<strong>'.$categorie.'</strong></p>';
			association_date_du_jour();	
			fin_boite_info();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('asso:Nouvelle cotisation'));
			
			echo '<form action="'.$url_action_cotisations.'" method="POST">';
			echo '<label for="date"><strong>'._T('asso:Date du paiement (AAAA-MM-JJ)').' :</strong></label>';
			echo '<input name="date" type="text" value="'.date('Y-m-d').'" id="date" class="formo" />';
			echo '<label for="montant"><strong>'._T('asso:Montant paye (en euros)').' :</strong></label>';
			$sql = spip_query( "SELECT * FROM spip_asso_categories WHERE valeur='$categorie' ");
			while($categorie = spip_fetch_array($sql)) {
				$duree=$categorie['duree'];
				$mois=$mois+$duree;
				$validite=date("Y-m-d", mktime(0, 0, 0, $mois, $jour, $annee));
				echo '<input name="montant" type="text" value="'.$categorie['cotisation'].'" id="montant" class="formo" />';
			}
			echo '<label for="journal"><strong>'._T('asso:Mode de paiement').' :</strong></label>';
			echo '<select name="journal" type="text" id="journal" class="formo" />';
			$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe=".lire_config('association/classe_banques')." ORDER BY code") ;
			while ($banque = spip_fetch_array($sql)) {
				echo '<option value="'.$banque['code'].'"> '.$banque['intitule'].' </option>';
			}
			echo '<option value="don"> Don </option>';
			echo '</select>';
			echo '<label for="validite"><strong>'._T('asso:Validite').' :</strong></label>';
			echo '<input name="validite" type="text" value="'.$validite.'" id="validite" class="formo" />';
			echo '<label for="justification"><strong>'._T('asso:Justification').' :</strong></label>';
			echo '<input name="justification" type="text" value="Cotisation '.$prenom.' '.$nom_famille.'" id="justification" class="formo" />';
			echo '<input type="hidden" name="id" value="'.$id_auteur.'">';
			echo '<input type="hidden" name="nom_famille" value_famille="'.$nom_famille.'">';
			echo '<input type="hidden" name="prenom" value="'.$prenom.'">';
			echo '<input type="hidden" name="categorie" value="'.$categorie.'">';
			echo '<input type="hidden" name="action" value="ajoute">';
		}
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		echo '</fieldset>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
