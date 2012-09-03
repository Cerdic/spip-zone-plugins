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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_categories()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('categories_de_cotisations');
		// notice
		echo '';
		// quelques stats sur les categories
		echo association_totauxinfos_stats('tous', 'categories', array('entete_duree'=>'duree', 'entete_montant'=>'cotisation') );
		// datation et raccourcis
		raccourcis_association('association', array(
			'ajouter_une_categorie_de_cotisation' => array('calculatrice.gif', 'edit_categorie'),
		));
		debut_cadre_association('calculatrice.gif','toutes_categories_de_cotisations');
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_categories'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_code') .'</th>';
		echo '<th>'. _T('asso:libelle_intitule') .'</th>';
		echo '<th>'. _T('asso:entete_duree') .'</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th>'. _T('asso:entete_commentaire') .'</th>';
		echo '<th colspan="2" class="actions">' . _T('asso:entete_actions') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_categories', '', 'id_categorie') ;
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_categorie'].'</td>';
			echo '<td class="text">'.$data['valeur'].'</td>';
			echo '<td class="text">'.$data['libelle'].'</td>';
			echo '<td class="decimal">'. association_formater_duree($data['duree'],'m') .'</td>';
			echo '<td class="decimal">'. association_formater_prix($data['cotisation']) .'</td>';
			echo '<td class="text">'. propre($data['commentaires']) .'</td>';
			echo association_bouton_supprimer('categorie', $data['id_categorie'], 'td');
			echo association_bouton_modifier('categorie', $data['id_categorie'], 'td');
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>