/* ce code s'inspire du travail de http://www.spip-contrib.net/_courcy_ 
  * il a été en grande partie ré-écrit pour : 
  * - générer une syntaxe de tableau SPIP et non HTML (l'éditeur de couleur de cellules disparaît donc aussi)
  * - pour permettre non seulement de créer un tableau mais aussi de modifier un tableau existant
  * - pour intégrer les éléments HTML titre et résumé de tableau 
  */
/******************************************
   * FONCTIONNEMENT Général 
 *********************BUT****************************************
  * Le but de ce script est de permettre à l'utilisateur de créer son propre 
   * tableau sans s'encombrer de la syntaxe SPIP. Si un tableau à syntaxe SPIP est 
   * sélectionné avant l'appel de l'assistant, les données  sont récupérées par l'assistant.
   * Si rien n'est sélectionné l'appel de l'assistant, un nouveau tableau est créé au point
   * d'insertion.   
   ************Structure de donnees ***********************************
   *  
   * Classe ihm qui gère l'interface homme-machine de l'assistant et sa construction
   * On trouve en autres les méthodes permettant d'insérer et de supprimer lignes et colonnes
   * 
   * Classe Cellule qui contient les informations relatives à une cellule
   * Ses attributs sont des informations relatives a une cellule  (contenu)
   * chaque objet cellule est contenu dans le tableau lc qui est un tableau bidimensionnel de cellules
   * ce tableau a autant d'éléments que de ligne * colonne dans le tableau 
   *
   * 
   *******************ALGORITHME*************************************
   * Le tableau est sauvé en mémoire dans la variable lc
   * La fonction construit_tableau( ) de la classe ihm construit l'interface pour manipuler les données du tableau
   * qui (re)initialisera la valeur innerHTML de <div id=tableau></div>
   * toute modification des valeurs dans l'assistant modifie lc
   * toute modification de l'interface (ajout/suppression de ligne/colonne)   modifie lc puis appelle la fonction construit_tableau 
   * simple mais efficace+
   *
   * La validation des modifications déclenche construit_code_tableau qui construit le code SPIP du tableau
   */
   
   
   /********INITIALISATION**************/ 
    //classe ihm : classe générique de gestion de tableau 
    function ihm() {
		this.titre_t = "";
		this.resume_t = "";
		/* méthodes */
		this.construit_tableau = construit_tableau;
		this.bouton_insere_ligne = bouton_insere_ligne;
		this.bouton_supprime_ligne = bouton_supprime_ligne;
		this.bouton_insere_colonne = bouton_insere_colonne;
		this.bouton_supprime_colonne = bouton_supprime_colonne;
		this.insere_commande_ligne = insere_commande_ligne;
		this.insere_commande_colonne = insere_commande_colonne;
		this.insere_ligne = insere_ligne;
		this.supprime_ligne = supprime_ligne;
		this.insere_colonne = insere_colonne;
		this.supprime_colonne = supprime_colonne;		

	    /*******CONStrUIT tableAU**********/
		function construit_tableau(){
			var texte = "";
			texte+="<table id='ihm' cellspacing='"+cellspacing+"'>\n";
			for (i=0;i<nl+2;i++){//on rajoute deux lignes en plus pour l'interface
				texte+="<tr>\n";
				for (j=0;j<nc+2;j++){//on rajoute deux lignes  en plus pour l'interface
					var position = "" ;
					if (i==0 && j==0)	position = "coin"; //on est dans le coin on met le menu
					if (i==0 && j>0)	position = "1st_l"; //on est sur la premiere ligne on insere des colonnes
					if (i>0 && j==0)	position = "1st_c"; //on est sur la premiere colonne on insere des lignes
					if (i==nl+1 && j>0) position = "last_l"; //on est dans la zone non éditée de la derniere ligne
					if (i>0 && j==nc+1) position = "last_c"; //on est dans la zone non éditée la derniere colonne
					switch (position) {
						case "coin" :	texte+="<td></td>\n"; break;
						case "1st_l" :	texte+="<td class='first_l'>"+this.insere_commande_colonne(j-1)+"</td>\n"; break;
						case "1st_c" :	texte+="<td class='first_c'>"+this.insere_commande_ligne(i-1)+"</td>\n"; break;
						case "last_l" :	texte+="<td class='last_l'></td>\n"; break;
						case "last_c" :	texte+="<td class='last_c'></td>\n"; break;
						default: //on est dans la partie éditable du tableau
							texte+="<td>"+ lc[i-1][j-1].insere() +"</td>\n";
					}
				}//fin du for j
				texte+="</tr>";
			}//fin du for i
			texte+="</table>";
			//win=window.open('');
			//win.document.write("<textarea cols='100' rows='50'>"+texte+"</textarea>");
			table.innerHTML=texte;//on modifie le texte de ce noeud
		}
		/**
		*cette fonction crée  un bouton  pour l'insertion d'une ligne
		*/
		function bouton_insere_ligne(ligne){
			var texte = "<a href='javascript:ihm_tableau.insere_ligne("+ligne+")'><img ";
			texte += " title='Insérer une ligne au-dessus' ";
			texte += " alt='Insérer une ligne au-dessus' ";
			texte += "src='inserer_ligne.gif' value=''></a>\n";
			return texte;
		}

		/**
		*cette fonction crée un bouton  pour la supression d'une ligne
		*/
		function bouton_supprime_ligne(ligne){
			if (nl == 1) return "";
			var texte="<a href='javascript:ihm_tableau.supprime_ligne("+ligne+")'><img  ";
			texte += " title='Supprimer cette ligne' ";
			texte += " alt='Supprimer cette ligne' ";
			texte += "src='supprimer_ligne.png' value='ligne' /></a>\n";
			return texte;
		}

		/**
		 *cette fonction crée un bouton  pour l'insertion d'une colonne
		 */
		function bouton_insere_colonne(colonne){	
			var texte="<a href='javascript:ihm_tableau.insere_colonne("+colonne+")'> <img ";
			texte+=" title='Insérer une colonne avant' ";
			texte+=" alt='Insérer une colonne avant' ";
			texte+="src='inserer_colonne.gif' value='colonne'></a>\n";	
			return texte;
		}
		/**
		 *cette fonction crée un bouton  pour la supression d'une colonne 
		 */
		function bouton_supprime_colonne(colonne){
			if (nc == 1) return "";
			var texte="<a href='javascript:ihm_tableau.supprime_colonne("+colonne+")'><img";
			texte+=" title='Supprimer cette colonne'";
			texte+=" alt='Supprimer cette colonne'";
			texte+=" src='supprimer_ligne.png' value='colonne' /></a>\n";
			return texte;
		}
		/**
		*cette fonction met en forme les commandes pour une ligne
		*/
		function insere_commande_ligne(ligne){	
			var texte;

			texte = this.bouton_insere_ligne(ligne);
			if (ligne<nl){
				 texte += this.bouton_supprime_ligne(ligne);
			}
			return texte;
		}

		/**
		 *cette fonction met en forme les commandes pour une colonne
		 */
		function insere_commande_colonne(colonne){
			var texte="";

			texte+= this.bouton_insere_colonne(colonne);
			if (colonne<nc){
				texte += this.bouton_supprime_colonne(colonne);
			}
			return texte;
		}
	
		/*********LES CALLBACKS depuis les boutons de l'IHM ****************************************/
		function insere_ligne(num_ligne){
			//création d'un tableau temporaire bidimensionnel de cellules
		    var lctemp = new tableau_cellules(nl+1, nc);
			
			lctemp.insL(num_ligne);		//insertion d'une ligne dans le tableau lc
			lc=lctemp.getT();			//on a une ligne en plus

			nl++;
			this.construit_tableau();
		}
	
		function supprime_ligne(num_ligne){
			//création d'un tableau temporaire bidimensionnel de cellule
		    var lctemp = new tableau_cellules(nl-1, nc);

			lctemp.supL(num_ligne);		//supression d'une ligne dans le tableau lc
			lc=lctemp.getT();			//on a une ligne en moins

			nl--;
			this.construit_tableau();
		}
		function insere_colonne(num_colonne){
			//création d'un tableau temporaire bidimensionnel de cellule
		    var lctemp = new tableau_cellules(nl, nc+1);

			lctemp.insC(num_colonne);	//insertion d'une colonne dans le tableau lc
			lc=lctemp.getT();

			nc++;						//on a une ligne en plus
			this.construit_tableau();
		}
		function supprime_colonne(num_colonne){
			//création d'un tableau temporaire bidimensionnel de cellules
		    var lctemp = new tableau_cellules(nl, nc-1);
	    				
			lctemp.supC(num_colonne);	//supression d'une colonne dans le tableau lc
			lc=lctemp.getT();			//on remplace le tableau lc par le tableau temp
			
			nc--;						//on a une colonne en moins
			this.construit_tableau();
		}
    }

    //classe cellule
    function cellule(i,j){
		this.l=i;
		this.c=j;
    	this.content='';

		/* les méthodes getters et setters */
		this.setC = function(c){this.c = c}
		this.getC = function(){return this.c}
		this.setL = function(l){this.l = l}
		this.getL = function(){return this.l}

		/* les méthodes  de la classe cellule */
		this.insere = insere;

		/* cette fonction insere une zone de texte dans la cellule */
		function insere(){

			var texte ="<textarea cols='10' rows='2'";	
			texte+=" onfocus='this.cols=25; this.rows=3;'";
			//on remet aux dimensions et on sauve le contenu
			texte+=" onchange='lc[" + this.l + "][" + this.c + "].content=this.value;'";			
			texte+=" onblur='this.cols=10; this.rows=2;'>";
			texte+=this.content;			
			texte+="</textarea> \n";	
			return texte;
		}
    } //fin de la classe "cellule"
	
	function tableau_cellules(nb_l,nb_c){
		this.t = new Array();
   		for (var i=0; i<nb_l; i++){
    	 	this.t[i]=new Array(); 
    	 	for (var j=0; j<nb_c; j++){
    	 		this.t[i][j]=new cellule(i,j);
    		}		    	
   		}

   		this.getT = function(){return this.t}

		this.insL = function(num_ligne) {
			//insertion d'une ligne dans le tableau lc
			for (var i=0;i<nl+1;i++){
				for (var j=0;j<nc;j++){
					if (i<num_ligne){
						//recopiage simple
						this.t[i][j]=lc[i][j];
					}
					if (i>num_ligne){
						//on incrémente de 1
						this.t[i][j]=lc[i-1][j];
						this.t[i][j].setL(i); 
					}
				}
			}
		}
		
		this.supL = function(num_ligne){
			for (i=0;i<nl-1;i++){
				for (j=0;j<nc;j++){
					if (i<num_ligne){
						//recopiage simple
						this.t[i][j]=lc[i][j];
					}else{
						this.t[i][j]=lc[i+1][j];
						this.t[i][j].setL(i); 
					}
				}
			}
		
		}
		
		this.insC = function(num_colonne){
			for (i=0;i<nl;i++){
				for (j=0;j<nc+1;j++){
					if (j<num_colonne){
						//recopiage simple
						this.t[i][j]=lc[i][j];
					}
					if (j>num_colonne){
						//on incrémente de 1
						this.t[i][j]=lc[i][j-1];
						this.t[i][j].setC(j);
					}
				}
			}
		}

		this.supC = function(num_colonne){
			for (i=0;i<nl;i++){
				for (j=0;j<nc-1;j++){
					if (j<num_colonne){
						//recopiage simple
						this.t[i][j]=lc[i][j];
					}else{
						this.t[i][j]=lc[i][j+1];
						this.t[i][j].setC(j);						
					}
				}
			}
		}

   	}
	
	function selection(zone){
		this.s1 = "";
		this.s2 = "";
		this.s3 = "";
		this.t = new Array(); // le tableau des valeurs à modifier
		this.premiere_ligne = "";

		this.avec_entete = avec_entete;
		this.recup_caption = recup_caption;
		this.recup_summary = recup_summary;
		this.compte_lignes = compte_lignes;
		this.recup_ligne = recup_ligne;
		this.recup_cellule = recup_cellule;
		this.contenu = contenu;
		this.existe = existe;
		
		if ((clientVer >= 4) && is_ie && is_win)
		{
			var theSelection = false;

 			theSelection = top.opener.document.selection.createRange().text; // Get text selection
			if (theSelection) {
				this.s2 = theSelection;
			} else {
				this.s1 = top.opener.document.getElementById("text_area").value;
			}
		}
		else {
			var selLength = zone.textLength;
			var selStart = zone.selectionStart;
			var selEnd = zone.selectionEnd;

			if (selEnd == 1 || selEnd == 2) selEnd = selLength;
	
			// Raccourcir la selection par double-clic si dernier caractere est espace	
			if (selEnd - selStart > 0 && (zone.value).substring(selEnd-1,selEnd) == ' ') selEnd = selEnd-1;
			this.s1 = (zone.value).substring(0,selStart);
			this.s2 = (zone.value).substring(selStart, selEnd)
			this.s3 = (zone.value).substring(selEnd, selLength);
		}
		this.premiere_ligne = this.s2.split("\n")[0];
		this.contenu();


		function avec_entete(){
			return (this.premiere_ligne.search(/^\|\|/) != -1); // si double pipe en tête de la première ligne(resumé)
		}
		function recup_caption(){
			return this.premiere_ligne.match(/^\|\|([^\|]*)/)[1];
		}
		function recup_summary(){
			return this.premiere_ligne.match(/^\|\|([^\|])*\|([^\|]*)/)[2];
		}
		function compte_lignes(){
			var sel = this.s2.split("|\n");
			return (this.avec_entete() ? sel.length-1 : sel.length);
		}
		function recup_ligne(num_ligne){
			var ligne = this.s2.split("|\n")[num_ligne] + "|";
			return (ligne.split("|"));
		}
		function contenu(){
			var ligne_data = (this.avec_entete() ? 1 : 0);
			for (var i=0; i<this.compte_lignes();i++){
				this.t[i] = this.recup_ligne(i+ligne_data); //t est un tableau bidimensionnel avec les valeurs à modifier
			}
		}
		function recup_cellule(i,j){
			return (this.t[i][j+1] ? this.t[i][j+1] : "");
		}
		function existe() {return (this.s2!="")} //indique si un tableau SPIP a été sélectionné
	}
	var ancien_tableau;

    //nombre de lignes et nombre de colonnes
    var nl;
    var nc;

    //création d'un tableau bidimensionnel de cellules
    var lc=new Array();

    var cellspacing=0;

    //noeud ou l'on va ecrire le tableau
    var table;
    var debug;
	
	var ihm_tableau = new ihm();
	
	function init(){
		ancien_tableau = new selection(top.opener.zone_selection);
	
		if (ancien_tableau.existe() & ancien_tableau.avec_entete()) {
			document.getElementById("titre_t").value = ancien_tableau.recup_caption() ;	//récupération du titre du tableau
			document.getElementById("resume_t").value = ancien_tableau.recup_summary() ;//récupération du résumé du tableau
		}

		nl= (ancien_tableau.existe()) ? ancien_tableau.compte_lignes() : 3;
		nc= (ancien_tableau.existe()) ? ancien_tableau.t[1].length - 2 : 3;

		for (i=0;i<nl;i++){
    	  lc[i]=new Array(); //2 lignes au depart
    	   for (j=0;j<nc;j++){
    	 	 lc[i][j]=new cellule(i,j);
    	 	 if (ancien_tableau.existe()) lc[i][j].content = ancien_tableau.recup_cellule(i,j);
    	    }		    	
        }
		
		table=document.getElementById("table");
		debug=document.getElementById("debug");

		/*********CONStrUCTION DE L'INTERFACE UTILISATEUR**********************/
		ihm_tableau.construit_tableau();
	}
    function d(s){debug.innerHTML+=s;}
    
    /****génération du code SPIP du tableau ******/
    
    function construit_code_tableau(){
    	var le_titre = document.getElementById("titre_t").value;
		var le_resume = document.getElementById("resume_t").value;
    	var texte="";
    	var la_ligne="";

		texte += "||" + le_titre + "|" + le_resume + "||\n";
		for (var j=0;j<nc;j++){
			//on supprime les accolades éventuelles avant de placer les accolades d'entete de tableau
			var cont_cell = lc[0][j].content.replace(/(\{)*([^\}]*)(\})*/,"$2");
			la_ligne += "|{{" + (cont_cell=="" ? " " : cont_cell) + "}}";
		}
		texte += la_ligne + "|\n";

		for (i=1;i<nl;i++){
			for (j=0;j<nc;j++) {texte += "|" + lc[i][j].content;}
			texte += "|\n";
		}//fin du for i
		return texte;	
    }
	
    /**********LES FONCTIONS DE CREATION DE L'INTERFACE**********/

	function enregistre(){
		if (ancien_tableau.existe()) {
			if ((clientVer >= 4) && is_ie && is_win) {
				top.opener.document.selection.createRange().text = construit_code_tableau();
			} else {
				top.opener.zone_selection.value = ancien_tableau.s1 + construit_code_tableau() + ancien_tableau.s3;
			}
		} else { //insertion d'un nouveau tableau
			if (top.opener.zone_selection.createTextRange && top.opener.zone_selection.caretPos) { //IE
				var caretPos = top.opener.zone_selection.caretPos;
				caretPos.text = caretPos.text + construit_code_tableau();
				top.opener.zone_selection.focus();
			} else {
				top.opener.zone_selection.value = ancien_tableau.s1 + construit_code_tableau() + ancien_tableau.s3;
			}
		}
		window.close();		
	}