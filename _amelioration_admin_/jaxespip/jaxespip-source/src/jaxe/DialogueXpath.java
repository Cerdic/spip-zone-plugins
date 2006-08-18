/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Toolkit;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.util.ArrayList;
import java.util.ResourceBundle;

import javax.swing.BorderFactory;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.ButtonGroup;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JComboBox;
import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

public class DialogueXpath extends JFrame implements ActionListener, ItemListener, CaretListener {
    
    private static ResourceBundle rb = JaxeResourceBundle.getRB();
    JTextField textF = new JTextField("");
    JCheckBox box1, box2;
    ButtonGroup groupe;
    private JButton butOk, butAnnuler;
    private ArrayList listingBalises = new ArrayList();
    DialogueRechercher DialRech;
    JaxeDocument doc;
    private JComboBox list;
    private Element baliseCourante;
    private String nomBaliseCourante = "";
    private int nbBalise; 
    private JTextField textInterne;
    private JComponent[] champsAtt;
    private String[] titresAtt;
    private String[] typeRech;
    private String typeRechTxt;
    private JPanel pane, exprPane, listPane, textPane, attrPane, buttonPane;
    private int natt;
    private String prefix = null;
    private boolean existNS = false;

    public DialogueXpath(final DialogueRechercher DR, JaxeDocument doc) {
        super(rb.getString("xpath.ExprXpath"));
        DialRech = DR;
        this.doc = doc;
        org.w3c.dom.Node DomRoot = (org.w3c.dom.Node)doc.DOMdoc.getDocumentElement();
        if (DomRoot.getNamespaceURI() != null)
            existNS = true;
        prefix = DomRoot.getPrefix();
        
        pane = new JPanel();
        pane.setLayout(new BoxLayout(pane, BoxLayout.Y_AXIS));

        listPane = new JPanel(new FlowLayout());
        JLabel textElt = new JLabel(rb.getString("xpath.TitreElt"));
        listingBalises = listingBalises();
        nbBalise = listingBalises.size();
        ArrayList listeTitre = new ArrayList();
        for (int i=0; i<nbBalise; i++) { 
            listeTitre.add(doc.cfg.titreBalise((Element)listingBalises.get(i)));
        }
        list = new JComboBox(listeTitre.toArray());
        if ("".equals(nomBaliseCourante)) {
            baliseCourante = (Element)listingBalises.get(0);
            nomBaliseCourante = doc.cfg.nomBalise(baliseCourante);
            ArrayList latt = doc.cfg.listeAttributs(baliseCourante);
            natt = latt.size();   
        }
        list.addItemListener(this);
        listPane.add(textElt);
        listPane.add(list);
                
        majAttPanel();
        majExprXpath();

        setContentPane(pane); 
        pack();
        Dimension screen = Toolkit.getDefaultToolkit().getScreenSize();
        setLocation((screen.width - getSize().width)/2,(screen.height - getSize().height)/2);
        setVisible(true);
        setResizable(false);
        
        addWindowListener(new WindowAdapter() {
            public void windowClosing(WindowEvent e) {
                dispose();
                DialRech.setVisible(true);
            }
        });
    }

    
    public void majAttPanel() {
        if (doc.cfg.schemaBaliseDef(doc.cfg.nomBalise(baliseCourante))==null){
            dispose();
            JOptionPane.showMessageDialog(this,rb.getString("xpath.ErrBalise"),rb.getString("xpath.Err"), JOptionPane.INFORMATION_MESSAGE);
            return;
        }
        textPane = new JPanel();
        textPane.setLayout(new BoxLayout(textPane, BoxLayout.X_AXIS));
        JLabel includingText = new JLabel(rb.getString("xpath.IncludingText"));
        textInterne = new JTextField("");
        textInterne.addCaretListener(this);
        typeRechTxt = "contient";
        groupe = new ButtonGroup();
        box1 = new JCheckBox(rb.getString("xpath.TexteExact"),false);
        groupe.add(box1);
        box1.addItemListener(new ItemListener() {
            public void itemStateChanged(ItemEvent e) {   
            	typeRechTxt = "texte exact";
                majExprXpath();
                }
            }
        );
        box2 = new JCheckBox(rb.getString("xpath.Contient"),true);
        groupe.add(box2);
        box2.addItemListener(new ItemListener() {
            public void itemStateChanged(ItemEvent e) {   
            	typeRechTxt = "contient";
                majExprXpath();
                }
            }
        );
        textPane.add(Box.createHorizontalStrut(5));
        textPane.add(includingText);
        textPane.add(Box.createHorizontalStrut(2));
        textPane.add(textInterne);
        textPane.add(box1);
        textPane.add(box2);
        textPane.add(Box.createHorizontalStrut(5));
        
        attrPane = new JPanel();
        attrPane.setBorder(BorderFactory.createTitledBorder(BorderFactory.createEtchedBorder(1,Color.white,new Color(70,70,70)),rb.getString("xpath.TitleAtt") + " \" " + doc.cfg.nomBalise(baliseCourante) + " \"",1,2));
        ArrayList latt = doc.cfg.listeAttributs(baliseCourante);
        natt = latt.size();
        titresAtt = new String[natt];
        typeRech = new String[natt];
        champsAtt = new JComponent[natt];
        attrPane.setLayout(new GridBagLayout());
        GridBagConstraints c = new GridBagConstraints();
        if (natt==0){
            JLabel textAtt = new JLabel(rb.getString("xpath.NoneAtt"));
            textAtt.setForeground(Color.gray);
            attrPane.add(textAtt);
            attrPane.add(Box.createVerticalStrut(30));
        }
        for (int i=0; i<natt; i++) {
            Element att = (Element)latt.get(i);
            titresAtt[i] = doc.cfg.nomAttribut(att);
            JLabel textAtt = new JLabel(titresAtt[i] + " : ");            
            String[] lval = doc.cfg.listeValeurs(att);
            if (lval != null && lval.length > 0) {
                champsAtt[i] = new JComboBox();
                ((JComboBox) champsAtt[i]).addActionListener(new ActionListener() {
                     public void actionPerformed(ActionEvent e) {   
                         majExprXpath();
                         }
                     }
                 );  
                ((JComboBox)champsAtt[i]).addItem("");
                for (int j=0; j<lval.length; j++) {
                    String sval = lval[j];
                    ((JComboBox)champsAtt[i]).addItem(sval);
                }
            }
            else {
                champsAtt[i] = new JTextField(42);
                ((JTextField) champsAtt[i]).addCaretListener(this);
            }
            c.gridx = 0;
            c.gridy = i;        
            c.anchor = GridBagConstraints.EAST;
            attrPane.add(textAtt,c);
            c.gridx = 1;
            c.gridy = i;
            c.anchor = GridBagConstraints.WEST;
            attrPane.add(champsAtt[i],c);             
            if (champsAtt[i] instanceof JTextField) {
                typeRech[i] = "contient";
                final int value = i;
                groupe = new ButtonGroup();
                box1 = new JCheckBox(rb.getString("xpath.MotExact"),false);
                groupe.add(box1);
                box1.addItemListener(new ItemListener() {
                    public void itemStateChanged(ItemEvent e) {   
                        typeRech[value] = "mot exact";
                        majExprXpath();
                        }
                    }
                );        
                c.gridx = 2;
                c.gridy = i;
                attrPane.add(box1,c);  
                box2 = new JCheckBox(rb.getString("xpath.Contient"),true);
                groupe.add(box2);
                box2.addItemListener(new ItemListener() {
                    public void itemStateChanged(ItemEvent e) {   
                        typeRech[value] = "contient";
                        majExprXpath();
                        }
                    }
                );
                c.gridx = 3;
                c.gridy = i;
                attrPane.add(box2,c);                  
            } 
            else {
                typeRech[i] = "mot exact";
            }
        } 
        
        exprPane = new JPanel(new FlowLayout());
        JLabel textRes = new JLabel(rb.getString("xpath.Expr"));
        textF = new JTextField("", 55);
        textF.setEditable(false);
        textF.setForeground(Color.red);
        exprPane.add(textRes);
        exprPane.add(textF);        
        
        buttonPane = new JPanel();
        butOk = new JButton(rb.getString("xpath.Ok"));
        butOk.addActionListener(this);
        butOk.setActionCommand("OK");
        getRootPane().setDefaultButton(butOk);
        butAnnuler = new JButton(rb.getString("xpath.Annuler"));
        butAnnuler.addActionListener(this);
        butAnnuler.setActionCommand("Annuler");
        buttonPane.add(butOk);
        buttonPane.add(butAnnuler);   
        
        pane.add(Box.createVerticalStrut(10));
        pane.add(listPane);
        pane.add(Box.createVerticalStrut(10));
        pane.add(textPane);        
        pane.add(Box.createVerticalStrut(10));
        pane.add(attrPane);
        pane.add(Box.createVerticalStrut(10));
        pane.add(exprPane);
        pane.add(Box.createVerticalStrut(5));
        pane.add(buttonPane);
    }
        
    
    public void caretUpdate(CaretEvent e) {
        majExprXpath();
    }
    
    
    public void actionPerformed(ActionEvent e) {
        if (e.getSource()==butOk){
            dispose();
            DialRech.tfRechercher.setText(textF.getText());
            DialRech.setVisible(true);
        }
        if (e.getSource()==butAnnuler){
            dispose();
            DialRech.setVisible(true);
        }
    }
    
    
    public void majExprXpath() {
        boolean existAtt = false;
        StringBuffer expr = new StringBuffer();
        if (existNS) {
            if (prefix != null){
                expr.append("//" + prefix + ":" + nomBaliseCourante);
            } 
            else {
                expr.append("//*[local-name()=\"" + nomBaliseCourante + "\"");
                existAtt = true;
            }
        }
        else {
            expr.append("//" + nomBaliseCourante);
        }    
        if (!textInterne.getText().equals("")) {
        	if (existAtt)
        		expr.append(" and ");
        	else {
            	expr.append("[");
            	existAtt = true;
        	}        	
        	if (typeRechTxt.equals("contient"))
    			expr.append("contains(.,\"" + textInterne.getText() + "\")");
    		else 
    			expr.append(".=\"" + textInterne.getText() + "\"");        	
        }
        for (int i=0; i<natt; i++){
            String valAtt = "";
            if (champsAtt[i] instanceof JTextField)
                valAtt = ((JTextField)champsAtt[i]).getText();
            else if (champsAtt[i] instanceof JComboBox)
                valAtt = (String)((JComboBox)champsAtt[i]).getSelectedItem();
            boolean nonvide = (!"".equals(valAtt));
            if (nonvide){
                if (existAtt) {
                    expr.append(" and ");
                } else {
                	expr.append("[");
                	existAtt = true;
                }
                if (typeRech[i].equals("contient")) {
                    expr.append("contains(@" + titresAtt[i] + ",\"" + valAtt + "\")"); 
                } else {
                     expr.append("@" + titresAtt[i] + "=\"" + valAtt + "\""); 
                } 
            }
        }
        if (existAtt)
            expr.append("]");
        textF.setText(expr.toString());
    }

    
    public void itemStateChanged(ItemEvent e) {
       Object source = e.getSource();
       if ((source == list)&&(e.getStateChange()==1)) {
           baliseCourante = (Element)listingBalises.get(list.getSelectedIndex());
           nomBaliseCourante = doc.cfg.nomBalise(baliseCourante);
           pane.removeAll();
           majAttPanel();
           majExprXpath();
           pane.updateUI();
           pack();
       }
    }
    
    
    public ArrayList listingBalises() {
        ArrayList liste = new ArrayList();
        NodeList lbalise = doc.cfg.jaxecfg.getElementsByTagName("BALISE");
        for (int i=0; i<lbalise.getLength(); i++) {
            liste.add((Element)lbalise.item(i));
        }
        return(TriListingElt(liste));       
    }
    
    
    public ArrayList TriListingElt (ArrayList list){
        ArrayList listeTriee = new ArrayList(list.size());
        char[][] replaceChar = {{'Ô','O'},{'Ï','I'},{'Î','I'},{'À','A'},{'Â','A'},{'Ë','E'},{'Ê','E'},{'È','E'},{'É','E'}};
        if (list.size()>0)
            listeTriee.add(0,(Element)list.get(0));
        for (int i=1; i<list.size(); i++) {
            boolean ajout = false;
            String eltTraite = (doc.cfg.titreBalise((Element)list.get(i)).toUpperCase());
            for (int k=0; k<replaceChar.length; k++) {
                eltTraite = eltTraite.replace(replaceChar[k][0],replaceChar[k][1]);
            }    
            for (int j=0; j<i; j++) {
                String eltTrie = (doc.cfg.titreBalise((Element)listeTriee.get(j)).toUpperCase());
                for (int k=0; k<replaceChar.length; k++) {
                    eltTrie = eltTrie.replace(replaceChar[k][0],replaceChar[k][1]);
                } 
                if (((eltTraite.trim()).compareToIgnoreCase((eltTrie.trim()).toUpperCase()))<=0) {
                    listeTriee.add(j,(Element)list.get(i));
                    ajout = true;
                    j=i;
                }
            }
            if (!ajout)
                listeTriee.add(i,(Element)list.get(i));
        }
        return listeTriee;
    }        
        
}       