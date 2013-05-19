<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_activite(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites') OR !test_plugin_actif('agenda')) {
		include_spip('inc/minipres');
		echo minipres();
	} else  exec_edit_activite_args(intval(_request('id')),  intval(_request('id_evenement')));
}

function exec_edit_activite_args($id_activite, $id_evenement)
{
	$data = !$id_activite ? '' : sql_fetsel("*", "spip_asso_activites", "id_activite=$id_activite");

	if ($data){
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
	} else $date = date('Y-m-d');

	$data = !$id_evenement ? '' : sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement");
	if (!$data) {
			include_spip('inc/minipres');
			echo minipres();
	} else {

		$titre=$data['titre'];
		$date_debut=$data['date_debut'];
		$lieu=$data['lieu'];
		$statut = ($statut=='ok') ? ' checked="checked"' : '';
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:activite_titre_mise_a_jour_inscriptions')) ;

		association_onglets(_T('asso:titre_onglet_activites'));
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">', _T('asso:activite_nd') . '<br />';
		echo '<span class="spip_xx-large">';
		echo $id_evenement;
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$titre.'</div>';
		echo '<br /><div>'.association_date_du_jour().'</div>';
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_mise_a_jour_inscriptions'));

		$res = ''
		.'<label for="date"><strong>'._T('asso:activite_libelle_date')." (AAAA-MM-JJ) :</strong></label>\n"
		.'<input name="date" type="text" value="'.$date.'" id="date" class="formo" />'
		.'<label for="nom"><strong>'._T('asso:activite_libelle_nomcomplet')." :</strong></label>\n"
		.'<input name="nom"  type="text" value="'.$nom.'" id="nom" class="formo" />'
		.'<label for="id_membre"><strong>'._T('asso:activite_libelle_adherent')." :</strong></label>\n"
		.'<input name="id_membre" type="text" value="'.$id_adherent.'" id="id_membre" class="formo" />'
		.'<label for="membres"><strong>'._T('asso:activite_libelle_membres')." :</strong></label>\n"
		.'<input name="membres"  type="text" value="'.$membres.'" id="membres" class="formo" />'
		.'<label for="non_membres"><strong>'._T('asso:activite_libelle_non_membres')." :</strong></label>\n"
		.'<input name="non_membres"  type="text" size="40" value="'.$non_membres.'" id="non_membres" class="formo" />'
		.'<label for="inscrits"><strong>'._T('asso:activite_libelle_nombre_inscrit')." :</strong></label>\n"
		.'<input name="inscrits"  type="text" value="'.$inscrits.'" id="inscrits" class="formo" />'
		.'<label for="email"><strong>'._T('asso:activite_libelle_email')." :</strong></label>\n"
		.'<input name="email"  type="text" value="'.$email.'" id="email" class="formo" />'
		.'<label for="telephone"><strong>'._T('asso:activite_libelle_telephone').' :</strong></label>'
		.'<input name="telephone" type="text" value="'.$telephone.'" id="telephone" class="formo" />'
		.'<label for="adresse"><strong>'._T('asso:activite_libelle_adresse_complete').' :</strong></label>'
		.'<textarea rows="3" cols="80" name="adresse" id="adresse" class="formo">'.$adresse."</textarea>\n"
		.'<label for="montant"><strong>'._T('asso:activite_libelle_montant_inscription'). " :</strong></label>\n"
		.'<input name="montant"  type="text" value="'.$montant.'" id="montant" class="formo" />'
		.'<label for="statut"><strong>'._T('asso:activite_libelle_statut'). " ok :</strong></label>\n"
		.'<input name="statut"  type="checkbox" value="ok"'
		. $statut
		. " id='statut' /><br />\n"
		.'<label for="commentaire"><strong>'._T('asso:activite_libelle_commentaires')." :</strong></label>\n"
		."<textarea rows='3' cols='80' name='commentaire' id='commentaire' class='formo'>".$commentaire."</textarea>\n"
		.'<input name="id_evenement" type="hidden" value="'.$id_evenement.'" />'
		.'<div style="float:right">'
		.'<input type="submit" value="'
		. _T('asso:bouton_' . ($id_activite ? 'modifie' : 'ajoute'))
		. '" class="fondo" /></div>';

		echo redirige_action_post(($id_activite ? 'modifier_activites' : 'ajouter_activites'), $id_activite, 'voir_activites', '', "\n<div>$res</div>");

		fin_cadre_relief();
		echo fin_page_association();
	}
}
?>
