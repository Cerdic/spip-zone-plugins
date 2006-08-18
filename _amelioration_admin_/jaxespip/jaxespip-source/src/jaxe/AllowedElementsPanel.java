/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.event.ActionEvent;
import java.util.ArrayList;

import javax.swing.AbstractAction;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.text.BadLocationException;
import javax.swing.text.Position;

import jaxe.elements.JESwing;
import jaxe.elements.JETexte;

import org.w3c.dom.Element;

/**
 * Creates a Panel that shows a Button-List of all allowed Elements
 * @author tasche
 */
public class AllowedElementsPanel  extends JPanel  implements EcouteurMAJ, CaretListener {
    /** The JaxeDocument for this Panel */
    private JaxeDocument _doc;
    private ArrayList _listeDefs; // def des éléments affichés

    /**
     * Creates the JPanel
     * @param doc the Document for this Panel
     */
    public AllowedElementsPanel(JaxeDocument doc) {
        _doc = doc;
        _listeDefs = new ArrayList();
        miseAJour();
    }

    /**
     * Updates the Panel
     * @see jaxe.EcouteurMAJ#miseAJour()
     */
    public void miseAJour() {
        if (_doc.cfg == null)
            return;
        
        ArrayList nouvelleListeDefs = new ArrayList();
        
        int pos = _doc.textPane.getCaretPosition();
        JaxeElement elem = null;
        if (_doc.rootJE != null)
            elem = _doc.rootJE.elementA(pos);
        if (elem != null) {
            if ((elem.debut.getOffset() == pos && !(elem instanceof JESwing)) ||
                    elem instanceof JETexte)
                elem = elem.getParent();
        }
        
        ArrayList autorisees = null;
        Config conf = null;
        if (elem != null) {
            conf = _doc.cfg.getElementConf((Element)elem.noeud);
            Element def;
            if (conf == null)
                def = null;
            else
                def = conf.getElementDef((Element)elem.noeud);
            if (def != null)
                autorisees = conf.listeSousbalises(def);
        } else if (_doc.rootJE == null) {
            conf = _doc.cfg;
            autorisees = conf.listeRacines();
        }
        if (autorisees != null) {
            //Collections.sort(autorisees); le tri est gênant pour les séquences
            Position ppos;
            try {
                ppos = _doc.createPosition(pos);
            } catch (BadLocationException ble) {
                System.err.println("BadLocationException: " + ble.getMessage());
                ppos = null;
            }
            for (int i = 0; i < autorisees.size(); i++) {
                String nombalise = (String) autorisees.get(i);
                Element balisedef = conf.getBaliseDef(nombalise);
                if ((balisedef != null) && !"style".equals(conf.typeBalise(balisedef))) {
                    if (conf == null || ppos == null || elem == null ||
                            conf.insertionPossible(elem, ppos, balisedef)) {
                        nouvelleListeDefs.add(balisedef);
                    }
                }
            }
        }

        
        if (!nouvelleListeDefs.equals(_listeDefs)) {
            _listeDefs = nouvelleListeDefs;
            
            this.removeAll();
            this.setLayout(new BorderLayout());
            
            JPanel buttonPanel = new JPanel();
            
            buttonPanel.setLayout(new GridBagLayout());

            GridBagConstraints c1 = new GridBagConstraints();
            c1.gridwidth = 1;
            c1.anchor = GridBagConstraints.NORTHWEST;
            c1.weightx = 0;
            c1.weighty = 0;

            GridBagConstraints c2 = new GridBagConstraints();
            c2.gridwidth = GridBagConstraints.REMAINDER;
            c2.anchor = GridBagConstraints.NORTH;
            c2.weightx = 1.0;
            c2.fill = GridBagConstraints.HORIZONTAL;
            c2.weighty = 0;
            
            for (int i=0; i<_listeDefs.size(); i++) {
                Element balisedef = (Element)_listeDefs.get(i);
                if (conf != null) {
                    JButton baide = new JButton(new ActionAide(balisedef));
                    baide.setFont(baide.getFont().deriveFont((float)9));
                    baide.putClientProperty("JButton.buttonType", "toolbar"); // pour MacOS X
                    buttonPanel.add( baide, c1);
                }
                buttonPanel.add( new JButton(new ActionInsertionBalise(_doc, balisedef)), c2);
            }
            
            // The following lines are used to put the Buttons to the top of the Panel
            JPanel tmp = new JPanel();
            tmp.setPreferredSize(new Dimension(0, 0));
            tmp.setMinimumSize(new Dimension(0, 0));
            c2.fill = GridBagConstraints.NONE;
            c2.weighty = 1.0;
            buttonPanel.add(tmp, c2);

            JScrollPane scroll = new JScrollPane(buttonPanel);
            this.add(scroll, BorderLayout.CENTER);

            _doc.textPane.grabFocus();
            validate();
        }
    }

    /**
     * If the Carret was moved, update the component
     * @see javax.swing.event.CaretListener#caretUpdate(CaretEvent)
     */
    public void caretUpdate(CaretEvent e) {
        /*int pos = _doc.textPane.getCaretPosition();
        JaxeElement el = null;
        if (_doc.rootJE != null)
            el = _doc.rootJE.elementA(pos);
        if (el != null) {
            if ((el.debut.getOffset() == pos && !(el instanceof JESwing)) || el instanceof JETexte)
                el = el.getParent();
        }
        if (el != _elem)     (utile quand on ne teste pas insertionPossible)*/
        if (!_doc.textPane.getIgnorerEdition())
            miseAJour();
    }
    
    class ActionAide extends AbstractAction {
        Element balisedef;
        ActionAide(Element balisedef) {
            super("?");
            this.balisedef = balisedef;
        }
        public void actionPerformed(ActionEvent e) {
            DialogueAideElement dlg = new DialogueAideElement(balisedef, _doc.cfg.getDefConf(balisedef),
                (JFrame)_doc.textPane.getTopLevelAncestor());
            dlg.show();
        }
    }
}
