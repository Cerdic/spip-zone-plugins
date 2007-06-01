// on a besoin des tableaux au cas ou plusieurs pendus figurent sur une meme page
var Mots = new Array(); // Listes de mots
var Paths = new Array(); // Chemin des images
var Images = new Array(); // nom des images
var nb_Images = new Array(); // Nombre d'images pour un pendu
var nb_Pendus = new Array(); // Nombre de coups avant d'etre pendu
var Cherche = new Array(); // Mot choisi
var Chaine = new Array(); // Chaine a afficher
var Propositions = new Array(); // Lettres proposees
var nb_Erreurs = new Array(); // Nombre d'erreurs
var Extremes = new Array(); // Afficher ou non les extremes

// Pour afficher les lettres du mot, on va aerer avec des espaces
function pendu_aff_mot(jeu) {
	Tampon=""; Long=Cherche[jeu].length;
	for(i=0;i<Long;i++){Tampon=Tampon+" "+Chaine[jeu].substr(i,1);}
	document.forms['pendu'+jeu].cache.value=Tampon;
}

// Afficher les images du pendu en fonction de nb_Erreurs
function pendu_aff_images(jeu) {
 for(i=0; i<nb_Images[jeu]; i++)
	 document.images['pict'+jeu+'_'+i].src = Paths[jeu]+Images[jeu][nb_Erreurs[jeu]][i];
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
	if (Propositions[jeu]!=undefined) {
		Long=Propositions[jeu].length;
		for (i=0;i<Long;i++) pendu_clavier_grise(jeu, Propositions[jeu].substr(i,1), false);
	}
	// choisir un mot au hasard et calculer la chaine a afficher
	Tirage=Math.floor(Math.random()*Mots[jeu].length); // choisir un mot au hasard
	Cherche[jeu]=Mots[jeu][Tirage]; // Stocker le mot choisi
	Chaine[jeu]=pendu_chaine(Cherche[jeu], Extremes[jeu]);
	// initialiser les variables
	Propositions[jeu]='';
	nb_Erreurs[jeu]=0;
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
	Long=Cherche[jeu].length; // Longueur du mot a trouver
	Propositions[jeu]+=lettre; // stocker la lettre dans les lettres deja proposees
	trv=0;
for (i=0;i<Long;i++) if(Cherche[jeu].substr(i,1)==lettre) {
		trv=1;	// Traitement si la lettre est trouvee
		aGauche=Chaine[jeu].substr(0,i);
		aDroite=Chaine[jeu].substr(i+1,Long);
		Chaine[jeu]=aGauche+lettre+aDroite;
	}
	pendu_aff_mot(jeu); // On affiche le mot 
	pendu_clavier_grise(jeu, lettre, true); // On grise le clavier
	if(trv==0) nb_Erreurs[jeu]++; // Si la lettre n'a pas ete trouvee, +1 dans Erreurs
	if(nb_Erreurs[jeu]>0) pendu_aff_images(jeu); // On change de pendu...
	if(nb_Erreurs[jeu]>=nb_Pendus[jeu]){alert(T_fini+Cherche[jeu]);pendu_init(jeu);} // Perdu ?
	if(Chaine[jeu]==Cherche[jeu]){alert(T_bravo);pendu_init(jeu);} // Gagne ?
}