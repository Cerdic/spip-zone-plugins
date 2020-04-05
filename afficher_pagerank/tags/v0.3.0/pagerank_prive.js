$(document).ready(function() {
	var compteur = 0;
	var afficher = true;

	url_site = escape("#URL_SITE_SPIP");

	$("ul.referers li a").addClass("afficherPR");
	$("ul.referers li li a").removeClass("afficherPR");

	$("ul.referers li a.afficherPR").each(function() {
		url = $(this).attr("href");

		url = "http://" + parseUri(url).host;

		compteur ++;
		var afficher = true;

		if (url.indexOf(url_site) >= 0) var afficher = false;

		if (afficher) {
			$(this).prepend("<span id='pr"+compteur+"'></span>");
			$("#pr"+compteur).load("../?page=afficher_minipagerank_racine&url="+escape(url));
		}

	});
});


/*
	parseUri 1.2.1
	(c) 2007 Steven Levithan <stevenlevithan.com>
	MIT License
*/

function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});

	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};
