<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_pdf_membres() {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();

		// on recupere ce qu'il faut pour faire la requete SQL pour generer la liste d'id_auteurs dont on a besoin pour recuperer les adresses et telephones
		$where = htmlspecialchars_decode(_request('where_adherents'));
		$jointure = _request('jointure_adherents');
		// la requete de base
		$query = sql_select('m.id_auteur AS id_auteur',"spip_asso_membres m $jointure", $where, '', 'm.nom_famille,m.prenom ');
		// tableau des resultats
		$liste_id_auteurs = array();
		while ($data = sql_fetch($query)) {
			$liste_id_auteurs[] = $data['id_auteur'];
		}

		include_spip('pdf/extends');
		$pdf = new PDF();
		$statut = _request('statut_interne');
		$pdf->titre = _T('asso:adherent_titre_liste_'.$statut);
		$pdf->Open();
		$pdf->AddPage();

		//On définit les colonnes (champs,largeur,intitulé,alignement)
		$champsExtras = association_trouver_iextras("asso_$objet");
		$desc_table = charger_fonction('trouver_table', 'base'); // cf. http://programmer.spip.net/sql_showtable,619
		$champs = $desc_table('spip_asso_membres');
		$sent = _request('champs');
		foreach ($champs['field'] as $k => $v) {
			if ($sent[$k]=='on') {
#				$type = strpos($v, 'text');
				$type_txt = preg_match('#(char|text|var)#',$v);
				$type_num = preg_match('#(dec|int|date|float)#',$v);
#				$p = ($type===FALSE) ? 'R' : (($type==0) ? 'L' : 'C');
				$p = $type_txt?'L':($type_num?'R':'C');
#				$n = ($type===FALSE) ? 20 : (($type==0) ? 45 : 25);
				$n = $type_txt?45:($type_num?20:25);
				$lang_clef = 'adherent_libelle_'. $k;
				$lang_trad = _T("asso:$lang_clef");
				$pdf->AddCol($k,$n, utf8_decode(html_entity_decode($lang_clef!=str_replace(' ', '_', $lang_trad)?$lang_trad:$champsExtras[$k])) , $p);
			}
		}
		// ainsi que les colonnes pour les champs hors table spip_asso_membres
		if ($sent['email']) {
			$pdf->AddCol('email',45 , utf8_decode(html_entity_decode(_T('asso:adherent_libelle_email'))), 'C');
			$emails =  association_formater_emails($liste_id_auteurs, 'auteur', '', "\n");
		}
		if ($sent['adresse']) {
			$pdf->AddCol('adresse',45 , utf8_decode(html_entity_decode(_T('coordonnees:label_adresse'))), 'L');
			$adresses =  association_formater_adresses($liste_id_auteurs, 'auteur', '', "\n"," ");
		}
		if ($sent['telephone']) {
			$pdf->AddCol('telephone',30 , utf8_decode(html_entity_decode(_T('coordonnees:label_numero'))), 'C');
			$telephones = association_formater_telephones($liste_id_auteurs, 'auteur', '', '', '', "\n");
		}
		$order = 'id_auteur';
		if ($sent['prenom'])
			$order = 'prenom' . ",$order";
		if ($sent['nom_famille'])
			$order = 'nom_famille' . ",$order";
		$adresses_tels = array();
		foreach($liste_id_auteurs as $id_auteur) {
			$adresses_tels[$id_auteur] = array();
			if ($sent['email'])
				$adresses_tels[$id_auteur]['email'] = $emails[$id_auteur];
			if ($sent['adresse'])
				$adresses_tels[$id_auteur]['adresse'] = preg_replace('/\&nbsp\;/', " ", preg_replace('/(\s*\<br\s*\/>\s*)+/i', "\n", $adresses[$id_auteur])); // recupere toutes les adresses dans un seul string separees par \n\n et remplace les <br/> par des \n et &nbsp; par des " " car la chaine est en HTML
			if ($sent['telephone']) {
				$adresses_tels[$id_auteur]['telephone'] = preg_replace('/\&nbsp\;/', " ", preg_replace('/(\s*\<br\s*\/>\s*)+/i', "\n", $telephones[$id_auteur]));
			}
		}

		$pdf->Query(sql_select('*, c.libelle as categorie','spip_asso_membres m LEFT JOIN spip_asso_categories c ON m.id_categorie = c.id_categorie', sql_in('id_auteur', $liste_id_auteurs), '', $order), $adresses_tels, 'id_auteur');
		$nom_fic = 'membres_'. _request('suffixe') .'.pdf';
		$pdf->Output($nom_fic, 'D');
}

?>