/*
 Jaxe - Editeur XML en Java

 Copyright (C) 2003 Observatoire de Paris

 Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

 Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

 Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
 */

package jaxe;

import java.awt.Component;
import java.awt.Container;
import java.awt.Font;
import java.awt.FontMetrics;
import java.awt.Point;
import java.awt.Rectangle;
import java.awt.Toolkit;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.ClipboardOwner;
import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.StringSelection;
import java.awt.datatransfer.Transferable;
import java.awt.event.ActionEvent;
import java.awt.event.InputEvent;
import java.awt.event.KeyEvent;
import java.awt.event.MouseEvent;
import java.io.ByteArrayInputStream;
import java.util.ArrayList;
import java.util.Stack;

import javax.swing.AbstractAction;
import javax.swing.Action;
import javax.swing.JApplet;
import javax.swing.JFrame;
import javax.swing.JPopupMenu;
import javax.swing.JTextPane;
import javax.swing.KeyStroke;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.event.UndoableEditEvent;
import javax.swing.event.UndoableEditListener;
import javax.swing.text.BadLocationException;
import javax.swing.text.DefaultEditorKit;
import javax.swing.text.DefaultHighlighter;
import javax.swing.text.Highlighter;
import javax.swing.text.JTextComponent;
import javax.swing.text.Keymap;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.StyleConstants;
import javax.swing.text.TabSet;
import javax.swing.text.TabStop;
import javax.swing.text.TextAction;
import javax.swing.undo.CannotUndoException;
import javax.swing.undo.CompoundEdit;
import javax.swing.undo.UndoManager;
import javax.swing.undo.UndoableEdit;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import jaxe.elements.JEStyle;
import jaxe.elements.JESwing;
import jaxe.elements.JETexte;

import org.w3c.dom.DocumentFragment;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.ProcessingInstruction;

/**
 * Zone de texte éditable correspondant à un document XML. Peut être utilisée
 * indépendamment de JaxeFrame et JaxeMenuBar.
 */
public class JaxeTextPane extends JTextPane implements ClipboardOwner {

    static int cmdMenu;

    //undo helpers
    private UndoManager undo = new UndoManager();

    private boolean ignorerEdition = false;

    private boolean editionSpeciale = false;

    private CompoundEdit editSpecial;

    private int niveauEditionSpeciale = 0;

    private Stack ignorerEditionStack = new Stack(); // de Boolean

    private static Object pressePapier = null;

    private static String ppTexte = null;

    static String texteRecherche = null;

    private ArrayList ecouteursArbre = new ArrayList();

    private ArrayList ecouteursAnnulation = new ArrayList();

    private DialogueRechercher dlg = null;
    private JaxeDocument doc;

    public JFrame jframe;
    public JApplet japplet;

    public JaxeTextPane(JaxeDocument doc, JApplet japplet) {
        super();
        setEditorKit(doc.createEditorKit());
        setStyledDocument(doc);
        this.doc = doc;
        this.jframe = null;
        this.japplet = japplet;
        doc.setTextPane(this);
        // setFont Serif to workaround a bug in the Java 1.4.2 JVM on MacOS X 10.3
        // where Lucida Grande (the default font) does not have italic glyphs
        setFont(new Font("Serif", Font.PLAIN, 14));
        cmdMenu = Toolkit.getDefaultToolkit().getMenuShortcutKeyMask();

        Keymap kmap = getKeymap();
        KeyStroke cmdx = KeyStroke.getKeyStroke(KeyEvent.VK_X, cmdMenu);
        kmap.removeKeyStrokeBinding(cmdx);
        kmap.addActionForKeyStroke(cmdx, new ActionCouper());
        KeyStroke cmdc = KeyStroke.getKeyStroke(KeyEvent.VK_C, cmdMenu);
        kmap.removeKeyStrokeBinding(cmdc);
        kmap.addActionForKeyStroke(cmdc, new ActionCopier());
        
        if (cmdMenu == InputEvent.META_MASK) {
            // cas du Mac: un bug de Swing empêche le coller du presse-papier système avec la
            // touche commande, et seule la touche ctrl est autorisée
            KeyStroke cmdvctrl = KeyStroke.getKeyStroke(KeyEvent.VK_V, InputEvent.CTRL_MASK);
            kmap.addActionForKeyStroke(cmdvctrl, new DefaultEditorKit.PasteAction());
            KeyStroke cmdcctrl = KeyStroke.getKeyStroke(KeyEvent.VK_C, InputEvent.CTRL_MASK);
            kmap.addActionForKeyStroke(cmdcctrl, new DefaultEditorKit.CopyAction());
            KeyStroke cmdxctrl = KeyStroke.getKeyStroke(KeyEvent.VK_X, InputEvent.CTRL_MASK);
            kmap.addActionForKeyStroke(cmdxctrl, new DefaultEditorKit.CutAction());
        }

        KeyStroke cmdsp = KeyStroke.getKeyStroke(KeyEvent.VK_D,
                cmdMenu);
        kmap.addActionForKeyStroke(cmdsp, new ActionMenuContextuel());

        doc.addUndoableEditListener(new MyUndoableEditListener());
        addCaretListener(new MyCaretListener());

        setTabs(4);

        setHighlighter(new JaxeHighlighter());
    }

