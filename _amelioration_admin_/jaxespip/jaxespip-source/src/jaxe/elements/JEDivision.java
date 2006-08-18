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
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.Preferences;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * Zone de division. Les balises sont affichées comme des bandes prenant toute la largeur de la page,
 * et le texte à l'intérieur est indenté.
 * Type d'élément Jaxe: 'division'
 * paramètre: titreAtt: un attribut pouvant servir de titre
 */
public class JEDivision extends JaxeElement {

    static String newline = "\n";
    static final String titreAttParDefaut = "titre";
    ArrayList attributsTitre = null;
    MonBouton lstart = null;
    MonBouton lend = null;
    boolean valide = true;

    public JEDivision(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;

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
        
        //debut = null;
        int offsetdebut = pos.getOffset();
        //fin = null;
        //try {
            //Position newpos = doc.createPosition(pos.getOffset());
            
            String titreBstart = el.getTagName();
            String titreBend = "< " + el.getTagName();
            String valeurTitre = null;
            if (attributsTitre != null)
                for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                    if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                        valeurTitre = el.getAttribute((String)attributsTitre.get(i));
            if (valeurTitre != null) {
                titreBstart += " '" + valeurTitre + "'";
                titreBend += " '" + valeurTitre + "'";
            }
            titreBstart += " >";

            lstart = new MonBouton(titreBstart, true);
            if (el.getPrefix() != null)
                lstart.setEnsembleCouleurs(1);
            Position newpos = insertComponent(pos, lstart);
            //if (newpos.getOffset() == 0) // bug fix with insertString
            //	newpos = doc.createPosition(newpos.getOffset() + 1);
            
            /*
            // on insère un \n si on ne peut pas utiliser celui de l'élément suivant pour changer le style
            Node suivant = null;
            JaxeElement jesuivant = null;
            if (noeud.getNextSibling() != null && noeud.getNextSibling().getNodeType() == Node.TEXT_NODE) {
                suivant = noeud.getNextSibling();
                if (suivant.getNodeValue() != null && suivant.getNodeValue().startsWith(newline)) {
                    jesuivant = doc.getElementForNode(suivant);
                }
            }
            
            if (jesuivant == null) {
                if (suivant != null)
                    suivant.setNodeValue(suivant.getNodeValue().substring(newline.length()));
                doc.insertString(newpos.getOffset(), newline, null);
                newpos = doc.createPosition(newpos.getOffset()-newline.length());
            }
            
            Style s = doc.textPane.addStyle(null, null);
            StyleConstants.setLeftIndent(s, (float)20.0*indentations());
            doc.setParagraphAttributes(newpos.getOffset(), newline.length(), s, false);
            
            creerEnfants(newpos);
            
            //doc.insertString(newpos.getOffset(), newline, null);
            lend = new MonBouton(titreBend);
            lend.addMouseListener(new MyMouseListener(this, doc.jframe, false));
            insertComponent(newpos, lend);
            */
            
            /* au lieu du \n potentiellement ajouté, il vaut mieux:
             - insérer le premier bouton
             - le décaler vers la droite
             - insérer les enfants
             - ajouter le bouton de fin
             - décaler les 2 boutons vers la gauche
             
             Ca marche bien avec les bons \n après les balises de division, et le programme
             ne se plante pas s'il n'y a pas les \n (les indentations ne sont pas très jolies dans ce cas)
            */
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
            
            lend = new MonBouton(titreBend, true);
            if (el.getPrefix() != null)
                lend.setEnsembleCouleurs(1);
            newpos = insertComponent(newpos, lend);

            if (prefs == null || !"true".equals(prefs.getProperty("consIndent"))) {
                StyleConstants.setLeftIndent(s, (float)20.0*indentations());
                doc.setParagraphAttributes(offsetdebut, 1, s, false);
                doc.setParagraphAttributes(newpos.getOffset()-1, 1, s, false);
            }
            
            //debut = doc.createPosition(offsetdebut);
            //fin = doc.createPosition(newpos.getOffset() - 1);

        //} catch (BadLocationException ex) {
        //    System.err.println("BadLocationException: " + ex.getMessage());
        //}
    }
    
    public Node nouvelElement(Element defbalise) {
        Element newel = nouvelElementDOM(doc, defbalise);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            String nombalise = doc.cfg.nomBalise(defbalise);
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                "division: " + nombalise, defbalise, newel);
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
                "division: " + el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
                majAffichage();
                doc.textPane.miseAJourArbre();
            }
            dlg.dispose();
        }
    }
    
    public void majAffichage() {
        Element el = (Element)noeud;
        
        String titreBstart = el.getTagName();
        String titreBend = "< " + el.getTagName();
        String valeurTitre = null;
        for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
            if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        if (valeurTitre != null) {
            titreBstart += " '" + valeurTitre + "'";
            titreBend += " '" + valeurTitre + "'";
        }
        titreBstart += " >";
        lstart.setValidite(valide);
        lend.setValidite(valide);
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
    
    public void selection(boolean select) {
        super.selection(select);
        lstart.selection(select);
        lend.selection(select);
    }
}
