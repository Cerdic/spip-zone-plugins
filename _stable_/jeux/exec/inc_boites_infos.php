<?
if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite SPIP 1.92
if(!function_exists('spip_abstract_countsel')) {
	function spip_abstract_countsel($from = 'spip_jeux', $where = '') {
		if($where!='') $where = ' WHERE '.$where;
		$r = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $from$where"));
		return $r['n'];
	}
}

// essai de pagination. A virualiser si certains jeux sont mis a la poubelle !
function jeux_navigation_pagination() {
	$num_rows = spip_abstract_countsel("spip_jeux");
	if(!$num_rows) return '';
	
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

function boite_infos_auteur($id_auteur, $nom) {
	debut_boite_info();
	echo "<strong>$nom</strong><br />",
		icone_horizontale(_T('jeux:infos_auteur'),generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur),find_in_path('images/auteur-24.gif')),
		(_request('exec')=='jeux_gerer_resultats'
			?icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_auteur','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png'))
			:icone_horizontale(_T('jeux:gerer_ses_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png')) );
	fin_boite_info();
}

function boite_infos_jeu($id_jeu, $nom) {
	debut_boite_info();
	$nb_res = spip_abstract_countsel('spip_jeux_resultats', 'id_jeu='.$id_jeu);
	$nom = _T('jeux:jeu_court',array('id'=>$id_jeu,'nom'=>$nom));
	echo "<strong>$nom</strong><br />",
		(_request('exec')=='jeux_voir'?'':
			icone_horizontale(_T('jeux:voir_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png')) ),
		(_request('exec')=='jeux_edit'?'':
			icone_horizontale(_T('jeux:modifier_ce_jeu'),generer_url_ecrire('jeux_edit','id_jeu='.$id_jeu),find_in_path('img/jeu-crayon.png')) ),
		( (_request('exec')=='jeux_resultats_jeu') || !$nb_res?'':
			icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_jeu','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png')) ),
		( (_request('exec')=='jeux_gerer_resultats') || !$nb_res?'':
			icone_horizontale(_T('jeux:gerer_ses_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png'))
	);
	fin_boite_info();
}

function boite_infos_accueil() {
	debut_boite_info();
	$nb_res = spip_abstract_countsel('spip_jeux_resultats');
	echo 
		// 'nouveau jeu' uniquement sur la page 'jeux_tous'
		( _request('exec')!='jeux_tous'?'':
		icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png')) ),
		// 'liste des jeux' sur les pages hors 'jeux_tous'
		( _request('exec')=='jeux_tous'?'':
		icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png')) ),
		// 'gerer les resultats' sur les pages hors 'jeux_gerer_resultats' si 1 jeu au moins est present
		( (_request('exec')=='jeux_gerer_resultats') || !$nb_res?'':
			icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats','tous=oui'),find_in_path('img/jeu-laurier.png')) ),
		// 'configurer le plugin' uniquement sur la page 'jeux_tous' si lire_config() existe
		( (_request('exec')!='jeux_tous') || !function_exists('lire_config')?'':
		icone_horizontale(_T('jeux:configurer_jeux'),generer_url_ecrire('cfg','cfg=jeux'),find_in_path('img/jeu-cfg.png'))
		);
	fin_boite_info();
}

function boite_infos_jeux_tous() {
	debut_boite_info();
	echo icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png'));
	echo icone_horizontale(_T('jeux:configurer_jeux'),generer_url_ecrire('cfg','cfg=jeux'),find_in_path('img/jeu-cfg.png'));
	echo icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats','tous=oui'),find_in_path('img/jeu-laurier.png'));
	fin_boite_info();
}

?>