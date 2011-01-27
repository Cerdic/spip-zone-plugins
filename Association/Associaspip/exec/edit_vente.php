<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_vente() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_agir_ventes = generer_url_ecrire('agir_ventes');
		
		$action=$_REQUEST['agir'];
		$id_vente= intval(_request('id'));
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$data = !$id_vente ? '' : sql_fetsel("*", "spip_asso_ventes INNER JOIN spip_asso_comptes ON id_vente=id_journal ", "id_vente=$id_vente AND imputation=" . sql_quote($GLOBALS['association_metas']['pc_ventes']));

		if ($data) {
			$date_vente=$data['date_vente'];
			$article=$data['article'];
			$code=$data['code'];
			$acheteur=$data['acheteur'];
			$id_acheteur=$data['id_acheteur'];
			$quantite=$data['quantite'];
			$prix_vente=$data['prix_vente'];
			$journal=$data['journal'];
			$don=$data['don'];
			$date_envoi=$data['date_envoi'];
			$frais_envoi=$data['frais_envoi'];
			$commentaire=$data['commentaire'];
		} else {
			$date_envoi=$date_vente=date('Y-m-d');
			$article=$code=$acheteur=$id_acheteur=$quantite=$prix_vente=$journal=$don=$frais_envoi=$commentaire='';
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page() ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		if ($id_vente) {
			echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:vente_libelle_numero').'<br />';
			echo '<span class="spip_xx-large">';
			echo $id_vente;
			echo '</span></div><br />';
		}
		echo '<div>'.association_date_du_jour().'</div>';	
		
		echo fin_boite_info(true);	

		echo association_retour();
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", _T('asso:ressources_titre_mise_a_jour'));
		
		echo '<form method="post" action="'.$url_agir_ventes.'"><div>';	
		echo '<label for="date_vente"><strong>' . _T('asso:date_aaaa_mm_jj') . '</strong></label>';
		echo '<input name="date_vente" type="text" value="'.$date_vente."\" id='date_vente' class='formo' />\n";
		echo '<label for="article"><strong>' . _T('asso:article') . "&nbsp;:</strong></label>\n";
		echo '<input name="article"  type="text" value="'.$article."\" id='article' class='formo' />\n";
		echo '<label for="code"><strong>', _T('asso:code_de_l_article'), '</strong></label>';
		echo '<input name="code"  type="text" value="'.$code."\" id='code' class='formo' />\n";
		echo '<label for="acheteur"><strong>', _T('asso:nom_de_l_acheteur'), '</strong></label>';
		echo '<input name="acheteur" type="text" value="'.$acheteur."\" id='acheteur' class='formo' />\n";
		echo '<label for="id_acheteur"><strong>' . _T('asso:nd_de_membre') . '</strong></label>';
		echo '<input name="id_acheteur" type="text" value="'.$id_acheteur."\" id='id_acheteur' class='formo' />\n";
		echo '<label for="quantite"><strong>' . _T('asso:quantite_achetee') . '</strong></label>';
		echo '<input name="quantite"  type="text" value="'.$quantite."\" id='quantite' class='formo' />\n";
		echo '<label for="prix_vente"><strong>' . _T('asso:prix_de_vente_en_e__') . '</strong></label>';
		echo '<input name="prix_vente"  type="text" value="'.$prix_vente."\" id='prix_vente' class='formo' />\n";
		echo association_mode_de_paiement($journal, _T('asso:prets_libelle_mode_paiement'));
		echo '<label for="don"><strong>' . _T('asso:don') . '</strong></label>';
		echo '<input name="don" type="text" value="'.$don."\" id='don' class='formo' />\n";
		echo '<label for="date_envoi"><strong>' . _T('asso:envoye_le_aaaa_mm_jj') . '</strong></label>';
		echo '<input name="date_envoi"  type="text" value="'.$date_envoi."\" id='date_envoi' class='formo' />\n";
		echo '<label for="frais_envoi"><strong>', _T('asso:frais_d_envoi_en_e__'), '</strong></label>';
		echo '<input name="frais_envoi" type="text" value="'.$frais_envoi."\" id='frais_envoi' class='formo' />\n";
		echo '<label for="commentaire"><strong>' . _T('asso:commentaires') . '&nbsp;:</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" rows="3" cols="80">'.$commentaire.'</textarea>';
		
		echo '<input name="id" type="hidden" value="'.$id_vente."\" />\n";		
		echo '<input name="agir" type="hidden" value="'.$action."\" />\n";
		echo '<input name="url_retour" type="hidden" value="'.$url_retour."\" />\n";
		
		echo '<div style="float:right;">';
		echo '<input type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		
		echo '</div></form>';
		
		fin_cadre_relief();  
		 echo fin_page_association(); 
	}  
}
?>
