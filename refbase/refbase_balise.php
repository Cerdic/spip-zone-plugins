<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_REFBASE_dist($p) {
	$option = interprete_argument_balise(1,$p);
	$p->code = "calculer_balise_REFBASE(\$Pile[0],$option)";
	return $p;
}

function calculer_balise_REFBASE($env, $option=''){
	// On determine l url a utiliser
	$url_refbase = $env['url_refbase'] ? trim($env['url_refbase']) : trim(lire_config('refbase/url_refbase'));
	// on ajoute le / final au besoin
	if (substr($url_refbase,-1)!='/') $url_refbase .= '/';
	$requete = $url_refbase;
	// Elements constants de la requete
	$requete .= 'show.php?client=inc-refbase-1.0&wrapResults=0';
	if ($env['id']!='') $requete .= '&records='.trim($env['id']);
	// On recupere le parametre par
	$par = $env['by'] ? trim($env['by']) : trim($env['par']);
	
	// On determine la vue (citations par defaut)
	$vue = $env['vue'] ? trim($env['vue']) : trim(lire_config('refbase/vue'));
	$submit = trim($env['submit']);
	switch ($submit) {
		case 'Cite':
		$vue = 'citations';
		break;
		case 'List':
		$vue = 'liste';
		break;
		case 'Display':
		$vue = 'details';
		break;
		case 'Browse':
		$vue = 'recap';
		break;
	}
	if (!$vue) $vue = 'citations';
	if ($env['id'])  $vue = 'citations'; // Si un id, on force en citations
	switch ($vue) {
		case 'citations':
		$requete .= '&submit=Cite';
		break;
		case 'liste':
		$requete .= '&submit=List';
		break;
		case 'details':
		$requete .= '&submit=Display';
		break;
		case 'recap':
		$requete .= '&submit=Browse';
		break;
	}
	if ($vue=='recap' and !$par) $par = 'year'; // Si vue recap, un par est obligatoire, year par defaut
	// Doit on afficher les liens externes ?
	$liens = $env['liens'] ? trim($env['liens']) : trim(lire_config('refbase/liens'));
	switch (trim($env['showLinks'])) {
		case '1':
		case 'yes':
		$liens = 'oui';
		break;
		case '0':
		case 'no':
		$liens = 'non';
		break;
	}
	if ($liens =='non' AND !$env['id']) // Si id, on force en oui
		$requete .= '&showLinks=0';
	else
		$requete .= '&showLinks=1';
	// Nombre maximum de liens affiches
	$max = $env['liens'] ? trim($env['max']) : trim(lire_config('refbase/max'));
	if ($env['showRows']) $max = trim($env['showRows']);
	if (!$max) $max = '100';
	$requete .= '&showRows='.$max;
	// Doit on afficher doublons ?
	$doublons = $env['doublons'] ? trim($env['doublons']) : trim(lire_config('refbase/doublons'));
	switch (trim($env['showDups'])) {
		case '1':
		case 'yes':
		$doublons = 'oui';
		break;
		case '0':
		case 'no':
		$doublons = 'non';
		break;
	}
	if (trim($env['without'])=='dups') $doublons = 'non';
	if ($doublons=='non') $requete .= '&without=dups';
	// On determine le tri (annee par defaut)
	$tri = $env['tri'] ? trim($env['tri']) : trim(lire_config('refbase/tri'));
	switch (trim($env['citeOrder'])) {
		case 'year':
		$tri = 'annee';
		break;
		case 'author':
		$tri = 'auteur';
		break;
		case 'type':
		$tri = 'type';
		break;
		case 'type-year':
		$tri = 'type-annee';
		break;
		case 'creation-date':
		$tri = 'date-creation';
		break;
	}	
	if (!$tri) $tri = 'annee';
	if (!$env['id'] && !$par)  { // Si un id ou si un par est passe, pas de tri via CiteOrder
		switch ($tri) {
			case "annee":
			$requete .= '&citeOrder=year';
			break;
			case "auteur":
			$requete .= '&citeOrder=author';
			break;
			case "type":
			$requete .= '&citeOrder=type';
			break;
			case "type-annee":
			$requete .= '&citeOrder=type-year';
			break;
			case "date-creation":
			$requete .= '&citeOrder=creation-date';
			break;
		}
	}
	if ($par) $requete .= '&by='.urlencode($par); //si on a un par, on transmet
	// Style des citations
	$style = $env['style'] ? trim($env['style']) : trim(lire_config('refbase/style'));
	if ($env['citeStyle']) $style = trim($env['citeStyle']);
	if (!$style) $style = 'APA';
	$requete .= '&citeStyle='.urlencode($style);
	// Doit on masquer abstracts et options export ?
	$liens_exports = $env['liens_exports'] ? trim($env['liens_exports']) : trim(lire_config('refbase/liens_exports'));
	switch (trim($env['showAbstract'])) {
		case '1':
		case 'yes':
		$liens_exports = 'oui';
		break;
		case '0':
		case 'no':
		$liens_exports = 'non';
		break;
	}
	switch (trim($env['viewType'])) {
		case 'Mobile':
		$liens_exports = 'non';
		break;
		case 'Web':
		$liens_exports = 'oui';
		break;
	}
	if ($liens_exports=='non' OR $env['id']) // Si id on n'affiche pas les liens
		$requete .= '&viewType=Mobile';
	else
		$requete .= '&viewType=Web';
	
	// Parametres de la recherche
	$where = trim($env['where']);
	if (!preg_match('#%20#',$where)) $where = urlencode($where); // On verifie la necissite d encode l url
	if ($where) $requete .= '&where='.$where;
	
	if ($env['type']) $requete .= '&type='.urlencode(trim($env['type']));
	
	$auteur = $env['author'] ? urlencode(trim($env['author'])) : urlencode(trim($env['auteur']));
	if ($auteur) $requete .= '&author='.$auteur;
	
	if (trim($env['voir'])=='tout' OR trim($env['records'])=='all') $requete .= '&records=all';
	
	$institution = $env['contribution_id'] ? urlencode(trim($env['contribution_id'])) : urlencode(trim($env['institution']));
	if ($institution) $requete .= '&contribution_id='.$institution;
	
	$motcle = $env['keywords'] ? urlencode(trim($env['keywords'])) : urlencode(trim($env['motcle']));
	if ($motcle) $requete .= '&keywords='.$motcle;
	
	$journal = $env['publication'] ? urlencode(trim($env['publication'])) : urlencode(trim($env['journal']));
	if ($journal) $requete .= '&publication='.$journal;
	
	$identifiants = trim($env['identifiants']);
	if ($env['records'] AND trim($env['records']) !='all') $identifiants=trim($env['records']);
	if ($identifiants) $requete .= '&records='.$identifiants;
	
	$annee = $env['year'] ? urlencode(trim($env['year'])) : urlencode(trim($env['annee']));
	if ($annee) $requete .= '&year='.$annee;
	
	$titre = $env['title'] ? urlencode(trim($env['title'])) : urlencode(trim($env['titre']));
	if ($titre) $requete .= '&title='.$titre;
	
	$depuis = $env['since'] ? trim($env['since']) : trim($env['depuis']);
	if ($depuis) {
		// Si c est une annee
		if (preg_match('#^[0-9]{4}$#',$depuis))
			$requete .= '&where=year+>+'.($depuis-1);
		// Si c est une duree en annee 
		elseif (preg_match('#^[0-9]{1,3}ans$#',$depuis))
			$requete .= '&where=year+>+YEAR(NOW())-'.substr($depuis,0,-3);
		elseif (preg_match('#^[0-9]{1,3}an$#',$depuis))
			$requete .= '&where=year+>+YEAR(NOW())-'.substr($depuis,0,-2);
		elseif (preg_match('#^[0-9]{1,3}y$#',$depuis))
			$requete .= '&where=year+>+YEAR(NOW())-'.substr($depuis,0,-1);
		elseif (preg_match('#^[0-9]{1,3}year$#',$depuis))
			$requete .= '&where=year+>+YEAR(NOW())-'.substr($depuis,0,-4);
		elseif (preg_match('#^[0-9]{1,3}years$#',$depuis))
			$requete .= '&where=year+>+YEAR(NOW())-'.substr($depuis,0,-5);
	}
	
	if ($env['doi']) $requete .= '&where=doi%20RLIKE%20%22'.urlencode(trim($env['doi'])).'%22';
	
	if ($option=='lien_requete')
		return $requete;
	else {
	
		// On recupere le flux
		$texte = spip_file_get_contents($requete);
		
		// Cas de plusieurs références
		if (!$env['id']) {
			// Renommage pour eviter toute interference
			$texte = str_replace('toggleVisibilitySlide','refbase_toggleVisibilitySlide',$texte);
			// Si vue=recap, il faut rendre les liens absolus
			if ($vue=='recap') {
				$texte = str_replace('<a href="search.php','<a href="'.$url_refbase.'search.php',$texte);
				$texte = str_replace('src="img/details','src="'.$url_refbase.'img/details',$texte);
			}
			// On affiche
			$retour = '<div class="refbasecss"';
			$css = $env['css'] ? trim($env['css']) : trim(lire_config('refbase/css'));
			if ($css) $retour .= ' style="'.$css.'"';
			$retour .= ">\n";
			$retour .= $texte;
			$retour .= "\n</div>";
			return $retour;
		}
		else { // Cas d une reference unique
			
			preg_match("#<div class=\"citation\">.*</div>#",$texte,$reference);
			preg_match("#<span class=\"Z3988\".*</span>#",$texte,$coins);
			$reference = substr($reference[0],22,-6);
			
			// rendre url clicable
			$in=array(
				'#((?:https?|ftp)://\S+)(\s|\z)#',
				'#((?<!//)(www\.)\S+)(\s|\z)#'
			);
			$out=array(
				'<a href="$1">$1</a>',
				'<a href="http://$1">$1</a>'
			);
			$reference = preg_replace($in,$out,$reference);
			
			return $reference.' '.$coins[0];
		}
	}
}

?>