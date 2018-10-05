
## Paramétrages et personalisations

Si on le souhaite, on peut surcharger/modiifer la configuration de code mirror depuis son plugin ou squelette.

Un fichier json défini les theme, extensions, et addons de codemirror qui sont chargés (css et js) `javascript/codemirror.addons.json`.

Le fichier d'initialisation, permettra d'affinner la configuration des addons chargés, de définir le thème, modifier les raccourcis claviers, etc.


## Raccourcis Claviers:

Code mirror

| KBD | Description |
|--|--|
| F11 | mode plein écran (`esc` pour sortir) |
| Ctrl-F / Cmd-F | Start searching |
| Ctrl-G / Cmd-G | Find next |
| Shift-Ctrl-G / Shift-Cmd-G | Find previous |
| Shift-Ctrl-F / Cmd-Option-F | Replace |
| Shift-Ctrl-R / Shift-Cmd-Option-F | Replace all |
| Alt-F | Persistent search (dialog doesn't autoclose, enter to find next, Shift-Enter to find previous)|
| Alt-G | Jump to line |

Emmet

| KBD | Description |
|--|--|
| Cmd-E or Tab | Expand abbreviation |
| Cmd-D | Balance Tag (matches opening and closing tag pair) |
| Shift-Cmd-D | Balance Tag Inward |
| Shift-Cmd-A | Wrap With Abbreviation |
| Ctrl-Alt-Right | Next Edit Point |
| Ctrl-Alt-Left | Previous Edit Point |
| Cmd-L | Select line |
| Cmd-Shift-M | Merge Lines |
| Cmd-/ | Toggle Comment |
| Cmd-J | Split/Join Tag |
| Cmd-K | Remove Tag |
| Shift-Cmd-Y | Evaluate Math Expression |
| Ctrl-Up | Increment Number by 1 |
| Ctrl-Down | Decrement Number by 1 |
| Ctrl-Alt-Up | Increment Number by 0.1 |
| Ctrl-Alt-Down | Decrement Number by 0.1 |
| Shift-Ctrl-Up | Increment Number by 10 |
| Shift-Ctrl-Down | Decrement Number by 10 |
| Shift-Cmd-. | Select Next Item |
| Shift-Cmd-, | Select Previous Item |
| Cmd-B | Reflect CSS Value |


---------------------------------------------
TODO plugin skel_editor
---------------------------------------------

- [ ] Rétablir la recherche
- [ ] Changer le theme, ou les addons utilisés

- [X] update de codemirror
- [X] support scss
- [X] ajout de emmet
  - [ ] ajout de snippets
        https://github.com/emmetio/codemirror-plugin/blob/master/example.html
        https://github.com/emmetio/config
- [X] fullscreen editing
- [X] match tags & brakets
- [X] code folding


ajout de fonctionnalites:
----------------------------
- telecharger l'ensemble du squelette en .zip ?
- documentation des boucles ?
- preview "live" ?

des commentaires ?
https://contrib.spip.net/Plugin-editeur-de-squelettes-Skel


---------------------------------------------
