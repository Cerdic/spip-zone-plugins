/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.Container;
import java.awt.Cursor;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Point;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.ArrayList;

import javax.swing.Box;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.text.AbstractDocument;
import javax.swing.text.BadLocationException;
import javax.swing.text.JTextComponent;
import javax.swing.text.Position;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;

import jaxe.DialogueAttributs;
import jaxe.DialogueChamps;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.DOMException;
import org.w3c.dom.Element;
import org.w3c.dom.Node;


/**
 * Table dans le texte, permettant l'insertion de sous-éléments dans les cellules.
 * Type d'élément Jaxe: 'tabletexte'
 * paramètre: trTag: un attribut correspondant à une ligne de tableau
 * paramètre: tdTag: un attribut correspondant à une cellule de tableau
 * paramètre: thTag: un attribut correspondant à une cellule d'entête de tableau
 * paramètre: colspanAttr: Attributename for colspan
 * paramètre: rowspanAttr: Attributename for rowspan
 */
public class JETableTexte extends JaxeElement implements ActionListener {

    static String newline = "\n";
    String tableTag = "table";
    String TRtag = "tr";
    String TDtag = "td";
    String THtag = null;
    String colspanAttr = "colspan";
    String rowspanAttr = "rowspan";
    JTable jtable = null;
    boolean avecEntete;
    int nblignes;
    int nbcolonnes;
    Element[][] grille; // utile pour gérer colspan et rowspan
    boolean inTable = true;

    TableHelper helper;
    
    public JETableTexte(JaxeDocument doc) {
        this.doc = doc;
    }
    
