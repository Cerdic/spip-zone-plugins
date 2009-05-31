var memo_obj = new Array();
var url_chargee = new Array();
var load_handlers = new Array();

function findObj_test_forcer(n, forcer) { 
	var p,i,x;

	// Voir si on n'a pas deja memorise cet element
	if (memo_obj[n] && !forcer) {
		return memo_obj[n];
	}

	var d = document; 
	if((p = n.indexOf("?"))>0 && parent.frames.length) {
		d = parent.frames[n.substring(p+1)].document; 
		n = n.substring(0,p);
	}
	if(!(x = d[n]) && d.all) {
		x = d.all[n]; 
	}
	for (i = 0; !x && i<d.forms.length; i++) {
		x = d.forms[i][n];
	}
	for(i=0; !x && d.layers && i<d.layers.length; i++) x = findObj(n,d.layers[i].document);
	if(!x && document.getElementById) x = document.getElementById(n); 

	// Memoriser l'element
	if (!forcer) memo_obj[n] = x;
	return x;
}

function findObj(n) { 
	return findObj_test_forcer(n, false);
}
// findObj sans memorisation de l'objet - avec Ajax, les elements se deplacent dans DOM
function findObj_forcer(n) { 
	return findObj_test_forcer(n, true);
}

function hide_obj(obj) {
	var element;
	if (element = findObj(obj)){
		if (element.style.visibility != "hidden") element.style.visibility = "hidden";
	}
}

function jquerySwapCouche() {
	var m = this.id.match(/([-\d]+)(_rtl)?/);
	var ids = m[1].split("-"); 
	var dir = m[2] || '';
	for(id in ids) $('#Layer'+ids[id]).toggle();
	this.src = this.src.search(/bas\.gif$/)==-1 ? this.src.replace(/haut(_rtl)?\.gif$/,'bas.gif'): this.src.replace(/bas\.gif$/,'haut'+dir+'.gif'); 
}
function ouvrir_couche(couche, rtl,dir) {
	var layer;
	var triangle = findObj('triangle' + couche);
	if (!(layer = findObj('Layer' + couche))) return;
	if (triangle) triangle.src = dir + 'deplierbas.gif';
	layer.style.display = 'block';
}
function fermer_couche(couche, rtl, dir) {
	var layer;
	var triangle = findObj('triangle' + couche);
	if (!(layer = findObj('Layer' + couche))) return;
	if (triangle) triangle.src = dir + 'deplierhaut' + rtl + '.gif';
	layer.style.display = 'none';
}
function manipuler_couches(action,rtl,first,last, dir) {
	if (action=='ouvrir') {
		for (j=first; j<=last; j+=1) {
			ouvrir_couche(j,rtl, dir);
		}
	} else {
		for (j=first; j<=last; j+=1) {
			fermer_couche(j,rtl, dir);
		}
	}
}

//
// Fonctions pour mini_nav
//

function slide_horizontal (couche, slide, align, depart, etape ) {
	var obj = findObj_forcer(couche);
	if (!obj) return;
	if (!etape) {
		if (align == 'left') depart = obj.scrollLeft;
		else depart = obj.firstChild.offsetWidth - obj.scrollLeft;
		etape = 0;
	}
	etape = Math.round(etape) + 1;
	pos = Math.round(depart) + Math.round(((slide - depart) / 10) * etape);

	if (align == 'left') obj.scrollLeft = pos;
	else obj.scrollLeft = obj.firstChild.offsetWidth - pos;
	if (etape < 10) setTimeout("slide_horizontal('"+couche+"', '"+slide+"', '"+align+"', '"+depart+"', '"+etape+"')", 60);
	//else obj.scrollLeft = slide;
}

function changerhighlight (couche) {
	var kids = couche.parentNode.childNodes;
	for (var i = 0; i < kids.length; i++) {
		kids[i].className = "pashighlight";
	}
	couche.className = "highlight";
}

function aff_selection (type, rac, id) {
//	alert (type + " - " + rac + " - " + id);
	
	findObj_forcer(rac+"_selection").style.display = "none";
	
	charger_id_url("./?exec=informer&type="+type+"&id="+id+"&rac="+rac, rac+"_selection");
}

// selecteur de rubrique et affichage de son titre dans le bandeau

function aff_selection_titre(titre, id_rubrique, racine, url, col, sens)
{
	findObj('titreparent').value=titre;
	findObj('id_parent').value=id_rubrique;
	findObj('selection_rubrique').style.display='none';
	return aff_selection_provisoire(id_rubrique, racine, url, col, sens);
}

function aff_selection_provisoire(id_rubrique, racine, url, col, sens)
{
    charger_id_url(url.href,
		   racine + '_col_' + (col+1),
		   function() {
		     slide_horizontal(racine + 'principal', ((col-1)*150), sens);
  // afficher le descriptif de la rubrique dans la div du dessous?
  // si trop lent, commenter la ligne ci-dessous
		     aff_selection('rubrique',racine,id_rubrique);
		   }
		   );
  // empecher le chargement non Ajax
  return false;
}

//
// Cette fonction charge du contenu - dynamiquement - dans un 
// Ajax

function createXmlHttp() {
	if(window.XMLHttpRequest)
		return new XMLHttpRequest(); 
	else if(window.ActiveXObject)
		return new ActiveXObject("Microsoft.XMLHTTP");
}

