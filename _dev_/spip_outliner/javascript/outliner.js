jQuery.fn.activeCrayons = function(){
	if (typeof cQuery != 'undefined'){
		cQuery(this).initcrayons();
	}
	return this.unbind('mouseover');
}

function init_events(){
	/*$('tr.row').click(function(){	$(this).selectRow();	});*/
	/*$('td').click(function(){	$(this).selectRow();	});*/
	$('th').click(function(){	$(this).selectCol();	});
	$('div.toggle').click(function(){	$(this).toggleLine();	});
	update_toolbar_icones();
}
function update_toolbar_icones(){
	if (row_selected!=undefined)
		$('#toolbar a.MoveLeft,#toolbar a.MoveRight,#toolbar a.RemoveItem').removeClass('inactif');
	else
		$('#toolbar a.MoveLeft,#toolbar a.MoveRight,#toolbar a.RemoveItem').addClass('inactif');
	if (col_selected!=undefined)
		$('#toolbar a.AddColumn,#toolbar a.RemoveColumn').removeClass('inactif');
	else
		$('#toolbar a.AddColumn,#toolbar a.RemoveColumn').addClass('inactif');
}
// selectionne une ligne
var row_selected=undefined;
var col_selected=undefined;
function unselect_all(){
	if (row_selected!=undefined)
		row_selected.removeClass('row_sel');
	row_selected = undefined;
	if (col_selected!=undefined)
		/*$('td.col_sel,th.col_sel').removeClass('col_sel');*/
		$('th.col_sel').removeClass('col_sel');
	col_selected = undefined;
}
jQuery.fn.selectRow = function() {
	if (row_selected!=undefined)
		row_selected.removeClass('row_sel');
	row_selected = this;
	update_toolbar_icones();
	return this
    .addClass('row_sel');
}
jQuery.fn.selectParentRow = function() {
	this.parents('tr.row').selectRow();
	return this;
}
jQuery.fn.selectCol = function() {
	if (col_selected!=undefined)
		$('th.col_sel').removeClass('col_sel');
	col_selected = this.attr('class');
	$('th.'+col_selected).addClass('col_sel');
	update_toolbar_icones();
	return this;
}

// replier/reduire par niveau
function getLevel(c){
  var n = c.match(/niveau-(\d+)/);
  if (n) {
    n = n[1] ? parseInt(n[1]) : 1;
  }
  else n=1;
  return n;
}
jQuery.fn.toggleLine = function() {
	cur = l = this.parents('tr.row');
	niveau = l.attr('name'); //getLevel(this.parent().attr('class'));
	expand=false;
	if (this.is('.ferme')) {	
		expand=true;
	}
	else {
		this.removeClass('ouvert').addClass('ferme');
	}
	next = l.next('tr.row');
	if (expand){ 
		while (next.size() && (n=next.attr('name')>niveau)){
			cur.find('div.toggle').removeClass('ferme').addClass('ouvert');
			cur = next.show();
			next = next.next('tr.row');
		}
		cur.find('div.toggle').removeClass('ferme').addClass('ouvert');
	}
	else{
		while (next.size() && (n=next.attr('name')>niveau)){
			cur = next.hide();
			next = next.next('tr.row');
		}
	}
	return this;
}
function filtre_niveau(niveau){
	l=$('#');
	for (i=0;i<niveau;i++)
		l = l.add('tr.row[@name='+i+']');
	l.show().find('div.toggle').removeClass('ferme').addClass('ouvert');
	$('tr.row[@name='+niveau+']').find('div.toggle').removeClass('ouvert').addClass('ferme');
	l=$('#');
	niveau++;
	for (i=niveau;i<10;i++)
		l = l.add('tr.row[@name='+i+']:visible');
	l.hide();
}

