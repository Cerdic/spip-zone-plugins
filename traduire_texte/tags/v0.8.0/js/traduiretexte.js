/*if (langue_visiteur) var language = langue_visiteur;
else */
	if (navigator.browserLanguage) var language = navigator.browserLanguage;
else var language = navigator.language;

if (language.indexOf('fr') > -1) language = "fr";
else if (language.indexOf('en') > -1) language = "en";
else if (language.indexOf('es') > -1) language = "es";
else if (language.indexOf('it') > -1) language = "it";
else language = "fr";

var traduire_avec_xxx = "traduire avec Yandex, Google ou Bing";

function afficher_traduire() {
		$(".translate_me").each(function() {
			var me = $(this);
			me.find(".traduire").remove();
			var langue = me.attr("lang");
			if (langue!="" && langue != language) {
				var contenu = encodeURIComponent(me.html());
				me.append("<div class='traduire'><a href='#'>"+traduire_avec_xxx+"</a></div>");
				me.find(".traduire").on("click", function() {
						me.attr("lang", "").find(".traduire").remove();
						me.append("<div class='loading_icone'></div>");
						$.post("?page=translate", { contenu: contenu+" ", dest: language, source: langue }, function (data) {
								me.find('loading_icone').remove();
								me.append("<div class='traduction'></div>");
								me.find('.traduction').html('<hr>'+data);
						});
					return false;
				});
			}
		});
}

$(function(){
	afficher_traduire();
});