var alpha=new Array();
var alpha_index=0;

var bravo=new Array();
var bravo_index=0;

var running=0;
var failnum=0;
var advising=0;

function pick()
{
  var choice="";
  var blank=0;
 
  for (i=0; i<words[index].length; i++)
  {
    t=0;
    for(j=0; j<=alpha_index; j++) 
    if(words[index].charAt(i)==alpha[j] || words[index].charAt(i)==alpha[j].toLowerCase()) t=1;
    
    if (t) choice+=words[index].charAt(i)+" ";
    else 
    {
      choice+="_ ";
      blank=1;
    }
  }   
    
  document.f.word.value=choice;
    
  if (!blank)
  {
    document.f.tried.value="   === GAGNE ! ===";
    document.f.score.value++;
    running=0;
  }
} 


function new_word(form)
{
  if(!running)
  {
    running=1;
    failnum=0;
    form.lives.value=failnum;
    form.tried.value="";
    form.word.value="";
    index=Math.round(Math.random()*10000) % 100;
    alpha[0]=words[index].charAt(0);
    alpha[1]=words[index].charAt(words[index].length-1);
    alpha_index=1;
    bravo[0]=words[index].charAt(0);
    bravo[1]=words[index].charAt(words[index].length-1);
    bravo_index=1;
    pick();
  }
  else advise("Le jeu est déjà commencé !");
}

function seek(lettre, jeu)
{
  if (!running) advise(".....Cliquez OK pour commencer !");
  else
  {
    t=0;
    for (i=0; i<=bravo_index; i++)
    {
      if (bravo[i]==lettre || bravo[i]==lettre.toLowerCase()) t=1;
    }

    if (!t) 
	  {
	    document.f.tried.value+=lettre+" "
	    bravo_index++;
	    bravo[bravo_index]=lettre;
	    
      for(i=0;i<words[index].length;i++)
   	  if(words[index].charAt(i)==lettre || words[index].charAt(i)==lettre.toLowerCase()) t=1;
  	  
      if(t)
      {
  	    alpha_index++;
	      alpha[alpha_index]=lettre;
	    }
	    else failnum++;
	
      document.f.lives.value=failnum;
	    if (failnum==6) 
      {
        document.f.tried.value="Perdu !!";
        document.f.word.value=words[index];
        document.f.score.value--;
        running=0;
      }
			else pick();
	  }
	  else advise("Lettre "+lettre+" déjà proposée!");
  }
}

function advise(msg)
{
  if (!advising)
  {
    advising=-1;
    savetext=document.f.tried.value;  
    document.f.tried.value=msg;
    window.setTimeout("document.f.tried.value=savetext; advising=0;",1000);
  }
}

var Mots = new Array();
//var Mots = new Array("","lacrimale","allegation","ameliorer","annihiler","antiseptique","articulation","authoritaire","cancerigene","chevaleresque","comprehensive","conclusion","considerer","denouement","determinante","elliptique","etranglement","extradition","fastidieux","flamboyant","gregaire","hypocrite","illustre","infaillible","obliterer","obsequieux","opalescent","ostensible","parapharmacie","pedestre","peremptoire","pernicieuse","perpetrer","pickpocket","precipitation","presomptueux","prevarication","pugnace","reciproquement","recrimination","redoutable","reprehensible","resolution","restitution","saccharine","insalubrite","transcripteur","soulignement","signatures");

// *****************************
/*
var NbMots=Mot.length; // Nb mots contenus dans la table Mot
var Tirage=Math.floor(Math.random()*NbMots); // Tirer aléatoirement un mot
var Cherche=Mot[Tirage]; // Stocker le mot tiré
//var Long=Cherche.length; // Calculer la longueur du mot tiré
var Chaine=Cherche.substr(0,1); // Créer la chaine à afficher
for(i=1;i<=(Long-2);i++) Chaine+="."; // en mettant des . au milieu
	Chaine+=Cherche.substr(Long-1,Long);
var Propos=""; // Lettres proposées
var NbErr=0; // Nombre d'erreurs
*/
//var NbMots=Mots.length; // Nb mots contenus dans la table Mot
//var Tirage=Math.floor(Math.random()*NbMots); // Tirer aléatoirement un mot
var Cherche=new Array(); //Mot[Tirage]; // Stocker le mot tiré
//var Long=new array(); //Cherche.length; // Calculer la longueur du mot tiré
var Chaine=new Array(); //=Cherche.substr(0,1); // Créer la chaine à afficher
//for(i=1;i<=(Long-2);i++) Chaine+="."; // en mettant des . au milieu
//	Chaine+=Cherche.substr(Long-1,Long);
var Propos=new Array(); //=""; // Lettres proposées
var NbErr=new Array(); //=0; // Nombre d'erreurs

// Pour afficher les lettres du mot, on va aerer avec des espaces
function pendu_aff_mot(jeu) {
	Tampon=""; Long=Cherche[jeu].length;
	for(i=0;i<Long;i++){Tampon=Tampon+" "+Chaine[jeu].substr(i,1);}
	document.forms['pendu'+jeu].cache.value=Tampon;
}

// Voir si la lettre existe dans le mot
function pendu_trouve(lettre, jeu) {
	Long=Cherche[jeu].length; // Longueur de la haine à trouver
	Propos[jeu]+=lettre; // La stocker dans les lettres proposées
	trv=0; // On considère au départ que la lettre n'est pas trouvée
	document.forms[jeu].jouees.value=Propos; // On affiche les lettres déjà jouées
	for (i=0;i<Long;i++) // Boucle de recherche de la lettre
	   {
	   if(Cherche[jeu].substr(i,1)==lettre){ //  Et prise en compte si trouvée
		  trv=1; // On a trouvé cette lettre
		  gauche=Chaine[jeu].substr(0,i); // On crée la chaine
		  droite=Chaine[jeu].substr(i+1,Long); // en y insérant
		  Chaine[jeu]=gauche+lettre+droite; // la lettre trouvée
		  }
	   }
	pendu_aff_mot(jeu); // On affiche le mot 
	//$('td.pendu_clavier').css('display', 'none');
	//document.forms[jeu].elements[lettre].style.display = 'none';
	document.forms[jeu].elements[lettre].disabled=true;
	document.forms[jeu].elements[lettre].style.color='graytext';
	document.forms[jeu].elements[lettre].blur();
	if(trv==0) NbErr[jeu]++; // Si la lettre n'a pas ete trouvee, +1 dans Erreurs
	if(NbErr[jeu]>0){document.pict.src="pendu"+NbErr[jeu]+".gif";} // Image à afficher
	if(NbErr[jeu]>5){ // A-t-il perdu ?
	   alert("Vous êtes pendu(e) !\nIl fallait trouver :\n"+Cherche);
	   location.reload(true);}
	if(Chaine[jeu]==Cherche[jeu]){alert("Bravo !");location.reload(true);} // A-t-il gagné ?
}