// augmenter/reduire le niveau
jQuery.fn.changeLevel = function(increment) {
	niveau = getLevel(this.find('div.niveau').attr('class'));
	if (niveau+increment<1) return this;
	ids = this.attr('id')+':'+niveau;
	this.find('div.niveau').attr('class','niveau niveau-'+(niveau+increment));
	this.attr('name',niveau+increment);
	cur = this.next('tr.row');
	while (cur.size() && ( (n=getLevel(cur.find('div.niveau').attr('class'))) >niveau)){
		ids = ids+','+cur.attr('id')+':'+n;
		cur.find('div.niveau').attr('class','niveau niveau-'+(n+increment));
		cur.attr('name',niveau+increment);
		cur = cur.next('tr.row');
	}
	//alert(ids);
	return this;
}

// fonctions de la toolbar
function CollapseAll(){
	filtre_niveau(0);
}
function ExpandAll(){
	filtre_niveau(10);
}
function MoveLeft(){
	$('tr.row_sel').changeLevel(-1);
}
function MoveRight(){
	$('tr.row_sel').changeLevel(1);
}
function actionItem(lien,ajout){
	href = $(lien).attr('href');
	sel = $('tr.row_sel');
	if (sel.size()==0){
		if (!ajout) return false;
		sel = $('tr.row:last');
	}
	if (sel.size()){
		sel = sel.eq(0);
		href = href+':'+sel.attr('id');
		sel = sel.next('tr.row');
		if (sel.size())
			href = href+':'+sel.attr('id');
		else
			href = href+':0';
	}
	else
		href = href+':0:0';
	$(lien).attr('href',href);
}
function AddItem(lien){
	return actionItem(lien,true)
}
function RemoveItem(lien){
	return actionItem(lien,false)
}

function actionColumn(lien){
	href = $(lien).attr('href');
	sel = $('th.col_sel');
	if (sel.size()==0) return false;
	sel = sel.eq(0);
	sel.removeClass('col_sel');
	href = href+':'+sel.attr('class');
	sel.addClass('col_sel');
	sel = sel.next('th');
	if (sel.size())
		href = href+':'+sel.attr('class');
	else
		href = href+':0';
	$(lien).attr('href',href);
}
function AddColumn(lien){
	return actionColumn(lien);
}
function RemoveColumn(lien){
	return actionColumn(lien);
}


