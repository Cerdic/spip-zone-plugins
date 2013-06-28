// Ces fonction ont pour but de lancer en ajax les calculs de tailles de r�pertoires et sous r�pertoire
// pour l'ensemble des sites mutualis�s.
// Si c'est r�pertoire sont trop gros, il arrive qu'on atteigne le temps limite d'execution d'une requete php
// Pour �viter cela, il est mis en place un syst�me qui relance l'op�ration en descendant d'un cran dans l'arborescence

nb_demandes = new Array();

// Lance la recherche de taille sur le r�pertoire rep de tous les sites du tableau tableau_sites
// @author      Yffic
// @param       string   rep    r�pertoire a dimensionner (typiquement local, IMG ou cache)

function rechercher_tailles(rep) {
	var id ;
	$("#"+rep+"calculer").attr('disabled', 'disabled');
	//tableau_sites = new Array();
	//tableau_sites.push(["../../sites/www.domaine.ltd"]); // Pour tester sur un seul site
	$.each(tableau_sites,function(num_site,site){
		// Cas particulier du dossier cache
		if(rep=="cache") cible="tmp/cache" ;
		else cible=rep ;
		// Recherche des sous-r�pertoires
		$.get("../mutualisation/inc/dirliste.php",{
			dir: site+"/"+cible
		},function(liste){
			id="#"+rep+(num_site+1) ;
			gerer_liste(liste,site,cible,500,id);
		});
	});
}

// R�cup�re la liste des sous-r�pertoires de cible
// @author      Yffic
// @param       string   liste        chaine de la forme XXX##rep1##rep2...
//										        o� XXX vaut -1 si erreur et sinon le poids des fichiers du r�pertoire (hors sous-r�pertoires) en Mo
//										        o� rep1, rep2,... le nom des sous-r�pertoires
//              string   site         site en cours de traitement
//              string   cible        sous r�pertoire du site
//              string   taille_max   taille_max au dessus de laquelle l'exploration doit descendre d'un niveau
//              string   ident        ident du td � renseigner

function gerer_liste(liste,site,cible,taille_max,ident){
	//$("#trace").append("liste: "+liste+" cible: "+cible+" ident: "+ident+"<br />") ;
	if(liste==-1) {
		$(ident).removeClass("loading").append("<em><small><span class=\"erreur\">Erreur&nbsp;!"+site+"/"+cible+"</span></small></em>");
	} else {
		var tableau_file = liste.split("##");
		// On r�cup�re le contenu courant du ident et on y ajoute la taille des fichiers de la cible
		if($(ident).text()=="") contenu=0;
		else contenu=parseFloat($(ident).text());
		somme = contenu + parseFloat(tableau_file[0]) ;
		$(ident).empty().append(somme.toFixed(2)) ;
		// On supprime le premier �l�ment, Il ne reste donc plus que les sous-r�pertoires
		tableau_file.shift() ;
		nb_demandes[ident] = 0;
		// Si aucun sous-r�pertoire, on efface la roue loading
		if(tableau_file.length == 0) $(ident).removeClass("loading") ;
		// Pour chaque sous-e�pertoire, on va demander sa taille
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

// R�cup�re la taille d'un des sous-r�pertoires de cible
// @author      Yffic
// @param       string   taille       chaine de la forme XXX##erreur
//										        o� XXX vaut -1 si erreur, -2 si taille max atteinte et sinon la taille du r�pertoire en Mo
//										        o� erreur explicite le type d'arreur
//              string   site         site en cours de traitement
//              string   cible        sous r�pertoire du site
//              string   taille_max   taille_max au dessus de laquelle l'exploration doit descendre d'un niveau
//              string   ident        ident du td � renseigner

function gerer_taille(taille,site,cible,taille_max,ident){
	//$("#trace").append("taille: "+taille+" cible: "+cible+" ident: "+ident+"<br />") ;
	nb_demandes[ident] -- ;
	var retour = taille.split("##");
	// Erreur si -1 ou vide (peut arriver si on atteint le temps max d'execution php
	if((retour[0]==-1) || (taille=="")) {
		$(ident).removeClass("loading").append("<em><small><span class=\"erreur\">Erreur&nbsp;! "+retour[1]+"</span></small></em>");
	} else if(retour[0]==-2) {
		// On a atteint la limite, on va relancer l'op�ration en descendant d'un niveau
		// mais en mettant une taille max a 0 (soit pas de limite)
		site=(retour[1])+"..";
		cible="";
		$.get("../mutualisation/inc/dirliste.php",{
			dir: site+"/"+cible
		},function(liste){gerer_liste(liste,site,cible,0,ident);});
	} else {
		// On ajoute le r�sultat au contenu du champs
		somme = parseFloat($(ident).text()) + parseFloat(retour[0]) ;
		$(ident).empty().append(somme.toFixed(2));
		// Si c'est la derni�re demande qui revient, on supprime la roue loading
		if(nb_demandes[ident] == 0) $(ident).removeClass("loading") ;
	}
}

