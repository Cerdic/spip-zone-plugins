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

	function exec_ajout_participation() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_action_activites=generer_url_ecrire('action_activites');
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_ajouter_inscriptions'));
		
		$id_activite=preg_replace("/[^0-9]/","",$_GET['id']);
		
		$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_activite=$id_activite ");
		while ($data = spip_fetch_array($query)) {
			$nom=$data['nom'];
			$id_adherent=$data['id_adherent'];
			$membres=$data['membres'];
			$non_membres=$data['non_membres'];
			$inscrits=$data['inscrits'];
			$montant=$data['montant'];
			$commentaire=$data['commentaire'];
		}
		
		echo '<form action="'.$url_action_activites.'" method="POST">';
		echo '<label for="nom"><strong>'._T('asso:activite_libelle_nomcomplet').' :</strong></label>';
		echo '<input name="nom"  type="text" size="40" value="'.$nom.'" id="nom" class="formo" />';
		echo '<label for="id_membre"><strong>'._T('asso:activite_libelle_adherent').' :</strong></label>';
		echo '<input name="id_membre" type="text" value="'.$id_adherent.'" id="id_membre" class="formo" />';
		echo '<label for="membres"><strong>'._T('asso:activite_libelle_membres').' :</strong></label>';
		echo '<input name="membres"  type="text" size="40" value="'.$membres.'" id="membres" class="formo" />';
		echo '<label for="non_membres"><strong>'._T('asso:activite_libelle_non_membres').' :</strong></label>';
		echo '<input name="non_membres"  type="text" size="40" value="'.$non_membres.'" id="non_membres" class="formo" />';
		echo '<label for="inscrits"><strong>'._T('asso:activite_libelle_nombre_inscrit').' :</strong></label>';
		echo '<input name="inscrits"  type="text" value="'.$inscrits.'" id="inscrits" class="formo" />';
		echo '<label for="montant"><strong>'._T('asso:activite_libelle_montant_inscription').' :</strong></label>';
		echo '<input name="montant"  type="text" value="'.$montant.'" id="montant" class="formo" />';
		echo '<label for="date_paiemen"><strong>'._T('asso:activite_libelle_date_paiement').' :</strong></label>';
		echo '<input name="date_paiement" value="'.date('Y-m-d').'" type="text" id="date_paiemen" class="formo" />';
		echo '<label for="journal"><strong>'._T('asso:activite_libelle_mode_paiement').' :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe=".lire_config('association/classe_banques')." ORDER BY code") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'"> '.$banque['intitule'].' </option>';
		}
		echo '</select>';
		echo '<label for="statut"><strong>'._T('asso:activite_libelle_statut').' ok :</strong></label>';
		echo '<input name="statut"  type="checkbox" value="ok" unchecked id="statut" /><br />';
		echo '<label for="commentaire"><strong>'._T('asso:activite_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		
		echo '<input name="action" type="hidden" value="paie">';
		echo '<input name="id_activite" type="hidden" value="'.$id_activite.'">';
		echo '<input name="id_evenement" type="hidden" value="'.$id_evenement.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="" type="submit" value="'._T('asso:bouton_ajoute').'" class="fondo" /></div>';
		echo '</form>';

		fin_cadre_relief();  
		fin_page();
	}

?>

