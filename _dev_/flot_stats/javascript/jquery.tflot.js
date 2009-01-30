(function($){
	
	/**
	 * Deux variables a garder globalement
	 * 
	 * collections : stockage de l'ensemble de toutes les valeurs de tous les graphs et leurs options
	 * idGraph : identifiant unique pour tous les graphs
	 */
	var collections,  id;
	collections = []; 
	plots = []; 
	vignettes = []; 
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
			parse:'row', // 'column' : tableaux verticaux par defaut... 
			legendeExterne:false,
			legendeActions:false, // ne fonctionne qu'avec l'option legende externe
			modeDate:false, // pour calculer les timestamp automatiquement
			moyenneGlissante:{
				show:false, // pour calculer une moyenne glissante automatiquement
				plage:7 // plage de glissement (nombre impair !)
			},
			infobulle:{show:false},
			vignette:{
				show:false, 
				zoom:true,
				width:'160px',
				height:'100px'},
			flot:{
				legend:{
					show:true,
					container:null,
					labelFormatter:null,
				},
				yaxis: { min: 0 },
				selection: { mode: "x" },
			}
		}
		$.extend(true, options, settings);

		
		$(this).each(function(){

			// identifiant unique pour tous les graphs
			// creer les cadres
			// .graphique
			//     .graphResult
			//	   .graphInfos
			//     	  .graphLegend
			//        .graphOverview
			$(this).hide().wrap("<div class='graphique' id='graphique"+idGraph+"'></div>");
			graphique = $(this).parent();
			values = $(this).tFlotParseTable({dataList:options.parse});
			$.extend(true, values.options, options.flot);
			graph = $("<div class='graphResult' style='width:" + options.width + ";height:" + options.height + ";'></div>").appendTo(graphique);
			gInfo = $("<div class='graphInfo'></div>").appendTo(graphique);
			
			// legende en dehors du dessin ?
			if (options.legendeExterne) {
				legend = $("<div class='graphLegend' id='grapLegend"+idGraph+"'></div>").appendTo(gInfo);
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
			if (options.moyenneGlissante.show) {
				values.series = $.tFlotMoyenneGlissante(values.series, options.moyenneGlissante);
			}

			// si infobulles, les ajouter
			if (options.infobulle.show) {
				$.extend(true, options.infobulle, {date:options.modeDate});
				$('#graphique'+idGraph).tFlotInfobulle(options.infobulle);
				$.extend(true, values.options, {
					grid:{hoverable:true}
				});
			}
			
					
			// stocker les valeurs
			collections.push({id:idGraph, values:values});
			// dessiner
			plots[idGraph] = $.plot(graph, values.series, values.options);
			
			// prevoir les actions sur les labels
			$('#graphique'+idGraph).tFlotActions();
			
			// ajouter une mini vue si demandee
			if (options.vignette.show) {
				$("<div class='graphVignette' id='#graphVignette"+idGraph 
					+ "' style='width:" + options.vignette.width + ";height:" 
					+ options.vignette.height + ";'></div>").appendTo(gInfo);
				$('#graphique'+idGraph).tFlotVignette(values, options.vignette);
			}
			
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
			ticks:[], // [1:"label 1", 2:"label 2"]
			dataList:'row', // 'column'
		}
		$.extend(options, settings);
		
		row = (options.dataList == 'row');
	
		// 
		// recuperer les points d'axes
		// 	
		axe=0; 
		if (row) {
			// dans le th de chaque tr
			$(this).find('tr:not(:first)').each(function(){
				$(this).find('th:first').each(function(){
					options.ticks.push([++axe, $(this).text()]);
				});
			});

		} else {
			// dans les th du premier tr
			$(this).find('tr:first th:not(:first)').each(function(){
				options.ticks.push([++axe, $(this).text()]);
			});
		}
		

		// 
		// recuperer les noms de series
		//
		axe = (axe ? 1 : 0);
		
		if (row) {
			// si axes definis, on saute une ligne
			if (axe) {
				columns = $(this).find('tr:first th:not(:first)');
			} else {
				columns = $(this).find('tr:first th');
			}
			// chaque colonne est une serie
			
			for(i=0; i<columns.length; i++){
				cpt=0, data=[];
				th = $(this).find('tr:first th:eq(' + (i + axe) + ')');
				label = th.text();
				serieOptions = th.tFlotCssOptions();
				$(this).find('tr td:nth-child(' + (i + 1 + axe) +')').each(function(){
					val = parseFloat($(this).text());
					data.push( [++cpt, val] );
				});
				serie = {label:label, data:data};
				$.extend(serie, serieOptions);
				flot.push(serie);
			}

			
		} else {
			// si axes definis, on saute une colonne
			if (axe) {
				rows = $(this).find('tr:not(:first)');
			} else {
				rows = $(this).find('tr');
			}
			// chaque ligne est une serie
			rows.each(function(){
				cpt=0, data=[];
				th = $(this).find('th');
				label = th.text();
				serieOptions = th.tFlotCssOptions();
				// recuperer les valeurs
				$(this).find('td').each(function(){
					val = parseFloat($(this).text());
					data.push( [++cpt, val] );
				});
				serie = {label:label, data:data};
				$.extend(serie, serieOptions);
				flot.push(serie);
			});		
		}

		// 
		// mettre les options dans les series
		//
		color=0;
		$.each(flot, function(i, serie) {
			serie = $.extend(true, {
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
				},	serie);
			flot[i] = serie;
		});
		

		opt = {
			xaxis: {}
		}
		if (options.ticks.length) 
			opt.xaxis.ticks = options.ticks;
		return {series:flot, options:opt};
	}
	
	
	
	$.fn.tFlotCssOptions = function (){
		options = {}
		// si classe 'flotLine' on met une ligne
		if ($(this).hasClass('flotLine')) {
			$.extend(true, options, {
				lines:{show:true},
				bars:{show:false}
			});
		}
		// si classe 'flotFill' on met rempli
		if ($(this).hasClass('flotFill')) {
			$.extend(true, options, {
				lines:{fill:true},
				bars:{fill:true}
			});
		}
		return options;
	}	
	
		
	//
	// Permettre de cacher certaines series
	//
	$.fn.tFlotActions = function() {
		// actions sur les items de legende
		// pour masquer / afficher certaines series
		// a ne charger qu'une fois par graph !!!
		$(this).find('.legendLabel a').click(function(){
			tr = $(this).parent().parent();
			tr.toggleClass('cacher').find('.legendColorBox div').toggle();

			// bof bof tous ces parent() et ca marche qu'avec legendeExterne:true
			master = tr.parent().parent().parent().parent().parent();
			pid = master.attr('id').substr(9); // enlever 'graphique'
			
			var seriesActives = [];
			tr.parent().find('tr:not(.cacher)').each(function(){
				nom = $(this).find('a').text();				
				n = collections[pid].values.series.length;
				for(i=0;i<n;i++) {
					if (collections[pid].values.series[i].label == nom) {
						seriesActives.push(collections[pid].values.series[i]);
						break;
					}
				}
			});
			$.extend(collections[pid].values.options, {legend:{container:null, show:false}});
			$.plot(master.find('.graphResult'), seriesActives, collections[pid].values.options);
		});			
	}
	
	
	//
	// Permettre d'afficher une miniature zoomable du tableau
	//
	$.fn.tFlotVignette = function(values, settings) {
		options = {
			show:true,
			zoom:true,
			flot:{
				legend: { show: false },
				lines: { show: true, lineWidth: 1 },
				shadowSize: 0,
				xaxis: { ticks: 4},
				yaxis: { ticks: 3},

				grid: { color: "#999" },
				selection: { mode: "x" }				
			}
		};
		$.extend(true, options, settings);
		// demarrer la vignette
		vignette = $(this).find('.graphVignette');
		pid = vignette.parent().parent().attr('id').substr(9);
		vignettes[pid] = $.plot(vignette, values.series, options.flot);
		
		// autoriser le zoom
		if (options.zoom) {
			$(this).find('.graphResult').bind("plotselected", function (event, ranges) {
				graph = $(event.target);
				pid = graph.parent().attr('id').substr(9);

				// clamp the zooming to prevent eternal zoom
				if (ranges.xaxis.to - ranges.xaxis.from < 0.00001)
					ranges.xaxis.to = ranges.xaxis.from + 0.00001;
				if (ranges.yaxis.to - ranges.yaxis.from < 0.00001)
					ranges.yaxis.to = ranges.yaxis.from + 0.00001;
				
				// do the zooming
				plots[pid] = $.plot(graph, collections[pid].values.series,
					$.extend(true, {}, options, {
					  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
					  yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
					}));
				
				// don't fire event on the overview to prevent eternal loop
				vignettes[pid].setSelection(ranges, true);
			});
			vignette.bind("plotselected", function (event, ranges) {
				graph = $(event.target);
				pid = graph.parent().parent().attr('id').substr(9);				
				plots[pid].setSelection(ranges);
			});		
		}


	}
	
	
	/*
	 * 
	 *  calcul d'une moyenne glissante
	 * 
	 */ 
	$.tFlotMoyenneGlissante = $.fn.tFlotMoyenneGlissante = function(lesSeries, settings) {
		options = {
			plage: 7,
			texte:"Moyenne glissante",
		}
		$.extend(options, settings);

		g = options.plage;
		series = [];
		$.each(lesSeries, function(i, val){
			data = [], moy = [];
			$.each(val.data, function (j, d){
				// ajout du nouvel element
				// et retrait du trop vieux
				moy.push(parseInt(d[1]));
				if (moy.length>=g) { moy.shift();}
				
				// calcul de la somme et ajout de la moyenne
				for(var k=0,sum=0;k<moy.length;sum+=moy[k++]);
				data.push([d[0], Math.round(sum/moy.length)]);						
			});
			
			serieG = $.extend(true, {}, val, {
				data:data,
				label:val.label + " ("+options.texte+")",
				lines:{
					show:true,
					fill:false	
				},
				bars:{show:false}
			});
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
	
	
	
	/*
	 * 
	 * Infobulles
	 * 
	 */
	var previousPoint = null;
	$.fn.tFlotInfobulle = function(settings) {
		options = {
			show:true
		};
		$.extend(true, options, settings);
		
		$(this).bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

			if (options.show) {
				if (item) {
					if (previousPoint != item.datapoint) {
						previousPoint = item.datapoint;
						
						$("#tooltip").remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];
						// si une date, remise du forme
						if (options.date) {
							x = formatDate((new Date(x)), "%d/%m/%y");
						}
						
						showTooltip(item.pageX, item.pageY,
									item.series.label + " [" + x + "] = " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;            
				}
			}
		});
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
	
	
	// Pris sur le site de Flot (exemple d'interactions)
	// montrer les informations des points
    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

	// copie de la fonction de jquery.flot.js
	// pour utilisation dans infobulle
	function formatDate(d, fmt, monthNames) {
		var leftPad = function(n) {
			n = "" + n;
			return n.length == 1 ? "0" + n : n;
		};
		
		var r = [];
		var escape = false;
		if (monthNames == null)
			monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		for (var i = 0; i < fmt.length; ++i) {
			var c = fmt.charAt(i);
			
			if (escape) {
				switch (c) {
				case 'h': c = "" + d.getUTCHours(); break;
				case 'H': c = leftPad(d.getUTCHours()); break;
				case 'M': c = leftPad(d.getUTCMinutes()); break;
				case 'S': c = leftPad(d.getUTCSeconds()); break;
				case 'd': c = "" + d.getUTCDate(); break;
				case 'm': c = "" + (d.getUTCMonth() + 1); break;
				case 'y': c = "" + d.getUTCFullYear(); break;
				case 'b': c = "" + monthNames[d.getUTCMonth()]; break;
				}
				r.push(c);
				escape = false;
			}
			else {
				if (c == "%")
					escape = true;
				else
					r.push(c);
			}
		}
		return r.join("");
	}

})(jQuery);
