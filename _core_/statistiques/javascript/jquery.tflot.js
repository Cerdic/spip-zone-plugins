/**
 * Librairie tFlot pour jQuery et jQuery.flot
 * Licence GNU/GPL - Matthieu Marcillaud
 * Version 1.1.0
 */

(function($){
	
	/**
	 * Des variables a garder globalement
	 * 
	 * collections : stockage de l'ensemble de toutes les valeurs de tous les graphs et leurs options
	 * collectionsActives : stockage des series actives
	 * plots : stockage des graphiques
	 * vignettes : stockage des vignettes
	 * idGraph : identifiant unique pour tous les graphs
	 */
	collections = [];
	collectionsActives = []; 
	plots = []; 
	vignettes = []; 
	vignettesSelection = [];
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
			parse:{
				orientation:'row', // 'column' : tableaux verticaux par defaut... 
				axeOnTitle:false // les coordonnees x d'axe sont donnes dans l'attribut title du <th> et non dans le <th> ?
			},
			legendeExterne:false,
			legendeActions:false, // ne fonctionne qu'avec l'option legende externe
			modeDate:false, // pour calculer les timestamp automatiquement
			moyenneGlissante:{
				show:false, // pour calculer une moyenne glissante automatiquement
				plage:7 // plage de glissement (nombre impair !)
			},
			grille:{weekend:false},
			infobulle:{show:false},
			vignette:{
				show:false, 
				zoom:true,
				width:'160px',
				height:'100px'
			},
			flot:{
				legend:{
					show:true,
					container:null,
					labelFormatter:null
				},
				yaxis: { min: 0 },
				selection: { mode: "x" }
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
			values = parseTable(this, options.parse);
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
				// mais toujours besoin des valeurs completes...
				values.options.xaxis = $.extend(true, {
							mode: "time",
							timeformat: "%d/%m/%y"
						}, 
						values.options.xaxis, 
						{ticks: null}
				);
				if (options.grille.weekend) {
					values.options.grid = { markings: weekendAreas }
				}				
			}

			// en cas de moyenne glissante, on la calcule
			if (options.moyenneGlissante.show) {
				values.series = moyenneGlissante(values.series, options.moyenneGlissante);
			}

			// si infobulles, les ajouter
			if (options.infobulle.show) {
				$.extend(true, options.infobulle, {date:options.modeDate});
				infobulle($('#graphique'+idGraph), options.infobulle);
				$.extend(true, values.options, {
					grid:{hoverable:true}
				});
			}
			
					
			// dessiner
			plots[idGraph] = $.plot(graph, values.series, values.options);
			
			// prevoir les actions sur les labels
			if (options.legendeActions) {
				$.extend(values.options, {legend:{container:null, show:false}});
				actionsLegendes($('#graphique'+idGraph));
			}
		
			// ajouter une mini vue si demandee
			if (options.vignette.show) {
				$("<div class='graphVignette' id='#graphVignette"+idGraph 
					+ "' style='width:" + options.vignette.width + ";height:" 
					+ options.vignette.height + ";'></div>").appendTo(gInfo);
				creerVignette($('#graphique'+idGraph), values.series, values.options, options.vignette);
				if (options.vignette.zoom) {
					zoomVignette($('#graphique'+idGraph));
				}
			}

			// stocker les valeurs
			collections.push({id:idGraph, values:values}); // sources
			collectionsActives = $.extend(true, {}, collections); // affiches
			
					
			++idGraph;
		});






		/*
		 * Prendre une table HTML
		 * et calculer les donnees d'un graph jQuery.plot
		 */
		function parseTable(table, settings){
			var options;
			flot = [];
			
			options = {
				ticks:[], // [1:"label 1", 2:"label 2"]
				orientation:'row', // 'column'
				ticksReels:[], // on sauve les vraies donnees pour les infobulles (1 janvier 2008) et non le code de date (1/1/2008)
				axeOnTitle:false,
				defaultSerie:{
					bars: {
						barWidth: 0.9,
						align: "center",
						show:true,
						fill:true
					},
					lines: {
						show:false,
						fill:false
					}
				}
			}
			$.extend(options, settings);
			
			row = (options.orientation == 'row');
		
			// 
			// recuperer les points d'axes
			// 	
			
			//
			// Une fonction pour simplifier la recup
			//
			function getValue(element) {
				if (options.axeOnTitle) {
					return element.attr('title');
				} else {
					return element.text();
				}
			}
			
			axe=0; 
			if (row) {
				// dans le th de chaque tr
				$(table).find('tr:not(:first)').each(function(){
					$(this).find('th:first').each(function(){
						options.ticks.push([++axe, getValue($(this))]);
						options.ticksReels.push([axe, $(this).text()]);
					});
				});

			} else {
				// dans les th du premier tr
				$(table).find('tr:first th:not(:first)').each(function(){
					options.ticks.push([++axe, getValue($(this))]);
					options.ticksReels.push([axe, $(this).text()]);
				});
			}

			
			// 
			// recuperer les noms de series
			//
			axe = (axe ? 1 : 0);
			
			if (row) {
				// si axes definis, on saute une ligne
				if (axe) {
					columns = $(table).find('tr:first th:not(:first)');
				} else {
					columns = $(table).find('tr:first th');
				}
				// chaque colonne est une serie
				
				for(i=0; i<columns.length; i++){
					cpt=0, data=[];
					th = $(table).find('tr:first th:eq(' + (i + axe) + ')');
					label = th.text();
					serieOptions = optionsCss(th);
					$(table).find('tr td:nth-child(' + (i + 1 + axe) +')').each(function(){
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
					rows = $(table).find('tr:not(:first)');
				} else {
					rows = $(table).find('tr');
				}
				// chaque ligne est une serie
				rows.each(function(){
					cpt=0, data=[];
					th = $(this).find('th');
					label = th.text();
					serieOptions = optionsCss(th);
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
				serie = $.extend(true, {}, options.defaultSerie, {color: color++}, serie);
				flot[i] = serie;
			});
			

			opt = {
				xaxis: {}
			}
			if (options.ticks.length) {
				opt.xaxis.ticks = options.ticks;
				opt.xaxis.ticksReels = options.ticksReels;
			}
			return {series:flot, options:opt};
		}


		
		
		/*
		 * 
		 * Recuperer les options en fonctions de CSS
		 * 
		 */
		function optionsCss(element) {
			var options = {};
			// si classe 'flotLine' on met une ligne
			if ($(element).hasClass('flotLine')) {
				$.extend(true, options, {
					lines:{show:true},
					bars:{show:false}
				});
			}
			// si classe 'flotFill' on met rempli
			if ($(element).hasClass('flotFill')) {
				$.extend(true, options, {
					lines:{fill:true},
					bars:{fill:true}
				});
			}
			return options;			
		}
		
		
		
		

		/*
		 * 
		 *  calcul d'une moyenne glissante
		 * 
		 */ 
		function moyenneGlissante(lesSeries, settings) {
			var options;
			options = {
				plage: 7,
				texte:"Moyenne glissante"
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
		
		
		


		//
		// Permettre de cacher certaines series
		//
		function actionsLegendes(graph) {
			// actions sur les items de legende
			// pour masquer / afficher certaines series
			// a ne charger qu'une fois par graph !!!
			$(graph).find('.legendLabel a').click(function(){
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
				collectionsActives[pid].values.series = seriesActives;

				$.plot(master.find('.graphResult'), seriesActives, collections[pid].values.options);
				// vignettes
				if (master.find('.graphVignette').length) {
					creerVignette(master, seriesActives, collections[pid].values.options);
				}

			});			
		}
		
		


		//
		// Afficher une miniature
		//
		function creerVignette(graphique, series, optionsParents, settings) {
			var options;
			options = {
				show:true,
				zoom:true,
				flot:{
					legend: { show: false },
					lines: { show: true, lineWidth: 1 },
					shadowSize: 0,

					grid: { color: "#999", hoverable:null },
					selection: { mode: "x" },
					xaxis:{min:null, max:null},			
					yaxis:{min:null, max:null}			
				}
			};
			$.extend(true, options, settings);
			options.flot = $.extend(true, {}, optionsParents, options.flot);

			// demarrer la vignette
			vignette = $(graphique).find('.graphVignette');
			pid = vignette.parent().parent().attr('id').substr(9);
			vignettes[pid] = $.plot(vignette, series, options.flot);
			
			if (vignettesSelection[pid] !== undefined) {
				vignettes[pid].setSelection(vignettesSelection[pid]);
			}
		}
		
		
		
		//
		// Permettre le zoom sur une miniature
		//		
		function zoomVignette(graphique) {	
			vignette = $(graphique).find('.graphVignette');
			pid = vignette.parent().parent().attr('id').substr(9);
						
			$(graphique).find('.graphResult').bind("plotselected", function (event, ranges) {
				graph = $(event.target);
				pid = graph.parent().attr('id').substr(9);

				// clamp the zooming to prevent eternal zoom
				if (ranges.xaxis.to - ranges.xaxis.from < 0.00001)
					ranges.xaxis.to = ranges.xaxis.from + 0.00001;
				if (ranges.yaxis.to - ranges.yaxis.from < 0.00001)
					ranges.yaxis.to = ranges.yaxis.from + 0.00001;
				
				// do the zooming
				// et sauver les parametres du zoom
				plots[pid] = $.plot(graph, collectionsActives[pid].values.series,
					$.extend(true, collections[pid].values.options, {
					  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
					  yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
					}));
				
				// don't fire event on the overview to prevent eternal loop
				vignettes[pid].setSelection(ranges, true);
			});
			// raz sur double clic
			$(graphique).find('.graphResult').dblclick(function (event) {
				var graphique;
				graphique = $(event.target).parent().parent();
				pid = graphique.attr('id').substr(9);	
				vignettesSelection[pid] = undefined;
				if (vignettes[pid] != undefined) {	
					vignettes[pid].clearSelection();
				}			
				plots[pid] = $.plot(graphique.find('.graphResult'), 
					collectionsActives[pid].values.series,
					$.extend(true, collections[pid].values.options, {
						xaxis: { min: null, max: null },
					  	yaxis: { min: null, max: null }
					}));
					
			});	
			
			// zoom depuis la miniature			
			vignette.bind("plotselected", function (event, ranges) {
				graph = $(event.target);
				pid = graph.parent().parent().attr('id').substr(9);	
				vignettesSelection[pid] = ranges;			
				plots[pid].setSelection(ranges);
			});
			// raz depuis la miniature sur double clic
			vignette.dblclick(function (event) {
				var graphique;
				graphique = $(event.target).parent().parent().parent();
				pid = graphique.attr('id').substr(9);	
				vignettesSelection[pid] = undefined;							
				
				plots[pid] = $.plot(graphique.find('.graphResult'), 
					collectionsActives[pid].values.series,
					$.extend(true, collections[pid].values.options, {
						xaxis: { min: null, max: null },
					  	yaxis: { min: null, max: null }
					}));
					
			});		
			
		}	
	
	
	
		
		/*
		 * 
		 * Infobulles
		 * 
		 */
		var previousPoint = null;
		function infobulle(graph, settings) {
			var options;
			options = {
				show:true
			};
			$.extend(true, options, settings);
			
			$(graph).bind("plothover", function (event, pos, item) {
				$("#x").text(pos.x.toFixed(2));
				$("#y").text(pos.y.toFixed(2));
				
				graph = $(event.target);
				pid = graph.parent().attr('id').substr(9);
				
				if (options.show) {
					if (item) {
						if (previousPoint != item.datapoint) {
							previousPoint = item.datapoint;
							
							$("#tooltip").remove();
							var x = item.datapoint[0],
								y = item.datapoint[1];

							x = collectionsActives[pid].values.options.xaxis.ticksReels[item.dataIndex][1];
							
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