$.addGridControl = function(t,p) {
	if (t.grid) return false;
	var grid = {
			table: t,
			scrollTop: 0,
			height: p.height,
			page: 0,	
			headers: [],
			cols: [],
			dragStart: function(i,x) {
				this.resizing = { idx: i, startX: x};
				this.hDiv.style.cursor = "e-resize"
	
			},
			dragMove: function(x) {
				if (this.resizing) {
					var diff = x-this.resizing.startX
					var h = this.headers[this.resizing.idx]
					var newWidth = h.width + diff
					if (newWidth > 10) { 
						h.el.style.width = newWidth+"px";
						h.newWidth = newWidth; 
						this.cols[this.resizing.idx].style.width = newWidth+"px";
						//this.newWidth = this.width+diff;
						//this.table.style.width = this.newWidth + "px"
						//this.hTable.style.width = this.newWidth + "px"
						this.hDiv.scrollLeft = this.bDiv.scrollLeft;
					}
				}
			},
			dragEnd: function() {
				this.hDiv.style.cursor = "default"
				if (this.resizing) {
					var idx = this.resizing.idx
					this.headers[idx].width = this.headers[idx].newWidth
					this.width = this.newWidth;
					this.resizing = false;
				}
			},
			scroll: function() {
				var scrollTop = this.bDiv.scrollTop
				if (scrollTop != this.scrollTop) {
					this.scrollTop = scrollTop
					if ((this.bDiv.scrollHeight-scrollTop-this.height) <= 0) {
						/*this.populate();*/
					}
				} else {
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				}


			},
			addXmlData: function(xml) {/*
				var tbody = $("tbody",this.table);
				$("rows row",xml).each(
					function() {
						var row = document.createElement("tr");
						row.id = this.getAttribute('id');
						$("cell",this).each(
							function () {
								var td = document.createElement("td");
								td.appendChild(document.createTextNode(this.firstChild.nodeValue));
								row.appendChild(td);
								
							}
						)
						tbody.append(row);
					}
				);
				this.loading = false;
				$("div.loading",this.hDiv).fadeOut("fast");*/
			},
			addJSONData: function(JSON) {
				/*eval("var data = " + JSON);
				var tbody = $("tbody",this.table);
				var row = ""
				var cur = ""
				for (var i=0;i<data.rows.length;i++) {
					cur = data.rows[i]
					row = '<tr id="'+cur.id+'">'
					for (var j=0;j<cur.cell.length;j++) row += "<td>"+cur.cell[j]+"</td>"
					row += '</tr>';
					tbody.append(row);
					
				}
				tbody = null;
				this.loading = false;
				$("div.loading",this.hDiv).fadeOut("fast");*/
			},
			populate: function() {/*
				if (!this.loading) {
					this.loading = true;
					this.page++
					$("div.loading",this.hDiv).fadeIn("fast");
					//$.get("dyndata.php/page/"+this.page,function(xml) { grid.addXmlData(xml) });
					$.get("dyndata.php/page/"+this.page+"/JSON",function(xml) { grid.addJSONData(xml) });
				}*/
			}
		}
		
	var thead = $("thead:first",t).get(0);
	var count = 0;
	$("tr:first th",thead).each(
		function () {
			var w = p.width[count]
			var res = document.createElement("span");
			$(res).addClass('resize').html("&nbsp;");
			var idx=count
			$(res).mousedown(
				function (e) {
					grid.dragStart(idx,e.clientX);
					return false;
				}
			);
			$(this).css("width",w+"px").prepend(res);
			
			grid.headers[count++] = { width: w, el: this };
		}
	)
	count = 0;
	$("tbody:first tr:first > td",t).each(
		function() {
			var w = p.width[count]
			$(this).css("width",w+"px");
			grid.cols[count++] = this ;
		}
	);
	grid.width = $(t).css("width");
	grid.bWidth = grid.width;
	grid.hTable = document.createElement("table");
	grid.hTable.cellSpacing="0"; 
	grid.hTable.className = "outline";
	grid.hTable.appendChild(thead);
	thead = null;
	grid.hDiv = document.createElement("div")
	$(grid.hDiv)
		.css({ width: (grid.width+16)+"px",padding: "0 16px 0 0", overflow: "hidden"})
		.append(grid.hTable)
	/*	.prepend('<div class="loading">loading</div>')			*/
		.bind("selectstart", function () { return false; });

	$(t)
	  /*.mouseover(
			function(e) {
				var td = (e.target || e.srcElement);
				$(td).addClass("ghover").parents('tr.row').addClass("ghover");
			}
		)
		.mouseout(
			function(e) {
				var td = (e.target || e.srcElement);
				$(td).removeClass("ghover").parents('tr.row').removeClass("ghover");
			}
		)*/
		.before(grid.hDiv)
	
	$(t).wrap("<div></div>");	
	grid.bDiv = $(t).parent();
	//grid.bDiv = document.createElement("div")
	h = $(window).height();
	h = h - $(grid.bDiv).offset().top;
	$(grid.bDiv)
		.scroll(function (e) {grid.scroll()})
		.css({ height: h+"px",width: (grid.width+16)+"px"})
		.addClass('outline_container');
		//.append(t)
	$(grid.hDiv).mousemove(function (e) {grid.dragMove(e.clientX);}).after(grid.bDiv)
	
	//while (grid.bDiv.scrollHeight<=grid.height) grid.populate()
	/*grid.populate()*/

	$(document).mouseup(function (e) {grid.dragEnd();})
	t.grid = grid;
	// MSIE memory leak
	$(window).unload(function () {
			t.grid = null;			
		}
	);
}

$.fn.grid = function(p) {
	return this.each(
		function() {
			$.addGridControl(this,p);
		}
	);
}


$(document).ready(function(){
	init_events();
	$("table.outline").grid({width: [30,150,150,150,150,150,150,150,150,150,150,150,150,150,150,150]});
});