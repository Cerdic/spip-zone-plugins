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

if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip ('inc/navigation_modules');

function exec_ajout_cotisation(){
		
	$id_auteur = intval(_request('id'));
	$row = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur");
	if (!autoriser('configurer') OR !$row) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ajout_de_cotisation')) ;
		association_onglets();
		echo debut_gauche("",true);
		ajout_cotisation($id_auteur, $row);
		echo fin_page_association();
	}
}
	
function ajout_cotisation($id_auteur, $row)
{
	$nom_famille=$row['nom_famille'];
	$prenom=$row['prenom'];
	$categorie=$row['categorie'];
	$validite=$row['validite'];
	list($annee, $mois, $jour) = explode("-",$validite); 

	$categorie = sql_fetsel("duree, cotisation", "spip_asso_categories", "id_categorie=" . intval($categorie));

	$h = generer_url_ecrire('voir_adherent', "id=$id_auteur");

	echo debut_boite_info(true);
	echo "<h3><a href='$h'>", $nom_famille.' '.$prenom.'</a></h3>';
	echo $categorie ? ('<strong>'.$categorie.'</strong>') :'';
	echo association_date_du_jour();	
	echo fin_boite_info(true);

	if ($jour==0 OR $mois==0 OR $annee==0)
		list($annee, $mois, $jour) = explode("-",date('Y-m-d'));
	$mois+=$categorie['duree'];
	$validite=date("Y-m-d", mktime(0, 0, 0, $mois, $jour, $annee));
	$full =  $nom_famille . ' ' . $prenom;
	$justification = _T('asso:nouvelle_cotisation') . " [$full" . "->membre$id_auteur]";

	echo debut_droite("",true);

	$res = '<label for="date"><strong>'._T('asso:date_du_paiement_AAAA-MM-JJ').' :</strong></label>'
	. '<input name="date" type="text" value="'.date('Y-m-d').'" id="date" class="formo" />'
	. '<label for="montant"><strong>'._T('asso:montant_paye_en_euros').' :</strong></label>'
	. '<input name="montant" type="text" value="'.$categorie['cotisation'].'" id="montant" class="formo" />'
	. '<label for="journal"><strong>'._T('asso:prets_libelle_mode_paiement').'&nbsp;:</strong></label>'
	. '<select name="journal" type="text" id="journal" class="formo" />';

	$sql = sql_select('*', 'spip_asso_plan', "classe=". _q($GLOBALS['association_metas']['classe_banques']), '', "code") ;
	while ($banque = sql_fetch($sql)) {
		$res .= '<option value="'.$banque['code'].'"> '.$banque['intitule'].' </option>';
	}

	$res .= '<option value="don"> Don </option>'
	. '</select>'
	. '<label for="validite"><strong>'._T('asso:Validite').' :</strong></label>'
	. '<input name="validite" type="text" value="'.$validite.'" id="validite" class="formo" />'
	. '<label for="justification"><strong>'._T('asso:Justification').' :</strong></label>'
	. '<input name="justification" type="text" value="'. htmlspecialchars($justification) . '" id="justification" class="formo" />'
	. '<div style="float:right;"><input name="submit" type="submit" value="';

	if ( isset($action)) {$res .= _T('asso:bouton_'.$action);}
	else {$res .= _T('asso:bouton_envoyer');}
	$res .= '" class="fondo" /></div>';

	echo debut_cadre_relief(  "", false, "", _T('asso:nouvelle_cotisation'));
	echo redirige_action_post('cotisation', $id_auteur, 'voir_adherent', "id=$id_auteur", $res);

	echo fin_cadre_relief(true);  
}
?>
