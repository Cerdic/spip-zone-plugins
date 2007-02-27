//JavaScript Document for walma spip 1.9 
//A mettre dans le dossier de squelettes de votre site spip, ne pas renommer
//version walma3.3  pour spip 1.9 CopID libre non marchand (c) 28 juillet 2006 Alm & Walk Galerie WALMA
//TODO
//ajouter vitesse de défilement diaporama
//diaporama fondu images
 
var delay = 2 * 1000;
rangencours = 0; 
timer= null; 

function play() { timer=setInterval("timediapowalma()", delay);  }
function stopdiapowalma(){ clearInterval(timer);   }

function timediapowalma() { 
				var bigpictures = document.getElementById("lesphotos");
				var bigpictdivs = bigpictures.getElementsByTagName("div");
				var nbphotos= bigpictdivs.length;
				var txtcompteurcentre = document.getElementById('compteurcentreref'); 
	
for (var i=0; i<bigpictdivs.length; i++){
			   bigpictdivs[i].className = "hidden" ;
				  }
				  
	if(rangencours >= nbphotos){
	var imagetoview=bigpictdivs[0].id;
	rangencours = 0; 
	}else{
	var imagetoview=bigpictdivs[rangencours].id;
	}
	
	var suivante = document.getElementById(imagetoview);
	
	suivante.className="voir";			
	
	affichecompteur = rangencours+1;
 // alert("rangencours = "+rangencours+"");
 txtcompteurcentre.innerHTML=affichecompteur+" / "+bigpictdivs.length;  
 

	if (rangencours >= nbphotos-1){
	rangencours = 0; 
	} else {
	rangencours = rangencours + 1;	
	}

 return false;   
}

							
function walma(){
BYID = (document.getElementById)
	if (!BYID) return;
	else { 		
				var bigpictures = document.getElementById("lesphotos");
				var bigpictdivs = bigpictures.getElementsByTagName("div");
				var nbphotos= bigpictdivs.length;
				if (nbphotos<=1){ return; } 
				
			    var galerie_walma = document.getElementById('galeriewalma');
				var aliens =  galerie_walma.getElementsByTagName('a') ;
				var vignspetitdiv = document.getElementById('petitdiv');
				var ulvignette =  vignspetitdiv.getElementsByTagName('ul') ;
				var livignette =  vignspetitdiv.getElementsByTagName('li') ;
				
				var compteurw = document.getElementById('compteurwalma');
				var compteurdevoile = document.getElementById('cachecompteur');
				var nofleches = document.getElementById('nofleches');
  
				var txtcompteurcentre = document.getElementById('compteurcentreref'); 
				var flechebackref = document.getElementById('backref');
				var flechegoref = document.getElementById('goref');
				
				var totalvigns = parseInt(livignette.length); //total des vignettes de la galerie
 				var totalblocs = parseInt(ulvignette.length); //total des blocs de la galerie
				var totalvignsfirstbloc = ulvignette[0].getElementsByTagName('li').length; //total des vignettes du 1er bloc
				
				var iconemosaic = document.getElementById('ico1');
				var iconegauche = document.getElementById('ico2');
				var iconedroite = document.getElementById('ico3');
				var iconeimage = document.getElementById('ico4');
				var iconediapo = document.getElementById('ico5');
				
				//reset de toutes les icones
						function reseticones(){
						iconemosaic.className = "ico icomosaiclic";
						iconegauche.className = "ico icogauche"; 
						iconedroite.className = "ico icodroite"; 
						iconeimage.className = "ico icoimage"; 
						iconediapo.className = "ico icoimage";
						} 
				
		for(var i = 0 ; i < aliens.length ; i++){ // pour tous les liens
				 aliens[i].onclick = function(e){
									var targ //merci to http://www.w3schools.com
									if (!e) var e = window.event
									if (e.target) targ = e.target
									else if (e.srcElement) targ = e.srcElement
									if (targ.nodeType == 3) // defeat Safari bug
									   targ = targ.parentNode
									var idname //merci http://weblogs.asp.net/bleroy/archive/2005/02/15/373815.aspx
									idname=targ.parentNode.parentNode.id //retourne l'id sur lequel on clique
								  //alert("You clicked on " + idname) 
								 
													str_array=this.href.split('/'); 
													str=""+str_array[str_array.length-1];
													idoc=str.substring(str.lastIndexOf("#")+1);
										//alert("You ask for " + idoc);
										
													//recup url avant #
													lienfirst = this.href;  
													retire = parseInt(lienfirst.length)-parseInt(idoc.length); 
													lienclean = lienfirst.substring(0,retire); 
													// alert ("lienclean"+lienclean+""); 
										
										/*
										if (idname){ 
												var surid = document.getElementById(idname); //on recupere l'element html sur lequel on clic
													}
										*/
												var show = document.getElementById(idoc); //on change la valeur du div id=idoc88 en récupérant le ahref
												var tab = idoc.split(''); 
												firstletter= tab[0]; //*d*ocxx ou *b*locxx ou *p*opxx ...etc
											//  alert("firstletter is " + firstletter);
												 
						//reset du compteur
						function construitcompteur(blocnumber){
													blocnbdebut = blocnumber+1; // n° rang 1er image du bloc 
												// alert("totalvignsfirstbloc" +totalvignsfirstbloc);
													nbredoublevign = blocnumber + totalvignsfirstbloc; 
													nbreblocsuivant = blocnumber + totalvignsfirstbloc;
													if (nbredoublevign > totalvigns){ //si on dépasse le total de la galerie 
													nblastbloc = show.getElementsByTagName('li').length; //on est au dernier bloc
													nbredoublevign = blocnumber + nblastbloc; //on affiche le nb images qui restent
													nbreblocsuivant = 0; //et on revient au debut de la galerie
													} 
													nbreblocprecedent = blocnumber - totalvignsfirstbloc;
													if (nbreblocprecedent < 0){ //alors on est au début de la galerie
													nbreblocprecedent = (totalblocs*totalvignsfirstbloc)-totalvignsfirstbloc; //on va au dernier bloc
													}
												flechebackref.className = "voir";
												flechegoref.className = "voir";
												flechebackref.setAttribute("href", lienclean+"bloc"+nbreblocprecedent );
												flechegoref.setAttribute("href", lienclean+"bloc"+nbreblocsuivant);
												txtcompteurcentre.setAttribute("href",lienclean+"bloc0");
												affiche="[ "+blocnbdebut+"-"+ nbredoublevign +" ] / "+totalvigns;
												txtcompteurcentre.innerHTML=affiche; 
						 }
						 
						 
  
if(firstletter){   
		if(timer != null){ stopdiapowalma(); }
		}
		
		/******* mode image diapo ********/ 											
			if ((firstletter=='i')||(firstletter=='p')||(firstletter=='c')||(firstletter=='z')) { 
			
			 //alert("bigpictdivs.length nombre de photos "+bigpictdivs.length+"");
			//alert('mode image - precedente - suivante - diaporama demandé ');  
			//on garde le compteur, on cache les vignette 
				vignspetitdiv.className = "voircentrer";
				for (var i=0; i<ulvignette.length; i++){
					ulvignette[i].className ='hidden';
				}
				bigpictures.className = "voircentrer";
				reseticones();
				iconeimage.setAttribute("class", "ico icoselectimage");
				iconediapo.setAttribute("class","ico icoselectdiapoimage");
				if (document.all) {
				  iconeimage.setAttribute("className","ico icoselectimage");
				  iconediapo.setAttribute("className","ico icoselectdiapoimage");
				}
				 
		//scan all div of photo looking for class=voir while classname!=voir
		//look for a div in all nextsibling merci à http://www.brainjar.com/dhtml/domviewer/
		imagevisible="yes"; 
		//function nextandback(){
			for (var i=0; i<bigpictdivs.length; i++){
			   if(bigpictdivs[i].className == "hidden voir" ||  bigpictdivs[i].className == "voir")
				  {  rangencours = [i];
					 encours = bigpictdivs[i].id; //recup div de la photo visible actuellement
					 if (i<1){ //alert("premier div");
					 iddivprecedent = bigpictdivs[nbphotos-1].id;
					 iddivsuivant = bigpictdivs[1].id;
					 }else{ 
					 if (i==nbphotos-1){//alert("on est à la fin");
					 iddivprecedent = bigpictdivs[nbphotos-2].id;
					 iddivsuivant = bigpictdivs[0].id;
						 }else{// alert("autres div");
						 iddivprecedent = bigpictdivs[i-1].id;
						 iddivsuivant = bigpictdivs[i+1].id;
						 }
					 } 
					 imagevisible="change"; //alert(imagevisible);
					 break;
				  } 
			 }
		//}
		//nextandback();
			 
			// alert ("div iddivsuivant et rangencours"+iddivsuivant+"--"+rangencours+""); 
			
				var idfirstphoto = bigpictdivs[0].id;
				var firstphoto = document.getElementById(idfirstphoto);
				var encourshide = document.getElementById(encours);
				var suivante = document.getElementById(iddivsuivant);
				var precedent = document.getElementById(iddivprecedent);
				
				rangencours = parseInt(rangencours)+1; 
				flechebackref.className = "voir";
				flechegoref.className = "voir";
				flechebackref.setAttribute("href",lienclean+"preced");
				flechegoref.setAttribute("href",lienclean+"image"); 
				txtcompteurcentre.setAttribute("href",lienclean+"centre"); 
				
		if(compteurdevoile){ //si il n'y a pas le compte de vignettes il faut montrer le div caché...
				compteurdevoile.className = "voir";
				nofleches.className = "hidden";
				} 
				
		if (idname=="compteurwalma"){ //lien au centre compteur
			rangencours = 1;
			encourshide.className = "hidden"; //on cache la photo visible actuellement
			firstphoto.className = "voir";//on revient à la première
			}		

 
			if(firstletter=='z'){ 
					play();
			//diaporama quand ça démarre? icoselectdiapoimage
					iconeimage.setAttribute("class", "ico icoselectdiapo");
					iconediapo.setAttribute("class", "ico icoselectimage");
				    if (document.all) {
				    iconeimage.setAttribute("className","ico icoselectdiapo");
					iconediapo.setAttribute("class", "ico icoselectimage");
					}
 					
					}
					
		//compteur au dessus de l'image
		if ((idname!="galeriewalma")&(idname!="compteurwalma")){ //si pas icone du menu on avançe!
					encourshide.className = "hidden"; //on cache la photo visible actuellement 
					
					if(firstletter=='p'){ 		//lien precedent <
					//bigpictdivs.reverse();
					precedent.className="voir"; 
							if (rangencours==1){
								rangencours = totalvigns;
							}else{rangencours = rangencours-1;}
						}else{					//alors lien suivant >
					suivante.className="voir"; 
							if (rangencours==totalvigns){
								rangencours = 1;
							}else{rangencours = rangencours+1;}
					}
		} else { 
			if (imagevisible=="yes"){ 
					for (var i=0; i<bigpictdivs.length; i++){
					bigpictdivs[i].className = "hidden"; //on cache les divs des photos
								 }
				rangencours = 1; 
				firstphoto.className = "voir";//on revient à la première
				
				}
			 
			
			}
		 
	txtcompteurcentre.innerHTML=rangencours+" / "+totalvigns; 
	return false; //on stop le lien
	}

/******* mode droite ou gauche ********/
			if ((firstletter=='r')||(firstletter=='l')) {
				//on fait disparaitre images et blocs
				for (var i=0; i<bigpictdivs.length; i++){
					bigpictdivs[i].className = "hidden";
					}
				for (var i=0; i<ulvignette.length; i++){
					ulvignette[i].className ='hidden';
					}
				
				ulvignette[0].className ='voir';  
				bigpictdivs[0].className ='voir';
				reseticones(); 
				construitcompteur(0);
			}
 
/******* mode droite ********/
			 if (firstletter=='r') {//alert("mode droite demande");
				 vignspetitdiv.className = "droitevignettes";
				 bigpictures.className = "droitebigimage";   
				 iconedroite.setAttribute("class", "ico icoselectdroite");
				 if (document.all) {
				 iconedroite.setAttribute("className","ico icoselectdroite");
				 }
				 return false; //on stop le lien
			 }		
			 		 
/******* mode gauche ********/
			 if (firstletter=='l') {//alert("mode gauche demande");
				 vignspetitdiv.className = "gauchevignettes";
				 bigpictures.className = "gauchebigimage"; 
				 iconegauche.setAttribute("class", "ico icoselectgauche");
				 if (document.all) {
				 iconegauche.setAttribute("className","ico icoselectgauche");
				 }
				 return false; //on stop le lien
			 }			

/******* mode mosaiclic ********/ 
			if (firstletter=='m') {//alert("mosaic demande par menu"); 
				for (var i=0; i<livignette.length; i++){
				livignette[i].setAttribute("style", "float:left;"); 
				if (document.all)
				livignette[i].style.setAttribute("cssText", "float:left;");
				}   
				vignspetitdiv.className = "mosaic";
				bigpictures.className = "hidden";
				flechebackref.className = "hidden";
				flechegoref.className = "hidden";
				if (totalvigns>2)
				images=" images";
				else images=" image";  
				txtcompteurcentre.innerHTML=totalvigns+images;
				reseticones();
				//merci à http://www.nanoum.net/blog/9_setAttribute.html
				iconemosaic.setAttribute("class", "ico icoselectmosaiclic");
				if (document.all) {
				iconemosaic.setAttribute("className","ico icoselectmosaiclic");
				} 
				return false; //on stop le lien
			}
			 

	/*********** suite *************/				
	//if(show.className){alert(show.className);}; //class du div demandé
	
	
	if(show.className != "voir"){ //si doc demandé n'est pas visible -- affichage depuis menu vignette 	
								for (var i=0; i<bigpictdivs.length; i++){
								bigpictdivs[i].className = "hidden"; //on cache les divs des photos
								 }
	}
	

								 
				/*** d pour les documents ****/							 
						if (firstletter=='d'){ //alert("document demandé!");	 
							//tester si on est bien en mosaic (soit aucune grande image visible)
						//if (idoc==idname){alert("clic grande");}
							 
							if((bigpictures.className == 'hidden')||(idoc==idname))
								{   
								//alert('mode mosaic actif');	
								//donc on peut faire popup ici le doc est +pleinimage
								 
								photo=show.getElementsByTagName('img')[0].src; //on recup chemin de l'image de l'id de ce div
								//on analyse le chemin pour connaitre la taille
												str_array=photo.split('/'); 
												str=""+str_array[str_array.length-2]; // cache-500x600
												taille=str.substring(str.lastIndexOf("-")+1); // L800xH600
												idocheight=taille.substring(taille.lastIndexOf("x")+1); //de droite à gauche 600
												idocwidth=taille.substring(0,taille.indexOf("x")); //de gauche à droite 500
												idocheight=str.substring(str.lastIndexOf("H")+1);
												idocwidth=str.substring(str.lastIndexOf("L")+1);
										
								//idocheight=show.getElementsByTagName('img')[0].height;  //hauteur
								  
								if (show.getElementsByTagName('dt')[0]) {
								titre = show.getElementsByTagName('dt')[0].firstChild.nodeValue; //son titre 
								} else { titre = ''; };
								if (show.getElementsByTagName('dd')[0]) {
								descriptif = show.getElementsByTagName('dd')[0].firstChild.nodeValue; ; //son descriptif
								} else { descriptif = ''; };  
								//alert (photo+" "+idocheight+""+idocwidth+""+titre+""+descriptif);
								
//function popupwalma(photo,largeur,hauteur,titre,descriptif) {
	//la hauteur du doc est superieur de la hauteur de page?
	hauteurpage= screen.availHeight-20;
	hauteur=parseInt(idocheight); 
	largeur=parseInt(idocwidth); 
	rapport = hauteur/largeur;
	
	if (hauteur>hauteurpage){ //si image trop haute
	largeur = Math.round(largeur/rapport) +40; 
	} else {
	largeur=largeur +40; 
	}hauteur=parseInt(idocheight) +150; 
	//alert("largeur "+largeur+" screen " +screen.availHeight+"");
								
	if ((titre!='')&&(descriptif!='')){hauteur=parseInt(hauteur) +42;}
 	var top=Math.round((screen.availHeight-hauteur)/2); //pour centrer
  	var left=Math.round((screen.availWidth-largeur)/2);
	var fenetre=open("","fenetre","top="+top+",left="+left+",width="+largeur+",height="+hauteur+",toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no"); 
	fenetre.resizeTo(largeur,hauteur);  
	fenetre.document.write("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'  'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html><head><title>"+titre+"</title></head><body style='padding:0; text-align:center; padding-top:20px;'>");
	fenetre.document.write("<a href='javascript:self.close()'><img src='"+photo+"' style='margin:0 auto; border:0; width:100%;' alt='"+titre+"'/></a>");
	fenetre.document.write("<div><strong>"+titre+"</strong><br />"+descriptif+"</div><div style='position:absolute; z-index:1; left: 2px; top: 2px; font-size:12px;  background-color:#E9E9E9'><a href='javascript:self.close()' style='color:#666666; text-decoration:none;'><:walma:clic_fermer:></a></div></body></html>");
	fenetre.document.close();
    fenetre.focus();	
//}
show.className='voir';
								 return false;

								
								}
								else 
								{ 
								show.className='voir'; //ok pour gauche ou droite car ne fonctionnera pas en mosaic car secu div supérieur hidden
								}
								 
							 
						}
															
				/*** b pour les blocs ****/
						if (firstletter=='b') { //alert('bloc demandé!');	 
								//cache tous les blocs
								for (var i=0; i<bigpictdivs.length; i++){
								bigpictdivs[i].className = "hidden"; //on cache les divs des photos
								 }
								for (var i=0; i<ulvignette.length; i++){
								ulvignette[i].className ='hidden';
								}
								show.className='voir'; //ok voir le bloc vignette que l'on veut
								idoor=show.getElementsByTagName('a')[0]; //on recup le 1er <a> du bloc vignette demandé (après le compteur)
								str_array=idoor.href.split('/'); //on analyse l'ancre de ce <a href> comme précedemment
								str=str_array[str_array.length-1];  
								idocok=str.substring(str.lastIndexOf("#")+1);
								// alert("idocok" + idocok); //on recup le 1er doc du bloc
								var showdeux = document.getElementById(idocok);
								showdeux.className='voir'; //voir la première image du bloc demandé
								
								//LE COMPTEUR
								blocnumber=parseInt(idoc.substring(idoc.lastIndexOf("bloc")+4)); //recup only n° du bloc 
								// alert("You ask for " + idoc + "number" +blocnumber);
								
								construitcompteur(blocnumber);
								return false; //on stop le lien
						}

 
							return false;
						}
				}
		}
}	

function addEvent(obj, evType, fn){
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, true);
    return true;
  } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
  } else {
    return false;
  }

}

addEvent(window, 'load', walma); //merci to Simon Willison
