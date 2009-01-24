(function($){
	
	/**
	 * Deux variables a garder globalement
	 * 
	 * collections : stockage de l'ensemble de toutes les valeurs de tous les graphs et leurs options
	 * idGraph : identifiant unique pour tous les graphs
	 */
	var collections,  id;
	collections = []; 
	idGraph = 0;
	
	/*
	 * Fait un graphique d'un tableau donne
	 * $("table.graph").makeGraph();
	 * necessite la librairie "flot".
	 */	
	$.fn.tFlot = function(settings) {
		var options, flot;

			  
		options = {
			width:'500px',
			height:'250px',
			legendeExterne:false,
			legendeActions:false, // ne fonctionne qu'avec l'option legende externe
			modeDate:false, // pour calculer les timestamp automatiquement
			moyenneGlissante:false, // pour calculer une moyenne glissante automatiquement
			moyenneGlissantePlage:7, // plage de glissement (nombre impair !)
		}
		$.extend(options, settings);
		

		flot = {
			legend:{
				show:true,
				container:null,
				labelFormatter:null,
			},
			yaxis: { min: 0 },
			selection: { mode: "x" },
		}		
		
		$(this).each(function(){

			// identifiant unique pour tous les graphs
			// creer les cadres
			// .graphique
			//     .graphResult
			//     .graphLegend 
			$(this).hide().wrap("<div class='graphique' id='graphique"+idGraph+"'></div>");
			graphique = $(this).parent();
			values = $(this).tFlotParseTable();
			$.extend(values.options, flot);
			
			graph = $("<div class='graphResult' style='width:" + options.width + ";height:" + options.height + ";'></div>").appendTo(graphique);
			
			// legende en dehors du dessin ?
			if (options.legendeExterne) {
				legend = $("<div class='graphLegend' id='grapLegend"+idGraph+"'></div>").appendTo(graphique);
				values.options.legend.container = legend;
			}
			// legende avec items clicables pour desactiver certaines series
			if (options.legendeActions) {
				  values.options.legend.labelFormatter = function(label) {
					return '<a href="#label">' + label + '</a>';
				  }
			}
			// si mode time, on calcule des timestamp
			// les series sont alors de la forme [[timestamp, valeur],...]
			// et pas besoin de ticks declare
			if (options.modeDate) {
				timestamps = [];
				// calcul des timestamps
				$.each(values.options.xaxis.ticks, function(i, val){
					timestamps.push([val[0], (new Date(val[1])).getTime()]);
				});
				// les remettre dans les series
				$.each(values.series, function(i, val){
					data = [];
					$.each(val.data, function (j, d){
						data.push([timestamps[j][1], d[1]]);
					});
					values.series[i].data = data;
				});
				// plus besoin du ticks
				values.options.xaxis.ticks = null;
				values.options.xaxis = {
					mode: "time",
					timeformat: "%d/%m/%y",					
				}
				values.options.grid = { markings: weekendAreas }				
			}
			
			// en cas de moyenne glissante, on la calcule
			// sur la base de 7 elements par defaut
			if (options.moyenneGlissante) {
				values.series = $.tFlotMoyenneGlissante(values.series, {
					plage: options.moyenneGlissantePlage
				});
			}
		
			// stocker les valeurs
			collections.push({id:idGraph,values:values});
			// dessiner
			$.plot(graph, values.series, values.options);
			
			// prevoir les actions sur les labels
			$('#graphique'+idGraph).tFlotActions();
			++idGraph;
		});
		
	}
	


	/*
	 * Prendre une table HTML
	 * et calculer les donnees d'un graph jQuery.plot
	 */
	$.fn.tFlotParseTable = function(settings){
		var table, series, data, labels, cpt, options, color;
		flot = series = data = labels = [];
		color=0;
		
		options = {
			ticks:[] // [1:"label 1", 2:"label 2"]
		}
		$.extend(options, settings);
				
		$(this).find('tr').each(function(){
			cpt = 1;
			data = [];
			
			// si th, le prendre en label
			// si plusieurs th, prendre comme valeurs d'axes
			th = $(this).find('th');
			if (th.length > 1) {
				n = 0;
				th.each(function(){
					if (n == 0) {
						label = $(this).text();
					} else {
						options.ticks.push([n, $(this).text()]);
					}
					n++;
				});
			} else {
				label = th.text();
			}

			// recuperer les valeurs
			$(this).find('td').each(function(){
				val = $(this).text();
				if (val || (val == "0")) {
					data.push( [cpt, val] );
				}
				cpt++;
			});

			// seulement s'il y a des resultats
			if (data.length) {
				series = {
					label:label,
					data:data,
					bars: {
						barWidth: 0.9,
						align: "center",
						show:true,
						fill:true,
					},
					lines: {
						show:false,
						fill:false,
					},
					color: color++,
				}
				// si classe 'flotLine' on met une ligne
				if ($(this).hasClass('flotLine')) {
					series.lines.show = true;
					series.bars.show = false;
				}
				// si classe 'flotFill' on met rempli
				if ($(this).hasClass('flotFill')) {
					series.lines.fill = true;
					series.bars.fill = true;
				}
				flot.push(series);
			}
			
		});
		opt = {
			xaxis: {}
		}
		if (options.ticks.length) 
			opt.xaxis.ticks = options.ticks;
		return {series:flot, options:opt};
	}
	
	
	
	
		
	
	$.fn.tFlotActions = function() {
		// actions sur les items de legende
		// pour masquer / afficher certaines series
		// a ne charger qu'une fois par graph !!!
		$(this).find('.legendLabel a').click(function(){
			tr = $(this).parent().parent();
			tr.toggleClass('cacher').find('.legendColorBox div').toggle();

			// bof bof tous ces parent() et ca marche qu'avec legendeExterne:true
			master = tr.parent().parent().parent().parent();
			pid = master.attr('id').substr(9); // enlever 'graphique'
			
			var seriesActives = [];
			tr.parent().find('tr:not(.cacher)').each(function(){
				nom = $(this).find('a').text();				
				//console.log(collections[pid]);
				n = collections[pid].values.series.length;
				for(i=0;i<n;i++) {
					if (collections[pid].values.series[i].label == nom) {
						seriesActives.push(collections[pid].values.series[i]);
						break;
					}
				}
			});
			$.extend(collections[pid].values.options, {legend:{container:null, show:false}});
			$.plot(master.find(' .graphResult'), seriesActives, collections[pid].values.options);
		});			
	}
	
	
	$.tFlotMoyenneGlissante = $.fn.tFlotMoyenneGlissante = function(lesSeries, settings) {
		options = {
			plage: 7
		}
		$.extend(options, settings);

		g = options.plage; // nombre impair
		series = [];
		$.each(lesSeries, function(i, val){
			data = [], moy = [];
			r = (g-1)/2;
			$.each(val.data, function (j, d){
				// quand on peut calculer une moyenne
				if ((j>r) && (val.data.length > j+r)) {
					// initialisation, on remplit moy des valeurs, sauf la derniere
					if (!moy.length) {
						for (s=0;s<g-1;s++) {
							moy.push(parseInt(val.data[s][1]));
						}
					}
					// ajout du nouvel element
					// et retrait du trop vieux
					moy.push(parseInt(val.data[j+r][1]));
					if (moy.length>=g) { moy.shift();}
					// calcul de la somme et ajout de la moyenne
					for(var k=0,sum=0;k<moy.length;sum+=moy[k++]);
					data.push([d[0], Math.round(sum/g)]);						
				}


			});
			
			serieG = $.extend(true, {}, val);
			serieG.data = data;
			serieG.label = val.label + " (Moyenne glissante) ";
			serieG.lines.show=true;
			serieG.lines.fill=false;
			series.push(val);
			series.push(serieG);
		});
		// remettre les couleurs
		color=0;
		$.each(series, function(i, val) {
			val.color = color++;
		});
		return series;
	}
	
	
	// Pris sur le site de Flot (exemple de visites)
    // helper for returning the weekends in a period
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);

        return markings;
    }

})(jQuery);
