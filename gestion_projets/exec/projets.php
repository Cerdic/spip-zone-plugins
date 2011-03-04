<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('public/assembler');


function exec_projets_dist(){

	$contexte = Array();
	$contexte = calculer_contexte();
	
	$voir= _request('voir');
	
	
	
	// si pas autorise : message d'erreur
	/*if (!autoriser('modifier', 'article')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}*/
	
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'projets'),'data'=>''));
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');


	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('gestpro:projets'),_T('gestpro:projets'),_T('gestpro:projets'));
	
	
	// colonne gauche
	echo debut_gauche('', true);
	echo debut_boite_info(true);
	echo '<div class="infos">';
	echo '<h2 style="text-align:center;">'._T('gestpro:gestion_projets').'</h2>';
	if($id_projet=_request('id_projet'))echo '<div class="numero">'._T('gestpro:numero_projet').'<p>'.$id_projet.'</p></div>';
	if($id_tache=_request('id_tache'))echo '<div class="numero">'._T('gestpro:numero_tache').'<p>'.$id_tache.'</p></div>';	
	echo	'</div>';
	echo fin_boite_info(true);
	
					
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'projets'),'data'=>''));	
	

	// colonne droite
	echo creer_colonne_droite('', true);
	$bloc = '<div>';
	$bloc .=recuperer_fond('prive/colonne_droite/raccourcis',$contexte,Array("ajax"=>true));
	$bloc .= pipeline('affiche_droite', array('args'=>array('exec'=>'projets'),'data'=>''));
	$bloc .= '</div>';
	echo bloc_des_raccourcis($bloc);
	
	// centre
	echo debut_droite('', true);

	// contenu
	// ...
	
	switch($voir){
		case 'ajouter_projet':
		
			// si pas autorise : message d'erreur
			if (!autoriser('creer', 'projet')) {
				include_spip('inc/minipres');
				echo minipres();
				exit;
			}
			echo recuperer_fond('prive/editer/projet',$contexte,Array("ajax"=>true));
			break;
			
		case 'ajouter_tache':
		
			// si pas autorise : message d'erreur
			if (!autoriser('creer', 'tache',_request('id_projet'))) {
				include_spip('inc/minipres');
				echo minipres();
				exit;
			}
			echo recuperer_fond('prive/editer/tache',$contexte,Array("ajax"=>true));
			break;			
		case 'projet': 
			$id_projet=_request('id_projet');
			
			$contexte=sql_fetsel('participants,id_projet,nom','spip_projets','id_projet='.sql_quote($id_projet));
			

			
			$actions= icone_inline(_T('gestpro:editer_projet'),
			generer_url_ecrire("projets",
				"voir=editer_projet&id_projet=$id_projet"),'racine-site-24.gif', "edit.gif",'right');
				
			if(autoriser('editer','projet',$id_projet))	{
				$bandeau='<div class="bandeau_actions">'.$actions.'</div>';
				}
				
			$haut='<div class="fiche_objet">'.$bandeau.'<h1>'.$contexte['nom'].'</h1><div class="nettoyeur"></div>';	
				
			$contenu=recuperer_fond('prive/voir/projet',$contexte,array('ajax'=>true));
			
			$participants  = recuperer_fond('prive/voir/participants', $contexte);
			$participants = cadre_depliable('',_T('gestpro:participants_projet'),$deplie,$participants ,'form_participants','e');    
			
			$deplie=_request('deplie_taches');
			
			$taches  = recuperer_fond('prive/voir/taches', $contexte,array('ajax'=>true));
			$taches = cadre_depliable('',_T('gestpro:taches_projet'),$deplie,$taches ,'form_taches','e');    
				 
			echo	
			$haut.
			afficher_onglets_pages(
				array(
					'projet'=> _T('gestpro:projet'),
					'participants'=> _T('gestpro:participants'),	
					'participants'=> _T('gestpro:taches'),									
				),
				array(
					'projet'=>$contenu,
					'participants'=>$participants,	
					'taches'=>$taches,									
					)
				)
			.'</div>';

		break;
		
		case 'tache': 
			$id_tache=_request('id_tache');
			
			$contexte=sql_fetsel('participants,id_tache,nom','spip_projets_taches','id_tache='.sql_quote($id_tache));
			
			$actions= icone_inline(_T('gestpro:editer_tache'),
			generer_url_ecrire("projets",
				"voir=editer_tache&id_tache=$id_tache"),'racine-site-24.gif', "edit.gif",'right');
				
			if(autoriser('editer','tache',$id_tache))	{
				$bandeau='<div class="bandeau_actions">'.$actions.'</div>';
				}
				
			$haut='<div class="fiche_objet">'.$bandeau.'<h1>'.$contexte['nom'].'</h1><div class="nettoyeur"></div>';	
				
			$contenu=recuperer_fond('prive/voir/tache',$contexte,array('ajax'=>true));
			
				 
			echo	
			$haut.
			afficher_onglets_pages(
				array(
					'tache'=> _T('gestpro:tache'),								
				),
				array(
					'tache'=>$contenu,									
					)
				)
			.'</div>';

		
		break;		
		
		case 'editer_projet': 
		
			// si pas autorise : message d'erreur
			if (!autoriser('editer', 'projet')) {
				include_spip('inc/minipres');
				echo minipres();
				exit;
			}
			$contexte['id_projet']=_request('id_projet');
			echo recuperer_fond('prive/editer/projet',$contexte,array('ajax'=>true));
		break;
		
		case 'editer_tache': 
		
			// si pas autorise : message d'erreur
			if (!autoriser('editer', 'tache')) {
				include_spip('inc/minipres');
				echo minipres();
				exit;
			}
			$contexte['id_projet']=_request('id_projet');
			echo recuperer_fond('prive/editer/tache',$contexte,array('ajax'=>true));
		break;		
		
		 default: echo recuperer_fond('prive/contenu/projets',$contexte,Array("ajax"=>true));
		};

	// ...
	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'boutique'),'data'=>''));
	
	echo fin_gauche(), fin_page();

}
?>
