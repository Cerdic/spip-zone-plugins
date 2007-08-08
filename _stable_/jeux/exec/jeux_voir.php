<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

// essai de pagination. A revoir si certains jeux sont mis a la poubelle !
function jeux_navigation_pagination($num_rows) {
	$texte = ''; $href = 'jeux_voir'; $tmp_var = 'id_jeu'; $nb_aff = 1;
	$self = self();
	$deb_aff = isset($tmp_var) ? intval(_request($tmp_var)) : 0;

	for ($i = 0; $i < $num_rows; $i += $nb_aff){
		$deb = $i + 1;
		// Pagination : si on est trop loin, on met des '...'
		if (abs($deb-$deb_aff)>10) {
			if ($deb<$deb_aff) {
				if (!isset($premiere)) { $premiere = '1 ... '; $texte .= $premiere; }
			} else {
				$derniere = ' | ... '.$num_rows; $texte .= $derniere; break;
			}
		} else {
			$fin = $i + $nb_aff;
			if ($fin > $num_rows) $fin = $num_rows;
			if ($deb > 1) $texte .= " |\n";
			if ($deb_aff >= $deb AND $deb_aff <= $fin) $texte .= "<b>$deb</b>";
			else {
				$script = parametre_url($self, $tmp_var, $deb);
				$texte .= "<a href=\"$script\">$deb</a>";
			}
		}
	}
	return $texte;
}

function exec_jeux_voir(){
	$id_jeu = _request('id_jeu');
	
	$requete = spip_fetch_array(spip_query("SELECT contenu,id_jeu,nom,titre,date FROM spip_jeux WHERE id_jeu =".$id_jeu));
	list($contenu, $id_jeu, $nom, $titre_prive, $date) =
		array($requete['contenu'], $requete['id_jeu'], $requete['nom'], $requete['titre'], $requete['date']);
	$titre_prive = propre($titre_prive);
	include_spip('jeux_utils');
	$titre_public = jeux_trouver_titre_public($contenu);
	if($titre_prive=='') $titre_prive = _T('jeux:sans_titre');
	if($titre_public) {
		$titre_prive = _T('jeux:jeu_titre_prive_') . ' ' . $titre_prive;
		$titre_public = _T('jeux:jeu_titre_public_') . ' ' . $titre_public;
	}
	$contenu = $nom==_T('jeux:jeu_vide')?_T('jeux:introuvable'):propre($contenu);
	
	if(!$id_jeu){
		debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"));
		fin_page();
		return;
	}
	
	debut_page(_T("jeux:jeu_numero",array('id'=>$id_jeu,'nom'=>$nom)));
			
	debut_gauche();
	
	boite_infos_jeu($id_jeu, $nom);
	boite_infos_accueil();
	
	debut_cadre_relief();
	echo "<strong>"._t("jeux:derniere_modif")."</strong><br />".affdate($date).' '.heures($date).":".minutes($date);
	fin_cadre_relief();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$nom)));
	echo "<div style='font-weight:bold'>$titre_prive</div>";
	if($titre_public) echo "<div style='font-weight:bold'>$titre_public</div>";
	echo '<br />', $contenu;

	fin_cadre_relief();

	$nb = spip_abstract_countsel("spip_jeux");
	if($nb>1) jeux_navigation_pagination($nb);

	echo fin_gauche(), fin_page();
}


?>