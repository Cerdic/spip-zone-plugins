/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe.elements;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.Cursor;
import java.awt.Font;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.Vector;

import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.table.AbstractTableModel;
import javax.swing.table.DefaultTableCellRenderer;
import javax.swing.table.TableModel;
import javax.swing.text.JTextComponent;
import javax.swing.text.Position;
import javax.swing.text.Style;

import jaxe.DialogueChamps;
import jaxe.JaxeDocument;
import jaxe.JaxeElement;
import jaxe.JaxeResourceBundle;

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;


/**
 * Tableau affiché comme tel dans le texte. Les éléments du tableau ne peuvent être que
 * de courts textes.
 * Type d'élément Jaxe: 'tableau'
 * paramètre: trTag: un attribut correspondant à une ligne de tableau
 * paramètre: tdTag: un attribut correspondant à une cellule de tableau
 * paramètre: thTag: un attribut correspondant à une cellule d'entête de tableau
 */
public class JETable extends JaxeElement implements ActionListener {

    static String newline = "\n";
    String TRtag = "tr";
    String TDtag = "td";
    String THtag = null;
    JTable jtable = null;
    boolean avecEntete;

    /*
    Comme les tables JETable ne permettent pas de mettre autre chose que du texte dans
    les cases, il vaut mieux créer une zone à la place si le fichier ouvert contient
    autre chose que du texte dans les cases du tableau ou si des attributs sont utilisés
    dans les balises TD
    */
    public static boolean preferreZone(JaxeDocument doc, Element el) {
         // la méthode est statique, il faut utiliser des variables locales...
        String TRtag = "tr";
        String TDtag = "td";
        String THtag = null;
        
        Element defbalise = doc.cfg.getBaliseAvecType("tableau");
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
                TRtag = doc.cfg.getParamFromDefinition(defbalise, "trTag", TRtag);
                TDtag = doc.cfg.getParamFromDefinition(defbalise, "tdTag", TDtag);
                THtag = doc.cfg.getParamFromDefinition(defbalise, "thTag", THtag);
            }
        }

        for (Node n=el.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag)) {
                    for (Node n2=n.getFirstChild(); n2 != null; n2=n2.getNextSibling()) {
                        if (n2.getNodeType() == Node.ELEMENT_NODE) {
                            String bal2 = n2.getNodeName();
                            if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                                if (n2.getAttributes() != null && n2.getAttributes().getLength() > 0)
                                    return(true);
                                for (Node n3=n2.getFirstChild(); n3 != null; n3=n3.getNextSibling()) {
                                    if (n3.getNodeType() == Node.ELEMENT_NODE)
                                        return(true);
                                }
                            }
                        }
                    }
                }
            }
        }
        return(false);
    }
    
    public JETable(JaxeDocument doc) {
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
                TRtag = doc.cfg.getParamFromDefinition(defbalise, "trTag", TRtag);
                TDtag = doc.cfg.getParamFromDefinition(defbalise, "tdTag", TDtag);
                THtag = doc.cfg.getParamFromDefinition(defbalise, "thTag", THtag);
            }
        }
    }
    
    public void init(Position pos, Node noeud) {
        Element el = (Element)noeud;
        Element defbalise = doc.cfg.getBaliseNomType(el.getTagName(), "tableau");
        obtenirTags(defbalise);
        
        Style s = doc.textPane.addStyle(null, null);
        
        jtable = makeTable(el);
        
        //jtable.addMouseListener(new MyMouseListener(this, doc.jframe));
        
        JPanel p = new JPanel(new BorderLayout());
        p.setCursor(Cursor.getDefaultCursor());
        p.add(jtable, BorderLayout.CENTER);
        JPanel pboutons = new JPanel();
        pboutons.setLayout(new BoxLayout(pboutons, BoxLayout.Y_AXIS));
        if (THtag != null) {
            NodeList thnl = el.getElementsByTagName(THtag);
            avecEntete = (thnl != null && thnl.getLength() > 0);
            JCheckBox bcheck = new JCheckBox(getString("table.Entete"), avecEntete);
            bcheck.addActionListener(this);
            bcheck.setActionCommand("entête");
            bcheck.setFont(bcheck.getFont().deriveFont((float)9));
            bcheck.setMargin(new Insets(0,0,0,0));
            pboutons.add(bcheck);
        } else
            avecEntete = false;
        JButton bajligne = new JButton(getString("table.AjouterLigne"));
        bajligne.addActionListener(this);
        bajligne.setActionCommand("ajligne");
        bajligne.setFont(bajligne.getFont().deriveFont((float)9));
        bajligne.setMargin(new Insets(0,0,0,0));
        pboutons.add(bajligne);
        JButton bajcolonne = new JButton(getString("table.AjouterColonne"));
        bajcolonne.addActionListener(this);
        bajcolonne.setActionCommand("ajcolonne");
        bajcolonne.setFont(bajcolonne.getFont().deriveFont((float)9));
        bajcolonne.setMargin(new Insets(0,0,0,0));
        pboutons.add(bajcolonne);
        JButton bsupligne = new JButton(getString("table.SupprimerLigne"));
        bsupligne.addActionListener(this);
        bsupligne.setActionCommand("supligne");
        bsupligne.setFont(bsupligne.getFont().deriveFont((float)9));
        bsupligne.setMargin(new Insets(0,0,0,0));
        pboutons.add(bsupligne);
        JButton bsupcolonne = new JButton(getString("table.SupprimerColonne"));
        bsupcolonne.addActionListener(this);
        bsupcolonne.setActionCommand("supcolonne");
        bsupcolonne.setFont(bsupcolonne.getFont().deriveFont((float)9));
        bsupcolonne.setMargin(new Insets(0,0,0,0));
        pboutons.add(bsupcolonne);
        p.add(pboutons, BorderLayout.EAST);

        insertComponent(pos, p);
    }
    
    protected TableModel makeTableModel(Element el) {
        Vector v = new Vector();
        Vector ventete = new Vector();
        int nligne = 0;
        for (Node n=el.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag)) {
                    Vector v2 = new Vector();
                    for (Node n2=n.getFirstChild(); n2 != null; n2=n2.getNextSibling()) {
                        if (n2.getNodeType() == Node.ELEMENT_NODE) {
                            String bal2 = n2.getNodeName();
                            if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                                Node n3 = n2.getFirstChild();
                                String sval;
                                if (n3 != null && n3.getNodeValue() != null)
                                    sval = n3.getNodeValue().trim();
                                else
                                    sval = "";
                                v2.add(sval);
                                if (nligne == 0)
                                    ventete.add("");
                            }
                        }
                    }
                    if (nligne == 0 || v2.size() == ((Vector)v.get(0)).size()) {
                        v.add(v2);
                        nligne++;
                    } else
                        System.err.println("Erreur: nombre de <TD> incorrect dans la ligne");
                }
            }
            // on ignore le reste
        }
        return(new MyTableModel(v, ventete));
    }
    
    class MyTableModel extends AbstractTableModel {
        Vector rowData, columnNames;
        public MyTableModel(Vector rowData, Vector columnNames) {
            this.rowData = rowData;
            this.columnNames = columnNames;
        }
        public int getRowCount() {
            return(rowData.size());
        }
        public int getColumnCount() {
            return(columnNames.size());
        }
        public Object getValueAt(int row, int column) {
            return(((Vector)rowData.elementAt(row)).elementAt(column));
        }
        public String getColumnName(int column) {
            return((String)columnNames.get(column));
        }
        public boolean isCellEditable(int row, int column) {
            return(true);
        }
        public void setValueAt(Object aValue, int row, int column) {
            ((Vector)rowData.elementAt(row)).setElementAt(aValue, column);
            Element tr = findligne(row);
            Element td = findcellule(tr, column);
            String s = (String)aValue;
            if (td.getFirstChild() == null) {
                Node textnode = doc.DOMdoc.createTextNode(s);
                td.appendChild(textnode);
            } else
                td.getFirstChild().setNodeValue(s);
        }
    }
    
    protected JTable makeTable(Element el) {
        JTable ntable = new JTable(makeTableModel(el));
        ntable.setShowGrid(true);
        ntable.setGridColor(Color.black);
        ntable.setDefaultRenderer(Object.class, new CustomCellRenderer());
        return(ntable);
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
        String slignes = champs[0].getText();
        String scolonnes = champs[1].getText();
        
        int nlignes, ncolonnes;
        try {
            nlignes = (Integer.valueOf(slignes)).intValue();
            ncolonnes = (Integer.valueOf(scolonnes)).intValue();
        } catch (NumberFormatException ex) {
            JOptionPane.showMessageDialog(doc.jframe, JaxeResourceBundle.getRB().getString("erreur.Conversion"),
                JaxeResourceBundle.getRB().getString("table.NouvelleBalise"), JOptionPane.ERROR_MESSAGE);
            return(null);
        }
        
        obtenirTags(defbalise);
        avecEntete = false;

        Element newel = nouvelElementDOM(doc, defbalise);
        for (int i=0; i<nlignes; i++) {
            Element ligneel = nouvelElementDOM(doc, TRtag, newel);
            newel.appendChild(ligneel);
            for (int j=0; j<ncolonnes; j++) {
                Element cellel = nouvelElementDOM(doc, TDtag, ligneel);
                ligneel.appendChild(cellel);
            }
            Node textnode = doc.DOMdoc.createTextNode(newline);
            newel.appendChild(textnode);
        }

        return(newel);
    }
    
    public void afficherDialogue(JFrame jframe) {
    }
    
    public void majAffichage() {
        jtable.setModel(makeTableModel((Element)noeud));
    }
    
    public void mettreAJourDOM() {
        Element el = (Element)noeud;
        Element tr = null;
        for (int l=0; l<jtable.getRowCount(); l++) {
            Node nr;
            if (tr == null)
                nr = el.getFirstChild();
            else
                nr = tr.getNextSibling();
            tr = null;
            for (; nr != null && tr == null; nr=nr.getNextSibling())
                if (nr.getNodeType() == Node.ELEMENT_NODE) {
                    String bal = nr.getNodeName();
                    if (bal.equals(TRtag))
                        tr = (Element)nr;
                }
            if (tr == null) {
                System.err.println("Erreur: balise TR non trouvée dans JETable.mettreAJourDOM()");
                return;
            }
            Element td = null;
            for (int c=0; c<jtable.getColumnCount(); c++) {
                Node nd;
                if (td == null)
                    nd = tr.getFirstChild();
                else
                    nd = td.getNextSibling();
                td = null;
                for (; nd != null && td == null; nd=nd.getNextSibling())
                    if (nd.getNodeType() == Node.ELEMENT_NODE) {
                        String bal = nd.getNodeName();
                        if (bal.equals(TDtag) || bal.equals(THtag))
                            td = (Element)nd;
                    }
                if (td == null) {
                    System.err.println("Erreur: balise TD non trouvée dans JETable.mettreAJourDOM()");
                    return;
                }
                String s = (String)jtable.getValueAt(l, c);
                if (td.getFirstChild() == null) {
                    Node textnode = doc.DOMdoc.createTextNode(s);
                    td.appendChild(textnode);
                } else
                    td.getFirstChild().setNodeValue(s);
            }
        }
    }
    
    protected Element findligne(int lsel) {
        Element el = (Element)noeud;
        int l = 0;
        for (Node n=el.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag)) {
                    if (l == lsel) {
                        Element tr = (Element)n;
                        return(tr);
                    }
                    l++;
                }
            }
        }
        return(null);
    }
    
    protected Element findcellule(Element tr, int csel) {
        int c = 0;
        for (Node n=tr.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TDtag) || bal.equals(THtag)) {
                    if (c == csel) {
                        Element td = (Element)n;
                        return(td);
                    }
                    c++;
                }
            }
        }
        return(null);
    }
    
    public void ajligne() {
        int lsel = jtable.getSelectedRow();
        mettreAJourDOM();
        Element el = (Element)noeud;
        Element tr = nouvelElementDOM(doc, TRtag, el);
        if (lsel == -1) {
            el.appendChild(tr);
        } else {
            Element trsel = findligne(lsel+1);
            Node textnode = doc.DOMdoc.createTextNode(newline);
            if (trsel == null) {
                el.appendChild(tr);
                el.appendChild(textnode);
            } else {
                el.insertBefore(tr, trsel);
                el.insertBefore(textnode, trsel);
            }
        }
        for (int j=0; j<jtable.getColumnCount(); j++) {
            Element td = nouvelElementDOM(doc, TDtag, tr);
            tr.appendChild(td);
        }
        jtable.setModel(makeTableModel(el));
    }
    
    public void ajcolonne() {
        int csel = jtable.getSelectedColumn();
        mettreAJourDOM();
        Element el = (Element)noeud;
        for (Node n=el.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag)) {
                    Element tr = (Element)n;
                    Element td;
                    if (n == el.getFirstChild() && avecEntete)
                        td = nouvelElementDOM(doc, THtag, tr);
                    else
                        td = nouvelElementDOM(doc, TDtag, tr);
                    if (csel == -1) {
                        tr.appendChild(td);
                    } else {
                        Element tdsel = findcellule(tr, csel+1);
                        if (tdsel == null)
                            tr.appendChild(td);
                        else
                            tr.insertBefore(td, tdsel);
                    }
                }
            }
        }
        jtable.setModel(makeTableModel(el));
    }
    
    public void supligne() {
        int lsel = jtable.getSelectedRow();
        if (lsel == -1)
            return;
        mettreAJourDOM();
        Element el = (Element)noeud;
        Element tr = findligne(lsel);
        if (tr != null) {
            if (tr.getNextSibling() != null && tr.getNextSibling().getNodeType() == Node.TEXT_NODE)
                el.removeChild(tr.getNextSibling());
            el.removeChild(tr);
            jtable.setModel(makeTableModel(el));
        }
    }
    
    public void supcolonne() {
        int csel = jtable.getSelectedColumn();
        if (csel == -1)
            return;
        mettreAJourDOM();
        Element el = (Element)noeud;
        for (Node n=el.getFirstChild(); n != null; n=n.getNextSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag)) {
                    Element td = findcellule((Element)n, csel);
                    if (td != null)
                        n.removeChild(td);
                }
            }
        }
        jtable.setModel(makeTableModel(el));
    }
    
    public void modifEntete() {
        avecEntete = !avecEntete;
        Element tr1 = findligne(0);
        if (tr1 == null)
            return;
        if (avecEntete) {
            for (Node n = tr1.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE && n.getNodeName().equals(TDtag)) {
                    Element td = (Element)n;
                    Node nval = n.getFirstChild();
                    String sval;
                    if (nval != null && nval.getNodeValue() != null)
                        sval = nval.getNodeValue().trim();
                    else
                        sval = "";
                    Element th = nouvelElementDOM(doc, THtag, tr1);
                    Node textnode = doc.DOMdoc.createTextNode(sval);
                    th.appendChild(textnode);
                    tr1.replaceChild(th, td);
                    n = th;
                }
            }
        } else {
            for (Node n = tr1.getFirstChild(); n != null; n=n.getNextSibling()) {
                if (n.getNodeType() == Node.ELEMENT_NODE && n.getNodeName().equals(THtag)) {
                    Element th = (Element)n;
                    Node nval = n.getFirstChild();
                    String sval;
                    if (nval != null && nval.getNodeValue() != null)
                        sval = nval.getNodeValue().trim();
                    else
                        sval = "";
                    Element td = nouvelElementDOM(doc, TDtag, tr1);
                    Node textnode = doc.DOMdoc.createTextNode(sval);
                    td.appendChild(textnode);
                    tr1.replaceChild(td, th);
                    n = td;
                }
            }
        }
        jtable.repaint();
    }
    
    // pour avoir la première ligne en gras quand c'est un "entête"
    class CustomCellRenderer extends DefaultTableCellRenderer {
        public CustomCellRenderer() {
        }
    
	public Component getTableCellRendererComponent( JTable table, Object value,
                boolean isSelected, boolean hasFocus, int row, int column ) {
            
            if (avecEntete && row == 0)
                setFont(new Font("Helvetica", Font.BOLD, 13));
            
            super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column );
        
            if (avecEntete && row == 0)
                setFont(new Font("Helvetica", Font.BOLD, 13));
            
            return this;
        }
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("ajligne".equals(cmd))
            ajligne();
        else if ("ajcolonne".equals(cmd))
            ajcolonne();
        else if ("supligne".equals(cmd))
            supligne();
        else if ("supcolonne".equals(cmd))
            supcolonne();
        else if ("entête".equals(cmd))
            modifEntete();
    }

    /*
    class MyMouseListener extends MouseAdapter {
        JETable jei;
        JFrame jframe;
        public MyMouseListener(JETable obj, JFrame jframe) {
            super();
            jei = obj;
            this.jframe = jframe;
        }
        public void mouseClicked(MouseEvent e) {
            if (e.getClickCount() == 2) {
                //int index = list.locationToIndex(e.getPoint());
                //System.out.println("Double clicked on Item " + index);
                jei.afficherDialogue(jframe);
            }
        }
    }
    */

}
