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
	
	function exec_edit_activite(){
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_activites = generer_url_ecrire('action_activites');
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$action=$_REQUEST['action'];
		if($action='ajout'){$id_evenement=$_REQUEST['id'];}
		if($action='modifie'){$id_activite=$_REQUEST['id'];}	
		
		$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_activite='$id_activite' ");
		while ($data = spip_fetch_array($query)){
			$id_evenement=$data['id_evenement'];
			$nom=$data['nom'];
			$id_adherent=$data['id_adherent'];
			$membres=$data['membres'];
			$non_membres=$data['non_membres'];
			$inscrits=$data['inscrits'];
			$email=$data['email'];
			$telephone=$data['telephone'];
			$adresse=$data['adresse'];
			$montant=$data['montant'];
			$date=$data['date'];
			$statut=$data['statut'];
			$commentaire=$data['commentaires'];
		}
		
		debut_page(_T('asso:activite_titre_mise_a_jour_inscriptions'));
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_mise_a_jour_inscriptions'));
		
		echo '<form method="post" action="'.$url_action_activites.'">';
		echo '<label for="date"><strong>'._T('asso:activite_libelle_date').' (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date" type="text" value="'.$date.'" id="date" class="formo" />';
		echo '<label for="nom"><strong>'._T('asso:activite_libelle_nomcomplet').' :</strong></label>';
		echo '<input name="nom"  type="text" size="40" value="'.$nom.'" id="nom" class="formo" />';
		echo '<label for="id_membre"><strong>'._T('asso:activite_libelle_adherent').' :</strong></label>';
		echo '<input name="id_membre" type="text" value="'.$id_adherent.'" id="id_membre" class="formo" />';
		echo '<label for="membres"><strong>'._T('asso:activite_libelle_membres').' :</strong></label>';
		echo '<input name="membres"  type="text" size="40" value="'.$membres.'" id="membres" class="formo" />';
		echo '<label for="non_membres"><strong>'._T('asso:activite_libelle_non_membres').' :</strong></label>';
		echo '<input name="non_membres"  type="text" size="40" value="'.$non_membres.'" id="non_membrese" class="formo" />';
		echo '<label for="inscrits"><strong>'._T('asso:activite_libelle_nombre_inscrit').' :</strong></label>';
		echo '<input name="inscrits"  type="text" value="'.$inscrits.'" id="inscrits" class="formo" />';
		echo '<label for="email"><strong>'._T('asso:activite_libelle_email').' :</strong></label>';
		echo '<input name="email"  type="text" size="40" value="'.$email.'" id="email" class="formo" />';
		echo '<label for="telephone"><strong>'._T('asso:activite_libelle_telephone').' :</strong></label>';
		echo '<input name="telephone" type="text" value="'.$telephone.'" id="telephone" class="formo" />';
		echo '<label for="adresse"><strong>'._T('asso:activite_libelle_adresse_complete').' :</strong></label>';
		echo '<textarea name="adresse" id="adresse" class="formo" />'.$adresse.'</textarea>';
		echo '<label for="montant"><strong>'._T('asso:activite_libelle_montant_inscription').' :</strong></label>';
		echo '<input name="montant"  type="text" value="'.$montant.'" id="montant" class="formo" />';
		echo '<label for="statut"><strong>'._T('asso:activite_libelle_statut').' ok :</strong></label>';
		echo '<input name="statut"  type="checkbox" value="ok"';
		if ($statut=='ok') { echo ' checked="checked"'; }
		echo ' id="statut" /><br />';
		echo '<label for="commentaire"><strong>'._T('asso:activite_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		echo '<input name="action" type="hidden" value="'.$action.'">';
		echo '<input name="id_evenement" type="hidden" value="'.$id_evenement.'">';
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