    class JaxeHighlighter extends DefaultHighlighter {

        public Object addHighlight(int p0, int p1,
                Highlighter.HighlightPainter p) throws BadLocationException {
            Object o = super.addHighlight(p0, p1, p);
            selectZone(p0, p1, true, false);
            return (o);
        }

        public void changeHighlight(Object tag, int p0, int p1)
                throws BadLocationException {
            Highlighter.Highlight highlight = (Highlighter.Highlight) tag;
            int v0 = highlight.getStartOffset();
            int v1 = highlight.getEndOffset();
            super.changeHighlight(tag, p0, p1);
            selectZone(v0, v1, false, false);
            selectZone(p0, p1, true, false);
            return;
        }

        public void removeHighlight(Object tag) {
            super.removeHighlight(tag);
            Highlighter.Highlight highlight = (Highlighter.Highlight) tag;
            selectZone(highlight.getStartOffset(), highlight.getEndOffset(),
                    false, false);
            return;
        }
    }

    public UndoManager getUndo() {
        return (undo);
    }

    public void undo() {
        try {
            undo.undo();
        } catch (CannotUndoException ex) {
            System.out.println(JaxeResourceBundle.getRB().getString(
                    "annulation.ImpossibleAnnuler")
                    + ": " + ex);
            ex.printStackTrace();
        }
        miseAJourAnnulation();
    }

    public boolean getEditionSpeciale() {
        return (editionSpeciale);
    }

    public boolean getIgnorerEdition() {
        return (ignorerEdition);
    }

    // inspiré de DefaultEditorKit.CutAction, mais EN FRANCAIS
    protected class ActionCouper extends TextAction {

