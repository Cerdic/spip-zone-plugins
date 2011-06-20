// Ces fonction ont pour but de lancer en ajax les calculs de tailles de répertoires et sous répertoire
// pour l'ensemble des sites mutualisés.
// Si c'est répertoire sont trop gros, il arrive qu'on atteigne le temps limite d'execution d'une requete php
// Pour éviter cela, il est mis en place un système qui relance l'opération en descendant d'un cran dans l'arborescence

nb_demandes = new Array();

// Lance la recherche de taille sur le répertoire rep de tous les sites du tableau tableau_sites
// @author      Yffic
// @param       string   rep    répertoire a dimensionner (typiquement local, IMG ou cache)

function rechercher_tailles(rep) {
	var id ;
	$("#"+rep+"calculer").attr('disabled', 'disabled');
	//tableau_sites = new Array();
	//tableau_sites.push(["../../sites/www.domaine.ltd"]); // Pour tester sur un seul site
	$.each(tableau_sites,function(num_site,site){
		// Cas particulier du dossier cache
		if(rep=="cache") cible="tmp/cache" ;
		else cible=rep ;
		// Recherche des sous-répertoires
		$.get("../mutualisation/inc/dirliste.php",{
			dir: site+"/"+cible
		},function(liste){
			id="#"+rep+(num_site+1) ;
			gerer_liste(liste,site,cible,500,id);
		});
	});
}

// Récupère la liste des sous-répertoires de cible
// @author      Yffic
// @param       string   liste        chaine de la forme XXX##rep1##rep2...
//										        où XXX vaut -1 si erreur et sinon le poids des fichiers du répertoire (hors sous-répertoires) en Mo
//										        où rep1, rep2,... le nom des sous-répertoires
//              string   site         site en cours de traitement
//              string   cible        sous répertoire du site
//              string   taille_max   taille_max au dessus de laquelle l'exploration doit descendre d'un niveau
//              string   ident        ident du td à renseigner

function gerer_liste(liste,site,cible,taille_max,ident){
	//$("#trace").append("liste: "+liste+" cible: "+cible+" ident: "+ident+"<br />") ;
	if(liste==-1) {
		$(ident).removeClass("loading").append("<em><small><span class=\"erreur\">Erreur&nbsp;!"+site+"/"+cible+"</span></small></em>");
	} else {
		var tableau_file = liste.split("##");
		// On récupère le contenu courant du ident et on y ajoute la taille des fichiers de la cible
		if($(ident).text()=="") contenu=0;
		else contenu=parseFloat($(ident).text());
		somme = contenu + parseFloat(tableau_file[0]) ;
		$(ident).empty().append(somme.toFixed(2)) ;
		// On supprime le premier élément, Il ne reste donc plus que les sous-répertoires
		tableau_file.shift() ;
		nb_demandes[ident] = 0;
		// Si aucun sous-répertoire, on efface la roue loading
		if(tableau_file.length == 0) $(ident).removeClass("loading") ;
		// Pour chaque sous-eépertoire, on va demander sa taille
		$.each(tableau_file,function(k,file){
			$(ident).addClass("loading")
			nb_demandes[ident] ++ ;
			$.get("../mutualisation/inc/dirsize.php",{
				dir: site+"/"+cible+"/"+file,
				taille_max: taille_max
			},function(taille){gerer_taille(taille,site,cible,taille_max,ident);});
		});
	}
}

// Récupère la taille d'un des sous-répertoires de cible
// @author      Yffic
// @param       string   taille       chaine de la forme XXX##erreur
//										        où XXX vaut -1 si erreur, -2 si taille max atteinte et sinon la taille du répertoire en Mo
//										        où erreur explicite le type d'arreur
//              string   site         site en cours de traitement
//              string   cible        sous répertoire du site
//              string   taille_max   taille_max au dessus de laquelle l'exploration doit descendre d'un niveau
//              string   ident        ident du td à renseigner

function gerer_taille(taille,site,cible,taille_max,ident){
	//$("#trace").append("taille: "+taille+" cible: "+cible+" ident: "+ident+"<br />") ;
	nb_demandes[ident] -- ;
	var retour = taille.split("##");
	// Erreur si -1 ou vide (peut arriver si on atteint le temps max d'execution php
	if((retour[0]==-1) || (taille=="")) {
		$(ident).removeClass("loading").append("<em><small><span class=\"erreur\">Erreur&nbsp;! "+retour[1]+"</span></small></em>");
	} else if(retour[0]==-2) {
		// On a atteint la limite, on va relancer l'opération en descendant d'un niveau
		// mais en mettant une taille max a 0 (soit pas de limite)
		site=(retour[1])+"..";
		cible="";
		$.get("../mutualisation/inc/dirliste.php",{
			dir: site+"/"+cible
		},function(liste){gerer_liste(liste,site,cible,0,ident);});
	} else {
		// On ajoute le résultat au contenu du champs
		somme = parseFloat($(ident).text()) + parseFloat(retour[0]) ;
		$(ident).empty().append(somme.toFixed(2));
		// Si c'est la dernière demande qui revient, on supprime la roue loading
		if(nb_demandes[ident] == 0) $(ident).removeClass("loading") ;
	}
}

