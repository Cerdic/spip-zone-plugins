/*
 * twitter-text-js 1.4.10
 *
 * La version d'origine non compactée étant ici : https://github.com/twitter/twitter-text-js
 *
 *
 * Copyright 2011 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this work except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Modifications :
 *
 * "//widgets.twimg.com/i/widget-bird.png" => twidget_logo
 * "//widgets.twimg.com/i/widget-bird-large.png" => twidget_logo_large
 * "//widgets.twimg.com/j/1/default.gif" => twidget_rien
 * t+"search."+q => twidget_proxy
 * t+"api."+q => twidget_proxy
 * "/stylesheets/widgets/widget.css" => twidget_css
 * "//widgets.twimg.com/j/2/widget.css" => twidget_css
 */
window.twttr || (window.twttr = {});
(function (){
	function c(b, a){
		a = a || "";
		if (typeof b!=="string"){
			b.global && a.indexOf("g")<0 && (a = a+"g");
			b.ignoreCase && a.indexOf("i")<0 && (a = a+"i");
			b.multiline && a.indexOf("m")<0 && (a = a+"m");
			b = b.source
		}
		return RegExp(b.replace(/#\{(\w+)\}/g, function (a, b){
			var c = twttr.txt.regexen[b] || "";
			if (typeof c!=="string")c = c.source;
			return c
		}), a)
	}

	function k(b, a){
		return b.replace(/#\{(\w+)\}/g, function (b, e){
			return a[e] || ""
		})
	}

	function g(b, a, f){
		var e = String.fromCharCode(a);
		f!==a && (e = e+("-"+String.fromCharCode(f)));
		b.push(e);
		return b
	}

	function m(b){
		var a = {}, f;
		for (f in b)b.hasOwnProperty(f) && (a[f] = b[f]);
		return a
	}

	function n(b, a, f){
		return!f ? typeof b==="string" && b.match(a) && RegExp["$&"]===b : !b || b.match(a) && RegExp["$&"]===b
	}

	twttr.txt = {};
	twttr.txt.regexen = {};
	var v = {"&": "&amp;", ">": "&gt;", "<": "&lt;", '"': "&quot;", "'": "&#39;"};
	twttr.txt.htmlEscape = function (b){
		return b && b.replace(/[&"'><]/g, function (a){
			return v[a]
		})
	};
	var h = String.fromCharCode, d = [h(32), h(133), h(160), h(5760), h(6158), h(8232), h(8233), h(8239), h(8287), h(12288)];
	g(d, 9, 13);
	g(d, 8192, 8202);
	twttr.txt.regexen.spaces_group = c(d.join(""));
	twttr.txt.regexen.spaces = c("["+d.join("")+"]");
	twttr.txt.regexen.punct = /\!'#%&'\(\)*\+,\\\-\.\/:;<=>\?@\[\]\^_{|}~/;
	twttr.txt.regexen.atSigns = /[@\u00ef\u00bc ]/;
	twttr.txt.regexen.extractMentions = c(/(^|[^a-zA-Z0-9_])(#{atSigns})([a-zA-Z0-9_]{1,20})(?=(.|$))/g);
	twttr.txt.regexen.extractReply = c(/^(?:#{spaces})*#{atSigns}([a-zA-Z0-9_]{1,20})/);
	twttr.txt.regexen.listName = /[a-zA-Z][a-zA-Z0-9_\-\u0080-\u00ff]{0,24}/;
	twttr.txt.regexen.extractMentionsOrLists = c(/(^|[^a-zA-Z0-9_])(#{atSigns})([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9_\-]{0,24})?(?=(.|$))/g);
	d = [];
	g(d, 1024, 1279);
	g(d, 1280, 1319);
	g(d, 11744, 11775);
	g(d, 42560, 42655);
	g(d, 4352, 4607);
	g(d, 12592, 12677);
	g(d, 43360, 43391);
	g(d, 44032, 55215);
	g(d, 55216, 55295);
	g(d, 65441, 65500);
	g(d, 12449, 12538);
	g(d, 12540, 12542);
	g(d, 65382, 65439);
	g(d, 65392, 65392);
	g(d, 65296, 65305);
	g(d, 65313, 65338);
	g(d, 65345, 65370);
	g(d, 12353, 12438);
	g(d, 12441, 12446);
	g(d, 13312, 19903);
	g(d, 19968, 40959);
	g(d, 173824, 177983);
	g(d, 177984, 178207);
	g(d, 194560, 195103);
	g(d, 12293, 12293);
	g(d, 12347, 12347);
	twttr.txt.regexen.nonLatinHashtagChars = c(d.join(""));
	twttr.txt.regexen.latinAccentChars = c("\u00c3\u20ac\u00c3\u0081\u00c3\u201a\u00c3\u0192\u00c3\u201e\u00c3\u2026\u00c3\u2020\u00c3\u2021\u00c3\u02c6\u00c3\u2030\u00c3\u0160\u00c3\u2039\u00c3\u0152\u00c3\u008d\u00c3\u017d\u00c3\u008f\u00c3\u0090\u00c3\u2018\u00c3\u2019\u00c3\u201c\u00c3\u201d\u00c3\u2022\u00c3\u2013\u00c3\u02dc\u00c3\u2122\u00c3\u0161\u00c3\u203a\u00c3\u0153\u00c3\u009d\u00c3\u017e\u00c3\u0178\u00c3 \u00c3\u00a1\u00c3\u00a2\u00c3\u00a3\u00c3\u00a4\u00c3\u00a5\u00c3\u00a6\u00c3\u00a7\u00c3\u00a8\u00c3\u00a9\u00c3\u00aa\u00c3\u00ab\u00c3\u00ac\u00c3\u00ad\u00c3\u00ae\u00c3\u00af\u00c3\u00b0\u00c3\u00b1\u00c3\u00b2\u00c3\u00b3\u00c3\u00b4\u00c3\u00b5\u00c3\u00b6\u00c3\u00b8\u00c3\u00b9\u00c3\u00ba\u00c3\u00bb\u00c3\u00bc\u00c3\u00bd\u00c3\u00be\u00c5\u0178\\303\\277");
	twttr.txt.regexen.endScreenNameMatch = c(/^(?:#{atSigns}|[#{latinAccentChars}]|:\/\/)/);
	twttr.txt.regexen.hashtagBoundary = c(/(?:^|$|#{spaces}|[\u00e3\u20ac\u0152\u00e3\u20ac\u008d\u00e3\u20ac\u201a\u00e3\u20ac\u0081.,!\u00ef\u00bc\u0081?\u00ef\u00bc\u0178:;"'])/);
	twttr.txt.regexen.hashtagAlpha = c(/[a-z_#{latinAccentChars}#{nonLatinHashtagChars}]/i);
	twttr.txt.regexen.hashtagAlphaNumeric = c(/[a-z0-9_#{latinAccentChars}#{nonLatinHashtagChars}]/i);
	twttr.txt.regexen.autoLinkHashtags = c(/(#{hashtagBoundary})(#|\u00ef\u00bc\u0192)(#{hashtagAlphaNumeric}*#{hashtagAlpha}#{hashtagAlphaNumeric}*)/gi);
	twttr.txt.regexen.autoLinkUsernamesOrLists = /(^|[^a-zA-Z0-9_]|RT:?)([@\u00ef\u00bc ]+)([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9_\-]{0,24})?/g;
	twttr.txt.regexen.autoLinkEmoticon = /(8\-\#|8\-E|\+\-\(|\`\@|\`O|\&lt;\|:~\(|\}:o\{|:\-\[|\&gt;o\&lt;|X\-\/|\[:-\]\-I\-|\/\/\/\/\u00c3\u2013\\\\\\\\|\(\|:\|\/\)|\u00e2\u02c6\u2018:\*\)|\( \| \))/g;
	twttr.txt.regexen.validPrecedingChars = c(/(?:[^-\/"'!=A-Za-z0-9_@\u00ef\u00bc \.]|^)/);
	twttr.txt.regexen.invalidDomainChars = k("\u00a0#{punct}#{spaces_group}", twttr.txt.regexen);
	twttr.txt.regexen.validDomainChars = c(/[^#{invalidDomainChars}]/);
	twttr.txt.regexen.validSubdomain = c(/(?:(?:#{validDomainChars}(?:[_-]|#{validDomainChars})*)?#{validDomainChars}\.)/);
	twttr.txt.regexen.validDomainName = c(/(?:(?:#{validDomainChars}(?:-|#{validDomainChars})*)?#{validDomainChars}\.)/);
	twttr.txt.regexen.validGTLD = c(/(?:(?:aero|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel)(?=[^a-zA-Z]|$))/);
	twttr.txt.regexen.validCCTLD = c(/(?:(?:ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|ss|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|za|zm|zw)(?=[^a-zA-Z]|$))/);
	twttr.txt.regexen.validPunycode = c(/(?:xn--[0-9a-z]+)/);
	twttr.txt.regexen.validDomain = c(/(?:#{validSubdomain}*#{validDomainName}(?:#{validGTLD}|#{validCCTLD}|#{validPunycode}))/);
	twttr.txt.regexen.validShortDomain = c(/^#{validDomainName}#{validCCTLD}$/);
	twttr.txt.regexen.validPortNumber = c(/[0-9]+/);
	twttr.txt.regexen.validGeneralUrlPathChars = c(/[a-z0-9!\*';:=\+\$\/%#\[\]\-_,~|&#{latinAccentChars}]/i);
	twttr.txt.regexen.wikipediaDisambiguation = c(/(?:\(#{validGeneralUrlPathChars}+\))/i);
	twttr.txt.regexen.validUrlPathChars = c(/(?:#{wikipediaDisambiguation}|@#{validGeneralUrlPathChars}+\/|[\.,]?#{validGeneralUrlPathChars}?)/i);
	twttr.txt.regexen.validUrlPathEndingChars = c(/(?:[\+\-a-z0-9=_#\/#{latinAccentChars}]|#{wikipediaDisambiguation})/i);
	twttr.txt.regexen.validUrlQueryChars = /[a-z0-9!\*'\(\);:&=\+\$\/%#\[\]\-_\.,~|]/i;
	twttr.txt.regexen.validUrlQueryEndingChars = /[a-z0-9_&=#\/]/i;
	twttr.txt.regexen.extractUrl = c("((#{validPrecedingChars})((https?:\\/\\/)?(#{validDomain})(?::(#{validPortNumber}))?(\\/(?:#{validUrlPathChars}+#{validUrlPathEndingChars}|#{validUrlPathChars}+#{validUrlPathEndingChars}?|#{validUrlPathEndingChars})?)?(\\?#{validUrlQueryChars}*#{validUrlQueryEndingChars})?))", "gi");
	twttr.txt.regexen.validateUrlUnreserved = /[a-z0-9\-._~]/i;
	twttr.txt.regexen.validateUrlPctEncoded = /(?:%[0-9a-f]{2})/i;
	twttr.txt.regexen.validateUrlSubDelims = /[!$&'()*+,;=]/i;
	twttr.txt.regexen.validateUrlPchar = c("(?:#{validateUrlUnreserved}|#{validateUrlPctEncoded}|#{validateUrlSubDelims}|[:|@])", "i");
	twttr.txt.regexen.validateUrlScheme = /(?:[a-z][a-z0-9+\-.]*)/i;
	twttr.txt.regexen.validateUrlUserinfo = c("(?:#{validateUrlUnreserved}|#{validateUrlPctEncoded}|#{validateUrlSubDelims}|:)*", "i");
	twttr.txt.regexen.validateUrlDecOctet = /(?:[0-9]|(?:[1-9][0-9])|(?:1[0-9]{2})|(?:2[0-4][0-9])|(?:25[0-5]))/i;
	twttr.txt.regexen.validateUrlIpv4 = c(/(?:#{validateUrlDecOctet}(?:\.#{validateUrlDecOctet}){3})/i);
	twttr.txt.regexen.validateUrlIpv6 = /(?:\[[a-f0-9:\.]+\])/i;
	twttr.txt.regexen.validateUrlIp = c("(?:#{validateUrlIpv4}|#{validateUrlIpv6})", "i");
	twttr.txt.regexen.validateUrlSubDomainSegment = /(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)/i;
	twttr.txt.regexen.validateUrlDomainSegment = /(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)/i;
	twttr.txt.regexen.validateUrlDomainTld = /(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)/i;
	twttr.txt.regexen.validateUrlDomain = c(/(?:(?:#{validateUrlSubDomainSegment]}\.)*(?:#{validateUrlDomainSegment]}\.)#{validateUrlDomainTld})/i);
	twttr.txt.regexen.validateUrlHost = c("(?:#{validateUrlIp}|#{validateUrlDomain})", "i");
	twttr.txt.regexen.validateUrlUnicodeSubDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9_\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	twttr.txt.regexen.validateUrlUnicodeDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	twttr.txt.regexen.validateUrlUnicodeDomainTld = /(?:(?:[a-z]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
	twttr.txt.regexen.validateUrlUnicodeDomain = c(/(?:(?:#{validateUrlUnicodeSubDomainSegment}\.)*(?:#{validateUrlUnicodeDomainSegment}\.)#{validateUrlUnicodeDomainTld})/i);
	twttr.txt.regexen.validateUrlUnicodeHost = c("(?:#{validateUrlIp}|#{validateUrlUnicodeDomain})", "i");
	twttr.txt.regexen.validateUrlPort = /[0-9]{1,5}/;
	twttr.txt.regexen.validateUrlUnicodeAuthority = c("(?:(#{validateUrlUserinfo})@)?(#{validateUrlUnicodeHost})(?::(#{validateUrlPort}))?", "i");
	twttr.txt.regexen.validateUrlAuthority = c("(?:(#{validateUrlUserinfo})@)?(#{validateUrlHost})(?::(#{validateUrlPort}))?", "i");
	twttr.txt.regexen.validateUrlPath = c(/(\/#{validateUrlPchar}*)*/i);
	twttr.txt.regexen.validateUrlQuery = c(/(#{validateUrlPchar}|\/|\?)*/i);
	twttr.txt.regexen.validateUrlFragment = c(/(#{validateUrlPchar}|\/|\?)*/i);
	twttr.txt.regexen.validateUrlUnencoded = c("^(?:([^:/?#]+):\\/\\/)?([^/?#]*)([^?#]*)(?:\\?([^#]*))?(?:#(.*))?$", "i");
	twttr.txt.autoLink = function (b, a){
		a = m(a || {});
		return twttr.txt.autoLinkUsernamesOrLists(twttr.txt.autoLinkUrlsCustom(twttr.txt.autoLinkHashtags(b, a), a), a)
	};
	twttr.txt.autoLinkUsernamesOrLists = function (b, a){
		a = m(a || {});
		a.urlClass = a.urlClass || "tweet-url";
		a.listClass = a.listClass || "list-slug";
		a.usernameClass = a.usernameClass || "username";
		a.usernameUrlBase = a.usernameUrlBase || "http://twitter.com/";
		a.listUrlBase = a.listUrlBase || "http://twitter.com/";
		if (!a.suppressNoFollow)var f = ' rel="nofollow"';
		for (var e = "", c = twttr.txt.splitTags(b), l = 0; l<c.length; l++){
			var w = c[l];
			l!==0 && (e = e+(l%2===0 ? ">" : "<"));
			e = l%4!==0 ? e+w : e+w.replace(twttr.txt.regexen.autoLinkUsernamesOrLists, function (b, e, c, l, w, t, g){
				var t = g.slice(t+b.length), e = {before: e, at: c, user: twttr.txt.htmlEscape(l), slashListname: twttr.txt.htmlEscape(w), extraHtml: f, preChunk: "", chunk: twttr.txt.htmlEscape(g), postChunk: ""}, d;
				for (d in a)a.hasOwnProperty(d) && (e[d] = a[d]);
				if (w && !a.suppressLists){
					b = e.chunk = k("#{user}#{slashListname}", e);
					e.list = twttr.txt.htmlEscape(b.toLowerCase());
					return k('#{before}#{at}<a class="#{urlClass} #{listClass}" href="#{listUrlBase}#{list}"#{extraHtml}>#{preChunk}#{chunk}#{postChunk}</a>', e)
				}
				if (t && t.match(twttr.txt.regexen.endScreenNameMatch))return b;
				e.chunk = twttr.txt.htmlEscape(l);
				e.dataScreenName = !a.suppressDataScreenName ? k('data-screen-name="#{chunk}" ', e) : "";
				return k('#{before}#{at}<a class="#{urlClass} #{usernameClass}" #{dataScreenName}href="#{usernameUrlBase}#{chunk}"#{extraHtml}>#{preChunk}#{chunk}#{postChunk}</a>', e)
			})
		}
		return e
	};
	twttr.txt.autoLinkHashtags = function (b, a){
		a = m(a || {});
		a.urlClass = a.urlClass || "tweet-url";
		a.hashtagClass = a.hashtagClass || "hashtag";
		a.hashtagUrlBase = a.hashtagUrlBase || "http://twitter.com/search?q=%23";
		if (!a.suppressNoFollow)var f = ' rel="nofollow"';
		return b.replace(twttr.txt.regexen.autoLinkHashtags, function (b, c, l, w){
			var b = {before: c, hash: twttr.txt.htmlEscape(l), preText: "", text: twttr.txt.htmlEscape(w), postText: "", extraHtml: f}, d;
			for (d in a)a.hasOwnProperty(d) && (b[d] = a[d]);
			return k('#{before}<a href="#{hashtagUrlBase}#{text}" title="##{text}" class="#{urlClass} #{hashtagClass}"#{extraHtml}>#{hash}#{preText}#{text}#{postText}</a>', b)
		})
	};
	twttr.txt.autoLinkUrlsCustom = function (b, a){
		a = m(a || {});
		if (!a.suppressNoFollow)a.rel = "nofollow";
		if (a.urlClass){
			a["class"] = a.urlClass;
			delete a.urlClass
		}
		var f, e, c;
		if (a.urlEntities){
			f = {};
			e = 0;
			for (c = a.urlEntities.length; e<c; e++)f[a.urlEntities[e].url] = a.urlEntities[e]
		}
		delete a.suppressNoFollow;
		delete a.suppressDataScreenName;
		delete a.listClass;
		delete a.usernameClass;
		delete a.usernameUrlBase;
		delete a.listUrlBase;
		return b.replace(twttr.txt.regexen.extractUrl, function (b, e, c, d, g){
			if (g){
				var b = "", t;
				for (t in a)b = b+k(' #{k}="#{v}" ', {k: t, v: a[t].toString().replace(/"/, "&quot;").replace(/</, "&lt;").replace(/>/, "&gt;")});
				c = {before: c, htmlAttrs: b, url: twttr.txt.htmlEscape(d)};
				c.displayUrl = f && f[d] && f[d].display_url ? twttr.txt.htmlEscape(f[d].display_url) : c.url;
				return k('#{before}<a href="#{url}"#{htmlAttrs}>#{displayUrl}</a>', c)
			}
			return e
		})
	};
	twttr.txt.extractMentions = function (b){
		for (var a = [], b = twttr.txt.extractMentionsWithIndices(b), f = 0; f<b.length; f++)a.push(b[f].screenName);
		return a
	};
	twttr.txt.extractMentionsWithIndices = function (b){
		if (!b)return[];
		var a = [], f = 0;
		b.replace(twttr.txt.regexen.extractMentions, function (e, c, d, g, i){
			if (!i.match(twttr.txt.regexen.endScreenNameMatch)){
				e = b.indexOf(d+g, f);
				f = e+g.length+1;
				a.push({screenName: g, indices: [e, f]})
			}
		});
		return a
	};
	twttr.txt.extractMentionsOrListsWithIndices = function (b){
		if (!b)return[];
		var a = [], f = 0;
		b.replace(twttr.txt.regexen.extractMentionsOrLists, function (e, c, d, g, i, h){
			if (!h.match(twttr.txt.regexen.endScreenNameMatch)){
				i = i || "";
				e = b.indexOf(d+g+i, f);
				f = e+g.length+i.length+1;
				a.push({screenName: g, listSlug: i, indices: [e, f]})
			}
		});
		return a
	};
	twttr.txt.extractReplies = function (b){
		if (!b)return null;
		b = b.match(twttr.txt.regexen.extractReply);
		return!b ? null : b[1]
	};
	twttr.txt.extractUrls = function (b){
		for (var a = [], b = twttr.txt.extractUrlsWithIndices(b), f = 0; f<b.length; f++)a.push(b[f].url);
		return a
	};
	twttr.txt.extractUrlsWithIndices = function (b){
		if (!b)return[];
		var a = [];
		b.replace(twttr.txt.regexen.extractUrl, function (f, e, c, d, g, i, h, q){
			if (g || q || !i.match(twttr.txt.regexen.validShortDomain)){
				var f = b.indexOf(d, o), o = f+d.length;
				a.push({url: d, indices: [f, o]})
			}
		});
		return a
	};
	twttr.txt.extractHashtags = function (b){
		for (var a = [], b = twttr.txt.extractHashtagsWithIndices(b), f = 0; f<b.length; f++)a.push(b[f].hashtag);
		return a
	};
	twttr.txt.extractHashtagsWithIndices = function (b){
		if (!b)return[];
		var a = [], f = 0;
		b.replace(twttr.txt.regexen.autoLinkHashtags, function (e, c, d, g){
			e = b.indexOf(d+g, f);
			f = e+g.length+1;
			a.push({hashtag: g, indices: [e, f]})
		});
		return a
	};
	twttr.txt.splitTags = function (b){
		for (var b = b.split("<"), a, f = [], e = 0; e<b.length; e = e+1)if (a = b[e]){
			a = a.split(">");
			for (var c = 0; c<a.length; c = c+1)f.push(a[c])
		} else f.push("");
		return f
	};
	twttr.txt.hitHighlight = function (b, a, f){
		a = a || [];
		f = f || {};
		if (a.length===0)return b;
		var f = f.tag || "em", f = ["<"+f+">", "</"+f+">"], b = twttr.txt.splitTags(b), e, c, d = "", g = 0, i = b[0], h = 0, q = 0, o = false, k = i, n = [], s;
		for (e = 0; e<a.length; e = e+1)for (c = 0; c<a[e].length; c = c+1)n.push(a[e][c]);
		for (a = 0; a<n.length; a = a+1){
			c = n[a];
			e = f[a%2];
			for (s = false; i!=null && c>=h+i.length;){
				d = d+k.slice(q);
				if (o && c===h+k.length){
					d = d+e;
					s = true
				}
				b[g+1] && (d = d+("<"+b[g+1]+">"));
				h = h+k.length;
				q = 0;
				g = g+2;
				k = i = b[g];
				o = false
			}
			if (!s && i!=null){
				o = c-h;
				d = d+(k.slice(q, o)+e);
				q = o;
				o = a%2===0 ? true : false
			} else s || (d = d+e)
		}
		if (i!=null){
			q<k.length && (d = d+k.slice(q));
			for (a = g+1; a<b.length; a = a+1)d = d+(a%2===0 ? b[a] : "<"+b[a]+">")
		}
		return d
	};
	var x = [h(65534), h(65279), h(65535), h(8234), h(8235), h(8236), h(8237), h(8238)];
	twttr.txt.isInvalidTweet = function (b){
		if (!b)return"empty";
		if (b.length>140)return"too_long";
		for (var a = 0; a<x.length; a++)if (b.indexOf(x[a])>=0)return"invalid_characters";
		return false
	};
	twttr.txt.isValidTweetText = function (b){
		return!twttr.txt.isInvalidTweet(b)
	};
	twttr.txt.isValidUsername = function (b){
		if (!b)return false;
		var a = twttr.txt.extractMentions(b);
		return a.length===1 && a[0]===b.slice(1)
	};
	var i = c(/^#{autoLinkUsernamesOrLists}$/);
	twttr.txt.isValidList = function (b){
		b = b.match(i);
		return!(!b || !(b[1]=="" && b[4]))
	};
	twttr.txt.isValidHashtag = function (b){
		if (!b)return false;
		var a = twttr.txt.extractHashtags(b);
		return a.length===1 && a[0]===b.slice(1)
	};
	twttr.txt.isValidUrl = function (b, a, c){
		a==null && (a = true);
		c==null && (c = true);
		if (!b)return false;
		var e = b.match(twttr.txt.regexen.validateUrlUnencoded);
		if (!e || e[0]!==b)return false;
		var b = e[1], d = e[2], g = e[3], i = e[4], e = e[5];
		return c && (!n(b, twttr.txt.regexen.validateUrlScheme) || !b.match(/^https?$/i)) || !n(g, twttr.txt.regexen.validateUrlPath) || !n(i, twttr.txt.regexen.validateUrlQuery, true) || !n(e, twttr.txt.regexen.validateUrlFragment, true) ? false : a && n(d, twttr.txt.regexen.validateUrlUnicodeAuthority) || !a && n(d, twttr.txt.regexen.validateUrlAuthority)
	};
	if (typeof module!="undefined" && module.exports)module.exports = twttr.txt
})();
TWTR = window.TWTR || {};
(function (){
	function c(c, b, a){
		for (var f = 0, e = c.length; f<e; ++f)b.call(a || window, c[f], f, c)
	}

	function k(c, b, a){
		(Array.prototype.filter || function (a, b){
			for (var c = b || window, d = [], g = 0, i = this.length; g<i; ++g)a.call(c, this[g], g, this) && d.push(this[g]);
			return d
		}).call(c, b, a)
	}

	function g(c, b, a){
		this.el = c;
		this.prop = b;
		this.from = a.from;
		this.to = a.to;
		this.time = a.time;
		this.callback = a.callback;
		this.animDiff = this.to-this.from
	}

	function m(c){
		if (!twttr.widgets){
			for (var c = c || window.event, b = c.target || c.srcElement, a, f, e; b && b.nodeName.toLowerCase()!=="a";)b = b.parentNode;
			if (b && b.nodeName.toLowerCase()==="a" && b.href)if (a = b.href.match(n)){
				a = a[2]in v ? 420 : 560;
				f = Math.round(x/2-275);
				e = 0;
				d>a && (e = Math.round(d/2-a/2));
				window.open(b.href, "intent", h+",width=550,height="+a+",left="+f+",top="+e);
				c.returnValue = false;
				c.preventDefault && c.preventDefault()
			}
		}
	}

	if (!TWTR || !TWTR.Widget){
		g.canTransition = function (){
			var c = document.createElement("twitter");
			c.style.cssText = "-webkit-transition: all .5s linear;";
			return!!c.style.webkitTransitionProperty
		}();
		g.prototype._setStyle = function (c){
			switch (this.prop) {
				case "opacity":
					this.el.style[this.prop] = c;
					this.el.style.filter = "alpha(opacity="+c*100+")";
					break;
				default:
					this.el.style[this.prop] = c+"px"
			}
		};
		g.prototype._animate = function (){
			this.now = new Date;
			this.diff = this.now-this.startTime;
			if (this.diff>this.time){
				this._setStyle(this.to);
				this.callback && this.callback.call(this);
				clearInterval(this.timer)
			} else {
				this.percentage = Math.floor(this.diff/this.time*100)/100;
				this.val = this.animDiff*this.percentage+this.from;
				this._setStyle(this.val)
			}
		};
		g.prototype.start = function (){
			var c = this;
			this.startTime = new Date;
			this.timer = setInterval(function (){
				c._animate.call(c)
			}, 15)
		};
		TWTR.Widget = function (c){
			this.init(c)
		};
		(function (){
			function d(a, b, c){
				this.job = a;
				this.decayFn = b;
				this.interval = c;
				this.decayRate = 1;
				this.decayMultiplier = 1.25;
				this.maxDecayTime = 18E4
			}

			function b(a, b, c){
				this.time = a || 6E3;
				this.loop = b || false;
				this.repeated = 0;
				this.callback = c;
				this.haystack = []
			}

			function a(p){
				var p = '<div class="twtr-tweet-wrap"> <div class="twtr-avatar"> <div class="twtr-img"><a target="_blank" href="https://twitter.com/intent/user?screen_name='+p.user+'"><img alt="'+p.user+' profile" src="'+p.avatar+'"></a></div> </div> <div class="twtr-tweet-text"> <p> <a target="_blank" href="https://twitter.com/intent/user?screen_name='+p.user+'" class="twtr-user">'+p.user+"</a> "+p.tweet+' <em> <a target="_blank" class="twtr-timestamp" time="'+p.timestamp+'" href="https://twitter.com/'+p.user+"/status/"+p.id+'">'+p.created_at+'</a> &middot; <a target="_blank" class="twtr-reply" href="https://twitter.com/intent/tweet?in_reply_to='+p.id+'">reply</a> &middot; <a target="_blank" class="twtr-rt" href="https://twitter.com/intent/retweet?tweet_id='+p.id+'">retweet</a> &middot; <a target="_blank" class="twtr-fav" href="https://twitter.com/intent/favorite?tweet_id='+p.id+'">favorite</a> </em> </p> </div> </div>', b = document.createElement("div");
				b.id = "tweet-id-"+ ++a._tweetCount;
				b.className = "twtr-tweet";
				b.innerHTML = p;
				this.element = b
			}

			var f = window.twttr || {}, e = location.protocol.match(/^https/), h = function (a){
				return e ? a.profile_image_url_https : a.profile_image_url
			}, l = {}, n = function (a, b, c, d){
				var c = c || document, f = [], b = c.getElementsByTagName(b || "*"), c = l[a];
				if (!c){
					c = RegExp("(?:^|\\s+)"+a+"(?:\\s+|$)");
					l[a] = c
				}
				for (var a = c, c = 0, e = b.length; c<e; ++c)if (a.test(b[c].className)){
					f[f.length] = b[c];
					d && d.call(b[c], b[c])
				}
				return f
			}, u = navigator.userAgent.match(/MSIE\s([^;]*)/), m = function (a){
				return typeof a=="string" ? document.getElementById(a) : a
			}, q = function (){
				var a = self.innerHeight, b = document.compatMode;
				if (b || u)a = b=="CSS1Compat" ? document.documentElement.clientHeight : document.body.clientHeight;
				return a
			}, o = function (a){
				try {
					a.parentNode.removeChild(a)
				} catch (b) {
				}
			}, x = function (a){
				var b, c = a.relatedTarget;
				if (!c)if (a.type=="mouseout")c = a.toElement; else if (a.type=="mouseover")c = a.fromElement;
				a:{
					try {
						b = c && 3==c.nodeType ? c.parentNode : c;
						break a
					} catch (d) {
					}
					b = void 0
				}
				for (; b && b!=this;)try {
					b = b.parentNode
				} catch (f) {
					b = this
				}
				return b!=this ? true : false
			}, v = function (){
				if (document.defaultView && document.defaultView.getComputedStyle)return function (a, b){
					var c = null, d = document.defaultView.getComputedStyle(a, "");
					d && (c = d[b]);
					return a.style[b] || c
				};
				if (document.documentElement.currentStyle && u)return function (a, b){
					var c = a.currentStyle ? a.currentStyle[b] : null;
					return a.style[b] || c
				}
			}(), s = {has: function (a, b){
				return RegExp("(^|\\s)"+b+"(\\s|$)").test(m(a).className)
			}, add: function (a, b){
				if (!this.has(a, b)){
					var c = m(a), d;
					d = m(a).className.replace(/^\s+|\s+$/g, "");
					c.className = d+" "+b
				}
			}, remove: function (a, b){
				if (this.has(a, b))m(a).className = m(a).className.replace(RegExp("(^|\\s)"+b+"(\\s|$)", "g"), "")
			}}, y = {add: function (a, b, c){
				a.addEventListener ? a.addEventListener(b, c, false) : a.attachEvent("on"+b, function (){
					c.call(a, window.event)
				})
			}, remove: function (a, b, c){
				a.removeEventListener ? a.removeEventListener(b, c, false) : a.detachEvent("on"+b, c)
			}}, C = function (){
				return function (a){
					return[parseInt(a.substring(0, 2), 16), parseInt(a.substring(2, 4), 16), parseInt(a.substring(4, 6), 16)]
				}
			}(), j = {bool: function (a){
				return typeof a==="boolean"
			}, def: function (a){
				return typeof a!=="undefined"
			}, number: function (a){
				return typeof a==="number" && isFinite(a)
			}, string: function (a){
				return typeof a==="string"
			}, fn: function (a){
				return typeof a==="function"
			}, array: function (a){
				return a ? j.number(a.length) && j.fn(a.splice) : false
			}}, z = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], D = function (a){
				var b = new Date(a);
				u && (b = Date.parse(a.replace(/( \+)/, " UTC$1")));
				var c = "", a = function (){
					var a = b.getHours();
					if (a>0 && a<13){
						c = "am";
						return a
					}
					if (a<1){
						c = "am";
						return 12
					}
					c = "pm";
					return a-12
				}(), d = b.getMinutes();
				b.getSeconds();
				return a+":"+d+c+function (){
					var a = new Date;
					return a.getDate()!=b.getDate() || a.getYear()!=b.getYear() || a.getMonth()!=b.getMonth() ? " - "+z[b.getMonth()]+" "+b.getDate()+", "+b.getFullYear() : ""
				}()
			}, A = function (a){
				var b = new Date, c = new Date(a);
				u && (c = Date.parse(a.replace(/( \+)/, " UTC$1")));
				a = b-c;
				return isNaN(a) || a<0 ? "" : a<2E3 ? "right now" : a<6E4 ? Math.floor(a/1E3)+" seconds ago" : a<12E4 ? "about 1 minute ago" : a<36E5 ? Math.floor(a/6E4)+" minutes ago" : a<72E5 ? "about 1 hour ago" : a<864E5 ? Math.floor(a/36E5)+" hours ago" : a>864E5 && a<1728E5 ? "yesterday" : a<31536E6 ? Math.floor(a/864E5)+" days ago" : "over a year ago"
			};
			f.txt.autoLink = function (a, b){
				b = options_links = b || {};
				if (b.hasOwnProperty("extraHtml")){
					var c = b, d = {}, e;
					for (e in c)c.hasOwnProperty(e) && (d[e] = c[e]);
					options_links = d;
					delete options_links.extraHtml
				}
				return f.txt.autoLinkUsernamesOrLists(f.txt.autoLinkUrlsCustom(f.txt.autoLinkHashtags(a, b), options_links), b)
			};
			TWTR.Widget.ify = {autoLink: function (a){
				options = {extraHtml: "target=_blank", target: "_blank", urlEntities: []};
				if (a.needle.entities){
					if (a.needle.entities.urls)options.urlEntities = a.needle.entities.urls;
					if (a.needle.entities.media)options.urlEntities = options.urlEntities.concat(a.needle.entities.media)
				}
				return f && f.txt ? f.txt.autoLink(a.needle.text, options).replace(/([@\ufffd ]+)(<[^>]*>)/g, "$2$1") : a.needle.text
			}};
			d.prototype = {start: function (){
				this.stop().run();
				return this
			}, stop: function (){
				this.worker && window.clearTimeout(this.worker);
				return this
			}, run: function (){
				var a = this;
				this.job(function (){
					a.decayRate = a.decayFn() ? Math.max(1, a.decayRate/a.decayMultiplier) : a.decayRate*a.decayMultiplier;
					var b = a.interval*a.decayRate, b = b>=a.maxDecayTime ? a.maxDecayTime : b, b = Math.floor(b);
					a.worker = window.setTimeout(function (){
						a.run.call(a)
					}, b)
				})
			}, destroy: function (){
				this.stop();
				this.decayRate = 1;
				return this
			}};
			b.prototype = {set: function (a){
				this.haystack = a
			}, add: function (a){
				this.haystack.unshift(a)
			}, start: function (){
				if (this.timer)return this;
				this._job();
				var a = this;
				this.timer = setInterval(function (){
					a._job.call(a)
				}, this.time);
				return this
			}, stop: function (){
				if (this.timer){
					window.clearInterval(this.timer);
					this.timer = null
				}
				return this
			}, _next: function (){
				var a = this.haystack.shift();
				a && this.loop && this.haystack.push(a);
				return a || null
			}, _job: function (){
				var a = this._next();
				a && this.callback(a);
				return this
			}};
			a._tweetCount = 0;
			f.loadStyleSheet = function (a, b){
				if (!TWTR.Widget.loadingStyleSheet){
					TWTR.Widget.loadingStyleSheet = true;
					var c = document.createElement("link");
					c.href = a;
					c.rel = "stylesheet";
					c.type = "text/css";
					document.getElementsByTagName("head")[0].appendChild(c);
					var d = setInterval(function (){
						if (v(b, "position")=="relative"){
							clearInterval(d);
							d = null;
							TWTR.Widget.hasLoadedStyleSheet = true
						}
					}, 50)
				}
			};
			(function (){
				var a = false;
				f.css = function (b){
					var c = document.createElement("style");
					c.type = "text/css";
					if (u)c.styleSheet.cssText = b; else {
						var d = document.createDocumentFragment();
						d.appendChild(document.createTextNode(b));
						c.appendChild(d)
					}
					!u || a ? document.getElementsByTagName("head")[0].appendChild(c) : window.attachEvent("onload", function (){
						a = true;
						document.getElementsByTagName("head")[0].appendChild(c)
					})
				}
			})();
			TWTR.Widget.isLoaded = false;
			TWTR.Widget.loadingStyleSheet = false;
			TWTR.Widget.hasLoadedStyleSheet = false;
			TWTR.Widget.WIDGET_NUMBER = 0;
			TWTR.Widget.REFRESH_MIN = 6E3;
			TWTR.Widget.ENTITY_RANGE = 100;
			TWTR.Widget.ENTITY_PERCENTAGE = 100;
			TWTR.Widget.matches = {mentions: /^@[a-zA-Z0-9_]{1,20}\b/, any_mentions: /\b@[a-zA-Z0-9_]{1,20}\b/};
			TWTR.Widget.jsonP = function (a, b){
				var c = document.createElement("script"), d = document.getElementsByTagName("head")[0];
				c.type = "text/javascript";
				c.src = a;
				d.insertBefore(c, d.firstChild);
				b(c);
				return c
			};
			TWTR.Widget.randomNumber = function (a){
				return r = Math.floor(Math.random()*a)
			};
			TWTR.Widget.SHOW_ENTITIES = TWTR.Widget.randomNumber(TWTR.Widget.ENTITY_RANGE)<=TWTR.Widget.ENTITY_PERCENTAGE;
			TWTR.Widget.prototype = function (){
				var e = window.twttr || {}, f = twidget_proxy+"/search.", l = twidget_proxy+"/1/statuses/user_timeline.", v = twidget_proxy+"/1/favorites.", z = twidget_proxy+"/1/", B = twidget_rien;
				return{init: function (a){
					var c = this;
					this._widgetNumber = ++TWTR.Widget.WIDGET_NUMBER;
					TWTR.Widget["receiveCallback_"+this._widgetNumber] = function (a){
						c._prePlay.call(c, a)
					};
					this._cb = "TWTR.Widget.receiveCallback_"+this._widgetNumber;
					this.opts = a;
					this._base = f;
					this._profileImage = this._rendered = this._hasNewSearchResults = this._hasOfficiallyStarted = this._isRunning = false;
					this._isCreator = !!a.creator;
					this._setWidgetType(a.type);
					this.timesRequested = 0;
					this.newResults = this.runOnce = false;
					this.results = [];
					this.jsonMaxRequestTimeOut = 19E3;
					this.showedResults = [];
					this.sinceId = 1;
					this.source = "TWITTERINC_WIDGET";
					this.id = a.id || "twtr-widget-"+this._widgetNumber;
					this.tweets = 0;
					this.setDimensions(a.width, a.height);
					this.interval = a.interval ? Math.max(a.interval, TWTR.Widget.REFRESH_MIN) : TWTR.Widget.REFRESH_MIN;
					this.format = "json";
					this.rpp = a.rpp || 50;
					this.subject = a.subject || "";
					this.title = a.title || "";
					this.setFooterText(a.footer);
					this.setSearch(a.search);
					this._setUrl();
					this.theme = a.theme ? a.theme : this._getDefaultTheme();
					a.id || document.write('<div class="twtr-widget" id="'+this.id+'"></div>');
					this.widgetEl = m(this.id);
					a.id && s.add(this.widgetEl, "twtr-widget");
					a.version>=2 && !TWTR.Widget.hasLoadedStyleSheet && e.loadStyleSheet(twidget_css, this.widgetEl);
					this.occasionalJob = new d(function (a){
						c.decay = a;
						c._getResults.call(c)
					}, function (){
						return c._decayDecider.call(c)
					}, 25E3);
					this._ready = j.fn(a.ready) ? a.ready : function (){
					};
					this._isRelativeTime = true;
					this._tweetFilter = false;
					this._avatars = true;
					this._isFullScreen = false;
					this._isLive = true;
					this._isScroll = false;
					this._loop = true;
					this._behavior = "default";
					this.setFeatures(this.opts.features);
					this.intervalJob = new b(this.interval, this._loop, function (a){
						c._normalizeTweet(a)
					});
					return this
				}, setDimensions: function (a, b){
					this.wh = a && b ? [a, b] : [250, 300];
					this.wh[0] = a=="auto" || a=="100%" ? "100%" : (this.wh[0]<150 ? 150 : this.wh[0])+"px";
					this.wh[1] = (this.wh[1]<100 ? 100 : this.wh[1])+"px";
					return this
				}, setRpp: function (a){
					a = parseInt(a);
					this.rpp = j.number(a) && a>0 && a<=100 ? a : 30;
					return this
				}, _setWidgetType: function (a){
					this._isListWidget = this._isFavsWidget = this._isProfileWidget = this._isSearchWidget = false;
					switch (a) {
						case "profile":
							this._isProfileWidget = true;
							break;
						case "search":
							this._isSearchWidget = true;
							this.search = this.opts.search;
							break;
						case "faves":
						case "favs":
							this._isFavsWidget = true;
							break;
						case "list":
						case "lists":
							this._isListWidget = true
					}
					return this
				}, setFeatures: function (a){
					if (a){
						if (j.def(a.filters))this._tweetFilter = a.filters;
						if (j.def(a.dateformat))this._isRelativeTime = a.dateformat!=="absolute";
						if (j.def(a.fullscreen) && j.bool(a.fullscreen) && a.fullscreen){
							this._isFullScreen = true;
							this.wh[0] = "100%";
							this.wh[1] = q()-90+"px";
							var b = this;
							y.add(window, "resize", function (){
								b.wh[1] = q();
								b._fullScreenResize()
							})
						}
						if (j.def(a.loop) && j.bool(a.loop))this._loop = a.loop;
						if (j.def(a.behavior) && j.string(a.behavior))switch (a.behavior) {
							case "all":
								this._behavior = "all";
								break;
							case "preloaded":
								this._behavior = "preloaded";
								break;
							default:
								this._behavior = "default"
						}
						if (j.def(a.avatars) && j.bool(a.avatars))if (a.avatars){
							e.css("#"+this.id+" .twtr-avatar { display: block; } #"+this.id+" .twtr-user { display: inline; } #"+this.id+" .twtr-tweet-text { margin-left: "+(this._isFullScreen ? "90px" : "40px")+"; }");
							this._avatars = true
						} else {
							e.css("#"+this.id+" .twtr-avatar { display: none; } #"+this.id+" .twtr-tweet-text { margin-left: 0; }");
							this._avatars = false
						} else if (this._isProfileWidget){
							this.setFeatures({avatars: false});
							this._avatars = false
						} else {
							this.setFeatures({avatars: true});
							this._avatars = true
						}
						if (j.def(a.live) && j.bool(a.live))this._isLive = a.live;
						if (j.def(a.scrollbar) && j.bool(a.scrollbar))this._isScroll = a.scrollbar
					} else if (this._isProfileWidget || this._isFavsWidget)this._behavior = "all";
					return this
				}, _fullScreenResize: function (){
					n("twtr-timeline", "div", document.body, function (a){
						a.style.height = q()-90+"px"
					})
				}, setTweetInterval: function (a){
					this.interval = a;
					return this
				}, setBase: function (a){
					this._base = a;
					return this
				}, setUser: function (a, b){
					this.username = a;
					this.realname = b || " ";
					this._isFavsWidget ? this.setBase(v+this.format+"?screen_name="+a) : this._isProfileWidget && this.setBase(l+this.format+"?screen_name="+a);
					this.setSearch(" ");
					return this
				}, setList: function (a, b){
					this.listslug = b.replace(/ /g, "-").toLowerCase();
					this.username = a;
					this.setBase(z+a+"/lists/"+this.listslug+"/statuses.");
					this.setSearch(" ");
					return this
				}, setProfileImage: function (a){
					this._profileImage = a;
					this.byClass("twtr-profile-img", "img").src = a;
					this.byClass("twtr-profile-img-anchor", "a").href = "https://twitter.com/intent/user?screen_name="+this.username;
					return this
				}, setTitle: function (a){
					this.title = e.txt.htmlEscape(a);
					this.widgetEl.getElementsByTagName("h3")[0].innerHTML = this.title;
					return this
				}, setCaption: function (a){
					this.subject = a;
					this.widgetEl.getElementsByTagName("h4")[0].innerHTML = this.subject;
					return this
				}, setFooterText: function (a){
					this.footerText = j.def(a) && j.string(a) ? a : "Join the conversation";
					if (this._rendered)this.byClass("twtr-join-conv", "a").innerHTML = this.footerText;
					return this
				}, setSearch: function (a){
					this.searchString = a || "";
					this.search = encodeURIComponent(this.searchString);
					this._setUrl();
					if (this._rendered)this.byClass("twtr-join-conv", "a").href = "https://twitter.com/"+this._getWidgetPath();
					return this
				}, _getWidgetPath: function (){
					return this._isProfileWidget ? this.username : this._isFavsWidget ? this.username+"/favorites" : this._isListWidget ? this.username+"/"+this.listslug : "search/"+this.search
				}, _setUrl: function (){
					function a(){
						return b.sinceId==1 ? "" : "&since_id="+b.sinceId+"&refresh=true"
					}

					var b = this;
					if (this._isProfileWidget)this.url = this._includeEntities(this._base+"&callback="+this._cb+"&include_rts=true&count="+this.rpp+a()+"&clientsource="+this.source); else if (this._isFavsWidget)this.url = this._includeEntities(this._base+"&callback="+this._cb+a()+"&clientsource="+this.source); else if (this._isListWidget)this.url = this._includeEntities(this._base+this.format+"?callback="+this._cb+a()+"&clientsource="+this.source); else {
						this.url = this._includeEntities(this._base+this.format+"?q="+this.search+"&callback="+this._cb+"&rpp="+this.rpp+a()+"&clientsource="+this.source);
						if (!this.runOnce)this.url = this.url+"&result_type=filtered"
					}
					this.url = this.url+("&"+ +new Date+"=cachebust");
					return this
				}, _includeEntities: function (a){
					return TWTR.Widget.SHOW_ENTITIES ? a+"&include_entities=true" : a
				}, _getRGB: function (a){
					return C(a.substring(1, 7))
				}, setTheme: function (a, b){
					var c = " !important", d = window.location.hostname.match(/twitter\.com/) && window.location.pathname.match(/goodies/);
					if (b || d)c = "";
					this.theme = {shell: {background: a.shell.background || this._getDefaultTheme().shell.background, color: a.shell.color || this._getDefaultTheme().shell.color}, tweets: {background: a.tweets.background || this._getDefaultTheme().tweets.background, color: a.tweets.color || this._getDefaultTheme().tweets.color, links: a.tweets.links || this._getDefaultTheme().tweets.links}};
					d = "#"+this.id+" .twtr-doc, #"+this.id+" .twtr-hd a, #"+this.id+" h3, #"+this.id+" h4 { background-color: "+this.theme.shell.background+c+"; color: "+this.theme.shell.color+c+"; } #"+this.id+" .twtr-tweet a { color: "+this.theme.tweets.links+c+"; } #"+this.id+" .twtr-bd, #"+this.id+" .twtr-timeline i a, #"+this.id+" .twtr-bd p { color: "+this.theme.tweets.color+c+"; } #"+this.id+" .twtr-new-results, #"+this.id+" .twtr-results-inner, #"+this.id+" .twtr-timeline { background: "+this.theme.tweets.background+c+"; }";
					u && (d = d+("#"+this.id+" .twtr-tweet { background: "+this.theme.tweets.background+c+"; }"));
					e.css(d);
					return this
				}, byClass: function (a, b, c){
					a = n(a, b, m(this.id));
					return c ? a : a[0]
				}, render: function (){
					var a = this;
					if (!TWTR.Widget.hasLoadedStyleSheet){
						window.setTimeout(function (){
							a.render.call(a)
						}, 50);
						return this
					}
					this.setTheme(this.theme, this._isCreator);
					this._isProfileWidget && s.add(this.widgetEl, "twtr-widget-profile");
					this._isScroll && s.add(this.widgetEl, "twtr-scroll");
					!this._isLive && !this._isScroll && (this.wh[1] = "auto");
					if (this._isSearchWidget && this._isFullScreen)document.title = "Twitter search: "+escape(this.searchString);
					this.widgetEl.innerHTML = this._getWidgetHtml();
					var b = this.byClass("twtr-timeline", "div");
					if (this._isLive && !this._isFullScreen){
						var c = function (b){
							a._behavior!=="all" && x.call(this, b) && a.pause.call(a)
						}, d = function (b){
							a._behavior!=="all" && x.call(this, b) && a.resume.call(a)
						};
						this.removeEvents = function (){
							y.remove(b, "mouseover", c);
							y.remove(b, "mouseout", d)
						};
						y.add(b, "mouseover", c);
						y.add(b, "mouseout", d)
					}
					this._rendered = true;
					this._ready();
					return this
				}, removeEvents: function (){
				}, _getDefaultTheme: function (){
					return{shell: {background: "#8ec1da", color: "#ffffff"}, tweets: {background: "#ffffff", color: "#444444", links: "#1985b5"}}
				}, _getWidgetHtml: function (){
					var a = twidget_logo;
					this._isFullScreen && (a = twidget_logo_large);
					return'<div class="twtr-doc'+(this._isFullScreen ? " twtr-fullscreen" : "")+'" style="width: '+this.wh[0]+';"> <div class="twtr-hd">'+(this._isProfileWidget ? '<a target="_blank" href="https://twitter.com/" class="twtr-profile-img-anchor"><img alt="profile" class="twtr-profile-img" src="'+B+'"></a> <h3></h3> <h4></h4>' : this._isSearchWidget ? '<h3><a target="_blank" style="color:'+this.theme.shell.color+'" href="https://twitter.com/'+this._getWidgetPath()+'">'+this.title+'</a></h3> <h4><a target="_blank" style="color:'+this.theme.shell.color+'" href="https://twitter.com/'+this._getWidgetPath()+'">'+this.subject+"</a></h4>" : "<h3>"+this.title+"</h3><h4>"+this.subject+"</h4>")+' </div> <div class="twtr-bd"> <div class="twtr-timeline" style="height: '+this.wh[1]+';"> <div class="twtr-tweets"> <div class="twtr-reference-tweet"></div> <\!-- tweets show here --\> </div> </div> </div> <div class="twtr-ft"> <div><a target="_blank" href="https://twitter.com"><img alt="" src="'+a+'"></a> <span><a target="_blank" class="twtr-join-conv" style="color:'+this.theme.shell.color+'" href="https://twitter.com/'+this._getWidgetPath()+'">'+this.footerText+"</a></span> </div> </div> </div>"
				}, _appendTweet: function (a){
					this._insertNewResultsNumber();
					var b = this.byClass("twtr-reference-tweet", "div");
					b.parentNode.insertBefore(a, b.nextSibling);
					return this
				}, _slide: function (a){
					var b = this, c = a.firstChild.offsetHeight;
					this.runOnce && (new g(a, "height", {from: 0, to: c, time: 500, callback: function (){
						b._fade.call(b, a)
					}})).start();
					return this
				}, _fade: function (a){
					if (g.canTransition){
						a.style.webkitTransition = "opacity 0.5s ease-out";
						a.style.opacity = 1;
						return this
					}
					(new g(a, "opacity", {from: 0, to: 1, time: 500})).start();
					return this
				}, _chop: function (){
					if (this._isScroll)return this;
					var a = this.byClass("twtr-tweet", "div", true), b = this.byClass("twtr-new-results", "div", true);
					if (a.length){
						for (var c = a.length-1; c>=0; c--){
							var d = a[c];
							if (parseInt(d.offsetTop)>parseInt(this.wh[1]))o(d); else break
						}
						if (b.length>0){
							a = b[b.length-1];
							parseInt(a.offsetTop)>parseInt(this.wh[1]) && o(a)
						}
					}
					return this
				}, _appendSlideFade: function (a){
					a = a || this.tweet.element;
					this._chop()._appendTweet(a)._slide(a);
					return this
				}, _createTweet: function (b){
					b.tweet = TWTR.Widget.ify.autoLink(b);
					b.timestamp = b.created_at;
					b.created_at = this._isRelativeTime ? A(b.created_at) : D(b.created_at);
					this.tweet = new a(b);
					if (this._isLive && this.runOnce){
						this.tweet.element.style.opacity = 0;
						this.tweet.element.style.filter = "alpha(opacity:0)";
						this.tweet.element.style.height = "0"
					}
					return this
				}, _getResults: function (){
					var a = this;
					this.timesRequested++;
					this.jsonRequestRunning = true;
					this.jsonRequestTimer = window.setTimeout(function (){
						if (a.jsonRequestRunning){
							clearTimeout(a.jsonRequestTimer);
							a.jsonRequestTimer = null
						}
						a.jsonRequestRunning = false;
						o(a.scriptElement);
						a.newResults = false;
						a.decay()
					}, this.jsonMaxRequestTimeOut);
					TWTR.Widget.jsonP(a.url, function (b){
						a.scriptElement = b
					})
				}, clear: function (){
					var a = this.byClass("twtr-tweet", "div", true), b = this.byClass("twtr-new-results", "div", true), a = a.concat(b);
					c(a, function (a){
						o(a)
					});
					return this
				}, _sortByMagic: function (a){
					var b = this;
					if (this._tweetFilter){
						this._tweetFilter.negatives && (a = k(a, function (a){
							if (!b._tweetFilter.negatives.test(a.text))return a
						}));
						this._tweetFilter.positives && (a = k(a, function (a){
							if (b._tweetFilter.positives.test(a.text))return a
						}))
					}
					switch (this._behavior) {
						case "all":
							this._sortByLatest(a);
							break;
						default:
							this._sortByDefault(a)
					}
					if (this._isLive && this._behavior!=="all"){
						this.intervalJob.set(this.results);
						this.intervalJob.start()
					}
					return this
				}, _sortByLatest: function (a){
					this.results = a;
					this.results = this.results.slice(0, this.rpp);
					this.results.reverse();
					return this
				}, _sortByDefault: function (a){
					this.results.unshift.apply(this.results, a);
					c(this.results, function (a){
						if (!a.views)a.views = 0
					});
					this.results.sort(function (a, b){
						return(new Date(a.created_at)).getTime()>(new Date(b.created_at)).getTime() ? -1 : (new Date(a.created_at)).getTime()<(new Date(b.created_at)).getTime() ? 1 : 0
					});
					this.results = this.results.slice(0, this.rpp);
					this.results = this.results.sort(function (a, b){
						return a.views<b.views ? -1 : a.views>b.views ? 1 : 0
					});
					this._isLive || this.results.reverse()
				}, _prePlay: function (a){
					if (this.jsonRequestTimer){
						clearTimeout(this.jsonRequestTimer);
						this.jsonRequestTimer = null
					}
					u || o(this.scriptElement);
					if (a.error)this.newResults = false; else if (a.results && a.results.length>0){
						this.response = a;
						this.newResults = true;
						this.sinceId = a.max_id_str;
						this._sortByMagic(a.results);
						this.isRunning() && this._play()
					} else if ((this._isProfileWidget || this._isFavsWidget || this._isListWidget) && j.array(a) && a.length){
						this.newResults = true;
						if (!this._profileImage && this._isProfileWidget){
							var b = a[0].user.screen_name;
							this.setProfileImage(h(a[0].user));
							this.setTitle(a[0].user.name);
							this.setCaption('<a target="_blank" href="https://twitter.com/intent/user?screen_name='+b+'">'+b+"</a>")
						}
						this.sinceId = a[0].id_str;
						this._sortByMagic(a);
						this.isRunning() && this._play()
					} else this.newResults = false;
					this._setUrl();
					this._isLive && this.decay()
				}, _play: function (){
					var a = this;
					if (this.runOnce)this._hasNewSearchResults = true;
					this._avatars && this._preloadImages(this.results);
					this._isRelativeTime && (this._behavior=="all" || this._behavior=="preloaded") && c(this.byClass("twtr-timestamp", "a", true), function (a){
						a.innerHTML = A(a.getAttribute("time"))
					});
					(!this._isLive || this._behavior=="all" || this._behavior=="preloaded") && c(this.results, function (b){
						b.profile_image_url = h(b);
						if (b.retweeted_status)b = b.retweeted_status;
						if (a._isProfileWidget || a._isFavsWidget || a._isListWidget){
							b.from_user = b.user.screen_name;
							b.profile_image_url = h(b.user)
						}
						b.id = b.id_str;
						a._createTweet({id: b.id, user: b.from_user, tweet: b.text, avatar: b.profile_image_url, created_at: b.created_at, needle: b});
						b = a.tweet.element;
						a._behavior=="all" ? a._appendSlideFade(b) : a._appendTweet(b)
					});
					return this
				}, _normalizeTweet: function (a){
					a.views++;
					a.profile_image_url = h(a);
					if (this._isProfileWidget){
						a.from_user = this.username;
						a.profile_image_url = h(a.user)
					}
					if (this._isFavsWidget || this._isListWidget){
						a.from_user = a.user.screen_name;
						a.profile_image_url = h(a.user)
					}
					if (this._isFullScreen)a.profile_image_url = a.profile_image_url.replace(/_normal\./, "_bigger.");
					a.id = a.id_str;
					this._createTweet({id: a.id, user: a.from_user, tweet: a.text, avatar: a.profile_image_url, created_at: a.created_at, needle: a})._appendSlideFade()
				}, _insertNewResultsNumber: function (){
					if (this._hasNewSearchResults){
						if (this.runOnce && this._isSearchWidget){
							var a = this.response.total>this.rpp ? this.response.total : this.response.results.length, b = a>1 ? "s" : "", c = this.response.warning && this.response.warning.match(/adjusted since_id/) ? "more than" : "", d = document.createElement("div");
							s.add(d, "twtr-new-results");
							d.innerHTML = '<div class="twtr-results-inner"> &nbsp; </div><div class="twtr-results-hr"> &nbsp; </div><span>'+c+" <strong>"+a+"</strong> new tweet"+b+"</span>";
							a = this.byClass("twtr-reference-tweet", "div");
							a.parentNode.insertBefore(d, a.nextSibling);
							this._hasNewSearchResults = false
						}
					} else this._hasNewSearchResults = false
				}, _preloadImages: function (a){
					this._isProfileWidget || this._isFavsWidget || this._isListWidget ? c(a, function (a){
						(new Image).src = h(a.user)
					}) : c(a, function (a){
						(new Image).src = h(a)
					})
				}, _decayDecider: function (){
					var a = false;
					if (this.runOnce)this.newResults && (a = true); else a = this.runOnce = true;
					return a
				}, start: function (){
					var a = this;
					if (!this._rendered){
						setTimeout(function (){
							a.start.call(a)
						}, 50);
						return this
					}
					this._isLive ? this.occasionalJob.start() : this._getResults();
					this._hasOfficiallyStarted = this._isRunning = true;
					return this
				}, stop: function (){
					this.occasionalJob.stop();
					this.intervalJob && this.intervalJob.stop();
					this._isRunning = false;
					return this
				}, pause: function (){
					if (this.isRunning() && this.intervalJob){
						this.intervalJob.stop();
						s.add(this.widgetEl, "twtr-paused");
						this._isRunning = false
					}
					if (this._resumeTimer){
						clearTimeout(this._resumeTimer);
						this._resumeTimer = null
					}
					return this
				}, resume: function (){
					var a = this;
					if (!this.isRunning() && this._hasOfficiallyStarted && this.intervalJob)this._resumeTimer = window.setTimeout(function (){
						a.intervalJob.start();
						a._isRunning = true;
						s.remove(a.widgetEl, "twtr-paused")
					}, 2E3);
					return this
				}, isRunning: function (){
					return this._isRunning
				}, destroy: function (){
					this.stop();
					this.clear();
					this._profileImage = this._hasOfficiallyStarted = this.runOnce = false;
					this._isLive = true;
					this._isRunning = this.newResults = this._isScroll = this._tweetFilter = false;
					this.sinceId = 1;
					this.results = [];
					this.showedResults = [];
					this.occasionalJob.destroy();
					this.jsonRequestRunning && clearTimeout(this.jsonRequestTimer);
					s.remove(this.widgetEl, "twtr-scroll");
					this.removeEvents();
					return this
				}}
			}()
		})();
		var n = /twitter\.com(\:\d{2,4})?\/intent\/(\w+)/, v = {tweet: true, retweet: true, favorite: true}, h = "scrollbars=yes,resizable=yes,toolbar=no,location=yes", d = screen.height, x = screen.width;
		document.addEventListener ? document.addEventListener("click", m, false) : document.attachEvent && document.attachEvent("onclick", m)
	}
})();
