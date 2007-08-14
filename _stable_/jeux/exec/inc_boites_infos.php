<?
if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite SPIP 1.92
if(!function_exists('sql_countsel')) {
	function sql_countsel($from = 'spip_jeux', $where = '') {
		if($where!='') $where = ' WHERE '.$where;
		$r = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $from$where"));
		return $r['n'];
	}
}

// Pagination sur les jeux disponibles
function jeux_navigation_pagination() {
	$texte = ''; $href = 'jeux_voir'; $nb_aff = 1; $deb_aff = 1;
	$self = self();
	$id_jeu = intval(_request('id_jeu'));

	// liste des jeux disponibles
	$q = spip_query("SELECT id_jeu FROM spip_jeux");
	$ids = array(); $i = 0;
	while($r = spip_fetch_array($q)) { 
		$ids[$i++] = $r['id_jeu'];
		if ($id_jeu==$r['id_jeu']) $deb_aff = $i;
	}
	$num_rows = count($ids);
	if(!$num_rows) return '';

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
			if ($deb_aff >= $deb AND $deb_aff <= $fin) $texte .= "<strong>$deb</strong>";
			else {
				$script = parametre_url($self, 'id_jeu', $ids[$i]);
				$texte .= "<a href=\"$script\">$deb</a>";
			}
		}
	}
	return $texte;
}

function boite_infos_auteur($id_auteur, $type_jeu) {
	debut_boite_info();
	echo "<strong>$type_jeu</strong><br />",
	icone_horizontale(_T('jeux:infos_auteur'),generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur),find_in_path('images/auteur-24.gif'));
	
	if (_request('exec')=='jeux_gerer_resultats')
		{icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_auteur','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png'));
		}
	else if(autoriser('gererresultats','auteur',$id_auteur)) {icone_horizontale(_T('jeux:gerer_ses_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png')) ;}
	
	fin_boite_info();
}

function boite_infos_jeu($id_jeu, $type_jeu) {
	debut_boite_info();
	$nb_res = sql_countsel('spip_jeux_resultats', 'id_jeu='.$id_jeu);
	$type_jeu = _T('jeux:jeu_court',array('id'=>$id_jeu,'nom'=>$type_jeu));
	echo "<strong>$type_jeu</strong><br />",
		(_request('exec')=='jeux_voir'?'':
			icone_horizontale(_T('jeux:voir_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png')) ),
		(_request('exec')=='jeux_edit'?'':
			icone_horizontale(_T('jeux:modifier_ce_jeu'),generer_url_ecrire('jeux_edit','id_jeu='.$id_jeu),find_in_path('img/jeu-crayon.png')) ),
		( (_request('exec')=='jeux_resultats_jeu') || !$nb_res?'':
			icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_jeu','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png')) );
			
		if (autoriser('gererresultats')){echo ( (_request('exec')=='jeux_gerer_resultats') || !$nb_res?'':
			icone_horizontale(_T('jeux:gerer_ses_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png'))
	);}
	fin_boite_info();
}

function boite_infos_accueil() {
	debut_boite_info();
	$nb_res = sql_countsel('spip_jeux_resultats');
	echo 
		// 'nouveau jeu' uniquement sur la page 'jeux_tous'
		( _request('exec')!='jeux_tous'?'':
		icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png')) ),
		// 'liste des jeux' sur les pages hors 'jeux_tous'
		( _request('exec')=='jeux_tous'?'':
		icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png')) );
		// 'gerer les resultats' sur les pages hors 'jeux_gerer_resultats' si 1 jeu au moins est present
		if (autoriser('gererresultats')){
		( (_request('exec')=='jeux_gerer_resultats') || !$nb_res?'':
			icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats'),find_in_path('img/jeu-laurier.png')) );}
		// 'configurer le plugin' uniquement sur la page 'jeux_tous' si lire_config() existe
		echo ( (_request('exec')!='jeux_tous') || !function_exists('lire_config')?'':
		icone_horizontale(_T('jeux:configurer_jeux'),generer_url_ecrire('cfg','cfg=jeux'),find_in_path('img/jeu-cfg.png'))
		);
	fin_boite_info();
}

function boite_infos_jeux_tous() {
	debut_boite_info();
	echo icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png'));
	echo icone_horizontale(_T('jeux:configurer_jeux'),generer_url_ecrire('cfg','cfg=jeux'),find_in_path('img/jeu-cfg.png'));
	if (autoriser('gererresultats')) {echo icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats'),find_in_path('img/jeu-laurier.png'));}
	fin_boite_info();
}

function boite_info_jeux_edit(){
	
$r .= debut_cadre_relief(find_in_path('img/jeu-voir.png'),true,'',_T('jeux:inserer_jeu'));
	$r .= "<div>"._T('jeux:inserer_jeu_explication')."</div>";			
	$r .= icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'),'','',false) ;
	$r .= icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png'),'','',false);
	$r .= fin_cadre_relief(true);	
	return $r;
}



function boite_infos_spip_auteur($id_auteur) {
	$r = debut_boite_info();	
	$r .= icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_auteur','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png'));			
	$r .= fin_boite_info();
}	

?>
