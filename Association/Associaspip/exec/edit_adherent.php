<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_adherent() {
		
	$id_auteur= intval(_request('id'));

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
			include_spip('inc/minipres');
			echo minipres();
	} else exec_edit_adherent_args($id_auteur);
}
		
function exec_edit_adherent_args($id_auteur)
{
	$data = sql_fetsel("*",'spip_asso_membres', "id_auteur=$id_auteur");
	if (!$data) {
		include_spip('inc/minipres');
		echo minipres(_T('zxml_inconnu_id') . $id_auteur);
	} else {
		include_spip('inc/association_coordonnees');
		$nom_membre = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
		$adresses = association_recuperer_adresses_string(array($id_auteur));
		$emails = association_recuperer_emails_string(array($id_auteur));
		$telephones = association_recuperer_telephones_string(array($id_auteur));

		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case "0minirezo":
				$statut='auteur'; break;
			case "1comite":
				$statut='auteur'; break;
			default :
				$statut='visiteur'; break;
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		include_spip ('inc/navigation');
		
		association_onglets(_T('asso:titre_onglet_membres'));
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div class="infos"><div class="numero"><a href="'.generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur).'" title="'._T('asso:adherent_label_modifier_'.$statut).'">'._T('asso:adherent_libelle_numero_'.$statut);
		echo '<p>';
		echo $id_auteur;
		echo '</p></a></div></div>';

		$nom = htmlspecialchars($nom_membre);
		$adh = generer_url_ecrire('voir_adherent',"id=$id_auteur");
		$nom = "<a href='$adh' title=\"" . _T('asso:adherent_label_voir_membre') . "\">" . $nom . "</a>";
		$coord = '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">';
		if ($adresses[$id_auteur]) $coord .= '<br />' . $adresses[$id_auteur] . '<br/>';
		if ($emails[$id_auteur]) $coord .= '<br/>' . $emails[$id_auteur];
		if ($telephones[$id_auteur]) $coord .=  '<br/>'.$telephones[$id_auteur];
		$coord .= "</div>";

		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom."</div>".$coord;

		echo '<br /><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		echo fin_boite_info(true);
		
		echo association_retour();
	
		echo debut_droite("",true);

		echo recuperer_fond("prive/editer/editer_asso_membres", array (
			'id_auteur' => $id_auteur,
		));
		echo fin_page_association(); 
	}
}
?>
