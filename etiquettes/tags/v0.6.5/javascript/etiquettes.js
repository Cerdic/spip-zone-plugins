//#---------------------------------------------------#
//#  Plugin  : Étiquettes                             #
//#  Auteur  : RastaPopoulos                          #
//#  Licence : GPL                                    #
//#------------------------------------------------------------------------------------------------------#
//#  Documentation : https://contrib.spip.net/Plugin-Etiquettes                                       #
//#                                                                                                      #
//#  Javascript commun du plugin                                                                         #
//#------------------------------------------------------------------------------------------------------#



// Applique le sélecteur avec les paramètres sur un input précis
function appliquer_selecteur_cherche_mot(input, url_source) {
	
	// chercher l'input de saisie
	var input = jQuery(input);

	// ne pas appliquer pour rien
	if (!input) return;

	// attacher l'autocompleter
	jQuery(input)
		.autocomplete(
			url_source,
			{
				'delay': 200,
				'autofill': false,
				'helperClass': 'autocompleter',
				'selectClass': 'selectAutocompleter',
				'minChars': 1,
				'matchCase': true,
				'inputWidth': true,
				'cacheLength': 20,
				'multiple' : true,
				'multipleSeparator' : " ",
				'formatResult': function(row){
					return jointags(row);
				},
				'fx' : {type: "fade", duration: 400}
			}
		)
		.result(function(event, row, formatted){
			input.keyup();
		});
	jQuery('.autocompleter, .selectAutocompleter').css('opacity',0.7);
	
}

// Applique le nuage d'aide avec les paramètres sur un input précis
function appliquer_etiquettes_aide_nuage(input, nuage){
	
	// chercher l'input de saisie
	var input = $(input);
	// chercher les <span> du nuage
	var nuage = $(nuage+' span');
	var nuage_texte = new Array();	
	
	// pour chaque span son action
	nuage
		.each(function(i){
			nuage_texte[i] = 
				$(this)
					.click(function(){
						mots_attribues = splittags(input.val());
						if (mots_attribues.contains($(this).text()))
							input.val(
								jointags(
									mots_attribues.remove(
										$(this)
											.removeClass("selected")
											.text()
									)
								)
							);
						else
							input.val(
								jointags(
									(mots_attribues.unshift(
										$(this)
											.addClass("selected")
											.text()
									) > 0) ? mots_attribues : mots_attribues
								)
							);
						input.focus();
					})
					.text();
		});
	
	// quand on tapote dans le input
	input
		.keyup(function(){
			// ça cherche si chaque mot tapé est dans le nuage
			// et si oui ça le sélectionne
			nuage.removeClass("selected");
			$.each(nuage_texte, function(i_nuage,mot_nuage){
				$.each(splittags(input.val()), function(i_mot,mot_attribue){
					if (mot_nuage == mot_attribue)
						nuage.eq(i_nuage).addClass("selected");
				});
			});
		})
	
	// Au démarrage on cherche les mots déjà là
	input.keyup();
	
}

// Nouvelle méthode pour les tableaux
// Retourne la première occurence correspondant, sinon false
Array.prototype.contains = function (ele) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == ele) {
			return true;
		}
	}
	return false;
};

// Nouvelle méthode pour les tableaux
// Supprime un élément d'un talbeau
// qu'il y soit une ou plusieurs fois
Array.prototype.remove = function (ele) {
	var arr = new Array();
	var count = 0;
	for (var i = 0; i < this.length; i++) {
		if (this[i] != ele) {
			arr[count] = this[i];
			count++;
		}
	}
	return arr;
};

// Découpe une chaîne en un tableau de mots
function splittags(txt) {
	var temp = new Array();
	var r, i, debut;
	var compteur=1;

	if (txt.match(/^[ ,"]*$/))
		return new Array();

	while (r = txt.match(/(^| )"([^"]*)"(,| |$)/)) {
		debut = txt.search(r[0]);
		txt = txt.substring(0,debut)
			+ r[1]
			+ 'compteur'+compteur
			+ r[3]
			+ txt.substring(debut+r[0].length, 100000);
		temp['compteur'+compteur] = r[2];
		compteur++;
	}
	txt = txt.split(/[, ]+/);
	for (i=0; i<txt.length; i++) {
		if (txt[i].match('^compteur[0-9]+$')) {
			txt[i] = temp[txt[i]];
		}
	}
	return txt;
}

// Fabrique une chaîne à partir d'un tableau de mots
// Ajoute des guillemets si un mot contient des espaces
function jointags(a) {
	var tag, sp;
	for (var i = 0; i < a.length; i++) {
		tag = a[i];
		if (tag.split('"').length == 1
		&&(tag.split(' ').length > 1
			|| tag.split(',').length > 1
		)) {
			tag = '"'+tag+'"';
		}
		a[i] = tag;
	}

	return a.join(' ');  // ici mettre ' ' si on ne veut pas de virgule et ', ' dans le cas contraire
}

