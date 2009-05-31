/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.util.ArrayList;
import java.util.Properties;

import javax.swing.JFrame;
import javax.swing.text.BadLocationException;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;
import jaxe.Preferences;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * Zone de texte. Le texte à l'intérieur est indenté.
 * Type d'élément Jaxe: 'zone'
 * paramètre: titreAtt: un attribut pouvant servir de titre
 * paramètre: style: NORMAL | GRAS | ITALIQUE | EXPOSANT | INDICE | SOULIGNE
 */
public class JEZone extends JaxeElement {

    static String newline = "\n";
    /*JButton bstart = null;
    JButton bend = null;*/
    MonBouton lstart = null;
    MonBouton lend = null;
    static final String titreAttParDefaut = "titre";
    ArrayList attributsTitre = null;
    boolean valide = true;

    public JEZone(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        
        int offsetdebut = pos.getOffset();
        
        Element defbalise = null;
        if (doc.cfg != null)
            defbalise = doc.cfg.getElementDef(el);
        if (defbalise != null) {
            attributsTitre = doc.cfg.getValeursParam(defbalise, "titreAtt");
            if (attributsTitre == null)
                attributsTitre = new ArrayList();
            String param = defbalise.getAttribute("param");
            if (!"".equals(param))
                attributsTitre.add(param);
            if (attributsTitre.size() == 0)
                attributsTitre.add(titreAttParDefaut);
        }
        
        String titreBstart = el.getTagName();
        String titreBend = "< " + el.getTagName();
        String valeurTitre = null;
        if (attributsTitre != null)
            for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                    valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        if (valeurTitre != null) {
            titreBstart += " '" + valeurTitre + "'";
            titreBend += " '" + valeurTitre +"'";
        }
        titreBstart += " >";

        lstart = new MonBouton(titreBstart, false);
        if (el.getPrefix() != null)
            lstart.setEnsembleCouleurs(1);
        Position newpos = insertComponent(pos, lstart);
        
        Style s = null;
        Properties prefs = Preferences.getPref();
        // prefs peut être null dans le cas où JaxeTextPane est inclus
        // dans une autre application que Jaxe
        if (prefs == null || !"true".equals(prefs.getProperty("consIndent"))) {
            s = doc.textPane.addStyle(null, null);
            StyleConstants.setLeftIndent(s, (float)20.0*(indentations()+1));
            doc.setParagraphAttributes(offsetdebut, 1, s, false);
        }
        
        creerEnfants(newpos);
        
        lend = new MonBouton(titreBend, false);
        if (el.getPrefix() != null)
            lend.setEnsembleCouleurs(1);
        newpos = insertComponent(newpos, lend);

        if (prefs == null || !"true".equals(prefs.getProperty("consIndent"))) {
            StyleConstants.setLeftIndent(s, (float)20.0*indentations());
            doc.setParagraphAttributes(offsetdebut, 1, s, false);
            doc.setParagraphAttributes(newpos.getOffset()-1, 1, s, false);
        }
        
        if (defbalise != null && newpos.getOffset() - offsetdebut - 1 > 0) {
            SimpleAttributeSet style = attStyle(null);
            if (style != null)
                doc.setCharacterAttributes(offsetdebut, newpos.getOffset() - offsetdebut - 1, style, false);
        }
    }
    
    public Node nouvelElement(Element defbalise) {
        Element newel = nouvelElementDOM(doc, defbalise);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            String nombalise = doc.cfg.nomBalise(defbalise);
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                JaxeResourceBundle.getRB().getString("zone.NouvelleBalise") + " " + nombalise, defbalise, newel);
            if (!dlg.afficher())
                return null;
            dlg.enregistrerReponses();
        }
        
        Node textnode = doc.DOMdoc.createTextNode(newline+newline);
        newel.appendChild(textnode);
        
        return(newel);
    }
    
    public boolean avecIndentation() {
        return(true);
    }
    
    public Position insPosition() {
        try {
            return(doc.createPosition(debut.getOffset() + 1 + newline.length()));
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return(null);
        }
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;

        Element defbalise = doc.cfg.getElementDef(el);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                getString("zone.Zone") + ": " + el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
                doc.textPane.miseAJourArbre();
                majAffichage();
            }
            dlg.dispose();
        }
    }
    
    public void majAffichage() {
        Element el = (Element)noeud;
        
        String titreBstart = el.getTagName();
        //String titreBend = "FIN " + el.getTagName();
        String titreBend = el.getTagName();
        String valeurTitre = null;
        if (attributsTitre != null)
            for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                    valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        if (valeurTitre != null) {
            titreBstart += " '" + valeurTitre + "'";
            titreBend += " '" + valeurTitre +"'";
        }
        titreBstart += " >";
        lstart.setValidite(valide);
        lend.setValidite(valide);
        titreBend = "< " + titreBend;
        lstart.setText(titreBstart);
        lend.setText(titreBend);
        doc.imageChanged(lstart);
        doc.imageChanged(lend);
    }
    
    public void majValidite() {
        boolean valide2 = doc.cfg.elementValide(this, false, null);
        if (valide2 != valide) {
            valide = valide2;
            majAffichage();
        }
    }
    
    /*class MyActionListener implements ActionListener {
        JEZone jei;
        JFrame jframe;
        public MyActionListener(JEZone obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }
        public void actionPerformed(ActionEvent e) {
            jei.afficherDialogue(jframe);
        }
    }*/

}