        public ActionCouper() {
            super(JaxeResourceBundle.getRB().getString("menus.Couper"));
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane) target).couper();
        }
    }

    protected class ActionCopier extends TextAction {

        public ActionCopier() {
            super(JaxeResourceBundle.getRB().getString("menus.Copier"));
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane) target).copier();
        }
    }

    protected class ActionColler extends TextAction {

        public ActionColler() {
            super(JaxeResourceBundle.getRB().getString("menus.Coller"));
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane) target).coller();
        }
    }

    protected class ActionMenuContextuel extends TextAction {

        public ActionMenuContextuel() {
            super("menuContextuel");
        }

        public void actionPerformed(ActionEvent e) {
            JTextComponent target = getTextComponent(e);
            if (target instanceof JaxeTextPane)
                ((JaxeTextPane) target).menuContextuel(target
                        .getCaretPosition(), null);
        }
    }

    public void processMouseEvent(MouseEvent e) {
        if (e.isPopupTrigger() && this.isEditable()) {
            showPopup(e);
        } else {
            super.processMouseEvent(e);
        }
    }

    private void showPopup(MouseEvent e) {
        if (e.isPopupTrigger())
            menuContextuel(-1, e.getPoint());
    }

    // pos != -1 || pt != null
    private void menuContextuel(int pos, Point pt) {
        if (pos == -1 && pt == null)
            return;
        if (pos == -1 && doc.rootJE != null)
            pos = viewToModel(pt);
        if (pt == null) {
            try {
                Rectangle r = modelToView(pos);
                pt = r.getLocation();
            } catch (BadLocationException ex) {
                ex.printStackTrace();
                return;
            }
        }
        JPopupMenu popup = new JPopupMenu();
        ArrayList autorisees = null;
        Config conf;
        JaxeElement je;
        if (doc.rootJE == null) {
            je = null;
            conf = doc.cfg;
            autorisees = conf.listeRacines();
        } else {
            je = doc.elementA(pos);
            if (je == null)
                return;

            if (je instanceof JETexte)
                je = je.getParent();
            if (pos == je.debut.getOffset() && !(je instanceof JESwing))
                je = je.getParent();

            if (je == null || !je.getEditionAutorisee())
                return;

            int start = getSelectionStart();
            int end = getSelectionEnd();
            if (start == end || pos < start || pos > end) {
                setCaretPosition(pos);
                moveCaretPosition(pos);
            }

            if (doc.cfg == null) {
                conf = null;
                autorisees = new ArrayList();
            } else {
                conf = doc.cfg.getElementConf((Element) je.noeud);
                Element parentdef = conf.getBaliseDef(je.noeud.getNodeName());
                autorisees = conf.listeSousbalises(parentdef);
            }
        }
        Position ppos;
        try {
            ppos = doc.createPosition(pos);
        } catch (BadLocationException ble) {
            System.err.println("BadLocationException: " + ble.getMessage());
            ppos = null;
        }
        for (int i = 0; i < autorisees.size(); i++) {
            String nombalise = (String) autorisees.get(i);
            Element balisedef = conf.getBaliseDef(nombalise);

            if (balisedef != null) {
                boolean cache = "true".equals(balisedef.getAttribute("cache"));
                if (!("style".equals(doc.cfg.typeBalise(balisedef)))
                        && (!cache)) {
                    if (je == null
                            || conf.insertionPossible(je, ppos, balisedef))
                        popup.add(new ActionInsertionBalise(doc, balisedef));
                }

            }
        }

        if (autorisees.size() > 0) { // Seperator between elements and
            // Copy'n'Paste
            popup.addSeparator();
        }

        if (getSelectionEnd() != getSelectionStart()) { // Copy allowed ?
            popup.add(new ActionCouper());
            popup.add(new ActionCopier());
        }

        popup.add(new ActionColler());

        if (je != null && conf != null) {
            popup.addSeparator();
            popup.add(new ActionAide(conf.getElementDef((Element) je.noeud)));
        }

        popup.show(this, pt.x, pt.y);
    }

    class ActionAide extends AbstractAction {
        Element balisedef;

        ActionAide(Element balisedef) {
            super(JaxeResourceBundle.getRB().getString("aide.element") + " "
                    + doc.cfg.nomBalise(balisedef));
            this.balisedef = balisedef;
        }

        public void actionPerformed(ActionEvent e) {
            JFrame ancestor;
            if (getTopLevelAncestor() instanceof JFrame)
                ancestor = (JFrame) getTopLevelAncestor();
            else
                ancestor = null;
            DialogueAideElement dlg = new DialogueAideElement(balisedef,
                    doc.cfg.getDefConf(balisedef),
                    ancestor);
            dlg.show();
        }
    }

    public void selectZone(int debut, int fin, boolean select, boolean modsel) {
        ArrayList tel = doc.rootJE.elementsDans(debut, fin - 1);
        if (select) {
            // on change la sélection pour ne pas inclure des moitié d'éléments
            // (sauf pour le texte)
            int debut2;
            int fin2;
            int ndebut = debut;
            int nfin = fin;

            do {
                debut2 = ndebut;
                fin2 = nfin;
                JaxeElement firstel = doc.rootJE.elementA(debut2);
                if (firstel instanceof JETexte || firstel instanceof JEStyle)
                    firstel = firstel.getParent();
                while (firstel.debut.getOffset() == debut2 && firstel.getParent() instanceof JESwing)
                    firstel = firstel.getParent();
                if (firstel.fin.getOffset() < nfin - 1
                        && !tel.contains(firstel)
                        || firstel.noeud instanceof ProcessingInstruction) {
                    ndebut = firstel.fin.getOffset() + 1;
                }
                if (firstel.fin.getOffset() == nfin - 1
                        && !tel.contains(firstel)) {
                    nfin = firstel.fin.getOffset();
                }
                if (firstel.debut.getOffset() == ndebut
                        && !tel.contains(firstel)
                        && !(firstel instanceof JESwing))
                    ndebut++;
                JaxeElement lastel = doc.rootJE.elementA(fin2);
                if (lastel != null && lastel.fin.getOffset() == fin2 && lastel.getParent() instanceof JESwing)
                    lastel = lastel.getParent();
                if (lastel != null && lastel.debut.getOffset() == fin2)
                    lastel = lastel.getParent();
                if (lastel instanceof JETexte || lastel instanceof JEStyle)
                    lastel = lastel.getParent();
                if (doc.rootJE.elementA(nfin - 1).noeud instanceof ProcessingInstruction) {
                    lastel = doc.rootJE.elementA(nfin - 1);
                    nfin = lastel.debut.getOffset();
                } else if (lastel == null)
                    nfin = fin2 - 1;
                else if (lastel.debut.getOffset() == ndebut
                        && !tel.contains(lastel)
                        && !(lastel instanceof JESwing))
                    ndebut++;
                else if (lastel.debut.getOffset() > ndebut
                        && !tel.contains(lastel)) {
                    nfin = lastel.debut.getOffset();
                }
                if (nfin < ndebut)
                    nfin = ndebut;
            } while (ndebut != debut2 || nfin != fin2);

            if (modsel && (ndebut != debut || nfin != fin)) {
                if (nfin == ndebut)
                    nfin = ndebut = debut;
                setCaretPosition(ndebut);
                moveCaretPosition(nfin);
            }
            if (ndebut != debut || nfin != fin)
                tel = doc.rootJE.elementsDans(ndebut, nfin - 1);
        }
        for (int i = 0; i < tel.size(); i++) {
            JaxeElement je = (JaxeElement) tel.get(i);
            je.selection(select);
        }
    }

    /**
     * Positionne le document à la ligne indiquée (la première ligne a le numéro
     * 1)
     */
    public void allerLigne(int ligne) {
        if (ligne > 0)
            ligne--;
        else
            ligne = 0;
        int pos = doc.getDefaultRootElement().getElement(ligne)
                .getStartOffset();
        // bidouille pour afficher la position en haut de la fenêtre
        try {
            scrollRectToVisible(modelToView(doc.getLength()));
            scrollRectToVisible(modelToView(pos));
        } catch (BadLocationException ex) {
        }
    }

    public void debutIgnorerEdition() {
        ignorerEdition = true;
    }

    public void finIgnorerEdition() {
        ignorerEdition = false;
    }

    class EditSpecial extends CompoundEdit {

        String titre;

        public EditSpecial(String titre) {
            this.titre = titre;
        }

        public String getPresentationName() {
            return (titre);
        }

        public String getUndoPresentationName() {
            return (JaxeResourceBundle.getRB().getString("menus.Annuler") + " " + titre);
        }

        public String getRedoPresentationName() {
            return (JaxeResourceBundle.getRB().getString("menus.Retablir")
                    + " " + titre);
        }
    }

    /**
     * Edition spéciale: combinaison d'un ensemble de JaxeUndoableEdit.
     */
    public void debutEditionSpeciale(String titre, boolean ignorerEdition) {
        if (niveauEditionSpeciale < 0)
            System.err.println("Erreur: niveauEditionSpeciale < 0 !");
        if (niveauEditionSpeciale == 0) {
            editSpecial = new EditSpecial(titre);
            editionSpeciale = true;
            this.ignorerEdition = ignorerEdition;
        } else {
            ignorerEditionStack.push(new Boolean(ignorerEdition));
            this.ignorerEdition = ignorerEdition;
        }
        niveauEditionSpeciale += 1;
    }

    public void finEditionSpeciale() {
        niveauEditionSpeciale -= 1;
        if (niveauEditionSpeciale < 0)
            System.err.println("Erreur: niveauEditionSpeciale < 0 !");
        if (niveauEditionSpeciale == 0) {
            editSpecial.end();
            undo.addEdit(editSpecial);
            miseAJourAnnulation();
            editionSpeciale = false;
            ignorerEdition = false;
            editSpecial = null;
        } else {
            this.ignorerEdition = ((Boolean) ignorerEditionStack.pop())
                    .booleanValue();
        }
    }

    public void addEdit(UndoableEdit edit) {
        if (editionSpeciale) {
            editSpecial.addEdit(edit);
        } else {
            getUndo().addEdit(edit);
            miseAJourAnnulation();
        }
    }

    //This one listens for edits that can be undone.
    protected class MyUndoableEditListener implements UndoableEditListener {

        public void undoableEditHappened(UndoableEditEvent e) {
            //Remember the edit and update the menus.
            if (!ignorerEdition) {
                undo.addEdit(e.getEdit());
                miseAJourAnnulation();
            }
        }
    }

    public void couper() {
        int debut = getSelectionStart();
        int fin = getSelectionEnd();
        couper(debut, fin);
    }

    /**
     * Cuts something out of the document
     * @param debut Startposition
     * @param fin Endposition
     */
    public void couper(int debut, int fin) {
        JaxeElement firstel = doc.rootJE.elementA(debut);
        JaxeElement lastel = doc.rootJE.elementA(fin - 1);
        if (firstel == lastel && firstel instanceof JETexte) {
            pressePapier = null;
            ppTexte = null;
            cut();
        } else {
            Object pp = doc.copier(debut, fin);
            if (pp != null) {
                String s = doc.pp2string(pp);
                //Clipboard clip = getToolkit().getSystemClipboard();
                //StringSelection contents = new StringSelection(s);
                //clip.setContents(contents, this); // va appeler lostOwnership
                pressePapier = pp;
                ppTexte = s;
                try {
                    doc.remove(debut, fin - debut);
                } catch (BadLocationException ex) {
                    System.err.println("BadLocationException: "
                            + ex.getMessage());
                }
            } else
                getToolkit().beep();
        }
        verifRaccourci();
    }

    public void copier() {
        int debut = getSelectionStart();
        int fin = getSelectionEnd();
        JaxeElement firstel = doc.rootJE.elementA(debut);
        JaxeElement lastel = doc.rootJE.elementA(fin - 1);
        if (firstel == lastel
                && (firstel instanceof JETexte)) {
            pressePapier = null;
            ppTexte = null;
            copy();
        } else {
            Object pp = doc.removeProcessingInstructions(doc.copier(debut, fin));
            if (pp != null) {
                String s = doc.pp2string(pp);
                //Clipboard clip = getToolkit().getSystemClipboard();
                //StringSelection contents = new StringSelection(s);
                //clip.setContents(contents, this); // va appeler lostOwnership
                pressePapier = pp;
                ppTexte = s;
            } else
                getToolkit().beep();
        }
        verifRaccourci();
    }
    
    /**
     * Le presse-papier interne est maintenant utilisé: on remplace action-v par défaut
     * (qui permet d'utiliser le presse-papier du système) par une action
     * permettant de coller des arbres XML.
     */
    private void verifRaccourci() {
        KeyStroke cmdv = KeyStroke.getKeyStroke(KeyEvent.VK_V, cmdMenu);
        Keymap kmap = getKeymap();
        Action actionv = kmap.getAction(cmdv);
        if (!(actionv instanceof ActionColler)) {
            kmap.removeKeyStrokeBinding(cmdv);
            kmap.addActionForKeyStroke(cmdv, new ActionColler());
        }
    }
    
    public boolean coller() {
        boolean accept = false;
        if (pressePapier != null) {
            try {
                accept = doc.coller(pressePapier, doc.createPosition(getCaretPosition()));
            } catch (BadLocationException ex) {
                System.err.println("BadLocationException: " + ex.getMessage());
            }
        } else {
            doc.coller(this);
            accept = true;
        }
        return(accept);
    }

    private boolean hasOnlyTextnodes(Node n) {
        if (n.hasChildNodes()) {
            Node child = n.getFirstChild();
            while (child != null) {
                if (child.getNodeType() != Node.TEXT_NODE) return false;
                child = child.getNextSibling();
            }
        }
        return true;
    }
    
    private void copieEnfants(Node source, Node target) {
        Node child = source.getFirstChild();
        while (child != null) {
            target.appendChild(child);
            child = source.getFirstChild();
        }
    }

    public void toutSelectionner() {
        setCaretPosition(0);
        moveCaretPosition(doc.getLength());
    }

    public void rechercher() {
        if (dlg == null) {
            JFrame ancestor;
            if (getTopLevelAncestor() instanceof JFrame)
                ancestor = (JFrame) getTopLevelAncestor();
            else
                ancestor = null;
            dlg = new DialogueRechercher(doc, this, ancestor);
        }
        dlg.show();
    }

    public void rechercher(String s) {
        texteRecherche = s;
        int len = texteRecherche.length();
        int ind = -1;
        String text;
        // recherche bourrin
        try {
            for (int i = 0; i < doc.getLength() - len; i++) {
                text = doc.getText(i, len);
                if (text.equals(texteRecherche)) {
                    ind = i;
                    break;
                }
            }
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return;
        }
        if (ind != -1) {
            setCaretPosition(ind);
            moveCaretPosition(ind + len);
        } else
            getToolkit().beep();
    }

    public void suivant() {
        if (dlg != null) {
            texteRecherche = dlg.getTexteRecherche();
            if (dlg.RechXpath)
                dlg.suivantXpath(getSelectionStart());
            else    
                dlg.suivant(getSelectionStart());
    	}
    }

    public void lostOwnership(Clipboard clipboard, Transferable contents) { // ne marche pas :(
        pressePapier = null;
    }

    public void ajouterEcouteurArbre(EcouteurMAJ ec) {
        ecouteursArbre.add(ec);
    }

    public void retirerEcouteurArbre(EcouteurMAJ ec) {
        ecouteursArbre.remove(ec);
    }

    public void miseAJourArbre() {
        for (int i = 0; i < ecouteursArbre.size(); i++)
            ((EcouteurMAJ) ecouteursArbre.get(i)).miseAJour();
    }

    public void ajouterEcouteurAnnulation(EcouteurMAJ ec) {
        ecouteursAnnulation.add(ec);
    }

    public void retirerEcouteurAnnulation(EcouteurMAJ ec) {
        ecouteursAnnulation.remove(ec);
    }

    public void miseAJourAnnulation() {
        for (int i = 0; i < ecouteursAnnulation.size(); i++)
            ((EcouteurMAJ) ecouteursAnnulation.get(i)).miseAJour();
    }

    //This listens for and reports caret movements.
    protected class MyCaretListener implements CaretListener {

        int vdot = 0;

        int vmark = 0;

        public void caretUpdate(CaretEvent e) {
            int dot = e.getDot();
            int mark = e.getMark();
            if (dot == mark) { // no selection
                if (vmark - vdot > 0) // on déselectionne
                    selectZone(vdot, vmark, false, true);
            } else { //la sélection des images du texte n'est pas gérée par
                // Swing !
                if (dot > mark) {
                    dot += mark; // faut pas gâcher les variables
                    mark = dot - mark;
                    dot = dot - mark;
                }
                if (vdot != dot || vmark != mark)
                    selectZone(vdot, vmark, false, true);
                selectZone(dot, mark, true, true);
            }
            vdot = dot;
            vmark = mark;
        }
    }

    public void setTabs(int charactersPerTab) {
        FontMetrics fm = getFontMetrics(getFont());
        int charWidth = fm.charWidth('w');
        int tabWidth = charWidth * charactersPerTab;

        TabStop[] tabs = new TabStop[10];

        for (int j = 0; j < tabs.length; j++) {
            int tab = j + 1;
            tabs[j] = new TabStop(tab * tabWidth);
        }

        TabSet tabSet = new TabSet(tabs);
        SimpleAttributeSet attributes = new SimpleAttributeSet();
        StyleConstants.setTabSet(attributes, tabSet);
        int length = doc.getLength();
        debutIgnorerEdition();
        doc.setParagraphAttributes(0, length, attributes, false);
        finIgnorerEdition();
    }

    // evil kludge for Java bug 4839979
    public void add(Component comp, Object constraints) {
        if (System.getProperty("java.version").startsWith("1.4.2")
                && comp.getClass().getName().indexOf(
                        "ComponentView$Invalidator") != -1) {
            if (((Container) comp).getComponentCount() > 0) {
                // add a dummy component to the Invalidator
                Component child = ((Container) comp).getComponent(0);
                ((Container) comp).add(new Component() {
                });
                super.add(comp, constraints);
            }
        } else
            super.add(comp, constraints);
    }

}