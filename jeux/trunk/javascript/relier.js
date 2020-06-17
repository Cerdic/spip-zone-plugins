
/*
	Projet : https://github.com/baptx/connect-points
	Infos : https://drawcode.eu/projects/connect-points/
	Adapté et étendu pour le plugin jeux fonctionnant sous SPIP
	Par : Patrice Vanneufville (avril 2020)
	Contact : patrice¡.!vanneufville¡@!laposte¡.!net
*/


/**
 * Connect points ===============================================================
 * Draw and save data, correction tools
 *
 * Using HTML5 Canvas element & JavaScript
 *
 * Copyright (c) 2012 Baptiste Hassenfratz
 * Licensed under MIT license http://opensource.org/licenses/MIT
 **/

var relierDraws = [];
var drawLoaders = [];

function relierDone(dataIdG) {
	var done = document.getElementById(dataIdG);
	return done && done.classList.contains('relier_done');
}

function relierDraw_create(usr, ref, dataIdG, dataIdD, repg, repd, pImg, col='#FF0000', colErr='#000000', opt="140/40/30/20/0", counter=false) {
	var espH, espV, margX, margY, ttRelier;
	[espH, espV, margX, margY, ttRelier] = opt.split('/');
	relierDraws[ref] = new relierDraw_();
	drawLoaders[ref] = function(){ 
	  relierDraws[ref].Loader(usr, ref, dataIdG, dataIdD, repg, repd, pImg, col, colErr, espH, espV, margX, margY, ttRelier, counter);
	}
	window.addEventListener("load", drawLoaders[ref]);
}

function relierReDrawAll() {
	// Si ajax, on construit tout de suite les nouveaux jeux
	for (ref in drawLoaders)
		if(document.getElementById("canvas" + ref))
			drawLoaders[ref]();
}

(function($){
	$(document).ready(function(){ onAjaxLoad(relierReDrawAll); });
})(jQuery);

