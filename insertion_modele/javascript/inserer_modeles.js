/*
 * jQuery plugin: fieldSelection - v0.1.0 - last change: 2006-12-16
 * (c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 */
(function() {
	var fieldSelection = {
		getSelection: function() {
			var e = this.jquery ? this[0] : this;
			return (
				/* mozilla / dom 3.0 */
				('selectionStart' in e && function() {
					var l = e.selectionEnd - e.selectionStart;
					return { start: e.selectionStart, end: e.selectionEnd, length: l, text: e.value.substr(e.selectionStart, l) };
				}) ||
				/* exploder */
				(document.selection && function() {
					e.focus();
					var r = document.selection.createRange();
					if (r == null) {
						return { start: 0, end: e.value.length, length: 0 }
					}
					var re = e.createTextRange();
					var rc = re.duplicate();
					re.moveToBookmark(r.getBookmark());
					rc.setEndPoint('EndToStart', re);
					return { start: rc.text.length, end: rc.text.length + r.text.length, length: r.text.length, text: r.text };
				}) ||
				/* browser not supported */
				function() {
					return { start: 0, end: e.value.length, length: 0 };
				}
			)();
		},
		replaceSelection: function() {
			var e = this.jquery ? this[0] : this;
			var text = arguments[0] || '';
			return (
				/* mozilla / dom 3.0 */
				('selectionStart' in e && function() {
					e.value = e.value.substr(0, e.selectionStart) + text + e.value.substr(e.selectionEnd, e.value.length);
					return this;
				}) ||
				/* exploder */
				(document.selection && function() {
					e.focus();
					document.selection.createRange().text = text;
					return this;
				}) ||
				/* browser not supported */
				function() {
					e.value += text;
					return this;
				}
			)();
		}
	};
	jQuery.each(fieldSelection, function(i) { jQuery.fn[i] = this; });
})();

var curseur;
var datas;

$(document).ready(function() {
	// Insertion du div de la modale
	$("body").append('<div class="window" id="modale_images"></div><div id="inserer_modeles_mask"></div>');

	//select all the a tag with name equal to modal
	$('.outil_barre_img_dft').click(function(e) {

		// sauvegarder le curseur		
		var range = $("textarea[name=texte]").getSelection();

		//Cancel the link behavior
		e.preventDefault();

		// Qui ?
		var id_objet, objet;
		if ($('input[name=id_article]').length > 0)
		{
			id_objet = $('input[name=id_article]').val();
			objet = "article";
		} else if ($('input[name=id_rubrique]').length > 0)
		{
			id_objet = $('input[name=id_rubrique]').val();
			objet = "rubrique";
		}
		
		datas = "objet=" + objet + "&id_objet=" + id_objet ;
		
		// TODO : Mettre dans les pipeline de document la création des vignettes pour ne pas avoir une page vide !
		
		// Recupérer les images en AJAX pour l'afficher dans la modal
		$.ajax({
			type: "GET",
			url: "?exec=modale_images_select",
			data: datas,
			success: function(r){$("#modale_images").html(r);},
			async: false
		}).responseText;

		recalculTailleMask ();

		// la transition du mask (simple fade)
		$('#inserer_modeles_mask').fadeIn(500);
		$('#inserer_modeles_mask').fadeTo("fast",0.8);

		// la transition de la modale
		$("#modale_images").fadeIn(500);
		
		$(window).resize(function(){
			recalculTailleModale();
			recalculTailleMask ();
		});

	});

	// fermer si on clique sur le mask
	$('#inserer_modeles_mask').click(function () { 
		$(this).hide();
		$('.window').hide();
	});
});

onAjaxLoad(submitInsererModelesSelectImage);
onAjaxLoad(InsererModelesWindowClose);
onAjaxLoad(submitInsererModelesSelectParams);
onAjaxLoad(recalculTailleModale);

function recalculTailleMask ()
{
	// Choper les hauteur / largeur du document pour recouvrir tout
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	// Mettre les dimension au fond noir
	$('#inserer_modeles_mask').css({'width':maskWidth,'height':maskHeight});
}

function recalculTailleModale ()
{
	// Dimension de la fenêtre
	var winH = $(window).height();
	var winW = $(window).width();
	
	//Calculer et positionner la modale au centre
	$("#modale_images").css('top',  winH/2-$("#modale_images").height()/2);
	$("#modale_images").css('left', winW/2-$("#modale_images").width()/2);
}

function submitInsererModelesSelectParams(){
	$("#edit_parametres").unbind("submit");

	$("#edit_parametres").submit(function(){			
		$(".fadeOut").fadeTo("fast",0.5);
		var inputs = $("#edit_parametres").serialize();
		$.ajax({
			type: "GET",
			url: "?exec=calcul_parametres",
			data: inputs,
			success:function(r){
				$("textarea[name=texte]").replaceSelection(r, true);
				$('#inserer_modeles_mask, .window').hide();
			},
			async: false
		});

		return false;
	 });
}

function submitInsererModelesSelectImage(){
	$("#select_image").unbind("submit");
	$("#onglets a").unbind("click");

	$("#onglets a").click(function(){
		var url = $(this).attr("href");
		ajaxIS(url,$("#list_images"),datas);
		$("#onglets a.on").removeClass("on");
		$(this).addClass("on");

		return false;
	});

	$("#select_image").submit(function(){			
		$("#div_image_select").fadeTo("fast",0.5);
		var inputs = $("#select_image").serialize();
		$.ajax({
			type: "GET",
			url: "?exec=modale_images_parametres",
			data: inputs,
			success:function(r){
				$("#modale_images").html(r).fadeTo("fast", 1);
			},
			async: false
		});
		
		return false;
	});
}

function InsererModelesWindowClose(){
	$(".window .close").unbind("click");

	// fermer
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$('#inserer_modeles_mask, .window').hide();
	});
}

function ajaxIS (url, conteneur, datas){
	datas = "url=" + url + "&" + datas;
	$.ajax({
		type: "GET",
		url: "?exec=modale_liste_images",
		data: datas,
		success:function(r){
			conteneur.html(r).fadeTo("fast", 1);
		},
		async: false
	});
}