    protected void obtenirTags(Element defbalise) {
        if (defbalise != null) {
            String paramatt = defbalise.getAttribute("param");
            if (paramatt != null && !"".equals(paramatt) && paramatt.indexOf('/') != -1) {
                int inds1 = paramatt.indexOf('/');
                TRtag = paramatt.substring(0, inds1);
                String param2 = paramatt.substring(inds1+1);
                int inds2 = param2.indexOf('/');
                if (inds2 == -1) {
                    TDtag = param2;
                    THtag = null;
                } else {
                    TDtag = param2.substring(0, inds2);
                    THtag = param2.substring(inds2+1);
                }
            } else {
                tableTag = defbalise.getAttribute("nom");
                TRtag = doc.cfg.getParamFromDefinition(defbalise, "trTag", TRtag);
                TDtag = doc.cfg.getParamFromDefinition(defbalise, "tdTag", TDtag);
                THtag = doc.cfg.getParamFromDefinition(defbalise, "thTag", THtag);
                colspanAttr = doc.cfg.getParamFromDefinition(defbalise, "colspanAttr", colspanAttr);
                rowspanAttr = doc.cfg.getParamFromDefinition(defbalise, "rowspanAttr", rowspanAttr);
            }
        }
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        Element defbalise = doc.cfg.getBaliseNomType(el.getTagName(), "tabletexte");
        obtenirTags(defbalise);
       
        helper = new TableHelper(noeud, TRtag, TDtag, THtag, colspanAttr, rowspanAttr);
        
        grille = helper.getGrille();
        nblignes = grille.length;
        nbcolonnes = grille[0].length;
        
        Style s = doc.textPane.addStyle(null, null);
        
        if (!helper.inTable(noeud.getParentNode())) {
            inTable = false;
        JPanel pboutons = new JPanel();
        pboutons.setCursor(Cursor.getDefaultCursor());
        pboutons.setLayout(new FlowLayout(FlowLayout.LEFT));
        JButton bmodtable = new JButton(getString("table.Table"));
        bmodtable.addActionListener(this);
        bmodtable.setActionCommand("modtable");
        bmodtable.setFont(bmodtable.getFont().deriveFont((float)9));
        bmodtable.putClientProperty("JButton.buttonType", "toolbar"); // pour MacOS X
        pboutons.add(bmodtable);
        pboutons.add(Box.createRigidArea(new Dimension(5,0)));
        JButton bmodligne = new JButton(getString("table.Ligne"));
        bmodligne.addActionListener(this);
        bmodligne.setActionCommand("modligne");
        bmodligne.setFont(bmodligne.getFont().deriveFont((float)9));
        bmodligne.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bmodligne);
        JButton bajligne = new JButton("+");
        bajligne.addActionListener(this);
        bajligne.setActionCommand("ajligne");
        bajligne.setFont(bajligne.getFont().deriveFont((float)9));
        bajligne.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bajligne);
        JButton bsupligne = new JButton("-");
        bsupligne.addActionListener(this);
        bsupligne.setActionCommand("supligne");
        bsupligne.setFont(bsupligne.getFont().deriveFont((float)9));
        bsupligne.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bsupligne);
        pboutons.add(Box.createRigidArea(new Dimension(5,0)));
        JLabel lcol = new JLabel(getString("table.Colonne"));
        lcol.setFont(lcol.getFont().deriveFont((float)9));
        pboutons.add(lcol);
        JButton bajcolonne = new JButton("+");
        bajcolonne.addActionListener(this);
        bajcolonne.setActionCommand("ajcolonne");
        bajcolonne.setFont(bajcolonne.getFont().deriveFont((float)9));
        bajcolonne.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bajcolonne);
        JButton bsupcolonne = new JButton("-");
        bsupcolonne.addActionListener(this);
        bsupcolonne.setActionCommand("supcolonne");
        bsupcolonne.setFont(bsupcolonne.getFont().deriveFont((float)9));
        bsupcolonne.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bsupcolonne);
        pboutons.add(Box.createRigidArea(new Dimension(5,0)));
        JButton bmodcellule = new JButton(getString("table.Cellule"));
        bmodcellule.addActionListener(this);
        bmodcellule.setActionCommand("modcellule");
        bmodcellule.setFont(bmodcellule.getFont().deriveFont((float)9));
        bmodcellule.putClientProperty("JButton.buttonType", "toolbar");
        pboutons.add(bmodcellule);
        pboutons.add(Box.createRigidArea(new Dimension(5,0)));
        if (THtag != null) {
            Element tr = helper.trouverLigne(0, noeud);
            Element tdh = null;
            if (tr != null)
                tdh = helper.trouverCellule(tr, 0);
            avecEntete = (tdh != null && THtag.equals(tdh.getNodeName()));
            JCheckBox bcheck = new JCheckBox(getString("table.Entete"), avecEntete);
            bcheck.addActionListener(this);
            bcheck.setActionCommand("entête");
            bcheck.setFont(bcheck.getFont().deriveFont((float)9));
            pboutons.add(bcheck);
        } else
            avecEntete = false;
        
        Element deftd = doc.cfg.getBaliseDef(TDtag);
        ArrayList lattributs = null;
        if (deftd != null)
            lattributs = doc.cfg.listeAttributs(deftd);
        boolean avecRowspan = false;
        boolean avecColspan = false;
        if (lattributs != null)
            for (int i=0; i<lattributs.size(); i++) {
                String nomAtt = doc.cfg.nomAttribut((Element)lattributs.get(i));
                if (rowspanAttr.equals(nomAtt))
                    avecRowspan = true;
                else if (colspanAttr.equals(nomAtt))
                    avecColspan = true;
            }
        if (avecRowspan) {
            JButton concatColumns = new JButton(new ImageIcon(doc.getClass().getResource("images_Jaxe/concatcolumn.png")));
            concatColumns.setToolTipText(getString("table.ConcatColumns"));
            
            concatColumns.addActionListener(new ActionListener() {

                public void actionPerformed(ActionEvent e) {
                    JETableTexte jet = getTable();
                    if (jet != null) jet.concatColumns();
                }
                
            });
            
            pboutons.add(concatColumns);
            
            JButton splitColumns = new JButton(new ImageIcon(doc.getClass().getResource("images_Jaxe/splitcolumn.png")));
            splitColumns.setToolTipText(getString("table.SplitColumns"));
            
            splitColumns.addActionListener(new ActionListener() {

                public void actionPerformed(ActionEvent e) {
                    JETableTexte jet = getTable();
                    if (jet != null) jet.splitColumns();
                }
                
            });        
            
            pboutons.add(splitColumns);        
        }
        if (avecColspan) {
            JButton concatRows = new JButton(new ImageIcon(doc.getClass().getResource("images_Jaxe/concatrow.png")));
            concatRows.setToolTipText(getString("table.ConcatRows"));
            
            concatRows.addActionListener(new ActionListener() {

                public void actionPerformed(ActionEvent e) {
                    JETableTexte jet = getTable();
                    if (jet != null) jet.concatRows();
                }
                
            });
            
            pboutons.add(concatRows);
            
            JButton splitRows = new JButton(new ImageIcon(doc.getClass().getResource("images_Jaxe/splitrow.png")));
            splitRows.setToolTipText(getString("table.SplitRows"));
            
            splitRows.addActionListener(new ActionListener() {

                public void actionPerformed(ActionEvent e) {
                    JETableTexte jet = getTable();
                    if (jet != null) jet.splitRows();
                }
                
            });
            
            pboutons.add(splitRows);
        }
        
        pos = insertComponent(pos, pboutons);
        insertText(pos, "\n\n");
        } else {
            insertText(pos, "\n");
        }
       
        int offsetdebut = pos.getOffset();
        
        JaxeDocument.SwingElementSpec tableSpec = preparerSpecTable(el, offsetdebut);
        
        javax.swing.text.Element elSwing = doc.insereSpec(tableSpec, offsetdebut);
        creerElementsTableJaxe(el, elSwing);
        
        // correction des indentations
        s = doc.textPane.addStyle(null, null);
        StyleConstants.setLeftIndent(s, (float)0);
        doc.setParagraphAttributes(debut.getOffset(), fin.getOffset() - debut.getOffset(), s, false);
    }
    
    /**
     * Converts a String to Int
     * @param str String
     * @param def Default-Value
     * @return Value, if Value was 0 it will be set to def
     */
    private int stringToInt(String str, int def) {
        int num = def;
        try {
            num = Integer.parseInt(str);
            if (num == 0) {
                num = def;
            }
        } catch (Exception e) {
        }
        
        return Math.max(num, 1);
    }
    
    
    /**
     * Concat Rows
     */
    private void concatRows() {
        JaxeElement jesel = cellulesel();
        if (jesel == null) // No element selected
            return;

        // get Current Number
        int num = stringToInt(((Element)jesel.noeud).getAttribute(rowspanAttr), 1);

        int colnum = stringToInt(((Element)jesel.noeud).getAttribute(colspanAttr), 1);

        if (colnum > 1) {
            JOptionPane.showMessageDialog(doc.jframe, getString("table.noConcat"));
            return;
        }
        
        // get Position in Grille
        Point p = getPointInGrille(jesel);
        
        // Try to get next Cell in next Row and parse it's Rowspan
        if (grille.length > p.y+num) {
            Element el = grille[p.y+num][p.x];
            
            int addnum = 1;
            
            if (el != null) {
                String numstr = el.getAttribute(rowspanAttr);
                
                addnum = stringToInt(numstr, 1);

                colnum = stringToInt(el.getAttribute(colspanAttr), 1);
            }

            if (colnum > 1) {
                JOptionPane.showMessageDialog(doc.jframe, getString("table.noConcat"));
                return;
            }
            
            
            int result = JOptionPane.showConfirmDialog(doc.jframe, getString("table.BottomDeleteWarning"), getString("table.Attention"), JOptionPane.YES_NO_OPTION);
            
            if (result != JOptionPane.YES_OPTION) {
                return;
            }
            doc.textPane.getUndo().discardAllEdits();
            doc.textPane.miseAJourAnnulation();
            
            ArrayList allcomp = recupererComposants();

            // Add new rowspan-Value
            num += addnum;
            ((Element)jesel.noeud).setAttribute(rowspanAttr, Integer.toString(num));

            Element parent  = null;
            if (el != null) {
                // Remove the Cell in the next Row
                parent = (Element) el.getParentNode();
                parent.removeChild(el);
            }

            
            // If the Row is empty, decrease row-spans in the rows before
            // and remove the empty row
            if ((parent != null) && (!parent.hasChildNodes())) {

                for (int y = 0; y <= p.y; y++) {
                    for (int x = 0; x < grille[y].length; x++) {
                        Element ele = grille[y][x];
                        
                        if (ele != null) {
                            int rownums = stringToInt(ele.getAttribute(rowspanAttr), 1);                            
                            int colnums = stringToInt(ele.getAttribute(colspanAttr), 1);
                            
                            for (int v = 0; v > rownums; v++) {
                                for (int w = 0; w > colnums; w++) {
                                    grille[y+rownums][w+colnums] = null;
                                }
                            }
                            
                            if ((rownums + y > p.y) && (rownums > 1)) {
                                rownums--;
                                ele.setAttribute(rowspanAttr, Integer.toString(rownums));
                            }
                        }
                    }
                }
                
                parent.getParentNode().removeChild(parent);
            }
            
            // Recreate Table
            recreerTable(allcomp);
        }

    }
    
    /**
     * Split Rows
     */
    private void splitRows() {
        JaxeElement jesel = cellulesel();
        if (jesel == null)
            return;

        // Get the Number of Rowspans
        int num = stringToInt(((Element)jesel.noeud).getAttribute(rowspanAttr), 1);
        
        int colnum = stringToInt(((Element)jesel.noeud).getAttribute(colspanAttr), 1);

        if (colnum > 1) {
            JOptionPane.showMessageDialog(doc.jframe, getString("table.noSplit"));
            return;
        }

        ArrayList allcomp = recupererComposants();

        if (num > 1) {
            doc.textPane.getUndo().discardAllEdits();
            doc.textPane.miseAJourAnnulation();
            // Decrease Number
            num--;
            ((Element)jesel.noeud).setAttribute(rowspanAttr, Integer.toString(num));
            
            Point p = getPointInGrille(jesel);
            
            Element td = nouvelElementDOM(doc, TDtag, (Element)jesel.noeud);

            // Get TR-Node
            Node node = jesel.noeud.getParentNode().getNextSibling();
            
            // Get TR-Node for the row where the new td needs to be inserted
            int counter = 0;
            
            while ((node != null) && !((counter == num-1) && node.getNodeName().equals(TRtag) )) {
                if (node.getNodeName().equals(TRtag)) {
                    counter++;
                }
                node = node.getNextSibling();
            }
 
            // If tr was found
            if (node.getNodeName().equals(TRtag)) {
                boolean added = false;

                // find position in tr to insert it
                Node child = node.getFirstChild();
                while (child != null && !added) {
                    
                    if (child.getNodeName().equals(TDtag)) {
                        Point np =  getPointInGrille(doc.getElementForNode(child));
                        if ((np.y == p.y+num) && (np.x > p.x)) {
                            node.insertBefore(td, child);
                            added = true;
                        }
                    }
                    
                    child = child.getNextSibling();
                }
                
                if (!added) {
                    node.appendChild(td);
                }
                
            }
        }
        
        recreerTable(allcomp);
    }
    
    /** 
     * Concats the Columns
     */
    private void concatColumns() {
        JaxeElement jesel = cellulesel();
        if (jesel == null)
            return;
        
        // Get Number of Colspan
        int num = stringToInt(((Element)jesel.noeud).getAttribute(colspanAttr) , 1);

        int rownum = stringToInt(((Element)jesel.noeud).getAttribute(rowspanAttr) , 1);
        
        if (rownum > 1) {
            JOptionPane.showMessageDialog(doc.jframe, getString("table.noConcat"));
            return;
        }        
        
        // Get Position in Array
        Point p = getPointInGrille(jesel);
        
        if (grille[p.y].length > p.x+num) {
            Element el = grille[p.y][p.x+num];
            
            String numstr = el.getAttribute(colspanAttr);
            
            int addnum = stringToInt(numstr, 1);

            rownum = stringToInt(el.getAttribute(rowspanAttr) , 1);
            
            if (rownum > 1) {
                JOptionPane.showMessageDialog(doc.jframe, getString("table.noConcat"));
                return;
            }        
            
            
            int result = JOptionPane.showConfirmDialog(doc.jframe, getString("table.RightDeleteWarning"), getString("table.Attention"), JOptionPane.YES_NO_OPTION);
            
            if (result != JOptionPane.YES_OPTION) {
                return;
            }
            doc.textPane.getUndo().discardAllEdits();
            doc.textPane.miseAJourAnnulation();
            
            ArrayList allcomp = recupererComposants();
            
            // Remove unneeded Cell
            el.getParentNode().removeChild(el);
            num += addnum;
            
            // Set new Colspan
            ((Element)jesel.noeud).setAttribute(colspanAttr, Integer.toString(num));
            
            recreerTable(allcomp);
        }
        
    }

    /**
     * Split Columns
     */
    private void splitColumns() {
        JaxeElement jesel = cellulesel();
        if (jesel == null)
            return;

        // get Number of Colspans
        int num = stringToInt(((Element)jesel.noeud).getAttribute(colspanAttr), 1);

        int rownum = stringToInt(((Element)jesel.noeud).getAttribute(rowspanAttr) , 1);
        
        if (rownum > 1) {
            JOptionPane.showMessageDialog(doc.jframe, getString("table.noSplit"));
            return;
        }                
        
        ArrayList allcomp = recupererComposants();
        if (num > 1) {
            doc.textPane.getUndo().discardAllEdits();
            doc.textPane.miseAJourAnnulation();
            // Decrease Colspans
            num--;
            ((Element)jesel.noeud).setAttribute(colspanAttr, Integer.toString(num));
            
            // Create new Element
            Element td = nouvelElementDOM(doc, TDtag, (Element)jesel.noeud);
            jesel.noeud.getParentNode().insertBefore(td, jesel.noeud.getNextSibling());
        }
        
        recreerTable(allcomp);
    }
    
    /**
     * Returns the Position of a JaxeElement in the Grille
     * @param jesel Element to find the Position for
     */
    private Point getPointInGrille(JaxeElement jesel) {
        if (jesel == null) {
            return null;
        }
        
        for (int y = 0; y < grille.length; y++) {
            for (int x = 0; x < grille[y].length; x++) {
                if (grille[y][x] == jesel.noeud) {
                    return new Point(x,y);
                }
            }
        }
        
        return null;
    }
    
    
    /**
     * modif de JaxeElement.mettreAJourDOM pour éviter l'enregistrement de \n\n après <TABLE>
     */
    public void mettreAJourDOM() {
        if (debut == null || fin == null)
            return;
        for (JaxeElement je = getFirstChild(); je != null; je=je.getNextSibling())
            je.mettreAJourDOM();
    }
    
    protected JaxeDocument.SwingElementSpec preparerSpecTable(Element el, int offset) {
        JaxeDocument.SwingElementSpec tableSpec = doc.prepareSpec("table");
        int offc = offset;
        for (Element tr=helper.premiereLigne(el); tr != null; tr=helper.ligneSuivante(tr)) {
            JaxeDocument.SwingElementSpec ligneSpec = doc.prepareSpec("tr");
            doc.sousSpec(tableSpec, ligneSpec);
            for (Node n2=tr.getFirstChild(); n2 != null; n2=n2.getNextSibling()) {
                if (n2.getNodeType() == Node.ELEMENT_NODE) {
                    String bal2 = n2.getNodeName();
                    if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                        String colspan = ((Element)n2).getAttribute(colspanAttr);
                        if ("".equals(colspan))
                            colspan = null;
                        String rowspan = ((Element)n2).getAttribute(rowspanAttr);
                        if ("".equals(rowspan))
                            rowspan = null;
                        JaxeDocument.SwingElementSpec celluleSpec;
                        if (colspan != null || rowspan != null) {
                            SimpleAttributeSet att = new SimpleAttributeSet();
                            if (colspan != null)
                                att.addAttribute(javax.swing.text.html.HTML.Attribute.COLSPAN, colspan);
                            if (rowspan != null)
                                att.addAttribute(javax.swing.text.html.HTML.Attribute.ROWSPAN, rowspan);
                            celluleSpec = doc.prepareSpec("td", att);
                        } else
                            celluleSpec = doc.prepareSpec("td");
                        doc.sousSpec(ligneSpec, celluleSpec);
                        //Object contenuCelluleSpec = doc.prepareSpec("tdd");
                        JaxeDocument.SwingElementSpec contenuCelluleSpec = doc.prepareSpec(AbstractDocument.ParagraphElementName);
                        doc.sousSpec(celluleSpec, contenuCelluleSpec);
                        String sval = "\n";
                        JaxeDocument.SwingElementSpec contenuSpec = doc.prepareSpec("content", offc, sval);
                        offc += sval.length();
                        doc.sousSpec(contenuCelluleSpec, contenuSpec);
                    }
                }
            }
            // on ignore le reste
        }
        return(tableSpec);
    }
    
    protected JaxeDocument.SwingElementSpec preparerSpecLigne(Element el, int offset) {
        int offc = offset;
        JaxeDocument.SwingElementSpec ligneSpec = doc.prepareSpec("tr");
        for (Node n2=el.getFirstChild(); n2 != null; n2=n2.getNextSibling()) {
            if (n2.getNodeType() == Node.ELEMENT_NODE) {
                String bal2 = n2.getNodeName();
                if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                    String colspan = ((Element)n2).getAttribute(colspanAttr);
                    if ("".equals(colspan))
                        colspan = null;
                    String rowspan = ((Element)n2).getAttribute(rowspanAttr);
                    if ("".equals(rowspan))
                        rowspan = null;
                    JaxeDocument.SwingElementSpec celluleSpec;
                    if (colspan != null || rowspan != null) {
                        SimpleAttributeSet att = new SimpleAttributeSet();
                        if (colspan != null)
                            att.addAttribute(javax.swing.text.html.HTML.Attribute.COLSPAN, colspan);
                        if (rowspan != null)
                            att.addAttribute(javax.swing.text.html.HTML.Attribute.ROWSPAN, rowspan);
                        celluleSpec = doc.prepareSpec("td", att);
                    } else
                        celluleSpec = doc.prepareSpec("td");
                    doc.sousSpec(ligneSpec, celluleSpec);
                    JaxeDocument.SwingElementSpec contenuCelluleSpec = doc.prepareSpec(AbstractDocument.ParagraphElementName);
                    doc.sousSpec(celluleSpec, contenuCelluleSpec);
                    String sval = "\n";
                    JaxeDocument.SwingElementSpec contenuSpec = doc.prepareSpec("content", offc, sval);
                    offc += sval.length();
                    doc.sousSpec(contenuCelluleSpec, contenuSpec);
                }
            }
        }
        return(ligneSpec);
    }
    
    protected void creerElementsTableJaxe(Element elDOM, javax.swing.text.Element elSwing) {
        setEditionAutorisee(false);
        javax.swing.text.Element trSwing = null;
        int itrSwing = 0;
        Position dernierePos = fin;
        for (Element tr=helper.premiereLigne(elDOM); tr != null; tr=helper.ligneSuivante(tr)) {
            if (itrSwing >= elSwing.getElementCount())
                System.err.println("JETableTexte: Erreur: arbre swing != arbre DOM (ligne)");
            else {
                trSwing = elSwing.getElement(itrSwing++);
                dernierePos = creerElementsLigneJaxe(tr, trSwing);
            }
        }
        fin = dernierePos;
    }
    
    protected Position creerElementsLigneJaxe(Element trDOM, javax.swing.text.Element trSwing) {
        javax.swing.text.Element tdSwing = null;
        Position dernierePos = null;
        JESwing trje = new JESwing(doc, trDOM, trSwing);
        trje.creer(trje.debut, trDOM);
        trje.setEffacementAutorise(false);
        trje.setEditionAutorisee(false);
        int offsetdebutLigne = trje.debut.getOffset();
        int itdSwing = 0;
        for (Node n2=trDOM.getFirstChild(); n2 != null; n2=n2.getNextSibling()) {
            if (n2.getNodeType() == Node.ELEMENT_NODE) {
                String bal2 = n2.getNodeName();
                if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                    if (itdSwing >= trSwing.getElementCount())
                        System.err.println("JETableTexte: Erreur: arbre swing != arbre DOM (cellule)");
                    else {
                        tdSwing = trSwing.getElement(itdSwing++);
                        JESwingTD tdje = new JESwingTD(doc, (Element)n2, tdSwing);
                        int offsetdebut = tdje.debut.getOffset();
                        tdje.creer(tdje.debut, n2);
                        tdje.setEffacementAutorise(false);
                        tdje.creerEnfants(tdje.debut);
                        tdje.fin = tdje.debut;
                        try {
                            tdje.debut = doc.createPosition(offsetdebut);
                        } catch (BadLocationException ex) {
                            ex.printStackTrace();
                        }
                        dernierePos = tdje.fin;
                        if (dernierePos.getOffset() - offsetdebut > 0) {
                            SimpleAttributeSet style = tdje.attStyle(null);
                            if (style != null)
                                doc.setCharacterAttributes(offsetdebut,
                                    dernierePos.getOffset() - offsetdebut, style, false);
                        }
                    }
                }
            }
        }
        try {
            trje.debut = doc.createPosition(offsetdebutLigne);
        } catch (BadLocationException ex) {
            ex.printStackTrace();
        }
        trje.fin = dernierePos;
        return(dernierePos);
    }
    
    public Node nouvelElement(Element defbalise) {
        String[] titres = {JaxeResourceBundle.getRB().getString("table.NbLignes"),
            JaxeResourceBundle.getRB().getString("table.NbColonnes")};
        
        JTextComponent[] champs = new JTextComponent[2];
        champs[0] = new JTextField(10);
        champs[1] = new JTextField(10);
        
        DialogueChamps dlg = new DialogueChamps(doc.jframe,
            JaxeResourceBundle.getRB().getString("table.NouvelleBalise"), titres, champs);
        if (!dlg.afficher())
            return null;

        int nlignes, ncolonnes;
        
        try {
        	nlignes = Integer.parseInt(champs[0].getText());
        	ncolonnes = Integer.parseInt(champs[1].getText());
        } catch (NumberFormatException ne) {
            return null;
        }
        
/*        try {
            nlignes = (Integer.valueOf(slignes)).intValue();
            ncolonnes = (Integer.valueOf(scolonnes)).intValue();
        } catch (NumberFormatException ex) {
            JOptionPane.showMessageDialog(doc.jframe, JaxeResourceBundle.getRB().getString("erreur.Conversion"),
                JaxeResourceBundle.getRB().getString("table.NouvelleBalise"), JOptionPane.ERROR_MESSAGE);
            return(null);
        } */
        if (nlignes <= 0 || ncolonnes <= 0)
            return(null);
        
        obtenirTags(defbalise);
        avecEntete = false;

        Element newel = nouvelElementDOM(doc, defbalise);
        Node textnode = doc.DOMdoc.createTextNode(newline);
        newel.appendChild(textnode);
        for (int i=0; i<nlignes; i++) {
            Element ligneel = nouvelElementDOM(doc, TRtag, newel);
            newel.appendChild(ligneel);
            for (int j=0; j<ncolonnes; j++) {
                Element cellel = nouvelElementDOM(doc, TDtag, ligneel);
                ligneel.appendChild(cellel);
            }
            textnode = doc.DOMdoc.createTextNode(newline);
            newel.appendChild(textnode);
        }

        return(newel);
    }
    
    public void afficherDialogue(JFrame jframe) {
        Element el = (Element)noeud;

        Element defbalise = doc.cfg.getElementDef(el);
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        if (latt != null && latt.size() > 0) {
            DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                el.getTagName(), defbalise, el);
            if (dlg.afficher()) {
                dlg.enregistrerReponses();
            }
            dlg.dispose();
        }
    }
    
    protected void rechercherComposants(JaxeElement je, ArrayList al) {
        al.addAll(je.getComponents());
        for (Node n=je.noeud.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE || n.getNodeType() == Node.TEXT_NODE || n.getNodeType() == Node.PROCESSING_INSTRUCTION_NODE)  {
                JaxeElement je2 = doc.getElementForNode(n);
                if (je2 != null) {
                    rechercherComposants(je2, al);
                }
            }
        }
    }
    
    protected ArrayList recupererComposants() {
        int offsetdebut = getOffsetDebut();
        ArrayList tel = elementsDans(offsetdebut, fin.getOffset());
        ArrayList allcomp = new ArrayList();
        for (int i=0; i<tel.size(); i++)
            rechercherComposants((JaxeElement)tel.get(i), allcomp);
        return(allcomp);
    }
    
    protected void effacerComposants(ArrayList allcomp) {
        // on utiliser parentContainer.remove pour retirer les composants, sinon un bug de Java 1.4 les affiche
        for (int i=0, s = allcomp.size(); i< s; i++) {
            Object obj = allcomp.get(i);
            if (obj instanceof JComponent) {
            Container parentContainer = ((JComponent)obj).getParent();
            if (parentContainer != null)
                parentContainer.remove((JComponent)obj);
            }
        }
    }
    
    /**
     * Returns the OffsetDebut of the Table
     * @return OffsetDebut
     */
    private int getOffsetDebut() {
        int offsetdebut;
        if (inTable) {
            offsetdebut = debut.getOffset()+1;
        } else {
            offsetdebut = debut.getOffset()+3;
        }
        return offsetdebut;
    }
    
    public void recreerTable(ArrayList allcomp) {
        int caretpos = doc.textPane.getCaretPosition();
        doc.textPane.debutIgnorerEdition();
        
        try {
             doc.remove(debut.getOffset()+1, fin.getOffset()-debut.getOffset());
        } catch (BadLocationException ex) {
            ex.printStackTrace();
        }

        effacerComposants(allcomp);
        
        if (!inTable) {
            try {
                Position pos = doc.createPosition(debut.getOffset()+1);
                insertText(pos, "\n\n");
            } catch (BadLocationException ex) {
                ex.printStackTrace();
            }
        }
        
        grille = helper.updateGrille(noeud);
        nblignes = grille.length;
        nbcolonnes = grille[0].length;

        int offsetdebut = getOffsetDebut();
        Element el = (Element)noeud;
        JaxeDocument.SwingElementSpec tableSpec = preparerSpecTable(el, offsetdebut);
        javax.swing.text.Element elSwing = doc.insereSpec(tableSpec, offsetdebut);
 
        creerElementsTableJaxe(el, elSwing);
 
        doc.textPane.finIgnorerEdition();
        doc.textPane.setCaretPosition(caretpos);
    }
    
    public void ajligne() {
        doc.textPane.getUndo().discardAllEdits();
        doc.textPane.miseAJourAnnulation();
        int pos = doc.textPane.getCaretPosition();
        JaxeElement jetrsel = lignesel();
        Element trsel;
        if (jetrsel != null)
            trsel = (Element)jetrsel.noeud;
        else
            trsel = null;
        int rsel;
        if (trsel != null)
            rsel = helper.numeroLigne(trsel);
        else
            rsel = -1;
        Element trnext = null;
        Element el = (Element)noeud;
        if (jetrsel != null) {
            trnext = helper.ligneSuivante((Element)jetrsel.noeud);
        }
        Element tr = nouvelElementDOM(doc, TRtag, el);
        Node textnode = doc.DOMdoc.createTextNode(newline);
        for (int ic=0; ic<nbcolonnes; ic++) {
            if (rsel != -1 && rsel+1 < nblignes && grille[rsel][ic] == grille[rsel+1][ic]) {
                Element td = grille[rsel][ic];
                String rowspan = td.getAttribute(rowspanAttr);
                int irowspan = 1;
                if (!"".equals(rowspan)) {
                    try {
                        irowspan = Integer.parseInt(rowspan);
                    } catch (NumberFormatException e) {
                    }
                }
                td.setAttribute(rowspanAttr, ""+(irowspan+1));
                while (ic+1<nbcolonnes && grille[rsel][ic+1] == td)
                    ic++;
            } else {
                Element td = nouvelElementDOM(doc, TDtag, tr);
                tr.appendChild(td);
            }
        }
        if (trnext == null) {
            el.appendChild(tr);
            el.appendChild(textnode);
        } else {
            el.insertBefore(tr, trnext);
            el.insertBefore(textnode, trnext);
        }
        /*
        int offset;
        if (trnext != null) {
            jetrsel = doc.getElementForNode(trnext);
            offset = jetrsel.fin.getOffset() + 1;
        } else
            offset = fin.getOffset() + 1;
        Object specLigne = preparerSpecLigne(tr, offset);
        javax.swing.text.Element trSwing = doc.insereSpec(specLigne, offset);
        creerElementsLigneJaxe(tr, trSwing);
        */ // la ligne n'est pas insérée au bon endroit...
        recreerTable(recupererComposants());
        doc.textPane.setCaretPosition(pos);
    }
    
    public void supligne() {
        JaxeElement jetrsel = lignesel();
        if (jetrsel == null || nblignes == 1)
            return;
        
        int result = JOptionPane.showConfirmDialog(doc.jframe, getString("table.RemoveRow"), getString("table.Attention"), JOptionPane.YES_NO_OPTION);
        
        if (result != JOptionPane.YES_OPTION) {
            return;
        }
        doc.textPane.getUndo().discardAllEdits();
        doc.textPane.miseAJourAnnulation();
        
        ArrayList allcomp = recupererComposants();
        
        Element trsel = (Element)jetrsel.noeud;
        int rsel = helper.numeroLigne(trsel);
        for (int ic=0; ic<nbcolonnes; ic++) {
            Element td = grille[rsel][ic];
            if (td != null) {
                if (rsel > 0 && grille[rsel-1][ic] == td) {
                    String rowspan = td.getAttribute(rowspanAttr);
                    int irowspan = 1;
                    if (!"".equals(rowspan)) {
                        try {
                            irowspan = Integer.parseInt(rowspan);
                        } catch (NumberFormatException e) {
                        }
                    }
                    td.setAttribute(rowspanAttr, ""+(irowspan-1));
                    while (ic+1<nbcolonnes && grille[rsel][ic+1] == td)
                        ic++;
                } else if (rsel+1 < nblignes && grille[rsel+1][ic] == td) {
                    // déplacement de td vers la ligne suivante + réduction rowspan
                    Element td2 = null;
                    int itd2 = 1;
                    while (ic+itd2 < nbcolonnes) {
                        if (grille[rsel+1][ic+itd2] != td) {
                            td2 = grille[rsel+1][ic+itd2];
                            break;
                        }
                        itd2++;
                    }
                    ((Element)td.getParentNode()).removeChild(td);
                    Element tr2 = null;
                    if (td2 == null) {
                        tr2 = helper.ligneSuivante(trsel);
                        if (tr2 != null)
                            tr2.appendChild(td);
                    } else {
                        tr2 = (Element)td2.getParentNode();
                        tr2.insertBefore(td, td2);
                    }
                    String rowspan = td.getAttribute(rowspanAttr);
                    int irowspan = 1;
                    if (!"".equals(rowspan)) {
                        try {
                            irowspan = Integer.parseInt(rowspan);
                        } catch (NumberFormatException e) {
                        }
                    }
                    td.setAttribute(rowspanAttr, ""+(irowspan-1));
                }
            }
        }
        
        try {
            Node parent = jetrsel.noeud.getParentNode();
            if (jetrsel.noeud.getNextSibling() != null &&
                    jetrsel.noeud.getNextSibling().getNodeType() == Node.TEXT_NODE)
                parent.removeChild(jetrsel.noeud.getNextSibling()); // retire le \n après </TR>
            parent.removeChild(jetrsel.noeud);
        } catch (DOMException ex) {
            System.err.println("DOMException: " + ex.getMessage());
        }
        
        recreerTable(allcomp);
    }
    
    public void ajcolonne() {
        doc.textPane.getUndo().discardAllEdits();
        doc.textPane.miseAJourAnnulation();
        int csel = colonnesel();
        if (csel == -1)
            csel = nbcolonnes - 1;
        Element el = (Element)noeud;
        int il = 0;
        for (Element tr=helper.premiereLigne(el); tr != null; tr=helper.ligneSuivante(tr)) {
            if (csel+1 < nbcolonnes && grille[il][csel] == grille[il][csel+1]) {
                Element td = grille[il][csel];
                String colspan = td.getAttribute(colspanAttr);
                int icolspan = 1;
                if (!"".equals(colspan)) {
                    try {
                        icolspan = Integer.parseInt(colspan);
                    } catch (NumberFormatException e) {
                    }
                }
                td.setAttribute(colspanAttr, ""+(icolspan+1));
                while (il+1<nblignes && grille[il+1][csel] == td) {
                    il++;
                    tr = helper.ligneSuivante(tr);
                }
            } else {
                Element td;
                if (tr == el.getFirstChild() && avecEntete)
                    td = nouvelElementDOM(doc, THtag, tr);
                else
                    td = nouvelElementDOM(doc, TDtag, tr);
                if (csel == -1) {
                    tr.appendChild(td);
                } else {
                    Element tdsel = helper.trouverCellule(tr, csel+1);
                    if (tdsel == null)
                        tr.appendChild(td);
                    else
                        tr.insertBefore(td, tdsel);
                }
            }
            il++;
        }
        recreerTable(recupererComposants());
    }
    
    public void supcolonne() {
        int csel = colonnesel();
        if (csel == -1 || nbcolonnes == 1)
            return;
        
        int result = JOptionPane.showConfirmDialog(doc.jframe, getString("table.RemoveColumn"), getString("table.Attention"), JOptionPane.YES_NO_OPTION);
        
        if (result != JOptionPane.YES_OPTION) {
            return;
        }
        doc.textPane.getUndo().discardAllEdits();
        doc.textPane.miseAJourAnnulation();
        
        ArrayList allcomp = recupererComposants();
        
        for (int il=0; il<nblignes; il++) {
            Element td = grille[il][csel];
            if (td != null) {
                if ((csel > 0 && grille[il][csel-1] == td) ||
                        (csel+1 < nbcolonnes && grille[il][csel+1] == td &&
                        (csel == 0 || grille[il][csel-1] != td))) {
                    String colspan = td.getAttribute(colspanAttr);
                    int icolspan = 1;
                    if (!"".equals(colspan)) {
                        try {
                            icolspan = Integer.parseInt(colspan);
                        } catch (NumberFormatException e) {
                        }
                    }
                    td.setAttribute(colspanAttr, ""+(icolspan-1));
                } else
                    ((Element)td.getParentNode()).removeChild(td);
                while (il+1<nblignes && grille[il+1][csel] == td)
                    il++;
            }
        }
        
        recreerTable(allcomp);
    }
    
    public void modifEntete() {
        doc.textPane.getUndo().discardAllEdits();
        doc.textPane.miseAJourAnnulation();

        avecEntete = !avecEntete;
        Element tr1 = helper.trouverLigne(0, noeud);
        if (tr1 == null)
            return;
        ArrayList allcomp = recupererComposants();
        if (avecEntete) {
            for (Node n = tr1.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE && n.getNodeName().equals(TDtag)) {
                    Element td = (Element)n;
                    Element th = nouvelElementDOM(doc, THtag, tr1);
                    Node frero = null;
                    for (Node n2 = td.getFirstChild(); n2 != null; n2=frero) {
                        frero = n2.getNextSibling();
                        th.appendChild(n2);
                    }
                    tr1.replaceChild(th, td);
                    n = th;
                }
            }
        } else {
            for (Node n = tr1.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE && n.getNodeName().equals(THtag)) {
                    Element th = (Element)n;
                    Element td = nouvelElementDOM(doc, TDtag, tr1);
                    Node frero = null;
                    for (Node n2 = th.getFirstChild(); n2 != null; n2=frero) {
                        frero = n2.getNextSibling();
                        td.appendChild(n2);
                    }
                    tr1.replaceChild(td, th);
                    n = td;
                }
            }
        }
        recreerTable(allcomp);
    }
    
    private JaxeElement lignesel() {
        // si on utilise elementA on risque de tomber sur des éléments d'une sous-table
        int pos = doc.textPane.getCaretPosition();
        Element el = (Element)noeud;
        for (Element tr=helper.premiereLigne(el); tr != null; tr=helper.ligneSuivante(tr)) {
            JaxeElement je = doc.getElementForNode(tr);
            if (je.debut.getOffset() <= pos && je.fin.getOffset() >= pos) {
                return(je);
            }
        }
        return(null);
    }
    
    private int colonnesel() {
        // si on utilise elementA on risque de tomber sur des éléments d'une sous-table
        JaxeElement jecell = cellulesel();
        if (jecell == null)
            return(-1);
        for (int il=0; il<nblignes; il++)
            for (int ic=0; ic<nbcolonnes; ic++) {
                if (grille[il][ic] == jecell.noeud)
                    return(ic);
            }
        System.err.println("colonnesel: noeud non trouvé dans la grille:");
        System.err.println(jecell.noeud);
        return(-1);
    }
    
    private JaxeElement cellulesel() {
        // si on utilise elementA on risque de tomber sur des éléments d'une sous-table
        int pos = doc.textPane.getCaretPosition();
        JaxeElement lsel = lignesel();
        if (lsel == null)
            return(null);
        Element tr = (Element)lsel.noeud;
        for (Node n=tr.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TDtag) || bal.equals(THtag)) {
                    JaxeElement je = doc.getElementForNode(n);
                    if (je.debut.getOffset() <= pos && je.fin.getOffset() >= pos) {
                        return(je);
                    }
                }
            }
        }
        return(null);
    }
    
    public void modligne() {
        JaxeElement jsel = lignesel();
        if (jsel == null)
            return;
        jsel.afficherDialogue(doc.jframe);
    }
    
    public void modcellule() {
        JaxeElement jesel = cellulesel();
        if (jesel == null)
            return;
        jesel.afficherDialogue(doc.jframe);
        majCellule(jesel);
    }
    
    // mise à jour du modèle et de l'affichage après modification de colspan ou rowspan
    public void majCellule(JaxeElement jesel) {
        int icolspan;
        int icolspan2;
        int irowspan;
        int irowspan2;
        
        if (jesel == null)
            return;
        int csel = colonnesel();
        Element el = (Element)jesel.noeud;
        Element trsel = (Element)el.getParentNode();
        int rsel = helper.numeroLigne(trsel);
        
        // obtention des colspan et rowspan d'avant la maj à partir de la grille
        icolspan = 0;
        while (csel+icolspan < nbcolonnes-1 &&
                grille[rsel][csel+icolspan] == grille[rsel][csel+icolspan+1])
            icolspan++;
        icolspan++;
        irowspan = 0;
        while (rsel+irowspan < nblignes-1 &&
                grille[rsel+irowspan][csel] == grille[rsel+irowspan+1][csel])
            irowspan++;
        irowspan++;
        
        // nouveaux colspan et rowspan à partir de l'élément DOM
        String colspan2 = ((Element)jesel.noeud).getAttribute(colspanAttr);
        String rowspan2 = ((Element)jesel.noeud).getAttribute(rowspanAttr);
        try {
            icolspan2 = Integer.parseInt(colspan2);
        } catch (NumberFormatException e) {
            icolspan2 = 1;
        }
        try {
            irowspan2 = Integer.parseInt(rowspan2);
        } catch (NumberFormatException e) {
            irowspan2 = 1;
        }
        
        // maj du modèle
        if (icolspan != icolspan2 || irowspan != irowspan2) {
            
            if (icolspan2 > icolspan) {
                int ntd = icolspan2 - icolspan;
                Node nextsibling = jesel.noeud.getNextSibling();
                for (Node n=nextsibling; n != null && ntd > 0; n=nextsibling) {
                    nextsibling = n.getNextSibling();
                    if (n.getNodeType() == Node.ELEMENT_NODE) {
                        String bal = n.getNodeName();
                        if (bal.equals(TDtag) || bal.equals(THtag)) {
                            n.getParentNode().removeChild(n);
                            ntd--;
                        }
                    }
                }
            } else if (icolspan > icolspan2) {
                Element tr = (Element)el.getParentNode();
                int ntd = icolspan - icolspan2;
                for (int i=0; i<ntd; i++) {
                    Element td;
                    if (THtag.equals(el.getNodeName()))
                        td = nouvelElementDOM(doc, THtag, tr);
                    else
                        td = nouvelElementDOM(doc, TDtag, tr);
                    if (el.getNextSibling() == null)
                        tr.appendChild(td);
                    else
                        tr.insertBefore(td, el.getNextSibling());
                }
            }
            if (irowspan2 > irowspan || icolspan2 > icolspan) {
                int nrow = irowspan2;
                int irow = 1;
                for (Element tr=helper.ligneSuivante(trsel); tr != null && irow < nrow; tr=helper.ligneSuivante(tr)) {
                    if (irowspan2 > irowspan && irow >= irowspan) {
                        Element elsup = helper.trouverCellule(tr, csel);
                        Node nextsibling;
                        int colsup;
                        if (irowspan2 > irowspan || icolspan2 <= icolspan)
                            colsup = icolspan2;
                        else
                            colsup = icolspan;
                        for (int i=0; i<colsup && elsup != null; i++) {
                            nextsibling = elsup.getNextSibling();
                            tr.removeChild(elsup);
                            elsup = (Element)nextsibling;
                        }
                    } else if (icolspan2 > icolspan) {
                        Element elsup = helper.trouverCellule(tr, csel+icolspan);
                        Node nextsibling;
                        for (int i=0; i<icolspan2 - icolspan && elsup != null; i++) {
                            nextsibling = elsup.getNextSibling();
                            tr.removeChild(elsup);
                            elsup = (Element)nextsibling;
                        }
                    }
                    irow++;
                }
            }
            if (irowspan > irowspan2 || icolspan > icolspan2) {
                int nrow = irowspan;
                int irow = 1;
                for (Element tr=helper.ligneSuivante(trsel); tr != null && irow < nrow; tr=helper.ligneSuivante(tr)) {
                    if (irowspan > irowspan2 && irow >= irowspan2) {
                        Element elsuiv = null;
                        for (int i=csel+1; i<nbcolonnes; i++)
                            if (grille[rsel+irow][i] != grille[rsel+irow-1][i]) {
                                elsuiv = grille[rsel+irow][i];
                                break;
                            }
                        int colaj;
                        if (irowspan > irowspan2 || icolspan <= icolspan2)
                            colaj = icolspan;
                        else
                            colaj = icolspan2;
                        for (int i=0; i<colaj; i++) {
                            Element td = nouvelElementDOM(doc, TDtag, tr);
                            if (elsuiv != null)
                                tr.insertBefore(td, elsuiv);
                            else
                                tr.appendChild(td);
                        }
                    } else if (icolspan > icolspan2) {
                        Element elsuiv = null;
                        for (int i=csel+icolspan; i<nbcolonnes; i++)
                            if (grille[rsel+irow][i] != grille[rsel+irow-1][i]) {
                                elsuiv = grille[rsel+irow][i];
                                break;
                            }
                        for (int i=0; i<icolspan - icolspan2; i++) {
                            Element td = nouvelElementDOM(doc, TDtag, tr);
                            if (elsuiv != null)
                                tr.insertBefore(td, elsuiv);
                            else
                                tr.appendChild(td);
                        }
                    }
                    irow++;
                }
            }
            ArrayList allcomp = recupererComposants();
            recreerTable(allcomp);
        }
    }
    
    public void effacer() {
        effacerComposants(recupererComposants());
        super.effacer();
    }
    
    private JETableTexte getTable() {
        
        JaxeElement el = doc.elementA(doc.textPane.getCaretPosition());
        Node p = el.noeud;
        
        while (( p != null) && (!p.getNodeName().equals(tableTag))) {
            p = p.getParentNode();
        }
        
        if (p != null) {
            return (JETableTexte)doc.getElementForNode(p);
        }
        
        return null;
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        JETableTexte jetable = getTable();
        if (jetable == null) jetable = this;
        if ("ajligne".equals(cmd))
            jetable.ajligne();
        else if ("ajcolonne".equals(cmd))
            jetable.ajcolonne();
        else if ("supligne".equals(cmd))
            jetable.supligne();
        else if ("supcolonne".equals(cmd))
            jetable.supcolonne();
        else if ("entête".equals(cmd))
            this.modifEntete();
        else if ("modtable".equals(cmd))
            jetable.afficherDialogue(doc.jframe);
        else if ("modligne".equals(cmd))
            jetable.modligne();
        else if ("modcellule".equals(cmd))
            jetable.modcellule();
    }

    class JESwingTD extends JESwing {
        public JESwingTD(JaxeDocument doc, Element elDOM, javax.swing.text.Element elSwing) {
            super(doc, elDOM, elSwing);
        }
        
        public void afficherDialogue(JFrame jframe) {
            Element el = (Element)noeud;

            Element defbalise = doc.cfg.getElementDef(el);
            ArrayList latt = doc.cfg.listeAttributs(defbalise);
            if (latt != null && latt.size() > 0) {
                DialogueAttributs dlg = new DialogueAttributs(doc.jframe, doc,
                    el.getTagName(), defbalise, el);
                if (dlg.afficher()) {
                    doc.textPane.getUndo().discardAllEdits();
                    doc.textPane.miseAJourAnnulation();
                    dlg.enregistrerReponses();
                    majAffichage();
                }
                dlg.dispose();
            }
        }
        
        public void majAffichage() {
            JETableTexte.this.majCellule(this);
        }
    }
}