function relierDraw_() // Constructeur de la classe
{
	// Attributs (privés)
	
	// Attributs permettant l'accès aux éléments du module
	var canvas;
	var context;
	var btnBack;
	var btnForward;
	var btnConnection;
	var btnDelete;
	var btnEraser;
	var relierDivGauche;
	var relierDivDroite;
	var alertContainer;
	var imageFond;
	// Suppléments débogage
	var divCoordonnees;
	var btnReset;
	
	// Attributs contenant les données du module
	var sens;
	var del;
	var imgParent;
	var msgBox;
	var multisel;
	var canvasClickX;
	var canvasClickY;
	var offsetCanvasX;
	var offsetCanvasY;
	var intervalPtY0;
	var intervalPtYA;
	var intervalPtYB;
	var offsetPtX;
	var margeY0;
	var margeYA;
	var margeYB;
	var image;
	var ptGauche;
	var ptDroite;
	var ptGaucheBak;
	var ptDroiteBak;
	var coefdir;
	var coefdirBak;
	var tabDataURL;
	var reference;
	var relierRepGauche;
	var relierRepDroite;
	var toutRelier = true;
	var color;
	var colorError;
	var cursor;
	// Suppléments Elève
	var user;
	var correction;
	var ptFaux;
	// Suppléments Professeur
	var relierNbGauche;
	var relierNbDroite;
	var nbMax;
	// Suppléments débogage
	var dbg = false;
	var ref;
	// path images pour "src"
	var pathImg;
	// éléments extérieurs
	var ptsCount;
	var ptsDebug;
	// variables internes de fonctions d'évènements
	var pointA;
	var pointB;
	var back;
	var forward;
	var connection;
	var switchDel;
	var refresh;
	var rmGauche;
	var newGauche;
	var rmDroite;
	var newDroite;
	var cut;
	var ready;
	var dessin;
	var animation;
	
	// Méthodes (publiques -> this; privées -> variable locale var)
	
	// Fonction permettant d'initialiser tous les éléments et paramètres de la classe
	// (nécessite d'être déclenché sur l'événement load de l'objet window)
	this.Loader = function draw_Loader(usr, ref0, dataIdG, dataIdD, repg, repd, pImg, col='#FF0000', colErr='#000000', espH=140, espV=40, margX=30, margY=20, ttRelier=1, counter=false)
	{
		if(relierDone(dataIdG)) return;
		
		pathImg = pImg;
		ref = ref0;
		window.removeEventListener("load", drawLoaders[ref]);
		
		if(usr) // Détecter s'il s'agit de l'interface utilisateur ou administrateur
			user = true;
		else
			user = false;
		
		
		/** Configuration **/
		/**/
		
		// nombres de mots par défaut à gauche et droite pour la création d'un nouvel exercice
		relierNbGauche = 5;
		relierNbDroite = 5;

		// faut-il relier tous les points ?
		toutRelier = parseInt(ttRelier) > 0;
		
		// utilisation du plugin debug.js (console événements, coordonnées canvas, bouton reset)
		if(user) dbg = false; // true ou false pour la page utilisateur
		if(!user) dbg = false; // true ou false pour la page administrateur
		
		// couleur tracés + erreurs
		color = col; // "#ff0000";
		colorError = colErr; // "#000000";
		
		/**/
		/** Fin Configuration **/
		
		
		// on récupère l'accès aux éléments dans des variables
		canvas = document.getElementById("canvas" + ref);
		// messages d'erreurs dans le cas où le canvas ou son contexte n'existent pas
		if(!canvas) {
			alert("Impossible de récupérer le canvas");
			return;
		}
		context = canvas.getContext("2d");
		if(!context) {
			alert("Impossible de récupérer le contexte du canvas");
			return;
		}
		btnBack = document.getElementById("relierBack"+ref);
		btnForward = document.getElementById("relierForward"+ref);
		btnConnection = document.getElementById("connection"+ref);
		btnDelete = document.getElementById("del"+ref);
		if(!user) btnEraser = document.getElementById("eraser");
		alertContainer = document.getElementById("AlertContainer"+ref);
		imageFond = document.getElementById("ImageFond"+ref);
		if(imageFond) imageFond = imageFond.getElementsByTagName('img');

		if(counter && user) {
			ptsCount = ptsCount_create("count" + ref);
		} else
			ptsCount = false;

		if (window.getComputedStyle(canvas).getPropertyValue("cursor") == "auto")
			cursor = false;
		else
			cursor = true;

		// on récupère le contenu de la connexion au serveur si disponible et on génère les éléments du module
		
		var relierTexteGauche, relierTexteDroite;
		
		if(ref, dataIdG, dataIdD)
		{			
			reference = ref;

			relierTexteGauche = document.getElementById(dataIdG).childNodes;
			relierTexteDroite = document.getElementById(dataIdD).childNodes;
			
			if(repg, repd) {
				relierRepGauche = repg.slice(0);
				relierRepDroite = repd.slice(0);
				
				if(user) correction = "js";
			}
			
			relierNbGauche = relierTexteGauche.length;
			relierNbDroite = relierTexteDroite.length;
		}
		
		// On affiche la référence de l'exercice pour l'utilisateur
		if(reference) document.getElementById("exo"+ref).innerHTML = "<p>Exercice <span class=\"ref\">" + htmlspecialchars(reference) + "</span></p>";
		// innerHTML nécessaire au lieu de nodeValue pour que le code HTML <b> soit pris en compte et non affiché seulement (<b>)
		// htmlspecialchars() fonction additionnelle du fichier htmlspecialchars.js car existante sous PHP uniquement, evite l'injection de code XSS
		
		// calcul du nombre maximum de points à gauche ou à droite du canvas
		nbMax = Math.max(relierNbGauche, relierNbDroite);
		// multi sélection autorisée si le nombre de points à droite et à gauche n'est pas le même
		multisel = relierNbGauche - relierNbDroite;
		
		if(imageFond && imageFond.length) {
			imageFond = imageFond[0]; // en principe il n'y a qu'une seule image ici et SPIP a insere la hauteur et la largeur
			var ww = imageFond.width;
			var hh = imageFond.height;
console.log('espH=' + espH + '% et margX=' + margX + '%   -   espV=' + espV + '% et margY=' + margY + '%');
//			canvas.width = imageFond.width;
			// les donnees fournies sont en pourcentage ici !
			espH = Math.floor(parseFloat(espH) * ww / 100);
			espV = Math.floor(parseFloat(espV) * hh / 100);
			margX = Math.floor(parseFloat(margX) * ww / 100);
			margY = Math.floor(parseFloat(margY) * hh / 100);
console.log('espH=' + espH + 'px et margX=' + margX + 'px   -   espV=' + espV + 'px et margY=' + margY + 'px');
			var triptyque = document.getElementById("relierContent"+ref);
			triptyque.setAttribute("style", "text-align: left; background: no-repeat top left url(" + imageFond.src + ')');
			var maxi = 30;
			if(margX > maxi) {
				var divG = margX - maxi;
				margX = maxi;
			} else {
				var divG = 0;
			}
			var divD = (ww - (2*margX) - espH - divG)
			var maxi = Math.floor((espV + 1) / 2);
			if(margY > maxi) {
				var paddY = margY - maxi;
				margY = maxi;
			} else {
				var paddY = 0;
			}
			var divG = 'width: ' + divG + 'px;'; // largeur de la colonne de gauche
			//var divD = 'width: ' + divD + 'px;';  // largeur de la colonne de droite
			canvas.setAttribute('style', 'margin-top: ' + paddY + 'px;'); // décalage du canvas
			
				
		} else {
			imageFond = false;
			margX = 30;
			var divG = '';
			var divD = '';
			var paddY = 0;
		}

console.log('image=' + ww + 'x' + hh + 'px - divG=' + divG + 'px - divD=' + divD + 'px - margX=' + margX + 'px - margY=' + margX + 'px');

		intervalPtY0 = parseInt(espV); // espace en pixels (axe des ordonnées) entre les différents points du canvas
		offsetPtX = parseInt(margX); // espace en pixels (axe des abscisses) entre les points et les bords gauche et droit du canvas
		canvas.width = offsetPtX + parseInt(espH) + offsetPtX;

		margeY0 = Math.floor((intervalPtY0 + 1) / 2); // marge en haut et en bas du canvas : 50% de l'espace entre les points
		margeYA = margeY0; margeYB = margeY0;
		intervalPtYA = intervalPtY0; intervalPtYB = intervalPtY0;
		if(relierNbDroite < relierNbGauche) {
			var espaceDispo = (relierNbGauche - relierNbDroite) * intervalPtY0;
			// on écarte un peu les moins nombreux à 30%
			margeYB += Math.floor(espaceDispo * 0.70 / 2);
			if(relierNbDroite > 1) intervalPtYB += Math.floor(espaceDispo * 0.30 / (relierNbDroite - 1) );
		} else if(relierNbDroite > relierNbGauche) {
			var espaceDispo = (relierNbDroite - relierNbGauche) * intervalPtY0;
			// on écarte un peu les moins nombreux à 30%
			margeYA += Math.floor(espaceDispo * 0.70 / 2);
			if(relierNbGauche > 1) intervalPtYA += Math.floor(espaceDispo * 0.30 / (relierNbGauche - 1) );
		}
		
		// On redimenssionne le canvas en fonction du nombre de points maximum
		canvas.height = (nbMax-1) * intervalPtY0 + margeY0 * 2;

		relierDivGauche = document.getElementById("relierDivGauche"+ref);
		relierDivDroite = document.getElementById("relierDivDroite"+ref);
		relierDivGauche.setAttribute("style", divG + "margin-top:" + Math.floor(margeYA - intervalPtYA / 2) + 'px;');
		relierDivDroite.setAttribute("style", divD + "margin-top:" + Math.floor(margeYB - intervalPtYB / 2) + 'px;');
		
		// déplacement des noeuds vers les colonnes affichées
		if(user)
		{
			for(var i = 1; i <= relierNbGauche; i++) {
        		relierDivGauche.appendChild(relierTexteGauche[0]);
				//relierDivGauche.appendChild(document.createElement("div"));
				relierDivGauche.lastChild.setAttribute("id", "Gauche"+ref+i);
				relierDivGauche.lastChild.setAttribute("style", "height:"+intervalPtYA+'px');
				//if(relierTexteGauche) relierDivGauche.lastChild.appendChild(document.createTextNode(relierTexteGauche[i-1]));
				relierDivGauche.lastChild.setAttribute("class", 
					"relierElement eltGauche" + (relierDivGauche.lastChild.getElementsByTagName('img').length ? " hasImg" : ""));
			}
			for(var i = 1; i <= relierNbDroite; i++) {
        		relierDivDroite.appendChild(relierTexteDroite[0]);
				//relierDivDroite.appendChild(document.createElement("div"));
				relierDivDroite.lastChild.setAttribute("id", "Droite"+ref+i);
				relierDivDroite.lastChild.setAttribute("style", "height:"+intervalPtYB+'px');
				//if(relierTexteDroite) relierDivDroite.lastChild.appendChild(document.createTextNode(relierTexteDroite[i-1]));
				relierDivDroite.lastChild.setAttribute("class", 
					"relierElement eltDroite" + (relierDivDroite.lastChild.getElementsByTagName('img').length ? " hasImg" : ""));
			}
		}
		else
		{	// TODO !
			for(var i = 1; i <= relierNbGauche; i++) {
				relierDivGauche.appendChild(document.createElement("input"));
				relierDivGauche.lastChild.setAttribute("type", "text");
				relierDivGauche.lastChild.setAttribute("id", "Gauche"+ref+i);
				if(relierTexteGauche) relierDivGauche.lastChild.setAttribute("value", relierTexteGauche[i-1]);
				relierDivGauche.appendChild(document.createElement("br"));
			}
			for(var i = 1; i <= relierNbDroite; i++) {
				relierDivDroite.appendChild(document.createElement("input"));
				relierDivDroite.lastChild.setAttribute("type", "text");
				relierDivDroite.lastChild.setAttribute("id", "Droite"+ref+i);
				if(relierTexteDroite) relierDivDroite.lastChild.setAttribute("value", relierTexteDroite[i-1]);
				relierDivDroite.appendChild(document.createElement("br"));
			}
			
			if(reference) document.getElementById("Reference").value = reference;
		}
		
		document.getElementById(dataIdG).classList.add('relier_done');
		document.getElementById(dataIdD).classList.add('relier_done');
		
		// calcul le décalage du canvas par rapport à la partie haute gauche de la page web
		
				offsetCanvasX = canvas.offsetLeft;
		offsetCanvasY = canvas.offsetTop;
		while(canvas = canvas.offsetParent) {
			offsetCanvasX += canvas.offsetLeft;
			offsetCanvasY += canvas.offsetTop;
		}
		canvas = document.getElementById("canvas" + ref); // on réassigne la variable du canvas à la fin du traitement
		
		// initialisation de variables
		
		image = document.createElement("img");
		
		ptGauche = [];
		ptDroite = [];
		coefdir = [];
		tabDataURL = [];
		ptFaux = [];
		
		msgBox = false;
		
		// ajout d'événements
		canvas.addEventListener("click", pointA = function(e){if(!msgBox) PointA(e);});
		btnBack.addEventListener("click", back = function(){if(!msgBox) Restore("back");});
		btnForward.addEventListener("click", forward = function(){if(!msgBox) Restore("forward");});
		if(user) btnConnection.addEventListener("click", connection = function(){if(!msgBox) Connection("correct", ConnectionCallback);});
		else btnConnection.addEventListener("click", connection = function(){if(!msgBox) Connection("insert", ConnectionCallback);});
		btnDelete.addEventListener("click", switchDel = function(){if(!msgBox) SwitchDel();});
		if(!user) btnEraser.addEventListener("click", connectionDel = function(){if(!msgBox) Connection("delete", ConnectionCallback);});
		
		jeu = document.getElementById("JEU" + ref);
		if(jeu) 
			jeu.addEventListener('dblclick', function(e) { e.preventDefault(); e.stopPropagation(); }, false);		
		
		// Détecte si l'accès aux images des boutons et curseur de souris doit se faire par le dossier parent
		// (utile si l'index du module est dans un sous-dossier, comme la demo en local par exemple)
		
/*		
		// Détecte si le script debug.js a déjà été inséré dans la page HTML (utilisé pour une démo en local sans serveur)
		
		var scriptTags = document.getElementsByTagName("script");
		
		for(var i = 0; i < scriptTags.length; i++) {
			if(scriptTags[i].attributes.length != 0)
				if((scriptTags[i].attributes[0].value).match("debug.js") == "debug.js") {
					var detectScript = true;
					dbg = true; // les fonctions debug sont activées
				}
		}
*/		
		if(dbg) {
			// Création dynamique de la console de debug et ses outils
			var infoBottom = document.getElementById("infoBottom"+ref);
			
			var divExtra = document.createElement("div");
			if(user) divExtra.setAttribute("id", "extraUser"+ref);
			else divExtra.setAttribute("id", "extraAdmin"+ref);
			if(user) divExtra.setAttribute("class", "extraUser");
			else divExtra.setAttribute("class", "extraAdmin");
			var newTextarea = document.createElement("textarea");
			newTextarea.setAttribute("id","console"+ref);
			if(user) {
				newTextarea.setAttribute("rows","5");
				newTextarea.setAttribute("cols","50");
			}
			else {
				newTextarea.setAttribute("rows","5");
				newTextarea.setAttribute("cols","25");
			}
			newTextarea.setAttribute("readonly","readonly");
			newTextarea.appendChild(document.createComment("création console d'événements pour tous les navigateurs"));
			divExtra.appendChild(newTextarea);
			var newInput1 = document.createElement("input");
			newInput1.setAttribute("type","text");
			newInput1.setAttribute("id","coordonnees"+ref);
			newInput1.setAttribute("class","relierCoordonnees");
			newInput1.setAttribute("size","6");
			newInput1.setAttribute("readonly","readonly");
			divExtra.appendChild(newInput1);
			var newInput2 = document.createElement("input");
			newInput2.setAttribute("type","button");
			newInput2.setAttribute("id","reset"+ref);
			newInput2.setAttribute("value","reset");
			newInput2.setAttribute("class","relierReset");
			divExtra.appendChild(newInput2);
			
			infoBottom.appendChild(divExtra);
			
			// on récupère l'accès aux éléments debug dans des variables
			btnReset = document.getElementById("reset"+ref);
			divCoordonnees = document.getElementById("coordonnees"+ref);
/*			
			// si le script debug n'est pas détecté on l'insère dynamiquement en attendant qu'il est chargé pour ajouter des événements
			if(!detectScript)
			{
				var newScript = document.createElement("script");
				newScript.setAttribute("src","script/debug.js");
				var headTag = document.getElementsByTagName("head")[0];
				headTag.appendChild(newScript);
				
				headTag.lastChild.addEventListener("load", loader = function() {
					headTag.lastChild.removeEventListener("load", loader);
					ptsDebug.loader("console", 15);
					
					canvas.addEventListener("mousemove", Locate);
					btnReset.addEventListener("click", function(){if(!msgBox) Reset();});
				});
			}
			else if(detectScript) */
			{
				//ptsDebug.loader("console", 15);
				ptsDebug = ptsDebug_create(ref, "console", 15);
				canvas.addEventListener("mousemove", Locate);
				btnReset.addEventListener("click", function(){if(!msgBox) Reset();});
			}
		}
		
		if(user) Interface("init");
			else NewInput("init");
	};
	
	
	// Dessine l'interface des points sur le canvas
	var Interface = function draw_Interface(info)
	{		
		for(var i = 1; i <= relierNbGauche; i++)
		{
			context.beginPath();
			context.arc(offsetPtX, (i-1) * intervalPtYA + margeYA, 5, 0, Math.PI * 2);
			context.fill();
			context.closePath();
		}
		
		for(var i = 1; i <= relierNbDroite; i++)
		{
			context.beginPath();
			context.arc(canvas.width - offsetPtX, (i-1) * intervalPtYB + margeYB, 5, 0, Math.PI * 2);
			context.fill();
			context.closePath();
		}
		
		if(info == "copy") // info reçue de la fonction NewInput() si on demande à editer une référence déjà existante
		{
			Save("backup"); // On sauvegarde l'interface de l'application et initialise les tableaux de backup
			
			// charge les tableaux de réponses, construction du tableau d'image en dessinant sur le canvas, calcul du coefficient directeur des tracés et création des tableaux de backup
			for(var i = 0; i < relierRepGauche.length; i++)
			{
				ptGauche.push(relierRepGauche[i]);
				ptDroite.push(relierRepDroite[i]);
				
				context.lineWidth = 2;
				context.beginPath();
				context.moveTo(offsetPtX, (ptGauche[i]-1) * intervalPtYA + margeYA);
				context.lineTo(canvas.width - offsetPtX, (ptDroite[i]-1) * intervalPtYB + margeYB);
				context.strokeStyle = color;
				context.stroke();
				context.closePath();
				
				Coefdir();
				Save("backup");
			}
		}
		
		if(info == "init")
			Save("backup");
	};
	
	// Localiser le clic de la souris sur le canvas
	var Locate = function draw_Locate(e)
	{
		var pageClickX = e.pageX;
		var pageClickY = e.pageY;
		
		canvasClickX = pageClickX - offsetCanvasX;
		canvasClickY = pageClickY - offsetCanvasY;
		
		if(dbg && !msgBox) divCoordonnees.value = canvasClickX + "; " + canvasClickY;
	};
	
	// Afficher une animation après le clic sur un point A
	var Animation = function draw_Animation(e)
	{
		Locate(e);
		Refresh();
		
		if(sens == "gauche")
			SelCircle(ptGauche[ptGauche.length - 1], false);
		else if(sens == "droite")
			SelCircle(ptDroite[ptDroite.length - 1], true);

		context.lineWidth = 2;
		context.beginPath();
		
		if(sens == "gauche")
			context.moveTo(offsetPtX, (ptGauche[ptGauche.length - 1]-1) * intervalPtYA + margeYA);
		else if(sens == "droite")
			context.moveTo(canvas.width - offsetPtX, (ptDroite[ptDroite.length - 1]-1) * intervalPtYB + margeYB);
		
		context.lineTo(canvasClickX, canvasClickY);
		context.strokeStyle = color;
		context.stroke();
		context.closePath();
	};
	
	// Rafraîchir le canvas
	var Refresh = function draw_Refresh(info)
	{
		context.clearRect(0, 0, canvas.width, canvas.height);
		context.drawImage(image, 0, 0);
	};
	
	// Sauvegarder l'image du canvas, paramètre "backup" pour effectuer une sauvegarde des tableaux
	var Save = function draw_Save(info)
	{
		if(info == "backup")
		{
			while(tabDataURL.length > coefdir.length) // nouveau backup donc on supprime toutes les anciennes sauvegardes disponibles
				tabDataURL.splice(0, 1); // splice : à la première position du tableau (0) on supprime une valeur (1)
			
			ptGaucheBak = ptGauche.slice(0); // slice : on copie les tableaux actuellement utilisés pour remplacer les anciens tableaux de backup
			ptDroiteBak = ptDroite.slice(0);
			coefdirBak = coefdir.slice(0);
			// tableau.slice(0) -> copie par valeur du début à la fin, différent de référence (tableau = tableau)
		}
		
		image.src = canvas.toDataURL();
		tabDataURL.push(image.src);
	};
	
	// Supprimer les derniers points si le tracé n'a pas aboutit
	var Fix = function draw_Fix()
	{
		if(dbg) ptsDebug.log("Tracé annulé");
		
		if(sens == "gauche")
			ptGauche.pop();
		else if(sens == "droite")
			ptDroite.pop();
	};
	
	//Restaurer des points en avant/arrière
	var Restore = function draw_Restore(info)
	{
		var restored = false;
		
		if((info == "back") && (ptGauche.length > 0) && (ptDroite.length > 0))
		{
			// Algorithme de tri des tableaux de backup pour la restauration en arrière
			ptGauche.pop(); // on supprime la dernière valeur du tableau de points gauche
			ptGaucheBak.splice(0, 0, ptGaucheBak[ptGaucheBak.length - 1]); // on ajoute la dernière valeur du tableau gauche de sauvegarde à la première position du tableau (position 0, sans supprimer de valeur: 0)
			ptGaucheBak.pop(); // on supprime la dernière position du tableau gauche de sauvegarde
			
			ptDroite.pop();
			ptDroiteBak.splice(0, 0, ptDroiteBak[ptDroiteBak.length - 1]);
			ptDroiteBak.pop();
			
			coefdir.pop();
			coefdirBak.splice(0, 0, coefdirBak[coefdirBak.length - 1]);
			coefdirBak.pop();
			
			tabDataURL.splice(0, 0, tabDataURL[tabDataURL.length - 1]);
			tabDataURL.pop();
			
			if(dbg) ptsDebug.log("Supprimé: pt gauche " + ptGaucheBak[0] + " et pt droite " + ptDroiteBak[0]);
			restored = true;
		}
		else if((info == "forward") && (ptGauche.length < ptGaucheBak.length) && (ptDroite.length < ptDroiteBak.length))
		{
			// Algorithme de tri des tableaux de backup pour la restauration en avant
			ptGauche.push(ptGaucheBak[0]); // on ajoute la première valeur du tableau de sauvegarde de points gauche au tableau de points gauche
			ptGaucheBak.push(ptGaucheBak[0]); // on ajoute la première valeur du tableau de sauvegarde de points gauche à la fin du tableau
			ptGaucheBak.splice(0, 1); // on supprime (1 valeur) la première valeur (position 0) du tableau de sauvegarde de points gauche
			
			ptDroite.push(ptDroiteBak[0]);
			ptDroiteBak.push(ptDroiteBak[0]);
			ptDroiteBak.splice(0, 1);
			
			coefdir.push(coefdirBak[0]);
			coefdirBak.push(coefdirBak[0]);
			coefdirBak.splice(0, 1);
			
			tabDataURL.push(tabDataURL[0]);
			tabDataURL.splice(0, 1);
			
			if(dbg) ptsDebug.log("Restauré: pt gauche " + ptGauche[ptGauche.length - 1] + " et pt droite " + ptDroite[ptDroite.length - 1]);
			restored = true;
		}
		
		if(restored)
		{
			image.src = tabDataURL[tabDataURL.length - 1]; // dessine les tracés à restaurer dans une image depuis le tableau de backup
			image.addEventListener("load", refresh = function() {
				image.removeEventListener("load", refresh);
				Refresh();
			});
			// Attendre que les données soient inscritent dans l'image avant son impression sur le contexte du canvas, inutile pour Chrome (Bug?)
		}
	};
	
	// Ajouter des champs dynamiquement en cliquant sur le dernier lors de la création ou édition d'un exercice
	var NewInput = function draw_NewInput(input)
	{
		if(input == "gauche" || input == "init")
		{
			// si il y a + de 4 noeuds à gauche (après initialisation), il y a forcémment un ancien bouton "supprimer" à enlever
			if(input == "gauche" && relierDivGauche.childNodes.length > 4) {
				var removeGauche = document.getElementById("removeGauche"+ref);
				relierDivGauche.removeChild(removeGauche);
			}
			
			relierDivGauche.appendChild(document.createElement("input"));
			relierDivGauche.lastChild.setAttribute("type", "text");
			
			if(relierDivGauche.childNodes.length > 4) {
				var btn = document.createElement("button");
				btn.setAttribute("id", "removeGauche"+ref);
				btn.setAttribute("class", "inputRemove");
				var img = document.createElement("img");
				img.setAttribute("src", pathImg+"remove_10x10.gif");
				btn.appendChild(img);
				relierDivGauche.appendChild(btn);
				document.getElementById("removeGauche").addEventListener("click", rmGauche = function(){if(!msgBox) DelInput("gauche")});
				
				relierDivGauche.appendChild(document.createElement("br"));
				
				relierDivGauche.childNodes[relierDivGauche.childNodes.length - 3].setAttribute("id", "Gauche"+ref+((relierDivGauche.childNodes.length - 1)/2));
				if(input == "gauche") relierDivGauche.childNodes[relierDivGauche.childNodes.length - 5].removeEventListener("focus", newGauche);
				document.getElementById("Gauche"+ref+((relierDivGauche.childNodes.length - 1)/2)).addEventListener("focus", newGauche = function(){if(!msgBox) NewInput("gauche");});
				
				relierNbGauche = (relierDivGauche.childNodes.length - 1)/2 - 1;
			}
			else {
				relierDivGauche.appendChild(document.createElement("br"));
				
				relierDivGauche.childNodes[relierDivGauche.childNodes.length - 2].setAttribute("id", "Gauche"+ref+((relierDivGauche.childNodes.length)/2));
				if(input == "gauche") relierDivGauche.childNodes[relierDivGauche.childNodes.length - 4].removeEventListener("focus", newGauche);
				document.getElementById("Gauche"+ref+((relierDivGauche.childNodes.length)/2)).addEventListener("focus", newGauche = function(){if(!msgBox) NewInput("gauche");});
				
				relierNbGauche = (relierDivGauche.childNodes.length)/2 - 1;
			}
		}
		if(input == "droite" || input == "init")
		{
			if(input == "droite" && relierDivDroite.childNodes.length > 4) {
				var removeDroite = document.getElementById("removeDroite");
				relierDivDroite.removeChild(removeDroite);
			}
			
			relierDivDroite.appendChild(document.createElement("input"));
			relierDivDroite.lastChild.setAttribute("type", "text");
			
			if(relierDivDroite.childNodes.length > 4) {
				var btn = document.createElement("button");
				btn.setAttribute("id", "removeDroite"+ref);
				btn.setAttribute("class", "inputRemove");
				var img = document.createElement("img");
				img.setAttribute("src", pathImg+"remove_10x10.gif");
				btn.appendChild(img);
				relierDivDroite.appendChild(btn);
				document.getElementById("removeDroite").addEventListener("click", rmDroite = function(){if(!msgBox) DelInput("droite")});
				
				relierDivDroite.appendChild(document.createElement("br"));
				
				relierDivDroite.childNodes[relierDivDroite.childNodes.length - 3].setAttribute("id", "Droite"+ref+((relierDivDroite.childNodes.length - 1)/2));
				if(input == "droite") relierDivDroite.childNodes[relierDivDroite.childNodes.length - 5].removeEventListener("focus", newDroite);
				document.getElementById("Droite"+ref+((relierDivDroite.childNodes.length - 1)/2)).addEventListener("focus", newDroite = function(){if(!msgBox) NewInput("droite");});
				
				relierNbDroite = (relierDivDroite.childNodes.length - 1)/2 - 1;
			}
			else {
				relierDivDroite.appendChild(document.createElement("br"));
				
				relierDivDroite.childNodes[relierDivDroite.childNodes.length - 2].setAttribute("id", "Droite"+ref+((relierDivDroite.childNodes.length)/2));
				if(input == "droite") relierDivDroite.childNodes[relierDivDroite.childNodes.length - 4].removeEventListener("focus", newDroite);
				document.getElementById("Droite"+ref+((relierDivDroite.childNodes.length)/2)).addEventListener("focus", newDroite = function(){if(!msgBox) NewInput("droite");});
				
				relierNbDroite = (relierDivDroite.childNodes.length)/2 - 1;
			}
		}
		
		nbMax = Math.max(relierNbGauche, relierNbDroite);
		// multi sélection autorisée si le nombre de points à droite et à gauche n'est pas le même
		multisel = relierNbGauche - relierNbDroite;

		// le redimensionnement du canvas efface directement le contexte
		canvas.height = (nbMax-1) * intervalPtY0 + margeY0 * 2;
		Reset("auto"); // reset nécessaire pour remettre à zéro les tableaux de points et d'images si on ajoute de nouveaux champs après avoir déjà dessiné
		if(input == "init" && !user && reference) Interface("copy");
		else Interface("init");
	}
	
	// Supprimer des champs en cliquant sur le bouton supprimer (croix rouge) lors de la création ou édition d'un exercice
	var DelInput = function draw_DelInput(input)
	{
		if(input == "gauche")
		{
			relierDivGauche.removeChild(relierDivGauche.childNodes[relierDivGauche.childNodes.length - 1]);
			relierDivGauche.removeChild(relierDivGauche.childNodes[relierDivGauche.childNodes.length - 1]);
			relierDivGauche.removeChild(relierDivGauche.childNodes[relierDivGauche.childNodes.length - 1]);
			
			if(relierDivGauche.childNodes.length > 4)
			{
				var btn = document.createElement("button");
				btn.setAttribute("id", "removeGauche"+ref);
				btn.setAttribute("class", "inputRemove");
				var img = document.createElement("img");
				img.setAttribute("src", pathImg+"remove_10x10.gif");
				btn.appendChild(img);
				relierDivGauche.insertBefore(btn, relierDivGauche.childNodes[relierDivGauche.childNodes.length - 1]);
				document.getElementById("removeGauche"+ref).addEventListener("click", rmGauche = function(){if(!msgBox) DelInput("gauche")});
				
				document.getElementById("Gauche"+ref+((relierDivGauche.childNodes.length - 1)/2)).addEventListener("focus", newGauche = function(){if(!msgBox) NewInput("gauche");});
				relierNbGauche = (relierDivGauche.childNodes.length - 1)/2 - 1;
			}
			else {
				document.getElementById("Gauche"+ref+((relierDivGauche.childNodes.length)/2)).addEventListener("focus", newGauche = function(){if(!msgBox) NewInput("gauche");});
				relierNbGauche = (relierDivGauche.childNodes.length)/2 - 1;
			}
		}
		else if (input == "droite")
		{
			relierDivDroite.removeChild(relierDivDroite.childNodes[relierDivDroite.childNodes.length - 1]);
			relierDivDroite.removeChild(relierDivDroite.childNodes[relierDivDroite.childNodes.length - 1]);
			relierDivDroite.removeChild(relierDivDroite.childNodes[relierDivDroite.childNodes.length - 1]);
			
			if(relierDivDroite.childNodes.length > 4)
			{
				var btn = document.createElement("button");
				btn.setAttribute("id", "removeDroite"+ref);
				btn.setAttribute("class", "inputRemove");
				var img = document.createElement("img");
					img.setAttribute("src", pathImg+"remove_10x10.gif");
				btn.appendChild(img);
				relierDivDroite.insertBefore(btn, relierDivDroite.childNodes[relierDivDroite.childNodes.length - 1]);
				document.getElementById("removeDroite").addEventListener("click", rmDroite = function(){if(!msgBox) DelInput("droite")});
				
				document.getElementById("Droite"+ref+((relierDivDroite.childNodes.length - 1)/2)).addEventListener("focus", newDroite = function(){if(!msgBox) NewInput("droite");});
				relierNbDroite = (relierDivDroite.childNodes.length - 1)/2 - 1;
			}
			else {
				document.getElementById("Droite"+ref+((relierDivDroite.childNodes.length)/2)).addEventListener("focus", newDroite = function(){if(!msgBox) NewInput("droite");});
				relierNbDroite = (relierDivDroite.childNodes.length)/2 - 1;
			}
		}
		
		nbMax = Math.max(relierNbGauche, relierNbDroite);
		// multi sélection autorisée si le nombre de points à droite et à gauche n'est pas le même
		multisel = relierNbGauche - relierNbDroite;
		
		canvas.height = (nbMax - 1) * intervalPtY0 + margeY0 * 2;
		Reset("auto");
		Interface("init");
	}
	
	// Permet de basculer entre le mode dessin (crayon) et correction (ciseaux)
	var SwitchDel = function draw_SwitchDel()
	{
		if(!del) // Si le mode dessin est activé, on passe au mode correction
		{
			del = true;
			
			// Curseur par défaut, ciseaux ouverts
			if (cursor) canvas.style.cursor = "url("+pathImg+"scissors_ready.gif)5 5, auto";
			btnDelete.firstChild.setAttribute("src", pathImg+"pen_red_ff0000.gif");
			
			canvas.removeEventListener("click", pointA);
			
			// On change le curseur de la souris lorsque le clic est enfoncé (ciseaux fermés)
			canvas.addEventListener("mousedown", cut = function(ev){
				if (cursor) {
						canvas.style.cursor = "url("+pathImg+"scissors_cut.gif)5 5, auto";
				}
				
				// On lance la fonction permettant de supprimer le tracé
				Delete(ev);
			});
			
			// Changer le curseur de la souris lorsque le clic est relâché (ciseaux ouverts)
			if (cursor) {
				canvas.addEventListener("mouseup", ready = function(){
						canvas.style.cursor = "url("+pathImg+"scissors_ready.gif)5 5, auto";
				});
			}
		}
		else
		{ // Si le mode correction est activé, on passe au mode dessin
			del = false;
			
			if (cursor) canvas.style.cursor = "url("+pathImg+"pen_red_ff0000.gif)0 20, auto";
			btnDelete.firstChild.setAttribute("src", pathImg+"scissors_ready.gif");
			
			canvas.removeEventListener("mousedown", cut);
			if (cursor) canvas.removeEventListener("mouseup", ready);
			
			// On lance la fonction permettant de relier les points
			canvas.addEventListener("click", pointA = function(e){if(!msgBox) PointA(e);});
		}
	}
	
	// Calculer le coefficient directeur du dernier tracé
	var Coefdir = function draw_Coefdir()
	{
		var a;
		var xA, yA, xB, yB, yHA, yHB;
		
		xA = 0; // l'abscisse du point de gauche est définie comme étant 0
		xB = (canvas.width - offsetPtX) - offsetPtX; // l'abscisse du point de droite est égale à l'abscisse du point droite sur le canvas, moins l'abscisse du point gauche sur le canvas
		
		// Ordonnées des deux points les plus en haut (y0 est en bas)
		yHA = canvas.height - margeY0 - margeYA;
		yHB = canvas.height - margeY0 - margeYB;
		// calcul des ordonnées des points gauche et droite
		yA = yHA - ((ptGauche[ptGauche.length - 1]-1) * intervalPtYA);
		yB = yHB - ((ptDroite[ptDroite.length - 1]-1) * intervalPtYB);
		
		a = (yB - yA)/(xB - xA); // calcul du coefficient directeur
		coefdir.push(a); // javascript est limité à une précision de 16 chiffres (si nombre à virgule infini, le 17ème chiffre est arrondi)
	};
	
	// localiser et supprimer un tracé depuis le clic de la souris
	var Delete = function draw_Delete(e)
	{
		Locate(e);
		
		if(dbg) ptsDebug.log("Cherche point sur tracé à supprimer" + " (" + canvasClickX + "; " + canvasClickY + ")");
		
		 // Pour supprimer des tracés uniquement si le clic se situe sur le tracé ou un point du tracé
		 // (clic pris en compte sur ce segment uniquement et pas sur toute la droite du tracé, rayon du point de 5 pixels pris en compte)
		if(canvasClickX >= offsetPtX - 5 && canvasClickX <= (canvas.width - offsetPtX) + 5)
		{
			var index, nb;
			
			nb = 0;
			
			// recalcul de x/y afin de pouvoir utiliser l'équation y = ax + b
			var y = (relierNbGauche-1) * intervalPtYA + margeYA - canvasClickY;
			var x = canvasClickX - offsetPtX;
			
			for(var i = 0; i < coefdir.length; i++)
			{
				var a = coefdir[i];
				// b est en fait l'ordonnée du point relié de gauche
				var b = (relierNbGauche-1) * intervalPtYA - (ptGauche[i]-1) * intervalPtYA;
				
				// localiser clic sur droite avec marge d'erreur de 2x5 pixels (2 côtés de la droite) sur axe x ou bien y
				// marge d'erreur minimum de 2x1 pixel obligatoire pour localiser clic car le coefficient directeur peut être un nombre à virgule (réel, théoriquement infini)
				if((y + 5 >= a * x + b && y - 5 <= a * x + b) || (y >= a * (x - 5) + b && y <= a * (x + 5) + b))
				{
					index = i;
					nb++;
				}
			}
			
			if(nb == 1) // n'effectue pas plusieurs suppressions si clic sur droites superposées
			{
				if(dbg) ptsDebug.log("Tracé supprimé : " + ptGauche[index] + "-" + ptDroite[index] + " (y = " + Math.round(coefdir[index] * 100) / 100 + " * x + " + ((relierNbGauche-1) * intervalPtYA - (ptGauche[index]-1) * intervalPtYA) + ")");
				
				ptGauche.splice(index, 1); // splice : suppression point gauche du tracé (1 élément du tableau supprimé à la position donnée "index")
				ptDroite.splice(index, 1);
				coefdir.splice(index, 1);
				
				index = index + (coefdirBak.length - coefdir.length - 1); // recalcul de l'index pour les tableaux de backup (-1 car les tableaux de backups n'ont pas encore eu de suppressions de points)
				
				// On respecte l'algorithme de tri des restaurations en avant et arrière
				ptGaucheBak.splice(0, 0, ptGaucheBak[index]); // point gauche du tracé supprimé ajouté au début du tableau si restauration ultérieure souhaitée
				ptGaucheBak.splice(index + 1, 1); // supprime le point gauche du tracé ayant été déplacé au début du tableau (index + 1 car on a ajouté une valeur au début du tableau)
				
				ptDroiteBak.splice(0, 0, ptDroiteBak[index]);
				ptDroiteBak.splice(index + 1, 1);
				
				coefdirBak.splice(0, 0, coefdirBak[index]);
				coefdirBak.splice(index + 1, 1);
				
				// slice : copie uniquement l'interface du canvas suivant sa position dans le tableau tabDataURL, les anciennes données sont remplacées
				tabDataURL = tabDataURL.slice(coefdirBak.length - coefdir.length - 1, coefdirBak.length - coefdir.length);
				
				// On charge l'interface sur le canvas
				image.src = tabDataURL[0];
				image.addEventListener("load", refresh = function()
				{ // Attente du chargement de l'image nécessaire pour la plupart des navigateurs (évite l'impression inachevée du contexte du canvas sur l'image avant nouvelle écriture)
					image.removeEventListener("load", refresh);
					
					Refresh();
					
					for(var i = 0; i < coefdir.length; i++) // reconstruit les tracés à zéro puis indexés dans tableau d'images
					{
						context.lineWidth = 2;
						context.beginPath();
						
						var j = 0;
						while (j < ptFaux.length && (ptGauche[i] != ptFaux[j] || ptDroite[i] != ptFaux[j + 1]))
							j += 2;
						
						context.moveTo(offsetPtX, (ptGauche[i]-1) * intervalPtYA + margeYA);
						context.lineTo(canvas.width - offsetPtX, (ptDroite[i]-1) * intervalPtYB + margeYB);
						
						j < ptFaux.length ? context.strokeStyle = colorError : context.strokeStyle = color;
						
						context.stroke();
						context.closePath();
						
						Save();
					}
					
					var cvs = document.createElement("canvas"); // création d'un canvas, contexte et image à la volée pour dessiner et enregistrer les tracés supprimés sans les afficher
					cvs.height = canvas.height;
					cvs.width = canvas.width;
					var ctx = cvs.getContext("2d");
					var img = document.createElement("img");
					
					img.src = tabDataURL[tabDataURL.length - 1]; // récupère l'image du canvas actuel avec le tracé supprimé
					img.addEventListener("load", dessin = function() // Attente de fin de chargement nécessaire pour réécrire sur l'image
					{
						img.removeEventListener("load", dessin);
						ctx.drawImage(img, 0, 0);
						
						for(var i = 0; tabDataURL.length <= coefdirBak.length; i++) // sauvegarde les anciens tracés par-dessus l'image actuelle si restauration souhaitée par la suite
						{ // "<=" car tabDataURL contient une valeur en plus que les autres tableaux: l'interface de départ
							ctx.lineWidth = 2;
							ctx.beginPath();
							
							var j = 0;
							while (j < ptFaux.length && (ptGaucheBak[i] != ptFaux[j] || ptDroiteBak[i] != ptFaux[j + 1]))
								j += 2;
							
							ctx.moveTo(offsetPtX, (ptGaucheBak[i]-1) * intervalPtYA + margeYA);
							ctx.lineTo(canvas.width - offsetPtX, (ptDroiteBak[i]-1) * intervalPtYB + margeYB);
							
							j < ptFaux.length ? ctx.strokeStyle = colorError : ctx.strokeStyle = color;
							
							ctx.stroke();
							ctx.closePath();
							
							// Equivalent méthode Save() mais pour l'image "img" au lieu de "image"
							img.src = cvs.toDataURL();
							tabDataURL.splice(i, 0, img.src); // les premières valeurs des tableaux de points doivent correspondre avec les premières valeurs des tableaux d'images (i)
						}
					});
				});
			}
			else if(nb > 1) {
				if(dbg) ptsDebug.log("Droites superposées, supprimez ailleurs");
			}
		}
	};

	// Dessin d'un point selectionné
	var SelCircle = function draw_SelCircle(i = 1, droite = false) {
		var x = droite ? canvas.width - offsetPtX : offsetPtX;
		var y = droite ? (i-1) * intervalPtYB + margeYB : (i-1) * intervalPtYA + margeYA;
		context.beginPath();
		context.strokeStyle = color;
		context.lineWidth = "2";
		context.arc(x, y, 7, 0, Math.PI * 2);
		context.stroke();
		context.closePath();
	}
	
	// Cibler et relier le point de départ
	var PointA = function draw_PointA(e)
	{
		Locate(e);
		
		if(dbg) ptsDebug.log("Cherche ptA" + " (" + canvasClickX + "; " + canvasClickY + ")");
		
		sens = null;
		
		for(var i = 1; i <= nbMax; i++)
		{
			if((canvasClickX - 10 <= offsetPtX) && (canvasClickX + 10 >= offsetPtX)
			&& (canvasClickY - 10 <= (i-1) * intervalPtYA + margeYA) && (canvasClickY + 10 >= (i-1) * intervalPtYA + margeYA)
			&& relierNbGauche >=  i) // si clic de départ du côté gauche (rayon point 5 pixels avec marge d'erreur de 5 pixels)
			{
				sens = "gauche";
				var clone = false;
				
				if(multisel>=0) { // multi sélection autorisée s'il y a moins de points à gauche
					for(var j = 0; j < ptGauche.length; j++) {
						if(ptGauche[j] == i)
							clone = true;
					}
				}
				
				ptGauche.push(i);
				
				if(clone)
				{
					if(dbg) ptsDebug.log("Déjà utilisé : pt gauche " + ptGauche[ptGauche.length - 1]);
					
					Fix();
					sens = null;
				}
				else if(!clone)
				{
					if(dbg) ptsDebug.log("ptA gauche " + ptGauche[ptGauche.length - 1] + " prêt");
					SelCircle(i, false);
					canvas.removeEventListener("click", pointA);
					canvas.addEventListener("click", pointB = function(e){if(!msgBox) PointB(e);});
					canvas.addEventListener("mousemove", animation = function(e){if(!msgBox) Animation(e);});
				}
				break;
			}
			else if((canvasClickX - 10 <= canvas.width - offsetPtX) && (canvasClickX + 10 >= canvas.width - offsetPtX)
			&& (canvasClickY - 10 <= (i-1) * intervalPtYB + margeYB) && (canvasClickY + 10 >= (i-1) * intervalPtYB + margeYB)
			&& relierNbDroite >= i) // si clic de départ du côté droit (rayon point 5 pixels avec marge d'erreur de 5 pixels)
			{
				sens = "droite";
				var clone = false;
				
				if(multisel<=0) { // multi sélection autorisée s'il y a moins de points à droite
					for(var j = 0; j < ptDroite.length; j++) {
						if(ptDroite[j] == i)
							clone = true;
					}
				}
				
				ptDroite.push(i);
				
				if(clone)
				{
					if(dbg) ptsDebug.log("Déjà utilisé : pt droite " + ptDroite[ptDroite.length - 1]);
					
					Fix();
					sens = null;
				}
				else if(!clone)
				{
					if(dbg) ptsDebug.log("ptA droite " + ptDroite[ptDroite.length - 1] + " prêt");

					SelCircle(i, true);
					canvas.removeEventListener("click", pointA);
					canvas.addEventListener("click", pointB = function(e){if(!msgBox) PointB(e);});
					canvas.addEventListener("mousemove", animation = function(e){if(!msgBox) Animation(e);});
				}
				break;
			}
		}
	};
	
	 // Cibler et relier le point d'arrivée
	var PointB = function draw_PointB(e)
	{
		Refresh(); // Effacer le dernier tracé de l'animation
		
		Locate(e);
		
		if(dbg) ptsDebug.log("Cherche ptB" + " (" + canvasClickX + "; " + canvasClickY + ")");
		
		var found = false;
		
		for(var i = 1; i <= nbMax; i++)
		{
			if((canvasClickX - 10 <= offsetPtX) && (canvasClickX + 10 >= offsetPtX)
			&& (canvasClickY - 10 <= (i-1) * intervalPtYA + margeYA) && (canvasClickY + 10 >= (i-1) * intervalPtYA + margeYA)
			&& relierNbGauche >= i && (sens == "droite")) // si clic du côté gauche après clic du côté droit (rayon point 5 pixels avec marge d'erreur de 5 pixels)
			{
				found = true;
				var clone = false;
				
				if(multisel>=0) { // multi sélection autorisée s'il y a moins de points à gauche
					for(var j = 0; j < ptGauche.length; j++) {
						if(ptGauche[j] == i)
							clone = true;
					}
				}
				
				if(clone)
				{
					if(dbg) ptsDebug.log("Erreur : pt gauche " + ptGauche[ptGauche.length - 1] + " déjà utilisé");
					
					Fix();
				}
				else if(!clone)
				{
					ptGauche.push(i); // ajouter point dans tableau avant de dessiner (données utilisées)
					if(dbg) ptsDebug.log("ptB gauche " + ptGauche[ptGauche.length - 1] + " relié");
					
					context.lineWidth = 2;
					context.beginPath();
					context.moveTo(canvas.width - offsetPtX, (ptDroite[ptDroite.length - 1]-1) * intervalPtYB + margeYB);
					context.lineTo(offsetPtX, (ptGauche[ptGauche.length - 1]-1) * intervalPtYA + margeYA);
					context.strokeStyle = color;
					context.stroke();
					context.closePath();
					
					Coefdir();
					Save("backup");
				}
				break;
			}
			else if((canvasClickX - 10 <= canvas.width - offsetPtX) && (canvasClickX + 10 >= canvas.width - offsetPtX)
			&& (canvasClickY - 10 <= (i-1) * intervalPtYB + margeYB) && (canvasClickY + 10 >= (i-1) * intervalPtYB + margeYB)
			&& relierNbDroite >= i && (sens == "gauche")) // si clic du côté droit après clic du côté gauche (rayon point 5 pixels avec marge d'erreur de 5 pixels)
			{
				found = true;
				var clone = false;
				
				if(multisel<=0) { // multi sélection autorisée s'il y a moins de points à droite
					for(var j = 0; j < ptDroite.length; j++) {
						if(ptDroite[j] == i)
							clone = true;
					}
				}
				
				if(clone)
				{
					if(dbg) ptsDebug.log("Erreur : pt droite " + ptDroite[ptDroite.length - 1] + " déjà utilisé");
					
					Fix();
				}
				else if(!clone)
				{
					ptDroite.push(i);
					if(dbg) ptsDebug.log("ptB droite " + ptDroite[ptDroite.length - 1] + " relié");
					
					context.lineWidth = 2;
					context.beginPath();
					context.moveTo(offsetPtX, (ptGauche[ptGauche.length - 1]-1) * intervalPtYA + margeYA);
					context.lineTo(canvas.width - offsetPtX, (ptDroite[ptDroite.length - 1]-1) * intervalPtYB + margeYB);
					context.strokeStyle = color;
					context.stroke();
					context.closePath();
					
					Coefdir();
					Save("backup");
					
					if (ptFaux.length > 0)
					{
						ptFaux[ptGauche[ptGauche.length - 1] * 2 - 2] = 0;
						ptFaux[ptGauche[ptGauche.length - 1] * 2 - 1] = 0;
					}
				}
				break;
			}
		}
		if(!found)
		{
			Fix();
		}
		
		sens = null;
		canvas.removeEventListener("click", pointB);
		canvas.removeEventListener("mousemove", animation);
		canvas.addEventListener("click", pointA = function(e){if(!msgBox) PointA(e);});
	};
	
	// Connexion au serveur
	var Connection = function draw_Connection(script, callback)
	{
		var champs = true;
		
		if(script == "insert")
		{
			if(document.getElementById("Reference").value == "")
				champs = false;
			for(var i = 1; i <= relierNbGauche; i++)
				if(document.getElementById("Gauche"+ref+i).value == "")
					champs = false;
			for(var i = 1; i <= relierNbDroite; i++)
				if(document.getElementById("Droite"+ref+i).value == "")
					champs = false;
		}
		
		var nbMaxARelier = toutRelier 
			? Math.max(relierNbGauche, relierNbDroite) 
			: Math.min(relierRepGauche.length, relierRepDroite.length);
		var nbMinDejaRelies = Math.min(ptGauche.length, ptDroite.length);
		var liaisonsManquantes = nbMaxARelier - nbMinDejaRelies;

		// if(((user || champs) && (ptGauche.length >= relierNbGauche && ptDroite.length >= relierNbDroite)) || script == "delete")
		if(((user || champs) && (liaisonsManquantes<=0)) || script == "delete")
		{
			if(correction != "js" || !user)
			{
					var xhr = null;
					
					if(window.XMLHttpRequest || window.ActiveXObject)
					{
						if(window.ActiveXObject) // Objets utilisant le contrôle ActiveX, utilisé dans Internet Explorer avant v7
						{
							try {
								xhr = new ActiveXObject("McanvasXml2.XMLHTTP"); 
							} catch(e) {
								xhr = new ActiveXObject("Microsoft.XMLHTTP");
							}
						}
						else {
							xhr = new XMLHttpRequest(); // Objet standard W3C pour communication AJAX
						}
					}
					else
					{
						alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest");
						return;
					}
					
					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4) // A la 4ème étape du traitement de l'objet XMLHttpRequest (réponse serveur), on étudie le résultat
						{
							if(xhr.status == 200 || xhr.status == 0) // Eviter les codes d'erreurs 404/500 (200: OK, 0: pas de réponse, pour tests en local)
								callback(script, xhr.responseText);
							else
								Message("Erreur HTTP " + xhr.status); // On affiche le code d'erreur
						}
					};
					
					switch(script)
					{
						case "correct":
						{
							xhr.open("POST", "script/ajax.php", true);
							xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							xhr.send("script=" + encodeURIComponent(script) + "&reference=" + encodeURIComponent(reference) + "&relierRepGauche=" + encodeURIComponent(JSON.stringify(ptGauche)) + "&relierRepDroite=" + encodeURIComponent(JSON.stringify(ptDroite)));
							// encodeURIComponent encode tous les caractères spéciaux du lien créé (permet d'envoyer le caractère '&' par exemple, sans qu'il soit prît pour un paramètre du lien (ex. &Droite)
							break;
						}
						case "insert":
						{
							var relierTexteGauche = [];
							var relierTexteDroite = [];
							
							var newReference = document.getElementById("Reference").value;
							
							for(var i = 1; i <= relierNbGauche; i++)
								relierTexteGauche.push(document.getElementById("Gauche"+ref+i).value);
							for(var i = 1; i <= relierNbDroite; i++)
								relierTexteDroite.push(document.getElementById("Droite"+ref+i).value);
							
							xhr.open("POST", "script/ajax.php", true);
							xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							xhr.send("script=" + encodeURIComponent(script) + "&reference=" + encodeURIComponent(newReference)
							+ "&motGauche=" + encodeURIComponent(JSON.stringify(relierTexteGauche))
							+ "&motDroite=" + encodeURIComponent(JSON.stringify(relierTexteDroite))
							+ "&relierRepGauche=" + encodeURIComponent(JSON.stringify(ptGauche))
							+ "&relierRepDroite=" + encodeURIComponent(JSON.stringify(ptDroite)));
							break;
						}
						case "delete":
						{
							if(reference) Message("Supprimer l'exercice <span class=\"ref\">" + htmlspecialchars(reference) + "</span> ?", 1);
							else Message("Votre exercice n'a pas encore été créé");
							
							document.getElementById("AlertYes"+ref).addEventListener("click", function() {
								alertContainer.removeChild(alertContainer.firstChild);
								xhr.open("POST", "script/ajax.php", true);
								xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								xhr.send("script=" + encodeURIComponent(script) + "&reference=" + encodeURIComponent(reference));
								msgBox = false;
							});
							document.getElementById("AlertNo"+ref).addEventListener("click", function() {
								alertContainer.removeChild(alertContainer.firstChild);
								msgBox = false;
							});
							break;
						}
					}
			}
			else // Correction Javascript
			{
				ptFaux = [];

				// reservoir que l'on va vider au fur et à mesure de la correction pour voir si des réponses manquent
				var copieUserG = ptGauche.slice(0);
				var copieUserD = ptDroite.slice(0);

				// retirer toutes les reponses justes
				for(var i = 0; i < relierRepGauche.length; i++)
					for(var j = copieUserG.length - 1; j>=0 ; j--)
						if(relierRepGauche[i] == copieUserG[j] && relierRepDroite[i] == copieUserD[j]) {
							copieUserG.splice(j, 1);
							copieUserD.splice(j, 1);
						}
				// sauver les réponses fausses
				for(var i = 0; i < copieUserG.length; i++) {
					ptFaux.push(copieUserG[i]);
					ptFaux.push(copieUserD[i]);
				}

				/*		
				for(var i = 0; i < ptGauche.length; i++) {
					for(var j = 0; j < relierRepGauche.length; j++) {
						if(relierRepGauche[j] == ptGauche[j])
						{
							if(relierRepDroite[i] != ptDroite[j])
							{
								ptFaux.push(ptGauche[j]);
								ptFaux.push(ptDroite[j]);
							}
							break;
						}
					}
				}
				/*
				for(var i = 0; i < relierRepGauche.length; i++) {
					for(var j = 0; j < ptGauche.length; j++) {
						if(relierRepGauche[i] == ptGauche[j])
						{
							if(relierRepDroite[i] != ptDroite[j])
							{
								ptFaux.push(ptGauche[j]);
								ptFaux.push(ptDroite[j]);
							}
							break;
						}
					}
				}*/
				
				callback();
			}
		}
		else if(!champs) {
			Message("Veuillez remplir tous les champs");
		}
		else {
			Message(toutRelier
				? "Il reste encore " + liaisonsManquantes + " liaison" + (liaisonsManquantes>1?"s":"") + " à trouver..."
				: "Il reste encore des points à relier..."
			);
		}
	};
	
	// Fonction de callback permettant de traiter les informations renvoyées par le serveur
	var ConnectionCallback = function draw_ConnectionCallback(script, data)
	{
		if(data == -1) { // si il y a eu une erreur de connexion à la base, on le signale
			Message("Erreur de connexion à la base");
		}
		else if(data == 1) {
			Message("Contenu indisponible");
		}
		else if(user) // callback utilisateur
		{
			if(correction != "js") // si correction php, on convertit les données JSON reçues en tableau
			{
				ptFaux = JSON.parse(data);
			}
			
			if(ptFaux.length >= 1) // message personnalisé si il y a des erreurs
			{
				var pluriel = ptFaux.length / 2 > 1 ? " erreurs" : " erreur";
				Message("Il reste " + ptFaux.length / 2 + pluriel + " à corriger");

				// On charge l'interface sur le canvas
				image.src = tabDataURL[0];
				image.addEventListener("load", refresh = function()
				{ // Attente du chargement de l'image nécessaire pour la plupart des navigateurs (évite l'impression inachevée du contexte du canvas sur l'image avant nouvelle écriture)
					image.removeEventListener("load", refresh);
					
					tabDataURL = [];
					Refresh();
					Save();
					
					for(var i = 0; i < coefdir.length; i++) // reconstruit les tracés à zéro puis indexés dans tableau d'images
					{
						context.lineWidth = 2;
						context.beginPath();
						
						var j = 0;
						while (j < ptFaux.length && (ptGauche[i] != ptFaux[j] || ptDroite[i] != ptFaux[j + 1]))
							j += 2;
						
						context.moveTo(offsetPtX, (ptGauche[i]-1) * intervalPtYA + margeYA);
						context.lineTo(canvas.width - offsetPtX, (ptDroite[i]-1) * intervalPtYB + margeYB);
						
						j < ptFaux.length ? context.strokeStyle = colorError : context.strokeStyle = color;
						
						context.stroke();
						context.closePath();
						
						Save();
					}
				});
			}
			else { // message s'il n'y a pas d'erreurs
				Message("Toutes les réponses sont correctes, bravo !");
				if(ptsCount) ptsCount.stop();
			}
		}
		else if(!user)  // callback administrateur
		{
			if(script == "delete")
				Message(data, 2);
			else
				Message(data);
		}
	};
	
	// Création d'une boîte de dialogue en HTML/CSS
	var Message = function draw_Message(msg, confirm)
	{
		msgBox = true;
		
		var newDiv = document.createElement("div");
		newDiv.setAttribute("id", "AlertBox"+ref);
		newDiv.setAttribute("class", "relierAlertBox");
		
		var newP = document.createElement("p");
		newP.innerHTML = msg;
		newDiv.appendChild(newP);
		
		if(confirm == 1) // si la MessageBox demande une confirmation
		{
			var newInput1 = document.createElement("input");
			newInput1.setAttribute("type", "button");
			newInput1.setAttribute("id", "AlertYes"+ref);
			newInput1.setAttribute("value", "Oui");
			newDiv.appendChild(newInput1);
			
			newDiv.appendChild(document.createTextNode("\u00a0\u00a0")); // &nbsp en Unicode: Non-breaking space (espace insécable, plusieurs à la suite possible)
			
			var newInput2 = document.createElement("input");
			newInput2.setAttribute("type", "button");
			newInput2.setAttribute("id", "AlertNo"+ref);
			newInput2.setAttribute("value", "Non");
			newDiv.appendChild(newInput2);
		}
		else { // sinon MessageBox d'avertissement
			var newInput = document.createElement("input");
			newInput.setAttribute("type", "button");
			newInput.setAttribute("id", "AlertButton"+ref);
			newInput.setAttribute("class", "AlertButton");
			newInput.setAttribute("value", "OK");
			newDiv.appendChild(newInput);
		}
		
		alertContainer.appendChild(newDiv);
		var style = "margin-top:" + Math.floor(canvas.height/2) + "px;";
		style += "margin-left:" + Math.floor(canvas.width/2 - Math.min(alertContainer.clientWidth,500)/2) + "px;";
		alertContainer.setAttribute("style", style);
		
		// On remet les événements pour continuer à utiliser le module
		
		if(!confirm || confirm == 2) // si MessageBox d'avertissement simple ou suite à une confirmation (redirection)
		{
			document.getElementById("AlertButton"+ref).addEventListener("click", function() {
				alertContainer.removeChild(alertContainer.firstChild);
				msgBox = false;
				if(confirm == 2) window.location = "./";
			}); // événement supprimé automatiquement lors de la suppression de la div
		}
	};
	
	// Effectue une remise à zéro de l'application (mode débug uniquement)
	var Reset = function draw_Reset(clear)
	{
		ptGauche = [];
		ptDroite = [];
		ptGaucheBak = [];
		ptDroiteBak = [];
		ptFaux = [];
		tabDataURL = [];
		coefdir = [];
		coefdirBak = [];
		sens = null;
		if(!clear) {
			context.clearRect(0, 0, canvas.width, canvas.height);
			Interface("init");
		}
		if(dbg && !clear) {
			divCoordonnees.value = "";
			ptsDebug.reset();
		}
		if(ptsCount) ptsCount.reset();
	};
}

