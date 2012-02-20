$(document).ready(function() {
	var graphes = new Array();
	$('.bc_grapher table').each(function(i){
		graphes[i] = new Array();
		graphes[i]['container'] = $(this) ;
		graphes[i]['rowInputs'] = $(this).find('td.bc_graph_row input') ;
		graphes[i]['colInputs'] = $(this).find('td.bc_graph_col input') ;
		graphes[i]['chart'] = graphes[i]['container']
			.visualize({
				rowFilter: ':not(.bc_graph, .bc_row_cacher)',
				colFilter: ':not(.bc_graph, .bc_col_cacher)',
				width: 660
			}) ;
		graphes[i]['rowInputs']
			.click(function(){
				// On change la classe pour le tr parent et tous les td/th freres
				if($(this).attr("checked"))
					$(this).parent().parent().removeClass("bc_row_cacher").children().removeClass("bc_row_cacher");
				else
					$(this).parent().parent().addClass("bc_row_cacher").children().addClass("bc_row_cacher");
				graphes[i]['chart'] = graphes[i]['container'].visualize({
					rowFilter: ':not(.bc_graph, .bc_row_cacher)',
					colFilter: ':not(.bc_graph, .bc_col_cacher)',
					width: 660
				},graphes[i]['chart'].empty()) ;
			}) ;
		graphes[i]['colInputs']
			.click(function(){
				classe=this.name;
				if($(this).attr("checked"))
					graphes[i]['container'].find("."+classe).removeClass("bc_col_cacher");
				else
					graphes[i]['container'].find("."+classe).addClass("bc_col_cacher");
				graphes[i]['chart'] = graphes[i]['container'].visualize({
					rowFilter: ':not(.bc_graph, .bc_row_cacher)',
					colFilter: ':not(.bc_graph, .bc_col_cacher)',
					width: 660
				},graphes[i]['chart'].empty()) ;
			}) ;
	});
});
