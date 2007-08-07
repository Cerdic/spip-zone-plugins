<?
if (!defined("_ECRIRE_INC_VERSION")) return;

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
	$nom = _T('jeux:jeu_court',array('id'=>$id_jeu,'nom'=>$nom));
	echo "<strong>$nom</strong><br />",
		(_request('exec')=='jeux_voir'?'':
			icone_horizontale(_T('jeux:voir_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png')) ),
		(_request('exec')=='jeux_edit'?'':
			icone_horizontale(_T('jeux:modifier_ce_jeu'),generer_url_ecrire('jeux_edit','id_jeu='.$id_jeu),find_in_path('img/jeu-crayon.png')) ),
		(_request('exec')=='jeux_resultats_jeu'?'':
			icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_jeu','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png')) ),
		(_request('exec')=='jeux_gerer_resultats'?'':
			icone_horizontale(_T('jeux:gerer_ses_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png')) ),
	fin_boite_info();
}

function boite_infos_accueil() {
	debut_boite_info();
	echo 
		icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png')),
		(_request('exec')=='jeux_gerer_resultats'?'':
			icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats','tous=oui'),find_in_path('img/jeu-laurier.png')) ),
	fin_boite_info();
}
?>