// ================================================================================================


// HTML entities Encode/Decode

function htmlspecialchars(str) {
   return str.replace(/&/g, '&'+'amp;').replace(/</g, '&'+'lt;').replace(/>/g, '&'+'gt;').replace(/"/g, '&'+'quot;').replace(/'/g, '&'+'#39;'); // ' -> ' for XML only
}
function htmlspecialchars_decode(str) {
    return str.replace(/&amp\;/g, '&').replace(/&lt\;/g, '<').replace(/&gt\;/g, '>').replace(/&quot\;/g, '"').replace(/'\;/g, '\'');
}
function htmlentities(str) {
	var textarea = document.createElement("textarea");
	textarea.innerHTML = str;
	return textarea.innerHTML;
}
function htmlentities_decode(str) {
	var textarea = document.createElement("textarea");
	textarea.innerHTML = str;
	return textarea.value;
}


// ================================================================================================

/**
 * Browser-independent debug console
 * ptsDebug.log alternative to default console.log
 *
 * HTML textarea tag needed
 * Change loader parameters to fit your preferences
 *
 * Loader parameters, order to respect:
 *					  Console: textarea id (string),
 *					  maxLines: limit number of logs to display, null = no limit (int);
 *
 * Available methods: 	ptsDebug.loader(Console, maxLines)
 *							Variables to load when all DOM content is loaded,
 *						ptsDebug.log(msg)
 *							Log a message (string),
 *						ptsDebug.reset()
 *							Clear textarea;
 *
 * Version 1.0
 *
 * Copyright (c) 2012 Baptiste Hassenfratz
 * Licensed under MIT license http://opensource.org/licenses/MIT
 **/


var ptsDebugs = [];
var debugLoaders = [];

function ptsDebug_create(ref, Console = "console", maxLines = 15) {
	ref = Console + ref;
	ptsDebugs[ref] = new ptsDebug_();
	debugLoaders[ref] = ptsDebugs[ref].loader(ref, maxLines);
	return ptsDebugs[ref]
}

/*
var ptsDebug = new ptsDebug_();

// if script is already inserted on window load
(function() {
	// loader parameters
	var Console = "console"; // "+ ref" pour autoriser plusieurs id ?
	var maxLines = 15;
	
	if(window.addEventListener)
		window.addEventListener("load", debugLoaders[Console] = function(){ptsDebug.loader(Console, maxLines);});
	else if(window.attachEvent)
		window.attachEvent("onload", debugLoaders[Console] = function(){ptsDebug.loader(Console, maxLines);});
	else if(window.onload)
		window.onload = function(){ptsDebug.loader(Console, maxLines);};
})();
*/

function ptsDebug_()
{
	var Console;
	var maxLines;
	
	
	this.loader = function debug_loader(textarea, lines)
	{
		if(window.removeEventListener)
			window.removeEventListener("load", debugLoaders[textarea]);
		else if(window.detachEvent)
			window.detachEvent("onload", debugLoaders[textarea]);
		
		Console = document.getElementById(textarea);
		maxLines = lines;
	};
	
	var countLines = function debug_countLines() // count number of lines in textarea
	{
		var text = Console.value.replace(/\s+$/g,""); // regex: remove last \n of textarea content (replace with "")
		// replace(/\s+$/g) -> "\s" : whitespace character; "+" : one or more times; "$" : end of line; "/g" : global search (all matches)
		var split = text.split("\n");
		// cut string for each new line (\n)
		
		return split.length;
	};
	
	this.log = function debug_log(msg)
	{
		var lines = countLines();
		
		if(maxLines == "" || lines < maxLines)
			Console.value = msg + "\n" + Console.value;
		else if(maxLines != "" && lines >= maxLines)
			Console.value = msg + "\n" + Console.value.split("\n", maxLines - 1).join("\n");
	};
	
	this.reset = function debug_reset()
	{
		Console.value = "";
	};
}




/**
 * =============================================================================================
 * Customizable minutes/seconds counter and countdown
 * Start, Stop & Continue functions available
 *
 * HTML div tag needed
 * Change loader parameters to fit your preferences
 *
 * Loader parameters, order to respect:
 *					  myDiv: div id (string),
 *					  minText: minutes text (string),
 *					  secText: seconds text (string),
 *					  plural[Optional]: plural character (string),
 *					  sec01[Optional]: always display seconds with 2 digits (bool),
 *					  start[Optional]: start counter from specified seconds value, if negative -> countdown (int),
 *					  callback[Optional]: Callback function to call when countdown has finished (function);
 *
 * Available methods: 	ptsCount.loader(myDiv, minText, secText[, plural, sec01, start, callback])
 *							Variables to load when all DOM content is loaded,
 *						ptsCount.start(start)
 *							Continue count or start new count with arguments passed to function (int)
 *							Parameter value represents start time in seconds, use negative value for countdown
 *						ptsCount.stop()
 *							Stop/Pause timer,
 *						ptsCount.refresh()
 *							Refresh timer, useful if Firebug has broken timer (setInterval) after breakpoint,
 *						ptsCount.time()
 *							Returns minutes and seconds values in an array
 *						ptsCount.timeString()
 *							Returns time string as it is displayed
 *						ptsCount.reset()
 *							Restart timer;
 *
 * Changelog
 * 1.2 Countdown feature added, callback method (Suggestion: Alexandre Munch)
 * 1.1 User can personalize counter output (Suggestion: Alexandre Munch)
 * 1.0 Initial counter, API control
 *
 *
 * Version 1.2
 *
 * Copyright (c) 2012 Baptiste Hassenfratz
 * Licensed under MIT license http://opensource.org/licenses/MIT
 **/

var ptsLoaders = [];
var ptsCounts = [];

function ptsCount_create(myDivName = "count", minText = "minute", secText = "seconde", plural = "s", sec01 = true, start = 0, callback = false) {
	ptsCounts[myDivName] = new ptsCount_();
	
	if (callback==false) callback = function(){ alert("Do or display something"); };
	ptsLoaders[myDivName] = ptsCounts[myDivName].loader(myDivName, minText, secText, plural, sec01, start, callback);

	return ptsCounts[myDivName];
}

/*
var ptsCount = new ptsCount_();

// if script is already inserted on window load
(function() {
	// loader parameters
	var myDiv = "count";
	var minText = "minute";
	var secText = "seconde";
	var plural = "s";
	var sec01 = true;
	var start = 0;
	var callback = function(){ alert("Do or display something"); };
	
	if(window.addEventListener)
		window.addEventListener("load", ptsLoader = function(){ptsCount.loader(myDiv, minText, secText, plural, sec01, start, callback);});
	else if(window.attachEvent)
		window.attachEvent("onload", ptsLoader = function(){ptsCount.loader(myDiv, minText, secText, plural, sec01, start, callback);});
	else if(window.onload)
		window.onload = function(){ptsCount.loader(myDiv, minText, secText, plural, sec01, start, callback);};
})();
*/

function ptsCount_()
{
	var myDiv;
	var myDivName;
	var minText;
	var secText;
	var plural;
	var sec01;
	var down;
	var callback;
	var loopTime;
	var min;
	var sec;
	var time;
	
	
	this.loader = function count_loader(div, m, s)
	{
		if(window.removeEventListener)
			window.removeEventListener("load", ptsLoaders[div]);
		else if(window.detachEvent)
			window.detachEvent("onload", ptsLoaders[div]);
		
		myDivName = div;
		minText = m;
		secText = s;
		
		//Optional parameters
		plural = ""; // prevent undefined value to be displayed
		sec01 = false;
		var start = null;
		
		myDiv = document.getElementById(myDivName);
		
		if(!myDiv.childNodes.length)
			myDiv.appendChild(document.createTextNode(""));
		myDiv = myDiv.firstChild;
		
		try
		{
			if(arguments.length == 7)
			{
				if(typeof(arguments[5]) == "number" && typeof(arguments[6]) == "function")
				{
					start = arguments[5];
					callback = arguments[6];
					
					if(typeof(arguments[4]) == "boolean")
						sec01 = arguments[4]
					else throw 5;
					
					if(typeof(arguments[3]) == "string")
						plural = arguments[3]
					else throw 4;
				}
				else
				{
					if(typeof(arguments[6]) != "function")
						throw 7;
					else if(typeof(arguments[5]) != "number")
						throw 6;
				}
			}
			else if(arguments.length == 6)
			{
				if(typeof(arguments[4]) == "number" && typeof(arguments[5]) == "function")
				{
					start = arguments[4];
					callback = arguments[5];
					
					if(typeof(arguments[3]) == "boolean")
						sec01 = arguments[3]
					else if(typeof(arguments[3]) == "string")
						plural = arguments[3]
					else throw 4;
				}
				else
				{
					if(typeof(arguments[5]) == "number")
						start = arguments[5]
					else throw 6;
					
					if(typeof(arguments[4]) == "boolean")
						sec01 = arguments[4]
					else throw 5;
					
					if(typeof(arguments[3]) == "string")
						plural = arguments[3]
					else throw 4;
				}
			}
			else if(arguments.length == 5)
			{
				if(typeof(arguments[3]) == "number" && typeof(arguments[4]) == "function")
				{
					start = arguments[3];
					callback = arguments[4];
				}
				else
				{
					if(typeof(arguments[4]) == "number")
						start = arguments[4];
					else if(typeof(arguments[4]) == "boolean")
						sec01 = arguments[4]
					else throw 5;
					
					if(typeof(arguments[3]) == "boolean" && typeof(arguments[4]) != "boolean")
						sec01 = arguments[3];
					else if(typeof(arguments[3]) == "string")
						plural = arguments[3];
					else throw 4;
				}
			}
			else if(arguments.length == 4)
			{
				if(typeof(arguments[3]) == "number")
					start = arguments[3];
				else if(typeof(arguments[3]) == "boolean")
					sec01 = arguments[3];
				else if(typeof(arguments[3]) == "string")
					plural = arguments[3];
				else throw 4;
			}
			else if(arguments.length > 7) {
				throw "max";
			}
			else if(arguments.length < 3) {
				throw "min";
			}
		}
		catch(err)
		{
			if(typeof(err) == "number")
				console.log("Error: argument number " + err + " is not valid");
			else if(err == "max")
				console.log("Error: too much arguments");
			else if(err == "min")
				console.log("Error: not enough arguments");
			else
				console.log(err);
			return;
		}
		
		if(start)
			ptsCounts[myDivName].start(start);
		else
			ptsCounts[myDivName].start(0);
	};
	
	var timeCount = function count_timeCount(display)
	{
		if((sec < 59 && min == 0) || (down && ((sec <= 59 && min == 0) || (sec == 0 && min == 1))))
		{
			// Calc
			if(down)
			{
				if(sec > 0) {
					sec--;
				}
				else if(sec == 0 && min == 1) {
					min--;
					sec = 59;
				}
			}
			else {
				sec++;
			}
			
			// Display
			if(sec < 2)
			{
				if(sec == 0 && min == 0)
					ptsCounts[myDivName].stop();
				
				if(callback && sec == 0 && min == 0) {
					callback();
				}
				else {
					if(sec01 && sec < 10) time = "0" + sec + " " + secText;
					else time = sec + " " + secText;
				}
			}
			else {
				if(sec01 && sec < 10) time = "0" + sec + " " + secText + plural;
				else time = sec + " " + secText + plural;
			}
		}
		else
		{
			// Calc
			if(down) {
				if(sec > 0) {
					sec--;
				}
				else if(sec == 0) {
					min--;
					sec = 59;
				}
			}
			else {
				if(sec == 59)
				{
					sec = 0;
					min++;
				}
				else if(sec < 59) {
					sec++;
				}
			}
			
			
			// Display
			if(sec < 2 && min < 2)
			{
				if(sec01 && sec < 10) time = min + " " + minText + " 0" + sec + " " + secText;
				else time = min + " " + minText + " " + sec + " " + secText;
			}
			else if(sec >= 2 && min < 2)
			{
				if(sec01 && sec < 10) time = min + " " + minText + " 0" + sec + " " + secText + plural;
				else time = min + " " + minText + " " + sec + " " + secText + plural;
			}
			else if(sec < 2 && min >= 2)
			{
				if(sec01 && sec < 10) time = min + " " + minText + plural + " 0" + sec + " " + secText;
				else time = min + " " + minText + plural + " " + sec + " " + secText;
			}
			else if(sec >= 2 && min >= 2)
			{
				if(sec01 && sec < 10) time = min + " " + minText + plural + " 0" + sec + " " + secText + plural;
				else time = min + " " + minText + plural + " " + sec + " " + secText + plural;
			}
		}
		
		myDiv.nodeValue = time; // document.getElementById("count").textContent: DOM3 standard, IE9 ok
	};
	
	this.start = function count_start(start)
	{
		ptsCounts[myDivName].stop();
		
		if(typeof(start) != "undefined")
		{
			if(start < 0) {
				start = -start;
				down = start;
			}
			else {
				down = null;
			}
			
			if(start == 0)
			{
				min = 0;
				sec = 0;
			}
			else
			{
				min = 0;
				
				// for instant display via timeCount without waiting 1s
				if(down)
					start++; // if timer is a countdown, value incremented
				else
					start--; // else, value decremented
				
				
				if(start < 60) {
					sec = start;
				}
				else if(start > 60)
				{
					var totalSec = start;
					
					while(totalSec > 60) {
						totalSec -= 60;
						min++;
					}
					sec = totalSec;
				}
				
				timeCount();
			}
		}
		
		loopTime = setInterval(function(){timeCount()}, 1000); // Timer every second (1000 ms)
	};
	
	this.stop = function count_stop()
	{
		clearInterval(loopTime);
	};
	
	this.refresh = function count_refresh()
	{
		ptsCounts[myDivName].stop();
		ptsCounts[myDivName].start();
	};
	
	this.time = function count_time()
	{
		return [min, sec];
	};
	
	this.timeString = function count_timeString()
	{
		return time;
	}
	
	this.reset = function count_reset()
	{
		ptsCounts[myDivName].stop();
		if(down)
			ptsCounts[myDivName].start(-down);
		else
			ptsCounts[myDivName].start(0);
	};
}