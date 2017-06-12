// equivalent de $.get en pur js
var getAjax = function(url, success) {
		var r = new XMLHttpRequest();
		r.open('GET', url);
		r.onreadystatechange = function() {
				if (r.readyState > 3 && r.status == 200) success(r.responseText);
		};
		r.send();
		return r;
};
// peupler le zbloc et dire qu'il est prêt
function getZapl(bloc) {
		var myurl = window.location + "";
		myurl = myurl.split('#');
		myurl = myurl[0] + ((myurl[0].indexOf("?") > 0) ? "&" : "?") + "var_zajax=" + bloc;
		getAjax(myurl, function(data) {
				var el = document.querySelector("#zapl-" + bloc);
				var newEl = document.createElement('div');
				newEl.innerHTML = data;
				el.parentNode.replaceChild(newEl, el);
				// un array qui liste les zblocs traités
				if (!window.zapl_list) {
						window.zapl_list = [];
				}
				window.zapl_list.push(bloc);
		});
};

// attendre jQuery et les zblocs pour renclencher AjaxLoad
window.zapl_loop_index = 0;
zapl_loop = setInterval(function() {
		//console.log("loop index : " +  window.zapl_loop_index + " - jQuery chargé ? : " + (typeof jQuery.spip.triggerAjaxLoad !== 'undefined') + " - Nb zapl traités:" + window.zapl_list.length );
	if (	(typeof jQuery.spip === 'object') &&
				(typeof jQuery.spip.triggerAjaxLoad === 'function') &&
				(typeof window.zapl_list !== 'undefined') &&
				!(document.querySelectorAll("[id^='zapl-']").length) ) {
			clearInterval(zapl_loop);
			var h = window.location.hash;
			if (h) {
					// $b correspond à la selection jQuery des zblocs
					var $b = $();
					window.zapl_list.forEach(function(e) {
							$b = $b.add(e);
					});
					if ($b.find(h)[0]) {
							jQuery(h).positionner(true);
					}
			}
			jQuery.spip.triggerAjaxLoad(document);
			return;
		}
		// Au dela de 20 sec ...
		else if (window.zapl_loop_index < 200) {
				window.zapl_loop_index++;
		}
		// ... auto-débraillage
		else {
				clearInterval(zapl_loop);
		}

}, 100);