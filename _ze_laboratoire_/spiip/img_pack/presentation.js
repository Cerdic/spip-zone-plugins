//init document
var active_menu = $('empty');
var puce_popup;
$(document).ready(function() {
		//console.time("total");
		//init ajax links
		$('a.ajax').click(execAjaxLinks).not('[@href]').css({cursor:'pointer',visibility:'visible'});
		//init fast change of an article state
		//console.time("faststatut");
		puce_popup = $('div.puce_article_fixe,div.puce_breve_fixe').mouseover(function(){puce_popup.hide();$('+div',this).show();return false;})
		.find('+div').hover(function(){},function(){puce_popup.hide();return false;})
		//compatibility with default style_prive (temporary)
		.css({visibility:'visible',display:'none'});
		//console.timeEnd("faststatut");
		//console.timeEnd("total");
	}
);

function showMenu() {
	active_menu.hide();
	active_menu=$('#'+this.id.replace(/^bouton\d?_/,'bandeau')).show();
	//bug safari..runtime style returns null 
	active_menu.css('display','block');
}

function decalerCouche() {
	//make the submenu have a layout, center it and reset state
	var b = document.getElementById('bandeau-principal');
	$('div.bandeau_sec',this).css({visibility:'visible',display:'block'}).each(function() {
		if (bug_offsetwidth) {
			var demilargeur = Math.floor( this.offsetWidth / 2 );
			var offset = jQuery.browser.msie?this.parentNode.offsetLeft:this.offsetLeft;
			var gauche = offset - demilargeur	+ Math.floor(largeur_icone / 2);
			if (gauche < 0) gauche = 0;
			
			this.style.left = gauche+'px';
			//-1 is the border of bandeau-principal
			this.style.top = b.offsetTop+b.offsetHeight+-1+'px';
		}
	}).css({visibility:'',display:''});
	//i love ff...
	if(jQuery.browser.msie)$('>:first-child',this).css('height',b.childNodes[0].offsetHeight+'px');
	if(jQuery.browser.safari)$('>:first-child',this).css('height',b.offsetHeight-6+'px');
}

//call information is inside the link id
//id='page--var1:val1:var2:val2--dest_el'
//params are separated by --
//param 0 = page to exec
//param 1 = arguments of exec (pairs of name, value separated by : )
//param 2 = id of the receiving element
function execAjaxLinks() {
			var params = this.id.split('--');
			var url = './?exec='+params[0]+'&var_ajax=1';
			var args = params[1].split(':');
			for(var i=0;i<args.length;i+=2) {
				url += '&'+args[i]+'='+args[i+1];
			}
			if(url_chargee['mem_'+url]) {
				$('#'+params[2]).html(url_chargee['mem_'+url]).
				find('a.ajax').click(execAjaxLinks).not('[@href]').css({'cursor':'pointer','visibility':'visible'});
				return false;
			}
			return AjaxSqueeze(url,params[2],function(res,status){
				if(status=='success') {
					url_chargee['mem_'+url]=res;
					$('a.ajax',this).click(execAjaxLinks).not('[@href]').css({'cursor':'pointer','visibility':'visible'});
				}
			},'img_'+params[2]);
}

var accepter_change_statut;

function selec_statut(id, type, decal, puce, script) {

	if (!accepter_change_statut) {
		accepter_change_statut = confirm(confirm_changer_statut)
	}

	if (accepter_change_statut) {
		$('#statutdecal'+type+id).css({marginLeft:decal+'px',display:'none'})
		$('#imgstatut'+type+id).attr('src',puce);
		
		frames['iframe_action'].location.href = script;
	}
}

function changeclass(objet, myClass) {
	objet.className = myClass;
}
function changesurvol(iddiv, myClass) {
	document.getElementById(iddiv).className = myClass;
}

function setvisibility (objet, statut) {
	element = findObj(objet);
	if (element.style.visibility != statut) element.style.visibility = statut;
}

function montrer(objet) {
	setvisibility(objet, 'visible');
}
function cacher(objet) {
	setvisibility(objet, 'hidden');
}



