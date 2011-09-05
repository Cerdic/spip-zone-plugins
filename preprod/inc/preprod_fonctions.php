<?php
/**
* Sélectionner dans la base les tickets 
* qui correspondent à une url
*/
function lister_tickets_par_url($adresse)
{
	// on supprime les parties d'url inutiles
	$adresse	= preg_replace('/(lang=fr|lang=en|var_mode=calcul|var_mode=recalcul)/', '', $adresse);
	$adresse	= preg_replace('/&+/', '&', $adresse);
	$adresse	= trim($adresse, '&?');
	
	$tickets = array();
	if (!empty($adresse))
	{
	//	if ('/'==$adresse)
	//	$condition = "exemple LIKE '% http://www2.concurrences.com/ %' OR texte LIKE '% http://www2.concurrences.com/ %";
		$condition = "exemple LIKE '%".$adresse."' OR texte LIKE '%".$adresse."%'";
		
		// on recherche les id_ticket qui correspondent à notre adresse
		$tickets	= sql_allfetsel('id_ticket', 'spip_tickets', $condition);
		$tickets	= array_map('array_shift', $tickets);
	}
	return $tickets;
}

/**
* Construire par saisies le formulaire 
* de création/modification de ticket
*/
function saisies_formulaire_ticket_edit($id_ticket=0)
{
	$id_projet			= intval(_request('id_projet'));
	$id_livrable		= intval(_request('id_livrable'));
	$titre				= _request('titre');
	$texte				= _request('texte');
	$type				= _request('type');
	$severite			= _request('severite');
	$id_assigne 		= intval(_request('id_assigne'));
	$statut				= intval(_request('statut'));

	// si l'id_ticket est valide, on recherche dans la base
	// les données du ticket
	if (!empty($id_ticket))
	{
		$infos_ticket = sql_fetsel('titre, texte, severite, type, statut, id_auteur, id_assigne, exemple, id_livrable, statut', 
					'spip_tickets', 'id_ticket='.$id_ticket);

		if (!_request('id_livrable')) $composant = $infos_ticket['id_livrable'];
		if (!_request('titre')) $titre = $infos_ticket['titre'];
		if (!_request('texte')) $texte = $infos_ticket['texte'];
		if (!_request('type')) $type = $infos_ticket['type'];
		if (!_request('severite')) $severite = $infos_ticket['severite'];
		if (!_request('id_assigne')) $id_assigne = $infos_ticket['id_assigne'];
		if (!_request('statut')) $statut = $infos_ticket['statut'];
	}

	// on recherche dans la base les projets
	$projets_base	= sql_allfetsel('id_projet, titre', 'spip_projets');
	$aProjets = array();
	foreach($projets_base as $k => $v)
	{
		$aProjets[$v['id_projet']] = supprimer_numero($v['titre']);
	}

	// on génère les livrables
	$aLivrables = array(0 => _T('livrables:livrable_non_trouve'));
	if (!empty($id_projet))
	{
		$livrables_base = sql_allfetsel('id_livrable, titre', 'spip_livrables', 'id_projet='.$id_projet, '', 'titre');
		foreach($livrables_base as $k => $v)
		{
			$aLivrables[$v['id_livrable']] = supprimer_numero($v['titre']);
		}
	}

	// on recherche les auteurs à qui assigner le ticket
	$auteurs_base = sql_allfetsel('id_auteur, nom', 'spip_auteurs', 'webmestre=\'oui\'', 'id_auteur', 'nom');
	$aAssigne = array( '0' => '');
	foreach($auteurs_base as $ligne)
	{
		$aAssigne[$ligne['id_auteur']] = $ligne['nom'];
	}

	// Le tableau de saisies à retourner
	$saisies_ticket = array(
		array( // fieldset projet / composant
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'livrable',
				'label' => _T('livrables:lier_livrable')
			),
			'saisies' => array( // les champs dans le fieldset
				array( // champ projet : liste déroulante
					'saisie' => 'selection',
					'options' => array(
							'nom' => 'id_projet',
							'label' => ucfirst(_T('projet:projet')),
							'datas' => $aProjets,
							'defaut' => $id_projet
					)
				),
				array( // champ composant : liste déroulante
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'id_livrable',
						'label' => _T('livrables:livrable'),
						'cacher_option_intro' => 'oui',
						'datas' => $aLivrables,
						'defaut' => $id_livrable
					)
				)
			)
		),
		array( // fieldset titre / description
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'titre_description',
				'label' => 'Détails'
			),
			'saisies' => array( // les champs dans le fieldset
				array( // champ type : liste déroulante
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'type',
						'label' => 'Type',
						'cacher_option_intro' => 'oui',
						'datas' => array(
						'1' => _T('preprod:type_probleme'),
						'2' => _T('preprod:type_amelioration'),
						'3' => _T('preprod:type_tache')
						),
						'defaut' => $type,
						'obligatoire' => 'oui'
					)
				),
				array( // champ severite : liste déroulante
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'severite',
						'label' => 'Sévérité',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'1' => _T('preprod:severite_bloquant'),
							'2' => _T('preprod:severite_important'),
							'3' => _T('preprod:severite_normal'),
							'4' => _T('preprod:severite_peu_important')
						),
						'defaut' => $severite,
						'obligatoire' => 'oui'
					)
				),
				array( // champ titre : champ texte
					'saisie' => 'input',
					'options' => array(
							'nom' => 'titre',
							'label' => _T('preprod:label_titre_resume'),
							'defaut' => $titre,
							'obligatoire' => 'oui'
					)
				),
				array( // champ description : champ texte
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'texte',
						'label' => _T('preprod:label_description'),
						'rows' => 4,
						'cols' => 90,
						'inserer_barre' => 'edition',
						'defaut' => $texte,
						'obligatoire' => 'oui'
					)
				)
			)
		),
		array( // hors fieldset : assigner à
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'id_assigne',
				'label' => _T('preprod:label_assigner_a'),
				'cacher_option_intro' => 'oui',
				'datas' => $aAssigne,
				'defaut' => $id_assigne
			)
		)
	);
	if (!empty($id_ticket))
		$saisies_ticket[] = array( // hors fieldset : statut (modifiable)
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'statut',
				'label' => _T('statut'),
				'cacher_option_intro' => 'oui',
				'datas' => array(
					'ouvert' => _T('preprod:statut_ouvert'),
					'resolu' => _T('preprod:statut_resolu'),
					'ferme' => _T('preprod:statut_ferme')
				),
				'defaut' => $statut
			)
		);

	return $saisies_ticket;	
}

?>