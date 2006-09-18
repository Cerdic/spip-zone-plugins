/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.event.ActionEvent;

import javax.swing.text.BadLocationException;
import javax.swing.text.JTextComponent;
import javax.swing.text.Position;
import javax.swing.text.TextAction;

import jaxe.elements.JEStyle;
import jaxe.elements.JESwing;

import org.w3c.dom.DOMException;
import org.w3c.dom.DocumentFragment;
import org.w3c.dom.Element;
import org.w3c.dom.Node;

public class ActionInsertionBalise extends TextAction {
    
    static String newline = "\n";
    Element defbalise;
    JaxeDocument doc;
    
    public ActionInsertionBalise(JaxeDocument doc, Element balise) {
        super(doc.cfg.titreBalise(balise));
        defbalise = balise;
        this.doc = doc;
    }
    
    public void actionPerformed(ActionEvent e) {
        //JTextComponent target = getTextComponent(e); // ne marche pas si le focus n'est pas bon
        if (getTextComponent(e) == null && doc.textPane != null)
            doc.textPane.requestFocus();
        JTextComponent target = doc.textPane;
        if (target != null) {
            doc.modif = true;
            JaxeDocument doc = (JaxeDocument)target.getDocument();
            String typebalise = doc.cfg.typeBalise(defbalise);
            String noeudtype = doc.cfg.noeudtypeBalise(defbalise);
            String nombalise = doc.cfg.nomBalise(defbalise);
            int start = target.getSelectionStart();
            int end = target.getSelectionEnd();
            try {
                Position pos = doc.createPosition(start);
                JaxeElement parent = null;
                if (doc.rootJE != null)
                    parent = doc.rootJE.elementA(start);
                if (parent != null && parent.debut.getOffset() == start &&
                        !(parent instanceof JESwing))
                    parent = parent.getParent() ;
                if (parent != null && parent.noeud.getNodeType() == Node.TEXT_NODE) {
                    JaxeElement je1 = parent;
                    parent = parent.getParent();
                    if (start > je1.debut.getOffset() && start <= je1.fin.getOffset()) {
                        // couper la zone de texte en 2
                        je1.couper(pos);
                    }
                }
                if (end - start > 0 && doc.rootJE != null) {
                    JaxeElement parent2 = doc.rootJE.elementA(end);
                    if (parent2 != null && parent2.debut.getOffset() == end &&
                            !(parent2 instanceof JESwing))
                        parent2 = parent2.getParent() ;
                    if (parent2 != null && parent2.noeud.getNodeType() == Node.TEXT_NODE) {
                        if (end > parent2.debut.getOffset() && end <= parent2.fin.getOffset()) {
                            // couper la zone de texte à la fin de la sélection
                            parent2.couper(doc.createPosition(end));
                        }
                    }
                }
                if (parent == null && doc.rootJE != null) {
					doc.getErrorHandler().notInRootError(defbalise);
                    return;
                }
                if (parent != null && !parent.getEditionAutorisee()) {
					doc.getErrorHandler().editNotAllowed(parent, defbalise);
                    return;
                }
                if (parent != null && !(typebalise.equals("style") && nombalise.equals("NORMAL")) && (noeudtype == null || !noeudtype.equals("instruction"))) {
                // le hack ci-dessus est conservé temporairement pour la compatiblité
                // FONCTION doit normalement être utilisé à la place de BALISE pour NORMAL
                    Config conf = doc.cfg.getDefConf(defbalise);
                    if (conf == null)
                        conf = doc.cfg;
                    Element parentdef = null;
                    Element parentns = (Element)parent.noeud;
                    String pns = parentns.getNamespaceURI();
                    String cns = conf.namespace();
                    if ((pns != null || cns != null) && (pns == null || !pns.equals(cns)))
                        parentns = doc.cfg.chercheParentEspace(parentns, cns);
                    if (parentns != null)
                        parentdef = conf.getElementDef(parentns);
                    if (parentdef != null && !conf.sousbalise(parentdef, nombalise) && (noeudtype == null || noeudtype.endsWith("instruction")))  {// && !(parent instanceof JEStyle)) {
                        doc.getErrorHandler().childNotAllowedInParentdef(parentdef, defbalise);
                        return;
                    }
                    if (!doc.cfg.insertionPossible(parent, pos, defbalise)) { // && !typebalise.equals("style")) {
                        String expr = doc.cfg.expressionReguliere(parentdef);
                        doc.getErrorHandler().childNotAllowed(expr, parent, defbalise);
                        return ;
                    }
                }
                if (typebalise.equals("style")) {
                    if (end - start > 0) {
                    	JEStyle newje = JEStyle.nouveau(doc, start, end, defbalise);
                        if (newje != null) { // pas null si ajout de balise sur la sélection
                            String texte = doc.textPane.getText(start, end-start);
                            doc.textPane.debutEditionSpeciale(JaxeResourceBundle.getRB().getString("style.Style"), false);
                            JaxeUndoableEdit jedit = new JaxeUndoableEdit(JaxeUndoableEdit.SUPPRIMER,
                                doc, texte, start);
                            jedit.doit();
                            jedit = new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje);
                            jedit.doit();
                            doc.textPane.finEditionSpeciale();
                        }
                        if (start != end) {
                            doc.textPane.setSelectionStart(start);
                            doc.textPane.setSelectionEnd(end);
                        }
                    }
                } else {
                    DocumentFragment frag = null;
                    if (end - start > 0 && doc.rootJE != null) {
                        frag = (DocumentFragment)doc.copier(start, end);
                        if (frag == null) {
                            doc.textPane.undo();
                            return;
                        }
                        doc.textPane.debutEditionSpeciale(
                            JaxeResourceBundle.getRB().getString("insertion.InsertionBalise"), false);
                        doc.enableIgnore();
                        doc.remove(start, end-start);
                    }
                    JaxeElement newje = JEFactory.createJE(typebalise, doc, defbalise, (Element)null);
                    
                    Node newel = null;
                    if (newje != null)
                        newel = newje.nouvelElement(defbalise);

                    if (newel == null) { // null si annulation
                        if (end - start > 0 && doc.rootJE != null) {
                            doc.textPane.finEditionSpeciale();
                            doc.textPane.undo();
                        }

                    } else {
                        boolean event = !(noeudtype != null && noeudtype.equals("instruction"));
                        if (event) pos = doc.firePrepareElementAddEvent(pos);
                        
                        if ("true".equals(Preferences.getPref().getProperty("consIndent")) &&
                                newel.getFirstChild() != null) {
                            // ajout d'espaces d'indentation
                            int i1 = pos.getOffset() - 255;
                            if (i1 < 0)
                                i1 = 0;
                            String extrait = doc.textPane.getText(i1, pos.getOffset()-i1);
                            i1 = extrait.lastIndexOf('\n');
                            if (i1 != -1) {
                                extrait = extrait.substring(i1+1);
                                for (i1=0; i1<extrait.length() &&
                                        (extrait.charAt(i1) == ' ' || extrait.charAt(i1) == '\t'); i1++)
                                    ;
                                String sindent = extrait.substring(0, i1);
                                String texte = newel.getFirstChild().getNodeValue();
                                for (int i=0; i<texte.length(); i++)
                                    if (texte.charAt(i) == '\n') {
                                        texte = texte.substring(0, i+1) + sindent + texte.substring(i+1);
                                        i += sindent.length();
                                    }
                                newel.getFirstChild().setNodeValue(texte);
                            }
                        }
                        if (doc.rootJE == null) {
                            doc.DOMdoc.appendChild(newel);
                            doc.textPane.debutIgnorerEdition();
                            newje.creer(pos, newel);
                            doc.textPane.finIgnorerEdition();
                            doc.rootJE = newje;
                        } else
                            newje.inserer(pos, newel);
                        Position inspos = newje.insPosition();
                        if ("true".equals(Preferences.getPref().getProperty("consIndent")) &&
                                newel.getFirstChild() != null) {
                            int lg = 255;
                            if (inspos.getOffset() + 255 > doc.getLength())
                                lg = doc.getLength() - inspos.getOffset();
                            String suite = doc.getText(inspos.getOffset(), lg);
                            int in = suite.indexOf('\n');
                            if (in != -1)
                                inspos = doc.createPosition(inspos.getOffset() + in);
                        }
                        target.setCaretPosition(inspos.getOffset());
                        doc.textPane.addEdit(new JaxeUndoableEdit(JaxeUndoableEdit.AJOUTER, newje));
                        if (end - start > 0) {
                            if (!doc.testerInsertionFragment(frag, newje, inspos)) {
                                doc.textPane.finEditionSpeciale();
                                doc.textPane.undo();
                                return;
                            } else {
                                doc.coller(frag, inspos);
                                doc.textPane.finEditionSpeciale();
                            }
                        }
                        if (event) doc.fireElementAddedEvent(new JaxeEditEvent(this, newje), pos);
                        if (parent != null)
                            parent.majValidite();
                        newje.majValidite();
                    }
                    doc.textPane.miseAJourArbre();
                }
            } catch (BadLocationException ble) {
                System.err.println("Impossible d'insérer une balise.");
                //ble.printStackTrace();
            } catch (DOMException ex) {
                System.err.println("DOMException: " + ex.getMessage());
            }
        }
    }
    
    public Element getDefbalise() {
        return(defbalise);
    }
}

