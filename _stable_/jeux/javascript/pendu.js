// on a besoin des tableaux au cas ou plusieurs pendus figurent sur une meme page
var pendu_Mots = new Array(); // Listes de mots
var pendu_Paths = new Array(); // Chemin des images
var pendu_Images = new Array(); // nom des images
var nb_pendu_Images = new Array(); // Nombre d'images pour un pendu
var nb_Pendus = new Array(); // Nombre de coups avant d'etre pendu
var pendu_Cherche = new Array(); // Mot choisi
var pendu_Chaine = new Array(); // pendu_Chaine a afficher
var pendu_Propositions = new Array(); // Lettres proposees
var nb_pendu_Erreurs = new Array(); // Nombre d'erreurs
var pendu_Extremes = new Array(); // Afficher ou non les extremes

// Pour afficher les lettres du mot, on va aerer avec des espaces
function pendu_aff_mot(jeu) {
	Tampon=""; Long=pendu_Cherche[jeu].length;
	for(i=0;i<Long;i++){Tampon=Tampon+" "+pendu_Chaine[jeu].substr(i,1);}
	document.forms['pendu'+jeu].cache.value=Tampon;
}

// Afficher les images du pendu en fonction de nb_pendu_Erreurs
function pendu_aff_images(jeu) {
 for(i=0; i<nb_pendu_Images[jeu]; i++)
	 document.images['pict'+jeu+'_'+i].src = pendu_Paths[jeu]+pendu_Images[jeu][nb_pendu_Erreurs[jeu]][i];
}

// Calcul de la chaine a afficher au tout debut
function pendu_chaine(mot, extremes) {
	temp=extremes?mot.charAt(0):''; // Premiere lettre ?
	Long=mot.length;
	for(i=1;i<=(Long-(extremes?2:0));i++) temp+='.';
	if (extremes) temp+=mot.charAt(Long-1); // Derniere lettre ?
	return temp;
}

// Initialisation du jeu
function pendu_init(jeu) {
	// retablir le clavier
	if (pendu_Propositions[jeu]!=undefined) {
		Long=pendu_Propositions[jeu].length;
		for (i=0;i<Long;i++) pendu_clavier_grise(jeu, pendu_Propositions[jeu].substr(i,1), false);
	}
	// choisir un mot au hasard et calculer la chaine a afficher
	Tirage=Math.floor(Math.random()*pendu_Mots[jeu].length); // choisir un mot au hasard
	pendu_Cherche[jeu]=pendu_Mots[jeu][Tirage]; // Stocker le mot choisi
	pendu_Chaine[jeu]=pendu_chaine(pendu_Cherche[jeu], pendu_Extremes[jeu]);
	// initialiser les variables
	pendu_Propositions[jeu]='';
	nb_pendu_Erreurs[jeu]=0;
	pendu_aff_mot(jeu);
 	pendu_aff_images(jeu);
}

// griser (ou non !) une lettre du clavier
function pendu_clavier_grise(jeu, lettre, grise) {
	document.forms['pendu'+jeu].elements[lettre].disabled=grise;
	if (grise) document.forms['pendu'+jeu].elements[lettre].style.color='graytext';
	 else document.forms['pendu'+jeu].elements[lettre].style.color='';
	document.forms['pendu'+jeu].elements[lettre].blur();
}

// Voir si la lettre existe dans le mot
function pendu_trouve(lettre, jeu) {
	Long=pendu_Cherche[jeu].length; // Longueur du mot a trouver
	pendu_Propositions[jeu]+=lettre; // stocker la lettre dans les lettres deja proposees
	trv=0;
for (i=0;i<Long;i++) if(pendu_Cherche[jeu].substr(i,1)==lettre) {
		trv=1;	// Traitement si la lettre est trouvee
		aGauche=pendu_Chaine[jeu].substr(0,i);
		aDroite=pendu_Chaine[jeu].substr(i+1,Long);
		pendu_Chaine[jeu]=aGauche+lettre+aDroite;
	}
	pendu_aff_mot(jeu); // On affiche le mot 
	pendu_clavier_grise(jeu, lettre, true); // On grise le clavier
	if(trv==0) nb_pendu_Erreurs[jeu]++; // Si la lettre n'a pas ete trouvee, +1 dans Erreurs
	if(nb_pendu_Erreurs[jeu]>0) pendu_aff_images(jeu); // On change de pendu...
	if(nb_pendu_Erreurs[jeu]>=nb_Pendus[jeu]){alert(T_fini+pendu_Cherche[jeu]);pendu_init(jeu);} // Perdu ?
	if(pendu_Chaine[jeu]==pendu_Cherche[jeu]){alert(T_bravo);pendu_init(jeu);} // Gagne ?
}