/* Objet pour stocker les styles d'origine du textarea et de la page */
var textAreaActif = new Object();
	textAreaActif.id = 0;	

/* Ne fonctionne pas sous IE6 */
/* a cause du passage en "position: fixed" */
var ie6 = jQuery.browser.msie &&
	parseInt(jQuery.browser.version) == 6 &&
	typeof window['XMLHttpRequest'] != "object";

/* Innerheight parce que jquery 1.1.1 ne le fait pas... */
/* Nom de fonction impossible pour eviter conflits */
function hauteurWindowPourTextarea() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myHeight = document.body.clientHeight;
  }
  return myHeight;
}



/* Afficher en mode plein ecran et forcer le ScrollTop */
/* Est appele quand double clic, et quand redimensionnement de l'ecran */
function changerDimensions(el) {

	/* si le box-sizing a ete fixe a "border-box", 
	   il faut indiquer les dimensions externes */
	var boxSizing = false;
	if (el.css("-webkit-box-sizing") == "border-box") boxSizing = true;
	if (el.css("-moz-box-sizing") == "border-box") boxSizing = true;
	if (el.css("-ms-box-sizing") == "border-box") boxSizing = true;
	if (el.css("box-sizing") == "border-box") boxSizing = true;
	
	if (boxSizing) {
		el.css({
			position: "fixed", 
			fontSize: "14px",
			lineHeight: "135%",
			top: 0, 
			left: 0, 
			padding:"0", 
			width: $("body").width(), 
			paddingLeft: Math.floor(($("body").width() - 550) / 2),
			paddingRight: Math.ceil(($("body").width() - 550) / 2),
			height: hauteurWindowPourTextarea() - (30),
			paddingTop: "30px",
			paddingBottom: "30px",
			border: "0", 
			backgroundColor : "white",
			zIndex: 1000
		});
	
	} else {
		el.css({
			position: "fixed", 
			fontSize: "14px",
			lineHeight: "135%",
			top: 0, 
			left: 0, 
			padding:"0", 
			width: "550px", 
			paddingLeft: Math.floor(($("body").width() - 550) / 2),
			paddingRight: Math.ceil(($("body").width() - 550) / 2),
			height: hauteurWindowPourTextarea() - (60 + 30),
			paddingTop: "30px",
			paddingBottom: "30px",
			border: "0", 
			backgroundColor : "white",
			zIndex: 1000
		});
	}
	
	if (jQuery().scrollTop) {
		el.scrollTop(textAreaActif.scrollTop * 2);
	}
}

/* Sortir du mode plein ecran quand Escape */
function intercepterEscape(e) {
	if (e.which == 27) reduireTextarea (textAreaActif.id);
}

/* Sauvegarder les dimensions d'origine */
/* Afficher les elements necessaires hors textarea */
function agrandirTextarea(el) {


	/* Sauver les styles d'origine */
	textAreaActif.id = el;
	textAreaActif.submit = el.parents("form").find("input[type=submit]");
	textAreaActif.position = el.css("position");
	textAreaActif.fontSize = el.css("fontSize");
	textAreaActif.lineHeight = el.css("lineHeight");
	textAreaActif.top = el.css("top");
	textAreaActif.left = el.css("left");
	textAreaActif.padding = el.css("padding");
	textAreaActif.width = el.css("width");
	textAreaActif.paddingLeft = el.css("paddingLeft");
	textAreaActif.paddingRight = el.css("paddingRight");
	textAreaActif.paddingTop = el.css("paddingTop");
	textAreaActif.paddingBottom = el.css("paddingBottom");
	textAreaActif.height = el.css("height");
	textAreaActif.border = el.css("border");
	textAreaActif.zIndex = el.css("zIndex");
	textAreaActif.backgroundColor = el.css("backgroundColor");
	
	if (jQuery().scrollTop) {
		textAreaActif.scrollTop = el.scrollTop();
		textAreaActif.pageScrollTop = $("body").scrollTop();
	} else {
		textAreaActif.scrollTop = 0;
		textAreaActif.pageScrollTop = 0;
	}
	
	
	textAreaActif.submit.css({
		position: "fixed",
		width: "150px",
		zIndex: "1002",
		bottom: "3px",
		right: "20px"
	});
	
	textAreaActif.submit.bind("click", function() {
		reduireTextarea(el);
	});
	
	/* Planquer le contenu du body et, surtout, masquer le scroll general de la page */
	/* Au passage: corriger bug d'affichage Firefox 2: certains elements de pages restaient au dessus du textarea */
	$("body").css ({
		overflow: "hidden",
		marginTop: -1 * hauteurWindowPourTextarea(),
		height: hauteurWindowPourTextarea()
	});
	
	/* Afficher bouton expliquant touche Escape pour sortir */
	$("body").prepend("<div id='masque_fond_textarea' style='background-color: #ddd; position: fixed; width: 100%; bottom: 0px; z-index: 1001; height: 30px;'><div style='font-size: 12px; padding-left: 20px; padding-top: 8px;'>&laquo;&nbsp;Esc.&nbsp;&raquo; pour quitter le mode plein &eacute;cran</div></div>");
	
	/* Afficher le textarea en plein ecran */
	changerDimensions(el);
	
	/* Intercepter la touche Escape pour declencher sortie du mode plein ecran */
	$(document).bind("keydown", intercepterEscape);
	
	
}


/* Desactiver le mode plein ecran */
function reduireTextarea(el) {
	textAreaActif.id = 0;	

	/* Supprimer le bandeau "escape" */
	$("#masque_fond_textarea").remove();
	/* Redonner au body son comportement d'origine */
	$("body").css ({height: "auto", overflow: "auto", marginTop: "0px"});




	/* Remettre les dimensions d'origine du textarea, y compris son scroll d'origine */
	el.css({
		position: textAreaActif.position, 
		fontSize: textAreaActif.fontSize,
		lineHeight: textAreaActif.lineHeight,
		top: textAreaActif.top, 
		left: textAreaActif.left,
		padding: textAreaActif.padding,
		width: textAreaActif.width,
		paddingLeft: textAreaActif.paddingLeft,
		paddingRight: textAreaActif.paddingRight,
		paddingTop: textAreaActif.paddingTop,
		paddingBottom: textAreaActif.paddingBottom,
		height: textAreaActif.height, 
		border: textAreaActif.border,
		zIndex: textAreaActif.zIndex,
		backgroundColor: textAreaActif.backgroundColor
	});
	el.scrollTop(textAreaActif.scrollTop);


	/* Replacer la page a son scroll d'origine */
	/* Semble ne pas fonctionner sous IE */
	$("body").scrollTop(textAreaActif.pageScrollTop)

	/* Supprimer le comportement de la touche Escape */
	$(document).unbind("keydown", intercepterEscape);

	textAreaActif.submit.css({
		position: "static",
		width: "auto",
		zIndex: 1
	});

}


function bindTousTextarea() {
	if (!ie6) {
		$("textarea").dblclick(function(){
			if (textAreaActif.id == 0) agrandirTextarea($(this));
		});
	}
}

/* Activer au chargement */
$(document).ready(bindTousTextarea);

/* Activer quand on charge un element de page en Ajax */
/* Bon: la ca n'a pas l'air de fonctionner... */
$(document).ajaxComplete(bindTousTextarea);

/* Quand redimensionner la fenetre, si plein ecran, modifier les dimensions du textarea */
$(window).bind('resize', function() {
	if (textAreaActif.id !== 0) changerDimensions(textAreaActif.id);
});


