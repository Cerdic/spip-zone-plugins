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

function exec_agir_ventes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		
		$id_vente=intval($_REQUEST['id']);
		$id_compte=intval(_request('id_compte'));
		$action=$_REQUEST['agir'];
		$url_retour=$_POST['url_retour'];
		
		$date_vente=$_POST['date_vente'];
		$article=$_POST['article'];
		$code=$_POST['code'];
		$acheteur=$_POST['acheteur'];
		$id_acheteur=intval($_POST['id_acheteur']);
		if ($quantite_req =  _request('quantite')) {
			$quantite = floatval(preg_replace("/,/",".",$quantite_req));
		}
		else $quantite = 0;
		$date_envoi=$_POST['date_envoi'];
		if ($frais_envoi_req =  _request('frais_envoi')) {
			$frais_envoi = floatval(preg_replace("/,/",".",$frais_envoi_req));
		}
		else $frais_envoi = 0;
		$don=$_POST['don'];
		if ($prix_vente_req =  _request('prix_vente')) {
			$prix_vente = floatval(preg_replace("/,/",".",$prix_vente_req));
		}
		else $prix_vente = 0;
		$journal=$_POST['journal'];
		$justification='[vente n&deg; '.$id_vente.'->vente'.$id_vente.'] - '.$article;
		$commentaire=$_POST['commentaire'];
		$recette=$quantite*$prix_vente;

		//AJOUT VENTE
		if ($action=="ajoute"){
			if (!ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette)) {

				include_spip('inc/minipres');
				echo minipres(_T('avis_operation_impossible'));
			} else 	header ('location:'.$url_retour);
		}
		
		//MODIFICATION VENTE
		else if ($action=="modifie"){
			ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification, $recette, $id_compte);
			header ('location:'.$url_retour);
		}
		
		//SUPPRESSION PROVISOIRE VENTES
		elseif (isset($_POST['delete'])) {
		
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
			association_onglets();
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo association_retour();
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", true, "", $titre = _T('asso:action_sur_les_ventes_associatives'));
			
			$res = '<div><strong>'
			  . _T('asso:vous_vous_appretez_a_effacer')
			  . " $count "
			  . (($count==1) ? _T('asso:vente') : _T('asso:ventes'))
			  . "</strong>\n";

			for ( $i=0 ; $i < $count ; $i++ ) {	
				$id = $delete_tab[$i];
				$res .= '<input type="hidden" name="drop[]" value="'.$id.'" checked="checked" />' . "\n";
			}
			$res .= '</div>'
			. '<div style="text-align:right"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></div>';	

			echo redirige_action_auteur('supprimer_ventes', $count, 'ventes', '', $res, '  method="post"');

			fin_cadre_relief();  
			echo fin_page_association();
		}
	}
}

function ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification, $recette, $id_compte)
{
	sql_updateq('spip_asso_ventes', array(
		"date_vente" => $date_vente,
		"article" => $article,
		"code" => $code,
		"acheteur" => $acheteur,
		"id_acheteur" => $id_acheteur,
		"quantite" => $quantite,
		"date_envoi" => $date_envoi,
		"frais_envoi" => $frais_envoi,
		"don" => $don,
		"prix_vente" => $prix_vente,
		"commentaire" => $commentaire),
		    "id_vente=$id_vente" );

	if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) {
		/* si ventes et frais d'envoi sont associes a la meme reference, on modifie une seule operation */
		association_modifier_operation_comptable($date_vente, $recette+$frais_envoi, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
	} else { /* sinon on en modifie deux */
		association_modifier_operation_comptable($date_vente, $recette, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
		association_modifier_operation_comptable($date_vente, $frais_envoi, 0, $justification.' - frais d\'envoi', $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente, sql_getfetsel("id_compte", "spip_asso_comptes", "imputation=".$GLOBALS['association_metas']['pc_frais_envoi']." AND id_journal=$id_vente"));
	}

/*
	sql_updateq('spip_asso_comptes', array(
		"date" => $date_vente,
		"journal" => $journal,
		"recette" => $prix_vente,
		"depense" => $frais_envoi,
		"justification" => $justification),
		   "id_journal=$id_vente AND imputation=".sql_quote($GLOBALS['association_metas']['pc_ventes']));
*/
}

function ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette)
{
	$id_vente = sql_insertq('spip_asso_ventes', array(
		'date_vente' => $date_vente,
		'article' => $article,
		'code' => $code,
		'acheteur' => $acheteur,
		'id_acheteur' => $id_acheteur,
		'quantite' => $quantite,
		'date_envoi' => $date_envoi,
		'frais_envoi' => $frais_envoi,
		'don' => $don,
		'prix_vente' => $prix_vente,
		'commentaire' => $commentaire));

	if ($id_vente) {
		$justification='[vente n&deg; '.$id_vente.'->vente'.$id_vente.'] - '.$article;
		if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) {
			/* si ventes et frais d'envoi sont associes a la meme reference, on ajoute une seule operation */
			association_ajouter_operation_comptable($date_vente, $recette+$frais_envoi, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
		} else { /* sinon on en insere deux */
			association_ajouter_operation_comptable($date_vente, $recette, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
			association_ajouter_operation_comptable($date_vente, $frais_envoi, 0, $justification.' - frais d\'envoi', $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente);
		}
		return true;
	}
	return false;
}

?>
