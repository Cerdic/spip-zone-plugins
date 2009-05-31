/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.util.ArrayList;

import javax.swing.JComboBox;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTextField;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;

import jaxe.elements.JESwing;
import jaxe.elements.JETexte;

import org.w3c.dom.Element;

/**
 * Creates a Panel that shows the Attributes of the Element
 * @author tasche
 */
public class AttributePanel extends JPanel implements EcouteurMAJ, CaretListener {

    /** The JaxeDocument for this Panel */
    private JaxeDocument _doc;
    /** The current Element */
    private JaxeElement _elem;

    /**
     * Creates the JPanel
     * @param doc the Document for this Panel
     */
    public AttributePanel(JaxeDocument doc) {
        _doc = doc;
        miseAJour();
    }

    /**
     * Updates the Panel
     * @see jaxe.EcouteurMAJ#miseAJour()
     */
    public void miseAJour() {
        
        this.removeAll();
        this.setLayout(new BorderLayout());

        if (_doc.rootJE != null && _doc.cfg != null) {
            JPanel attribPanel = new JPanel();
            
            int pos = _doc.textPane.getCaretPosition();
            _elem = _doc.rootJE.elementA(pos);
            
            if (_elem != null) {
                if (_elem instanceof JETexte || (_elem.debut.getOffset() == pos &&
                        !(_elem instanceof JESwing)))
                    _elem = _elem.getParent();
                if (_elem != null)
                    attribPanel = createInputLists((Element)_elem.noeud);
            }

            // The following lines are used to put the Buttons to the top of the Panel
            JPanel tmp = new JPanel();
            tmp.setPreferredSize(new Dimension(0, 0));
            tmp.setMinimumSize(new Dimension(0, 0));

            GridBagConstraints c = new GridBagConstraints();
            c.gridwidth = GridBagConstraints.REMAINDER;
            c.anchor = GridBagConstraints.NORTH;
            c.weightx = 1.0;
            c.fill = GridBagConstraints.NONE;
            c.weighty = 1.0;

            attribPanel.add(tmp, c);

            JScrollPane scroll = new JScrollPane(attribPanel);
            this.add(scroll, BorderLayout.CENTER);

            _doc.textPane.grabFocus();
            this.updateUI();
        }
    }

    /**
     * Creates the Panel with Input-Elements
     * @param el the Element to be displayed
     * @return the Panel
     */
    public JPanel createInputLists(Element el) {
        JPanel attribPanel = new JPanel();
        attribPanel.setLayout(new GridBagLayout());

        Element def = _doc.cfg.getElementDef(el);

        if (def != null) {
            GridBagConstraints c = new GridBagConstraints();
            c.gridwidth = GridBagConstraints.REMAINDER;
            c.anchor = GridBagConstraints.NORTH;
            c.weightx = 1.0;
            c.fill = GridBagConstraints.HORIZONTAL;
            c.weighty = 0;

            GridBagConstraints l = new GridBagConstraints();
            l.anchor = GridBagConstraints.CENTER;
            l.weightx = 0;
            l.fill = GridBagConstraints.HORIZONTAL;
            l.weighty = 0;
            l.insets = new Insets(0, 0, 0, 5);

            ArrayList attrlist = _doc.cfg.listeAttributs(def);
            for (int i = 0; i < attrlist.size(); i++) {
                Element att = (Element) attrlist.get(i);
                String st = _doc.cfg.nomAttribut(att);
                String elval = el.getAttribute(st);
                String[] lval = _doc.cfg.listeValeurs(att);
     
                JLabel label = new JLabel(st);
                if (_doc.cfg.estObligatoire(att))
                    label.setForeground(new Color(150, 0, 0)); // rouge foncé
                else
                    label.setForeground(new Color(0, 100, 0)); // vert foncé
                
                attribPanel.add(label, l);                    
                
                if (lval != null) {
                    ElementComboBox popup = new ElementComboBox(el, st);

                    if (!_doc.cfg.estObligatoire(att))
                        popup.addItem("");
                    for (int j = 0; j < lval.length; j++) {
                        String sval = lval[j];
                        popup.addItem(sval);
                        if (sval.equals(elval))
                            popup.setSelectedItem(sval);
                    }
                    popup.startListener();

                    attribPanel.add(popup, c);
                } else {
                    JTextField text = new JTextField(elval);
                    text.getDocument().addDocumentListener(new FieldListener(el, st));
                    attribPanel.add(text, c);
                }

            }
        }
        return attribPanel;
    }

    /**
     * If the Carret was moved, update the component
     * @see javax.swing.event.CaretListener#caretUpdate(CaretEvent)
     */
    public void caretUpdate(CaretEvent e) {
        int pos = _doc.textPane.getCaretPosition();
        JaxeElement el = null;
        if (_doc.rootJE != null)
            el = _doc.rootJE.elementA(pos);
        if (el != null) {
            if (el instanceof JETexte || (el.debut.getOffset() == pos && !(el instanceof JESwing)))
                el = el.getParent();
        }
        if (el != _elem)
            miseAJour();
    }

    /**
     * A ComboBox that changes the Element if the selected Item is changed
     * @author tasche
     */
    class ElementComboBox extends JComboBox {
        
        /** The Element that is displayed */
        private Element _el;
        /** The Attribute that is displayed */
        private String _attr;
        /** If true, it start listening to changes */
        private boolean _listen;
        
        /**
         * Creates a ComboBox for an Attribute
         * @param el the shown Element
         * @param attr the shown Attribute
         */
        public ElementComboBox(Element el, String attr) {
            _el = el;
            _attr = attr;
            _listen = false;
        }
    
        /**
         * Starts to listen to changes in the JComboBox
         * and updates the Element.
         */
        public void startListener() {
            _listen = true;
        }
    
        /**
         * If the selected Item is changed, the Attribute is 
         * updated
         */
        public void selectedItemChanged() {
            super.selectedItemChanged();
            if (_listen) {
                _el.setAttribute(_attr, (String) getSelectedItem());
            }
        }

    }
    
    /**
     * A Listener that changes the Attribute of an Element, 
     * if the Text is changed
     * @author tasche
     */
    class FieldListener implements DocumentListener {
        private Element _el;
        private String _attr;
        
        /**
         * Creates a Listener for a Document
         * @param el the shown Element
         * @param attr the shown Attribute
         */
        public FieldListener (Element el, String attr){
            _el = el;
            _attr = attr;            
        }
        
        /**
         * Changes the Attribute if the Document was changed 
         * @param e the DocumentEvent
         */
        public void changed(DocumentEvent e) {
            try {
                _el.setAttribute(_attr, e.getDocument().getText(0, e.getDocument().getLength()));
                JaxeElement jel = _doc.getElementForNode(_el);
                if (jel != null)
                    jel.majAffichage();
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        }

        /**
         * Changes the Attribute if the Document was changed 
         * @param e the DocumentEvent
         */
        public void changedUpdate(DocumentEvent e) {
            changed(e);
        }
        
        /**
         * Changes the Attribute if the Document was changed 
         * @param e the DocumentEvent
         */
        public void insertUpdate(DocumentEvent e){
            changed(e);
        }
        
        /**
         * Changes the Attribute if the Document was changed 
         * @param e the DocumentEvent
         */
        public void removeUpdate(DocumentEvent e){
            changed(e);
        }
     }
}
