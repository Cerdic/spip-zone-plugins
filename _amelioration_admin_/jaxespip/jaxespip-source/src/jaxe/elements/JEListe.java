/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.ArrayList;
import java.util.Properties;

import javax.swing.JButton;
import javax.swing.text.BadLocationException;
import javax.swing.text.Position;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;

import jaxe.JaxeEditEvent;
import jaxe.JaxeElement;
import jaxe.JaxeDocument;
import jaxe.JaxeResourceBundle;
import jaxe.JaxeUndoableEdit;
import jaxe.JEFactory;
import jaxe.Preferences;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * Liste d'éléments JEItem, à points ou numérotée.
 * Type d'élément Jaxe: 'liste'
 * paramètre: typeListe: POINTS | NUMEROS
 */
public class JEListe extends JEZone implements ActionListener {
    public int typeListe;
    public static int POINTS = 1;
    public static int NUMEROS = 2;

    public JEListe(JaxeDocument doc) {
        super(doc);
    }

    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        
        int offsetdebut = pos.getOffset();
        
        Element defbalise = doc.cfg.getElementDef(el);
        if (defbalise != null) {
            attributsTitre = doc.cfg.getValeursParam(defbalise, "titreAtt");
            if (attributsTitre == null)
                attributsTitre = new ArrayList();
            if (attributsTitre.size() == 0)
                attributsTitre.add(titreAttParDefaut);
            String param = defbalise.getAttribute("param");
            param = doc.cfg.getParamFromDefinition(defbalise, "typeListe", param);
            if ("NUMEROS".equals(param))
                typeListe = NUMEROS;
            else
                typeListe = POINTS;
        }
        
        String titreBstart = el.getTagName();
        String titreBend = "< " + el.getTagName();
        String valeurTitre = null;
        for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
            if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        if (valeurTitre != null) {
            titreBstart += " '" + valeurTitre + "'";
            titreBend += " '" + valeurTitre +"'";
        }
        titreBstart += " >";
        
        ArrayList enfants = doc.cfg.listeSousbalises(defbalise);
        if (enfants.size() == 1)
            lstart = new BoutonListe(titreBstart);
        else
            lstart = new MonBouton(titreBstart, false);
        Position newpos = insertComponent(pos, lstart);
        
        Style s = null;
        Properties prefs = Preferences.getPref();
        if (prefs == null || !"true".equals(prefs.getProperty("consIndent"))) {
            s = doc.textPane.addStyle(null, null);
            StyleConstants.setLeftIndent(s, (float)20.0*(indentations()+1));
            doc.setParagraphAttributes(offsetdebut, 1, s, false);
        }
        
        creerEnfants(newpos);
        
        if (enfants.size() == 1)
            lend = new BoutonListe(titreBend);
        else
            lend = new MonBouton(titreBend, false);
        
        newpos = insertComponent(newpos, lend);

        if (prefs == null || !"true".equals(prefs.getProperty("consIndent"))) {
            StyleConstants.setLeftIndent(s, (float)20.0*indentations());
            doc.setParagraphAttributes(offsetdebut, 1, s, false);
            doc.setParagraphAttributes(newpos.getOffset()-1, 1, s, false);
        }
    }
    
    public boolean avecIndentation() {
        return(true);
    }
    
    /**
     * Bouton pour les listes, comme JaxeElement.MonBouton avec un bouton '+' en plus.
     */
    public class BoutonListe extends MonBouton {
        JButton bajitem;
        public BoutonListe(String texte) {
            super(texte, false);
            bajitem = new JButton("+");
            bajitem.addActionListener(JEListe.this);
            bajitem.setActionCommand("ajitem");
            bajitem.setFont(bajitem.getFont().deriveFont((float)9));
            bajitem.putClientProperty("JButton.buttonType", "toolbar");
            add(bajitem);
        }
        public Dimension getPreferredSize() {
            Dimension d = super.getPreferredSize();
            d.width += bajitem.getMinimumSize().width;
            return(d);
        }
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("ajitem".equals(cmd))
            ajouterItem();
    }
    
    protected void ajouterItem() {
        Element el = (Element)noeud;
        Element defbalise = doc.cfg.getElementDef(el);
        ArrayList enfants = doc.cfg.listeSousbalises(defbalise);
        if (enfants.size() != 1) {
            System.err.println("ajouterItem: erreur: liste avec plus d'un élément enfant ?!?");
            return;
        }
        Element itemdef = doc.cfg.getBaliseDef((String)enfants.get(0));
        String typeitem = doc.cfg.typeBalise(itemdef);
        JaxeElement newje = JEFactory.createJE(typeitem, doc, itemdef, (Element)null);
        
        Node newel = null;
        if (newje != null)
            newel = newje.nouvelElement(itemdef);
        
        if (newel != null) { // null si annulation
            boolean inutileDajouterUnRetour = false;
            Node texteavant = noeud.getLastChild();
            if (texteavant != null && texteavant.getNodeType() == Node.TEXT_NODE) {
                String s = texteavant.getNodeValue();
                if (s != null && s.endsWith("\n\n"))
                    inutileDajouterUnRetour = true;
            }
            Position posInsertion;
            if (inutileDajouterUnRetour) {
                try {
                    posInsertion = doc.createPosition(fin.getOffset() - 1);
                } catch (BadLocationException ble) {
                    System.err.println("BadLocationException: " + ble.getMessage());
                    posInsertion = fin;
                }
            } else
                posInsertion = fin;
            newje.inserer(posInsertion, newel);
            doc.textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString("annulation.Ajouter"), false);
            doc.textPane.addEdit(new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje));
            //doc.fireElementAddedEvent(new JaxeEditEvent(this, newje), posInsertion);
            //fireElementAddedEvent() has protected access in jaxe.JaxeDocument :(
            majValidite();
            newje.majValidite();
            doc.textPane.miseAJourArbre();
            
            if (!inutileDajouterUnRetour) {
                JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, doc, "\n", fin.getOffset());
                jedit.doit();
                //doc.fireTextAddedEvent(new JaxeEditEvent(this, fin.getOffset(), "\n"));
            }
            doc.textPane.finEditionSpeciale();
        }
    }
}

