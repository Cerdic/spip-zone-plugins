/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe ;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.util.ArrayList;
import java.util.Enumeration;

import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.event.TreeSelectionEvent;
import javax.swing.event.TreeSelectionListener;
import javax.swing.text.BadLocationException;
import javax.swing.tree.DefaultMutableTreeNode;
import javax.swing.tree.DefaultTreeModel;
import javax.swing.tree.TreeNode;
import javax.swing.tree.TreePath;
import javax.swing.tree.TreeSelectionModel;

import org.w3c.dom.Document;
import org.w3c.dom.Node;

/**
* Classe utilisée pour représenter graphiquement l'arbre XML
*/
public class ArbreXML extends JPanel implements EcouteurMAJ {
    JaxeDocument doc ;
    DOMTree m_tree ;
    
    public ArbreXML(JaxeDocument doc) {
        setLayout(new BorderLayout()) ;
        newdoc(doc) ;
    }
    
    public void newdoc(JaxeDocument doc) {
        removeAll() ;
        this.doc = doc ;
        affichage() ;
        creerArbre() ;
    }
    
    protected void affichage() {        
        // création de l'arbre DOM
        m_tree = new DOMTree();
        m_tree.getSelectionModel().setSelectionMode(TreeSelectionModel.SINGLE_TREE_SELECTION);

        // Listen for when the selection changes, call nodeSelected(node)
        m_tree.addTreeSelectionListener(
            new TreeSelectionListener() {
                public void valueChanged(TreeSelectionEvent e) {
                    TreeNode node = (TreeNode) e.getPath().getLastPathComponent() ;
                    nodeSelected(node);
                }
            }
        );
        
        // création de la fenêtre
        JScrollPane scrollArbre = new JScrollPane(m_tree) ;
        scrollArbre.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_ALWAYS); 
        scrollArbre.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_ALWAYS);
        scrollArbre.setPreferredSize(new Dimension(200, 460)) ;
        add(scrollArbre,BorderLayout.CENTER);
        setMinimumSize(new Dimension(0, 50)) ;
    }
    
    protected void creerArbre() {
        Document newRoot = doc.DOMdoc ;
        if (newRoot == null) return;
        if (m_tree!= null) {
            m_tree.setDocument(newRoot);
            expandTree();
            validate();
        }
    }

    void expandTree() {
        int maxLignes = 22 ;
        for (int j = 0 ; j < 6 ; j++) {
            int compteur = 0 ;
            ArrayList chemins = new ArrayList() ;
            int rows = m_tree.getRowCount();
            for (int i = 0; i < rows; i++) {
                TreePath chemin = m_tree.getPathForRow(i) ;
                chemins.add(chemin) ;
                compteur += ((DefaultMutableTreeNode)chemin.getLastPathComponent()).getChildCount() ;
            }
            if (compteur > maxLignes)
                break ;
            for (int i=0; i < chemins.size() ; i++) {
                m_tree.expandPath((TreePath)chemins.get(i)) ;
            }
        }
    }

    public void miseAJour() {
        TreePath cheminRacine = m_tree.getPathForRow(0) ;
        Enumeration enfants = m_tree.getExpandedDescendants(cheminRacine)  ;
        int taille = 0 ;
        Node[] chemins = null ;
        
        if (enfants != null) {
            for (; enfants.hasMoreElements() ;taille++,enfants.nextElement()) ;
            chemins = new Node[taille] ;
            enfants = m_tree.getExpandedDescendants(cheminRacine) ;
            for (int index = 0; enfants.hasMoreElements() ;index++) {
                  TreePath cheminNoeud = (TreePath)enfants.nextElement() ;
                  chemins[index] = m_tree.getNode((DefaultMutableTreeNode)cheminNoeud.getLastPathComponent()) ;
            }
        }
        Document newRoot = doc.DOMdoc ;
        if (newRoot == null) return;
        m_tree.setDocument(newRoot);
        m_tree.expandPath(cheminRacine) ;
        for (int i = 0 ; i< taille; i++) {
            Node noeudDOM = (Node)chemins[i] ;
            DefaultMutableTreeNode noeudMutable = m_tree.getMutNode(noeudDOM) ;
            DefaultTreeModel modeleArbre = (DefaultTreeModel)m_tree.getModel() ;
            TreeNode[] objetChemin = modeleArbre.getPathToRoot(noeudMutable) ;
            if (objetChemin != null) {
                TreePath cheminArbre = new TreePath(objetChemin) ;
                m_tree.expandPath(cheminArbre) ;
            }
        }
        validate();
    }
    
     void nodeSelected(TreeNode treeNode) {
        Node node = m_tree.getNode(treeNode);
        if (node == null) return ;
        JaxeElement je = doc.getElementForNode(node) ;
        if (je == null) return ;
        int placeCurseur = je.debut.getOffset() ;
        
        // bidouille pour afficher la position en haut de la fenêtre
        try {
            doc.textPane.scrollRectToVisible(doc.textPane.modelToView(doc.getLength()));
            doc.textPane.scrollRectToVisible(doc.textPane.modelToView(placeCurseur));
        } catch (BadLocationException ex) {
        }
    } 
}
