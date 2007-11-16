/******************************************
 * Code to manage the annotations window
 *  
 * Copyright (c) 2007 Renato Formato <renatoformato@virgilio.it>
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *       
 ******************************************/

(function($){
	var points_data = {ids:[]},csv_data = {};
	//IE 6 does not reflow absolute elements after DOM manipulation 
	var fix_absolute_IE6;
	if($.browser.msie && $.browser.version=='6.0')
		fix_absolute_IE6 = function() {$("#annotate_window_cancel").hide().show();};
	else
		fix_absolute_IE6 = function() {};

		
	//init function
	$(function(){
		var hash = $("#annotate_window").jqm().getHashWindow();
		//bind event to hide window
		$("#annotate_window_cancel").click(function(){
			$("#annotate_window").jqmHide();
		});
		//hide all wizard pages
		$("#annotate_window li.wizard_page").hide();
		//bind links that trigger the wizard
		$("a.annotate_link").click(function(){
			//setup and loading of infos about the document being annotated
			$.carto.id_document = this.id.match(/map_(\d+)/)[1];
			$.getJSON($.carto.cfg.getImageInfo,{id_document:$.carto.id_document},function(data){
				//set up preview and full images
				$("#map_annotate").attr(data.attributes);
				$("#annotate_image_summary").empty().append(data.imagePreview);
				//set up annotate window dimensions
				var w,h;
				w = $(window).width();
				h = $(window).height();
				$("#annotate_window").width(w*0.8);
				$("#annotate_window").height('auto');
				$("#annotate_window").css({marginLeft:"-"+(w*0.4)+24+"px",top:(h-h*.8)/2+"px"});
				$("#annotate_window div.image_container").width(w*0.8);
				$("#annotate_window div.image_container").height('auto');
				//set up post data
			  $.carto.postData = data.postData;
				//show panel
				$.carto.showAnnotatePanel(1);
				$("#annotate_window").jqmShow();
				hash.o.unbind("click");
				$.carto.annotate_fill_summary_panel(function(p){
					fix_absolute_IE6();
					$("#annotate_image_summary").find("div.map_marker,div.map_marker_highlight").remove();
					var images = {};
					images[$.carto.id_document] = $("#annotate_image_summary img");
					$.carto.displayMarkers(p,images,"map_marker_preview");
				});
			});
		});
	

	});
	
	$.carto.annotate_fill_summary_panel = function (data,callback) {
		var clean_it = function() {
			var panelBody = $("#annotate_summary_panel tbody").empty();
			panelBody.append("<tr><td colspan='5' style='text-align:center'><img src='"+$.carto.cfg.loaderImage+"'></td></tr>");		
		};
		var fill_it = function() {
			//var panel = $("#annotate_summary_panel");
			var panelBody = $("#annotate_summary_panel tbody").empty();
			if(!points_data.ids.length) {
				panelBody.append("<tr><td colspan='5' style='text-align:center'>"+points_data.msg.noData+"</td></tr>");							
				return callback(points_data);
			}; 
			
			$.each(points_data.ids,function(i,n){
				panelBody.append("<tr><td>"+n.id_annotation+"</td><td>"+n.title+"</td><td>"+n.x+","+n.y+"</td>"+
				"<td><a href='#' title='"+points_data.msg.modifyPoint.replace(/'/g,"&#39;")+"' class='annotate_action_change'><img src='"+$.carto.cfg.modifyImage+"' width='24' height='24' /></a>"+
				"<a href='#' title='"+points_data.msg.deletePoint.replace(/'/g,"&#39;")+"' class='annotate_action_delete'><img src='"+$.carto.cfg.deleteImage+"' width='26' height='24' /></a></td></tr>");
			});
			
			var rows = panelBody.find("tr");
			rows.hover(function(){
				rows.css("background","none");
				$(this).css("background","#FEFFAF");
				var id = $(this).find("td:first").text();
				var marker_container = $("#annotate_image_summary div.marker_container");
				var marker = $("#map_marker_preview"+id);
				if(!marker.size()) return;
				marker_container.find("div.map_marker_highlight").remove();
				var marker_highlight = $("<div class='map_marker_highlight'>");
				marker_highlight.css({
					position:'absolute',
					width:marker.width(),
					height:marker.height(),
					left:parseFloat(marker.css('left'))-2+"px",
					top:parseFloat(marker.css('top'))-2+"px",
					border:"2px solid yellow",
					zIndex:1
				});
				marker_container.append(marker_highlight);
			},function(){});

			//bind action change
			$("a.annotate_action_change",rows).click(function(){
				$.carto.showAnnotatePanel(3);
				var id = $(this).parent().siblings(":first").text();
				$.each(points_data.ids,function(i,n) {
					if(n.id_annotation==id) {
						$("#annotate_point_id").text(id);
						$("#annotate_point_title").val(n.title);
						$("#annotate_point_text").val(n.text);
						$("#annotate_point_x").text(n.x);
						$("#annotate_point_y").text(n.y);
						$("#annotate_window").find(".map_marker,.map_marker+map").remove();
						$.carto.addMarker($("#map_annotate"),{xy:[n.x,n.y]},{id:"map_marker_change"+id});
						return false;
					}
				});
			});
			//bind action delete
			$("a.annotate_action_delete").click(function(){
				var id = $(this).parent().siblings(":first").text();
				var points = {ids:[{id_annotation:id}]};
				$.carto.deleteAnnotations($.carto.postData,points,function(answer){
					console.log("answer delete: "+answer.result);
					points_data.ids = $.grep(points_data.ids,function(n,i) {
						return !(n.id_annotation==id); 
					});
					$.carto.showAnnotatePanel(1);
					$.carto.annotate_fill_summary_panel(points_data,function(p){
						$("#annotate_image_summary").find(".map_marker,.map_marker+map,div.map_marker_highlight").remove();
						var images = {};
						images[$.carto.id_document] = $("#annotate_image_summary img");
						$.carto.displayMarkers(p,images);
					});				
				});
			});
			callback(points_data);
		};
		
		if($.isFunction(data)) {
			callback = data;
			data = null;
		};
		callback = callback || function() {}; 
		 
		clean_it();
		if(!data)
			$.carto.loadAnnotations([$.carto.id_document],function(data){
				points_data = data;
				fill_it();
			});
		else
			fill_it();
	};
	
	$.carto.showAnnotatePanel = function(number) {
		$("#annotate_window li.wizard_page").hide();
		switch(number) {
			case 1:
				$("#annotate_wizard1").show();
				$("#annotate_summary_panel").appendTo("#annotate_wizard1 div.annotate_summary_container").show();
				$("#annotate_import_csv").unbind().click(function(){
					$.carto.showAnnotatePanel(2);
					return false;
				});
				$("#annotate_export_csv").unbind().click(function(){
					var params = [];
					$.each($.carto.postData,function(i,n){
						params.push(i+"="+n);
					});
					params.push("export=1");
					var url = "?"+params.join("&");
					window.location = url;
					return false;
				});
				$("#annotate_click_map").unbind().click(function(){
					$.carto.showAnnotatePanel(3);
					return false;
				});
				$("#annotate_delete_points").unbind().click(function(){
					if(!points_data.ids.length) return false;
					$.carto.deleteAnnotations($.carto.postData,points_data,function(answer){
						console.log("answer delete all: "+answer.result);
						points_data.ids = [];
						$.carto.annotate_fill_summary_panel(points_data,function(p){
							fix_absolute_IE6();
							$("#annotate_image_summary").find(".map_marker,.map_marker+map,div.map_marker_highlight").remove();
							var images = {};
							images[$.carto.id_document] = $("#annotate_image_summary img");
							$.carto.displayMarkers(p,images);
						});										
					});
					return false;
				});
				break;
			case 2:
				$("#annotate_wizard2").show();
				break;			
			case 3:
				//add,chenge point data
				$("#annotate_wizard3").show();
				//reset params
				$("#annotate_point_id").text('');
				$("#annotate_point_title").val(''),
				$("#annotate_point_text").val(''),
				$("#annotate_point_x").text(''),
				$("#annotate_point_y").text('')
				//bind click on the map
				$("#map_annotate").unbind().click(function(event){
					var xy = $.carto.get_xy_coord($(this),event);
					$("#annotate_point_x").text(xy.x);
					$("#annotate_point_y").text(xy.y);
					$("#annotate_window").find(".map_marker,.map_marker+map").remove();
					$.carto.addMarker(this,{xy:[xy.x,xy.y]},{id:"map_marker_preview0"});
					return false;
				});
				//bind click on save button
				$("#annotate_save_point").unbind().click(function(){
					var point = {
						id_annotation:$("#annotate_point_id").text(),
						id_document: $.carto.id_document,
						title: $("#annotate_point_title").val(),
						text: $("#annotate_point_text").val(),
						x:$("#annotate_point_x").text(),
						y:$("#annotate_point_y").text()
					};
					var points = {ids:[point]};
					$.carto.saveAnnotations($.carto.postData,points,function(answer){
						console.log("answer save point: "+answer.result);
						if(answer.mode=="insert") {
							point.id_annotation = answer.new_id;
							points_data.ids.push(point);
						} else if (answer.mode=="update") {
							points_data.ids = $.map(points_data.ids,function(n,i){
								return (n.id_annotation==point.id_annotation)?point:n;
							})
						}
						$.carto.showAnnotatePanel(1);
						window.scrollTo(0,0);
						$.carto.annotate_fill_summary_panel(points_data,function(p){
							fix_absolute_IE6();
							$("#annotate_image_summary").find(".map_marker,.map_marker+map,div.map_marker_highlight").remove();
							var images = {};
							images[$.carto.id_document] = $("#annotate_image_summary img");
							$.carto.displayMarkers(p,images,"map_marker_preview");
						});
					});
					return false;
				});
				break;
			case 4:
				$("#annotate_wizard4").show();
				$.carto.loadCsv($.carto.idCsv,function(content){
					csv_data = content;
					var options = $("<option value='' selected='selected'></option>");
					$.each(csv_data.fields,function(i,n){
						if(!n) return;
						options = options.add($("<option value='"+i+"'>"+n+"</option>"));
					});
					$("#annotate_select_title").empty().append(options.clone());
					$("#annotate_select_text").empty().append(options.clone());
					$("#annotate_select_x").empty().append(options.clone());
					$("#annotate_select_y").empty().append(options.clone());
					$("#annotate_import_csv_data").unbind().click(function(){
						var points = {ids:[]};
						$.each(csv_data.rows,function(i,n){
							var point = {
								id_document: $.carto.id_document,
								title: n[$("#annotate_select_title").val()],
								text: n[$("#annotate_select_text").val()],
								x: n[$("#annotate_select_x").val()],
								y: n[$("#annotate_select_y").val()]
							}
							points.ids.push(point);
						});
						$.carto.saveAnnotations($.carto.postData,points,function(answer){
							$.carto.showAnnotatePanel(1);
							window.scrollTo(0,0);
							$.carto.annotate_fill_summary_panel(function(p){
								fix_absolute_IE6();
								$("#annotate_image_summary").find(".map_marker,.map_marker+map,div.map_marker_highlight").remove();
								var images = {};
								images[$.carto.id_document] = $("#annotate_image_summary img");
								$.carto.displayMarkers(p,images);
							});							
						});
						return false;
					});
										
					$.carto.displayCsv(csv_data,10);					
				});
				break;
		}
		fix_absolute_IE6();
	}
	
	$.carto.displayCsv = function(data,rowsToShow) {
		rowsToShow = rowsToShow || -1; 
		var container = $("#annotate_csv_panel_container"); 
		var clean_it = function() {
			var panel = $("#annotate_csv_panel").empty();
			container.width($("#annotate_window").width()).
			height($(window).height()*0.8);
			
			panel.append("<tr><th style='text-align:center'><img src='"+$.carto.cfg.loaderImage+"'></th></tr>");		
		};

		var fill_it = function() {
			var panel = $("#annotate_csv_panel");
			panel.empty();
			if(!csv_data.count) {
				panel.append("<thead><tr><th style='text-align:center'>"+csv_data.noData+"</th></tr></thead>");							
				return;
			}; 

			var header = "<thead><tr>";
			$.each(csv_data.fields,function(i,n){
				header += "<th>"+n+"</th>";
			});
			header += "</tr></thead>";

			var body = "<tbody>";
			$.each(csv_data.rows,function(i,n){
				var row = "<tr>";
				$.each(n,function(i,n){
					row += "<td>"+n+"</td>";
				});
				row += "</tr>";
				body += row;
				if(rowsToShow==0) return;
				rowsToShow--;
			});
			body += "</tbody>";
			panel.append(header+body);
			
		};
		
		clean_it();
		if(!data) 
			$.carto.loadCsv($.carto.idCsv,function(content){
				csv_data = content;
				fill_it();
			});
		else
			fill_it();
	}
})(jQuery)
