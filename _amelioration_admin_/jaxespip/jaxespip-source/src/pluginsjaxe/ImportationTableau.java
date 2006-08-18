/*
Jaxe - Editeur XML en Java

Copyright (C) 2005 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package pluginsjaxe;

import java.awt.*;
import java.awt.event.*;
import java.util.ArrayList;
import javax.swing.*;
import javax.swing.text.BadLocationException;
import javax.swing.text.DefaultEditorKit;
import javax.swing.text.Keymap;
import javax.swing.text.Position;

import org.w3c.dom.*;

import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeUndoableEdit;
import jaxe.JEFactory;
import jaxe.Fonction;
import jaxe.JaxeResourceBundle;
import jaxe.elements.JETexte;


/**
 * Fonction permettant d'importer un tableau sous forme de texte tabulé.
 * compilation:
 * javac -encoding ISO-8859-1 -classpath .:Jaxe.jar pluginsjaxe/ImportationTableau.java
 */
public class ImportationTableau implements Fonction {

    JaxeDocument doc;
    Element deftableau;
    int offset;

    public boolean appliquer(JaxeDocument doc, int start, int end) {
        this.doc = doc;
        offset = start;
        deftableau = null;
        JaxeElement je = doc.elementA(start);
        if (je instanceof JETexte)
            je = je.getParent();
        if (je.noeud instanceof Element) {
            Element el = (Element)je.noeud;
            Element defbalise = doc.cfg.getElementDef(el);
            ArrayList enfants = doc.cfg.listeSousbalises(defbalise);
            for (int i=0; i<enfants.size(); i++) {
                Element eldef = doc.cfg.getBaliseDef((String)enfants.get(i));
                String typeel = doc.cfg.typeBalise(eldef);
                if ("tabletexte".equals(typeel))
                    deftableau = eldef;
            }
        }
        if (deftableau == null) {
            JOptionPane.showMessageDialog(doc.jframe,
                "Erreur: le curseur doit se trouver à un endroit où le tableau peut être inséré.",
                "Erreur", JOptionPane.ERROR_MESSAGE);
            return(false);
        }
        DialogueTexte dlg = new DialogueTexte(doc.jframe, "Importation de tableau",
            "Collez le tableau tabulé dans la zone de texte avec ctrl-v.");
        dlg.show();
        return(true);
    }
    
    public boolean importer(String texte) {
        Element eltableau = JaxeElement.nouvelElementDOM(doc, deftableau);
        String TRtag = doc.cfg.getParamFromDefinition(deftableau, "trTag", "tr");
        Element deftr = doc.cfg.getBaliseDef(TRtag);
        String TDtag = doc.cfg.getParamFromDefinition(deftableau, "tdTag", "td");
        Element deftd = doc.cfg.getBaliseDef(TDtag);
        int pos = 0;
        int posligne;
        String ligne;
        String cellule;
        String subtexte = texte;
        String subligne;
        int indret, indtab;
        Element tr, td;
        Node textnode;
        int noligne = 1;
        int nbcols = 0;
        int nocol;
        while (pos < texte.length()) {
            subtexte = texte.substring(pos);
            indret = subtexte.indexOf("\n");
            if (indret == -1)
                indret = subtexte.length();
            pos += indret + 1;
            ligne = subtexte.substring(0, indret);
            if (ligne.trim().length() == 0)
                continue;
            tr = JaxeElement.nouvelElementDOM(doc, deftr);
            posligne = -1;
            nocol = 0;
            while (posligne < ligne.length()) {
                posligne++;
                subligne = ligne.substring(posligne);
                indtab = subligne.indexOf("\t");
                if (indtab == -1)
                    indtab = subligne.length();
                posligne += indtab;
                cellule = subligne.substring(0, indtab).trim();
                td = JaxeElement.nouvelElementDOM(doc, deftd);
                textnode = doc.DOMdoc.createTextNode(cellule);
                td.appendChild(textnode);
                tr.appendChild(td);
                if (noligne == 1)
                    nbcols++;
                nocol++;
            }
            if (nocol != nbcols) {
                JOptionPane.showMessageDialog(doc.jframe,
                    "Erreur: nombre de colonnes incorrect à la ligne " + noligne,
                    "Erreur", JOptionPane.ERROR_MESSAGE);
                return(false);
            }
            eltableau.appendChild(tr);
            textnode = doc.DOMdoc.createTextNode("\n");
            eltableau.appendChild(textnode);
            noligne++;
        }
        
        Position posInsertion;
        try {
            posInsertion = doc.createPosition(offset);
        } catch (BadLocationException ble) {
            System.err.println("BadLocationException: " + ble.getMessage());
            return(false);
        }
        JaxeElement newje = JEFactory.createJE("tabletexte", doc, deftableau, (Element)null);
        newje.inserer(posInsertion, eltableau);
        doc.textPane.addEdit(new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje));
        return(true);
    }
    
    class DialogueTexte extends JDialog implements ActionListener {
        JTextArea tarea;
        public DialogueTexte(Frame frame, String titre, String question) {
            super(frame, titre, false);
            JPanel cpane = new JPanel(new BorderLayout());
            setContentPane(cpane);
            cpane.add(new JLabel(question), BorderLayout.NORTH);
            tarea = new JTextArea(null, 15, 60);
            int cmdMenu = Toolkit.getDefaultToolkit().getMenuShortcutKeyMask();
            Keymap kmap = tarea.getKeymap();
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
            cpane.add(tarea, BorderLayout.CENTER);
            JPanel bpane = new JPanel(new FlowLayout(FlowLayout.RIGHT));
            JButton boutonAnnuler = new JButton(JaxeResourceBundle.getRB().getString("bouton.Annuler"));
            boutonAnnuler.addActionListener(this);
            boutonAnnuler.setActionCommand("Annuler");
            bpane.add(boutonAnnuler);
            JButton boutonOK = new JButton(JaxeResourceBundle.getRB().getString("bouton.OK"));
            boutonOK.addActionListener(this);
            boutonOK.setActionCommand("OK");
            bpane.add(boutonOK);
            cpane.add(bpane, BorderLayout.SOUTH);
            getRootPane().setDefaultButton(boutonOK);
            cpane.setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));
            pack();
            addWindowListener(new WindowAdapter() {
                boolean gotFocus = false;
                public void windowActivated(WindowEvent we) {
                    if (!gotFocus) {
                        tarea.requestFocus();
                        gotFocus = true;
                    }
                }
            });
            if (frame != null) {
                Rectangle r = frame.getBounds();
                setLocation(r.x + r.width/4, r.y + r.height/4);
            } else {
                Dimension screen = Toolkit.getDefaultToolkit().getScreenSize();
                setLocation((screen.width - getSize().width)/3,(screen.height - getSize().height)/3);
            }
            setVisible(true);
        }
        public void actionPerformed(ActionEvent e) {
            String cmd = e.getActionCommand();
            if ("Annuler".equals(cmd))
                setVisible(false);
            else if ("OK".equals(cmd)) {
                if (ImportationTableau.this.importer(tarea.getText()))
                    setVisible(false);
            }
        }
    }
}
