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
include_spip ('inc/navigation_modules');

function exec_association() {

	include_spip('inc/autoriser');
	if (!autoriser('associer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;

		association_onglets();

		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));
		echo fin_boite_info(true);

		$res=association_icone(_T('asso:profil_de_lassociation'),  '?exec=configurer_association', 'assoc_qui.png');
		$res.=association_icone(_T('asso:categories_de_cotisations'),  generer_url_ecrire("categories"), 'cotisation.png',  '');
		$res.=association_icone(_T('asso:plan_comptable'),  generer_url_ecrire("plan"), 'plan_compte.png',  '');

		echo bloc_des_raccourcis($res);
		echo debut_droite("",true);
		echo debut_cadre_formulaire("",true);
#		echo gros_titre(_T('asso:votre_asso'),'',false);
#		echo "<br />\n";
		echo '<strong>'.$GLOBALS['association_metas']['nom'].'</strong><br/>';
		echo $GLOBALS['association_metas']['rue']."<br />\n";
		echo $GLOBALS['association_metas']['cp'].'&nbsp;';
		echo $GLOBALS['association_metas']['ville']."<br />\n";
		echo $GLOBALS['association_metas']['telephone']."<br />\n";
		echo $GLOBALS['association_metas']['email']."<br />\n";
		echo $GLOBALS['association_metas']['siret']."<br />\n";
		echo $GLOBALS['association_metas']['declaration']."<br />\n";
		echo $GLOBALS['association_metas']['prefet']."<br />\n";
		echo fin_cadre_formulaire(true);

		/* Provisoirement supprimé en attendant 1.9.3*/

		echo '<br />';
		echo gros_titre(_T('asso:votre_equipe'),'',false);
		echo '<br />';

		echo debut_cadre_relief('', true);

		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:nom') . "</th>\n";
		echo '<th>' . _T('asso:fonction') . "</th>\n";
		echo '<th>' . _T('asso:portable') . "</th>\n";
		echo '<th>' . _T('asso:telephone') . ' / ' . _T('asso:email') .  "</th>\n";
		echo '</tr>';
		$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS .  " a INNER JOIN spip_auteurs AS b ON a.id_auteur=b.id_auteur", "fonction !='' AND statut_interne != 'sorti'", '',  "a.nom_famille");
		while ($data = sql_fetch($query))
    {
			$id_auteur=$data['id_auteur'];
			$mob = print_tel($data['mobile']);
			$tel = print_tel($data['telephone']);
			if ($email = $data['email'])
			  $tel = "<a href='mailto:$email' title='"
			    . _L('Ecrire &agrave;') . ' ' . $email . "'>"
			    . ($tel ? $tel : 'mail')
			    . '</a>';
			$auteur = generer_url_ecrire('auteur_infos',"id_auteur=$id_auteur");
			$adh = generer_url_ecrire('voir_adherent',"id=$id_auteur");
			echo "\n<tr style='background-color: #EEEEEE;'>\n";

			echo "<td class='arial11 border1'>",
				"<a href='$auteur' title=\"",
				_T('lien_voir_auteur'),
				'">',
				htmlspecialchars($data['nom']),
				 "</a></td>\n";

			echo "<td class='arial11 border1'>",
				"<a href='$adh' title=\"",
				_T('asso:adherent_label_voir_membre'),
				"\">",
				htmlspecialchars($data['fonction']),
				 "</a></td>\n";

			echo '<td class="arial1 border1">'.$mobile.'</td>';
			echo '<td class="arial1 border1" style="text-align:center">'.$tel.'</td>';
			echo "</tr>\n";
		}
		echo '</table>';

		echo fin_cadre_relief(true);
		echo fin_page_association();

		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS,
			array("statut_interne"=> 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE ");
	}
}

function print_tel($n)
{
	$n = preg_replace('/\D/', '', $n);
	if (!intval($n)) return '';
	return preg_replace('/(\d\d)/', '\1&nbsp;', $n);
}

?>
