var cm_options = {
	lineNumbers: true,
	autoCloseBrackets: true,
	autoCloseTags: true,
	matchBrackets: true,
	styleActiveLine: true,
	matchTags: {
		bothTags: true
	},
	lineWrapping: false,
	foldGutter: true,
	gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
	theme: "default solarized dark",
	extraKeys: {
		"Alt-F": "findPersistent",
		"Ctrl-J": "toMatchingTag",
		"Ctrl-Q": function(cm){
			 cm.foldCode(cm.getCursor());
		},
		"F11": function(cm) {
			cm.setOption("fullScreen", !cm.getOption("fullScreen"));
		},
		"Esc": function(cm) {
			if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
		}
	}
};

if (cm_mode.length)
	cm_options.mode = cm_mode;

var editor;
var lastPos = null, lastQuery = null, marked = [];



function editor_init(){
	editor = CodeMirror.fromTextArea(document.getElementById('code'), cm_options);
	var res = emmetCodeMirror(editor);
}

function unmark() {
  for (var i = 0; i < marked.length; ++i) marked[i]();
  marked.length = 0;
}



function search() {
  unmark();
  var text = document.getElementById("query").value;
  if (!text) return;
  for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
    marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));

  if (lastQuery != text) lastPos = null;
  var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
  if (!cursor.findNext()) {
    cursor = editor.getSearchCursor(text);
    if (!cursor.findNext()) return;
  }
  editor.setSelection(cursor.from(), cursor.to());
  lastQuery = text; lastPos = cursor.to();
}

function replace() {
  unmark();
  var text = document.getElementById("query").value,
      replace = document.getElementById("replace").value;
  if (!text) return;
  for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
    editor.replaceRange(replace, cursor.from(), cursor.to());
}

setTimeout(editor_init,300);