function getHeight(obj) {
	if (obj == "window") {
		return hauteur_fenetre();
	}
	else
	{
		obj = document.getElementById(obj);
		if (obj.offsetHeight) return obj.offsetHeight;
	}
}
function hauteur_fenetre() {
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myHeight = window.innerHeight;
	} else {
		if( document.documentElement &&
			( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
			//IE 6+ in 'standards compliant mode'
			myHeight = document.documentElement.clientHeight;
		} else {
			if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
				//IE 4 compatible
				myHeight = document.body.clientHeight;
			}
		}
	}
	return myHeight;
}


function hauteurFrame(nbCol) {
	hauteur = hauteur_fenetre() - 40;
	hauteur = hauteur - getHeight('haut-page');
	
	if (findObj('brouteur_hierarchie')) hauteur = hauteur - getHeight('brouteur_hierarchie');
		
	for (i=0; i<nbCol; i++) {
		source = document.getElementById("iframe" + i);
		source.style.height = hauteur + 'px';
	}
}

function hauteurTextarea() {
	hauteur = hauteur_fenetre() - 80;
	
	source = document.getElementById("text_area");
	source.style.height = hauteur + 'px';
}

function changeVisible(input, id, select, nonselect) {
	if (input) {
		element = findObj(id);
		if (element.style.display != select)  element.style.display = select;
	} else {
		element = findObj(id);
		if (element.style.display != nonselect)  element.style.display = nonselect;
	}
}



// pour MOzilla >= 1.7
function verifForm(root) {
	/* if (pluginlist.indexOf("SVG")!=-1)
		document.cookie = "spip_svg_plugin=oui";
	else
		document.cookie = "spip_svg_plugin=non";
	*/

	//convert2math();
	root = root || document;
	retrait = 16;
	$('input,textarea',root).filter('.formo, .forml').each(function() {
		this['nouvelle-largeur'] = this.offsetWidth ? (this.offsetWidth - retrait) + 'px' : '95%';
	}
	).each(function() {
		if(this['nouvelle-largeur']) this.style.width = this['nouvelle-largeur'];
	});
}

// livesearchlike...


function lancer_recherche(champ, cible) {
	// Desactive pour l'instant (bouffe trop de ressources)
	/* et a reprendre suite au cght d'interface Ajax du 7/8/06
	valeur = findObj(champ).value;
	if (valeur.length > 3) {
		charger_id_url('./?exec=recherche_sugg='+valeur,'sugg_recherche');
		charger_id_url('./?exec=recherche='+valeur,'resultats_recherche');
	}
	*/
}

function lancer_recherche_rub(champ, rac, exclus) {
	valeur = findObj(champ).value;
	if (valeur.length > 0) {
		charger_id_url('./?exec=rechercher&type='+valeur+'&exclus='+exclus+'&rac='+rac, rac+'_col_1');
	}
}

// effacement titre quand new=oui
var antifocus=false;
// effacement titre des groupes de mots-cles de plus de 50 mots
var antifocus_mots = new Array();

function puce_statut(selection){
	if (selection=="publie"){
		return "puce-verte.gif";
	}
	if (selection=="prepa"){
		return "puce-blanche.gif";
	}
	if (selection=="prop"){
		return "puce-orange.gif";
	}
	if (selection=="refuse"){
		return "puce-rouge.gif";
	}
	if (selection=="poubelle"){
		return "puce-poubelle.gif";
	}
}

// lorsqu'on touche a un formulaire, desactiver les autres
// (a voir : onchange=... fonctionne sous FF, mais pas Safari)
function disable_other_forms(me) {
	var items = document.getElementsByTagName('form');
	for (var j = 0; j < items.length; j++) {
		if (items[j] != me) {
			var fields = items[j].getElementsByTagName('input');
			for (var k = 0; k < fields.length; k++) {
				fields[k].disabled=true;
			}
		}
	}
}

// Pour ne pas fermer le formulaire de recherche pendant qu'on l'edite	

function recherche_desesperement()
{
	if(active_menu && !active_menu.is('#bandeaurecherche')) {active_menu.hide();active_menu=$('empty');}
}
