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

function exec_ajout_participation() {
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		echo association_retour();
		echo debut_droite("",true);
		echo debut_cadre_relief("", true, "", $titre = _T('asso:activite_titre_ajouter_inscriptions'));
		
		$id_activite=intval($_GET['id']);
		
		$data = sql_fetsel("*", "spip_asso_activites", "id_activite=$id_activite ");
		$nom=$data['nom'];
		$id_adherent=$data['id_adherent'];
		$membres=$data['membres'];
		$non_membres=$data['non_membres'];
		$inscrits=$data['inscrits'];
		$montant=$data['montant'];
		$commentaire=$data['commentaire'];
		
		$res = '<label for="nom"><strong>'._T('asso:activite_libelle_nomcomplet')." :</strong></label>\n"
		. '<input name="nom"  type="text" size="40" value="'.$nom.'" id="nom" class="formo" />'
		. '<label for="id_membre"><strong>'._T('asso:activite_libelle_adherent')." :</strong></label>\n"
		. '<input name="id_membre" type="text" value="'.$id_adherent.'" id="id_membre" class="formo" />'
		. '<label for="membres"><strong>'._T('asso:activite_libelle_membres')." :</strong></label>\n"
		. '<input name="membres"  type="text" size="40" value="'.$membres.'" id="membres" class="formo" />'
		. '<label for="non_membres"><strong>'._T('asso:activite_libelle_non_membres')." :</strong></label>\n"
		. '<input name="non_membres"  type="text" size="40" value="'.$non_membres.'" id="non_membres" class="formo" />'
		. '<label for="inscrits"><strong>'._T('asso:activite_libelle_nombre_inscrit')." :</strong></label>\n"
		. '<input name="inscrits"  type="text" value="'.$inscrits.'" id="inscrits" class="formo" />'
		. '<label for="montant"><strong>'._T('asso:activite_libelle_montant_inscription')." :</strong></label>\n"
		. '<input name="montant"  type="text" value="'.$montant.'" id="montant" class="formo" />'
		. '<label for="date_paiemen"><strong>'._T('asso:activite_libelle_date_paiement')." :</strong></label>\n"
		. '<input name="date_paiement" value="'.date('Y-m-d').'" type="text" id="date_paiemen" class="formo" />';

		$sel = '';
		$sql = sql_select("code, intitule", "spip_asso_plan", "classe=".sql_quote(lire_config('association/classe_banques')), '', "code") ;
		while ($banque = sql_fetch($sql)) {
			$sel .= '<option value="'.$banque['code'].'"> '.$banque['intitule']."</option>\n";
		}
		if ($sel) $res .= '<label for="journal"><strong>'._T('asso:activite_libelle_mode_paiement')." :</strong></label>\n<select name='journal' id='journal' class='formo'>$sel</select>";

		$res .= '<label for="statut"><strong>'._T('asso:activite_libelle_statut').' ok :</strong></label>'
		. '<input name="statut"  type="checkbox" value="ok" id="statut" /><br />'
		. '<label for="commentaire"><strong>'._T('asso:activite_libelle_commentaires')." :</strong></label>\n"
		. '<textarea rows="4" cols="80" name="commentaire" id="commentaire" class="formo">'.$commentaire.'</textarea>'
		
		. '<input name="agir" type="hidden" value="ajoute" />'
		. '<input name="id_activite" type="hidden" value="'.$id_activite."\" />\n"
		. '<input name="id_evenement" type="hidden" value="'.$id_evenement."\" />\n"
		. '<input name="url_retour" type="hidden" value="'. str_replace('&', '&amp;', $_SERVER["HTTP_REFERER"]) . "\" />\n";

		echo generer_form_ecrire('action_activites', "\n<div>$res</div>", '', _T('asso:bouton_ajoute'));

		echo fin_cadre_relief(true);  
		echo fin_gauche(), fin_page();
	}
}
?>
