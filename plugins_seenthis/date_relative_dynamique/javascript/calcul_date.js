$(function() {

	function _T(chaine, nom_val, val) {
		return chaines_lang_date_relative_dynamique[chaine].replace("@" + nom_val + "@", val);
	}

	/**
	 * Intervalles pour formatter la date
	 * [0]: intervalle valide en desous de cette valeur
	 * [1]: facteur par lequel il faut diviser le nombre de secondes pour l'affichage
	 * [2]: clé pour la traduction
	 */
	var intervalleFormattageDates = [
		[2, 1, 'date_une_seconde'],
		[60, 1, 'date_secondes'],
		[120, 60, 'date_une_minute'],
		[3600, 60, 'date_minutes'],
		[7200, 3600, 'date_une_heure'],
		[86400, 3600, 'date_heures'],
		[172800, -1, 'date_hier'],
		[604800, 86400, 'date_jours'],
		[1209600, 604800, 'date_une_semaine'],
		[2419200, 604800, 'date_semaines'],
		[4838400, 2419200, 'date_un_mois'],
		[31449600, 2419200, 'date_mois']
	];

	// contient les élements de dates qui ont été trouvés
	// pour éviter de les recherche à chaque quoi
	var datesTrouvees = [];

	/**
	 * Trouve les éléments de date
	 */
	function trouveElementDate() {
		$(".calcul_date:not(.calcul_date_trouvee)").each(function() {
			var $this = $(this);
			var date;
			var dateTime;
			if ($this.attr("datetime")) {
				dateTime = new Date($this.attr("datetime"));
				date = Math.floor(dateTime.getTime() / 1000);
			} else if ($this.attr("title")) {
				date = Math.floor($this.attr("title"));
				dateTime = new Date(date);
			}
			if (date) {
				datesTrouvees.push({
					element: $this,
					date: date,
					dateTime: dateTime
				});
			}
			$this.addClass('calcul_date_trouvee');
		});
	}

	// la langue de date utilisée, au cas où on charge un nouveau fichier de langue de date ensuite
	var langueDateActuelle = null;

	function completerADeuxChiffres(valeur) {
		var resultat = "" + valeur;
		if(resultat.length < 2){
			resultat = "0" + resultat;
		}
		return resultat;
	}

	function afficher_dates() {
		trouveElementDate();
		var date_now = Math.floor((new Date()).getTime() / 1000) + Math.floor($.cookie('dateoffset'));

		// on teste si la langue a changé
		var changeDateTitle = false;
		if(chaines_lang_date_relative_dynamique.langue != langueDateActuelle) {
			langueDateActuelle = chaines_lang_date_relative_dynamique.langue;
			changeDateTitle = true;
		}

		for (var j = 0; j < datesTrouvees.length; j++) {
			var dateTrouvee = datesTrouvees[j];
			var element = dateTrouvee.element;
			var date = dateTrouvee.date;

			// si la langue a changé on définit le contenu du champ title
			if(changeDateTitle){
				var dateTime = dateTrouvee.dateTime;
				var dateString = "" + dateTime.getDate() + " " + chaines_lang_date_relative_dynamique["date_mois_" + (dateTime.getMonth() + 1)] + " " + dateTime.getFullYear() + " " + completerADeuxChiffres(dateTime.getHours()) + ":" + completerADeuxChiffres(dateTime.getMinutes()) + ":" + completerADeuxChiffres(dateTime.getSeconds());
				element.attr('title', dateString);
			}

			// L'age de l'article depend du serveur
			// l'age affichee depend du client
			var age = date_now - date;

			if (age < 1) {
				// date dans le futur :-(
				age = false;
			} else {
				var intervalle = null;
				// on parcourt les intervalles jusqu'à en trouver
				for (var i = 0; i < intervalleFormattageDates.length; i++) {
					var intervalleCourant = intervalleFormattageDates[i];
					if (age < intervalleCourant[0]) {
						intervalle = intervalleCourant;
						break;
					}
				}
				if (intervalle) {
					if (intervalle[1] == -1) {
						// formattage simplifié
						age = _T(intervalle[2])
					} else {
						age = _T("date_il_y_a", "delai", Math.max(1, Math.floor(age / intervalle[1])) + " " + _T(intervalle[2]));
					}
				} else {
					age = false;
				}
			}

			if (age) {
				element.html(age);
			}
		}
	}

	var calculDateInterval;

	function triggerCalculDateInterval() {
		calculDateInterval = setInterval(function() {
			afficher_dates();
		}, 1000)
	}

	triggerCalculDateInterval();
	$(document).on('visibilitychange', function() {
		if (document.hidden) {
			clearInterval(calculDateInterval);
		} else {
			triggerCalculDateInterval();
		}
	});

	if ($.cookie('dateoffset') == null) {
		var date_now = Math.floor((new Date()).getTime() / 1000);
		$.ajax({
			type: "POST",
			url: "spip.php?action=dateoffset",
			data: {
				time: date_now
			},
			complete: function(data) {
				var date_sql = Math.floor(data.responseText);
				if (date_sql > 1200000000) // sanity check sur la reponse
					$.cookie('dateoffset', date_sql - date_now, {
					path: "/"
				});
			}
		});
	}

});
