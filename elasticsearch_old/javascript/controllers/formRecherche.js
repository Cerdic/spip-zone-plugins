angular.module('CDMedia')

.controller('FormRechercheCtrl', ["rechercheSrv", function(rechercheSrv) {
	rechercheCtrl = this;
	rechercheCtrl.rechercheSrv = rechercheSrv;
}])

.provider('rechercheSrv', {
	$get: ["$http", "$timeout", function($http, $timeout){
		var provider = this;
		return {
			result: [],
			getRecherche: function(recherche, from, size) {
				if (recherche && recherche.length > 1) {
				var rechercheSrv = this;
				var q = "&recherche=" + (recherche) + "&from=" + from + "&size=" + size;

				$http.get("/?page=api&api=getRecherche" + q ).then(function(resp) {
					rechercheSrv.result = resp.data.hits.hits;
					return resp;
				});
			}},
			focus: function() {
				var rechercheSrv = this;
				if ($(".overlay-recherche").hasClass('on')) {
					$(".overlay-recherche").removeClass('on');
					$timeout(function() {
						rechercheSrv.result = [];
					},250);
				} else {
					$(".overlay-recherche").addClass('on');
					$("#recherche").focus();
				}
			},
			convertJour: function(d) {
				d = d.replace(" ", "T");
				var date = new Date(d);
				return date.getDate();
			},
			convertMois: function(d) {
				var monthNames = [
					"Janv.", "Févr.", "Mars",
					"Avr.", "Mai", "Juin", "Juil.",
					"Août", "Sept.", "Oct.",
					"Nov.", "Dec."
				];
				d = d.replace(" ", "T");
				var date = new Date(d);
				return monthNames[date.getMonth()];
			}
		};
	}]
})