/*
function ajah(method, url, flux, rappel)
{
	var xhr = createXmlHttp();
	if (!xhr) return false;
        xhr.onreadystatechange = function () {ajahReady(xhr, rappel);}
        xhr.open(method, url, true);
	// Necessaire au mode POST
	// Il manque la specification du charset
	if (flux) {
		xhr.setRequestHeader("Content-Type",
		       "application/x-www-form-urlencoded; ");
	}
	xhr.send(flux);
	return true;
}

function ajahReady(xhr, f) {
	if (xhr.readyState == 4) {
		if (xhr.status > 200) // Opera dit toujours 0 !
                      {f('Erreur HTTP :  ' +  xhr.status);}
                else  { f(xhr.responseText); }
        }
}
*/

//
// Add a function to the list of those to be executed on ajax load complete
//
function onAjaxLoad(f) {
	load_handlers.push(f);
}

//
// Call the functions that have been added to onAjaxLoad
//
function triggerAjaxLoad(root) {
	for ( var i = 0; i < load_handlers.length; i++ )
	load_handlers[i].apply( root );
}

//call information is inside the link id
//id='var1:val1:var2:val2--dest_el'
//params are separated by --
//param 0 = arguments of exec (pairs of name, value separated by : )
//param 1 = id of the receiving element
function execAjaxLinks() {
			var params = this.id.split('--');
			var url = './?';
			//var url = './?exec='+params[0]';
			var args = params[0].split(':');
			for(var i=0;i<args.length;i+=2) {
				url += args[i]+'='+args[i+1]+'&';
			}
			if(url_chargee['mem_'+url]) {
				var el = $('#'+params[1]).html(url_chargee['mem_'+url]);
				$(el).find('a.ajax').click(execAjaxLinks).not('[@href]').css({'cursor':'pointer','visibility':'visible'});
				triggerAjaxLoad(el[0]);
				return false;
			}
			//console.log("%o %o",url,params[1]);
			return AjaxSqueeze(url,params[1],function(res,status){
				if(status=='success') {
					url_chargee['mem_'+url]=res;
					$('a.ajax',this).click(execAjaxLinks).not('[@href]').css({'cursor':'pointer','visibility':'visible'});
				}
			});
}

// Si Ajax est disponible, cette fonction l'utilise pour envoyer la requete.
// Si le premier argument n'est pas une url, ce doit etre un formulaire.
// Le deuxieme argument doit etre l'ID d'un noeud qu'on animera pendant Ajax.
// Le troisieme, optionnel, est la fonction traitant la réponse.
// La fonction par defaut affecte le noeud ci-dessus avec la reponse Ajax.
// En cas de formulaire, AjaxSqueeze retourne False pour empecher son envoi
// Le cas True ne devrait pas se produire car le cookie spip_accepte_ajax
// a du anticiper la situation.
// Toutefois il y toujours un coup de retard dans la pose d'un cookie:
// eviter de se loger avec redirection vers un telle page

function AjaxSqueeze(trig, id, callback)
{
	var reqObj;
	//console.log("%o %o",trig,id);
	callback = callback || function(){};
	//needs a better way to display error to the user
	if(trig.constructor == String) {
		reqObj = $('#'+id).imgOn().load(trig+"&var_ajaxcharset=utf-8",function(res,status){
			if(status=='error') this.html('Erreur HTTP');
			callback(res,status);
			if(status=='success') triggerAjaxLoad(this[0]);
			imgOff(reqObj);
		});
	} else {
		//submit a form. Uses form plugin
		reqObj = $(trig).imgOn().ajaxSubmit('#'+id,function(res,status){
			if(status=='error') this.html('Erreur HTTP');
			callback(res,status);
			if(status=='success') {
				if(browser_verifForm) verifForm(this);
				triggerAjaxLoad(this[0]);
			} 
			imgOff(reqObj);
		});
	}
	return false;
}

//jQuery helper function to show and hide an image as the first children of a jQuery object
//if no id is passed or the image with the id specified 
jQuery.fn.imgOn = function() {
		//console.log("%o",this[0].id);
		var img = $('#img_'+this[0].id);
		if(img.is('img')) this.ajaxImg = img.css('visibility','visible');
		else this.prepend(ajax_image_searching);
		return this;
	} 	
imgOff = function(reqObj) {
	if(reqObj.ajaxImg) reqObj.ajaxImg.css('visibility','hidden');
	reqObj.ajaxImg = null;
}


function charger_id_url(myUrl, myField, jjscript) 
{
	var Field = findObj_forcer(myField);
	if (!Field) return;

	if (!myUrl) 
		retour_id_url('', Field, jjscript);
	else {
	  var r = url_chargee[myUrl];
		// disponible en cache ?
	  if (r) {
			retour_id_url(r, Field, jjscript);
	  } else {
			var img = findObj_forcer('img_' + myField);
			if (img) img.style.visibility = "visible";
			AjaxSqueezeNode(myUrl,
				'',
				function (r) {
					if (img) img.style.visibility = "hidden";
					url_chargee[myUrl] = r;
					retour_id_url(r, Field, jjscript);
				}
			)
		}
	}
}

function retour_id_url(r, Field, jjscript)
{
	Field.innerHTML = r;
	Field.style.visibility = "visible";
	Field.style.display = "block";
	if (jjscript) jjscript();
}

// ne sert que pour selecteur_rubrique_ajax() dans inc/chercher_rubrique.php
function charger_id_url_si_vide (myUrl, myField, jjscript) {
	var Field = findObj_forcer(myField); // selects the given element
	if (!Field) return;

	if (Field.innerHTML == "") {
		charger_id_url(myUrl, myField, jjscript) 
	}
	else {
		Field.style.visibility = "visible";
		Field.style.display = "block";
	}
}

