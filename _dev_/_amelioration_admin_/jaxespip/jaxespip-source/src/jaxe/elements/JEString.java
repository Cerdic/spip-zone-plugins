/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.util.ArrayList;

import javax.swing.JFrame;
import javax.swing.text.BadLocationException;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;

import jaxe.DialogueAttributs;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

/**
 * Petite zone de texte.
 * Type d'élément Jaxe: 'string'
 * paramètre: style: NORMAL | GRAS | ITALIQUE | EXPOSANT | INDICE | SOULIGNE
 */
public class JEString extends JaxeElement {

    MonBouton lstart = null;
    MonBouton lend = null;
    ArrayList attributsTitre = null;

    public JEString(JaxeDocument doc) {
        this.doc = doc;
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        
        String titre = el.getTagName();
        Element defbalise = null;
        if (doc.cfg != null)
            defbalise = doc.cfg.getElementDef(el);
        String valeurTitre = null;
        if (defbalise != null) {
            attributsTitre = doc.cfg.getValeursParam(defbalise, "titreAtt");
            if (attributsTitre != null)
                for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                    if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                        valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        }
        if (valeurTitre != null)
            titre += " '" + valeurTitre + "'";
        titre += " >";
        lstart = new MonBouton(titre, false);
        if (el.getPrefix() != null)
            lstart.setEnsembleCouleurs(1);
        int offsetdebut = pos.getOffset();
        Position newpos = insertComponent(pos, lstart);
        
        creerEnfants(newpos);
        
        lend = new MonBouton("< " + el.getTagName(), false);
        if (el.getPrefix() != null)
            lend.setEnsembleCouleurs(1);
        insertComponent(newpos, lend);
        if (defbalise != null && newpos.getOffset() - offsetdebut - 1 > 0) {
            SimpleAttributeSet style = attStyle(null);
            if (style != null)
                doc.setCharacterAttributes(offsetdebut, newpos.getOffset() - offsetdebut - 1, style, false);
        }
    }
    
    public Node nouvelElement(Element defbalise) {
        String nombalise = doc.cfg.nomBalise(defbalise);
        Element newel = nouvelElementDOM(doc, defbalise);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                JaxeResourceBundle.getRB().getString("zone.NouvelleBalise") + " " + nombalise, defbalise, newel);
            if (!dlg.afficher())
                return null;
            try {
                dlg.enregistrerReponses();
            } catch (Exception ex) {
                System.err.println(ex.getClass().getName() + ": " + ex.getMessage());
                return(null);
            }
        }
        
        return(newel);
    }
    
    public Position insPosition() {
        try {
            return(doc.createPosition(debut.getOffset() + 1));
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
                getString("string.String") + ": " + el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
                majAffichage();
            }
            dlg.dispose();
        }
    }
    
    public void majAffichage() {
        Element el = (Element)noeud;
        
        String titreBstart = el.getTagName();
        String valeurTitre = null;
        if (attributsTitre != null) {
            for (int i=0; i<attributsTitre.size() && valeurTitre == null; i++)
                if (!"".equals(el.getAttribute((String)attributsTitre.get(i))))
                    valeurTitre = el.getAttribute((String)attributsTitre.get(i));
        }
        if (valeurTitre != null)
            titreBstart += " '" + valeurTitre + "'";
        titreBstart += " >";
        String titreBend = "< " + el.getTagName();
        lstart.setText(titreBstart);
        lend.setText(titreBend);
        doc.imageChanged(lstart);
        doc.imageChanged(lend);
    }
}
