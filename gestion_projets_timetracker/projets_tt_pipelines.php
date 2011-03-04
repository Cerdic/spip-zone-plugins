<?php
function projets_tt_actions_projets($args){

	if(_request('exec')=='projets' AND _request('voir')=='projet'){
	$actions='<a class="ajax" href="'.generer_action_auteur('session',$args['id_tache'].'-lancer',self()).'" title="'._T('gestpro:cloturer').'"><img src="'.find_in_path('img/logo_timetracker_16.png').'" alt="not_checked"/></a>';
	}
     return $actions;
}

function projets_tt_affiche_droite($args){

echo debut_cadre_relief("racine-site-24.gif", true, "", $nom),
		  recuperer_fond('prive/colonne_droite/timetracker',$contexte,Array("ajax"=>true)),
		  fin_cadre_relief(true);
     return $actions;
}

     
?>