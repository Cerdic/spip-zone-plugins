<?php
function projets_tt_actions_projets($args){

	if(_request('exec')=='projets' AND _request('voir')=='projet'){
	$actions='<a class="ajax" href="'.generer_action_auteur('session',$args['id_tache'].'-lancer',self()).'" title="'._T('gestpro:cloturer').'"><img src="'.find_in_path('img/logo_timetracker_16.png').'" alt="not_checked"/></a>';
	}
     return $actions;
}

function projets_tt_affiche_droite($args){

	$sql=sql_select('id_session','spip_projets_timetracker','statut="active"');
	
	$contexte=array();
	while($row=sql_fetch($sql)){
	$contexte[]=$row['id_session'];
	}
	if($contexte){
	echo debut_cadre_relief(find_in_path('img/logo_timetracker_24.png'), true, "",_T('timetracker:timetracker')),
		  recuperer_fond('prive/colonne_droite/timetracker',$contexte,Array("ajax"=>true)),
	  fin_cadre_relief(true);
     return $actions;
     }
}

function projets_tt_insert_head($flux){
	$flux .='<link rel="stylesheet" href="'.find_in_path('css/timetracker_styles.css').'" type="text/css" media="all" />';
return $flux;
}
     
?>