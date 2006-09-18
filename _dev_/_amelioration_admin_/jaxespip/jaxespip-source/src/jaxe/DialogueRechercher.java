/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.Toolkit;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.util.ResourceBundle;

import javax.swing.BorderFactory;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.ButtonGroup;
import javax.swing.Icon;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JRadioButton;
import javax.swing.JTextField;
import javax.swing.text.BadLocationException;
import javax.swing.text.Element;
import javax.swing.text.StyleConstants;
import javax.swing.text.StyledDocument;
import javax.xml.transform.TransformerException;

import org.w3c.dom.NodeList;


public class DialogueRechercher extends JDialog implements ActionListener, ItemListener {

    private static ResourceBundle rb = JaxeResourceBundle.getRB();

    private JaxeTextPane textPane;
    private StyledDocument doc;
    private JPanel bpane, cpane;
    JTextField tfRechercher;
    private JTextField tfRemplacer;
    private JLabel textRech = new JLabel(rb.getString("rechercher.Rechercher"));
    private JLabel textRemp = new JLabel(rb.getString("rechercher.RemplacerPar")); 
    private JButton bXpath, btout, bremplacer, bremplrech;
    JRadioButton bfichier, bsel;
    private JCheckBox typeXpath, chkmaj; 
    private boolean dansSelection = false;
    private boolean ignorerCasse = false;
    public boolean RechXpath = false;
    static String texteRecherche = null;
    static String texteXpathRecherche = null;   
    public JaxeDocument Jdoc;

    public DialogueRechercher(JaxeDocument Jdoc, JaxeTextPane textPane, JFrame frame) {
        super(frame, rb.getString("rechercher.Rechercher"), false);
        this.textPane = textPane;
        this.Jdoc = Jdoc;
        doc = textPane.getStyledDocument();
        if (textPane.getSelectionEnd() != textPane.getSelectionStart()) dansSelection = true; 
        
        cpane = new JPanel();
        cpane.setLayout(new BoxLayout(cpane, BoxLayout.Y_AXIS));
        
        // rechercher / remplacer
        setContentPane(cpane);
        JPanel chpane = new JPanel(new BorderLayout());
        JPanel qpane = new JPanel(new GridLayout(2, 1));
        qpane.add(textRech);
        qpane.add(textRemp);
        qpane.setBorder(BorderFactory.createEmptyBorder(0, 5, 0, 5));
        JPanel tfpane = new JPanel(new GridLayout(2, 1)); // textfields
        tfRechercher = new JTextField("", 40);
        if (texteRecherche != null)
            tfRechercher.setText(texteRecherche);
        tfRechercher.selectAll();
        tfpane.add(tfRechercher);
        tfRemplacer = new JTextField("", 40);
        tfpane.add(tfRemplacer);
        
        //bpane = new JPanel(new GridLayout(2,1)); 
        //bpane.setPreferredSize(new Dimension(85,50));
        /*bXpath = new JButton(rb.getString("rechercher.Xpath"));
        bXpath.setActionCommand("xpath");
        bXpath.addActionListener(this);*/
        
        chpane.add(qpane, BorderLayout.WEST);
        chpane.add(tfpane, BorderLayout.CENTER);
        //chpane.add(bpane, BorderLayout.EAST);
        cpane.add(chpane);
        
        // options
        JPanel optpane = new JPanel(new FlowLayout());
        bfichier = new JRadioButton(rb.getString("rechercher.FichierEntier"));
        bfichier.setActionCommand("fichier");
        bfichier.setSelected(!dansSelection);
        bsel = new JRadioButton(rb.getString("rechercher.Selection"));
        bsel.setActionCommand("selection");
        bsel.setSelected(dansSelection);
        ButtonGroup groupe = new ButtonGroup();
        groupe.add(bfichier);
        groupe.add(bsel);
        bfichier.addActionListener(this);
        bsel.addActionListener(this);
        optpane.add(bfichier);
        optpane.add(bsel);
        chkmaj = new JCheckBox(rb.getString("rechercher.IgnorerCasse"));
        chkmaj.addItemListener(this);
        optpane.add(chkmaj);
        /*optpane.add(Box.createHorizontalStrut(50));
        typeXpath = new JCheckBox(rb.getString("rechercher.ExprXpath"));
        typeXpath.addItemListener(this);
        optpane.add(typeXpath);*/
        cpane.add(optpane);
        
        // actions
        JPanel actpane = new JPanel(new FlowLayout());
        btout = new JButton(rb.getString("rechercher.ToutRemplacer"));
        btout.setActionCommand("tout");
        btout.addActionListener(this);
        actpane.add(btout);
        bremplacer = new JButton(rb.getString("rechercher.Remplacer"));
        bremplacer.setActionCommand("remplacer");
        bremplacer.addActionListener(this);
        actpane.add(bremplacer);
        bremplrech = new JButton(rb.getString("rechercher.RemplRech"));
        bremplrech.setActionCommand("remplrech");
        bremplrech.addActionListener(this);
        actpane.add(bremplrech);
        JButton bprec = new JButton(rb.getString("rechercher.Precedent"));
        bprec.setActionCommand("precedent");
        bprec.addActionListener(this);
        actpane.add(bprec);
        JButton bsuiv = new JButton(rb.getString("rechercher.Suivant"));
        bsuiv.setActionCommand("suivant");
        bsuiv.addActionListener(this);
        actpane.add(bsuiv);
        cpane.add(actpane);
        getRootPane().setDefaultButton(bsuiv);
        addWindowListener(new WindowAdapter() {
            boolean gotFocus = false;
            public void windowActivated(WindowEvent we) {
                // Once window gets focus, set initial focus
                if (!gotFocus) {
                    tfRechercher.requestFocus();
                    gotFocus = true;
                }
            }
        });
        pack();
        Dimension screen = Toolkit.getDefaultToolkit().getScreenSize();
        setLocation((screen.width - getSize().width)/3,(screen.height - getSize().height)/3);
        setVisible(true);
        setResizable(false);
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("suivant".equals(cmd)){
            if (!RechXpath)
            suivant();
            else 
                suivantXpath();
        }
        else if ("precedent".equals(cmd)){
            if (!RechXpath)
                precedent();
            else 
                precedentXpath();
        }
        else if ("remplacer".equals(cmd))
            remplacer();
        else if ("tout".equals(cmd))
            toutRemplacer();
        else if ("remplrech".equals(cmd))
            remplRech();
        else if ("fichier".equals(cmd))
            dansSelection = false;
        else if ("selection".equals(cmd))
            dansSelection = true;
        else if ("xpath".equals(cmd)){
            this.setVisible(false);
            if (Jdoc.cfg!=null) {
                DialogueXpath Xpa = new DialogueXpath(this,Jdoc);
            } 
            else {
                JOptionPane.showMessageDialog(this,rb.getString("rechercher.ErrConfig"),rb.getString("rechercher.ExprXpath"), JOptionPane.INFORMATION_MESSAGE);
                this.setVisible(true);
            }
        }            
    }
    
    public void itemStateChanged(ItemEvent e) {
        if (e.getSource() == chkmaj) {
            ignorerCasse = (e.getStateChange() == ItemEvent.SELECTED);
        }
        /*if (e.getSource() == typeXpath) {
            RechXpath = (e.getStateChange() == ItemEvent.SELECTED);
            if (RechXpath) {
                enableButton(false);
                bpane.add(bXpath);
            } 
            else {
                enableButton(true);
                setTitle(rb.getString("rechercher.Rechercher"));
                bpane.remove(bXpath);
            }
            cpane.updateUI();
            pack(); 
        }*/
    }
    
    public void enableButton(boolean b) {
        btout.setEnabled(b);
        bremplacer.setEnabled(b);
        bremplrech.setEnabled(b);
        tfRemplacer.setEditable(b);
        bfichier.setEnabled(b);
        bsel.setEnabled(b);
        chkmaj.setEnabled(b);
        if (b) {
            tfRechercher.setText(texteRecherche);
        }
        else {
            tfRechercher.setText(texteXpathRecherche);
        }
    }
    
    public void rechercher() {
        texteRecherche = tfRechercher.getText();
        if (texteRecherche == null || texteRecherche.length() == 0)
            return;
        int len = texteRecherche.length();
        int ind = -1;
        String text;
        // recherche bourrin
        try {
            for (int i=0; i<doc.getLength()-len; i++) {
                text = doc.getText(i, len);
                if (text.equals(texteRecherche)) {
                    ind = i;
                    break;
                }
            }
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return;
        }
        if (ind != -1) {
            textPane.setCaretPosition(ind);
            textPane.moveCaretPosition(ind+len);
        } else
            getToolkit().beep();
    }
    
    public void suivantXpath() {
        suivantXpath(textPane.getCaretPosition());
    }
    
    public void suivantXpath(int rech_pos){
        String result = "";
    	int goTo = rech_pos;
        texteXpathRecherche = tfRechercher.getText();
        if (texteXpathRecherche == null || texteXpathRecherche.length() == 0)
            return;
        NodeList nodeOkXpath = getXpathNodeList(tfRechercher.getText());
        if (nodeOkXpath != null) {
        	for (int i=0; i<nodeOkXpath.getLength(); i++){
                int debNode = (Jdoc.getElementForNode(nodeOkXpath.item(i))).debut.getOffset();
                if (debNode>rech_pos){
                    goTo = debNode;
                    result = replace(rb.getString("rechercher.nbXpath"),"NUMRESULT",Integer.toString(i+1),true);
                    result = replace(result,"NBTOTAL",Integer.toString(nodeOkXpath.getLength()),true);
                    setTitle(rb.getString("rechercher.Rechercher") + " " + result );                    
                    break;
                }            
            }
            if ((nodeOkXpath.getLength()>0) && (goTo==rech_pos)) {
                result = replace(rb.getString("rechercher.nbXpath"),"NUMRESULT","1",true);
                result = replace(result,"NBTOTAL",Integer.toString(nodeOkXpath.getLength()),true);
            	setTitle(rb.getString("rechercher.Rechercher") + " " + result ); 
                goTo = (Jdoc.getElementForNode(nodeOkXpath.item(0))).debut.getOffset();
            }
            if (nodeOkXpath.getLength()!= 0) {
                try {
                    textPane.scrollRectToVisible(textPane.modelToView(doc.getLength()));
                    textPane.scrollRectToVisible(textPane.modelToView(goTo));
                } catch (BadLocationException ex) {
                    ex.printStackTrace();	
                    return;
                }
                textPane.setCaretPosition(goTo);
            } 
            else {
                getToolkit().beep();
                setTitle(rb.getString("rechercher.Rechercher"));                
            }
        }
        toFront();
    }
    
    public void precedentXpath() {
        precedentXpath(textPane.getCaretPosition());
    }
    
    public void precedentXpath(int rech_pos){
        String result = "";
    	int goTo = rech_pos;
        texteXpathRecherche = tfRechercher.getText();
        if (texteXpathRecherche == null || texteXpathRecherche.length() == 0)
            return;
        NodeList nodeOkXpath = getXpathNodeList(tfRechercher.getText());
        if (nodeOkXpath!=null) {
        	for (int i=(nodeOkXpath.getLength()-1); i>=0; i--){
                int debNode = (Jdoc.getElementForNode(nodeOkXpath.item(i))).debut.getOffset();
                if (debNode<rech_pos){
                    goTo = debNode;
                    result = replace(rb.getString("rechercher.nbXpath"),"NUMRESULT",Integer.toString(i+1),true);
                    result = replace(result,"NBTOTAL",Integer.toString(nodeOkXpath.getLength()),true);
                    setTitle(rb.getString("rechercher.Rechercher") + " " + result );  
                    break;
                }            
            }
            if ((nodeOkXpath.getLength()>0) && (goTo==rech_pos)){
                result = replace(rb.getString("rechercher.nbXpath"),"NUMRESULT",Integer.toString(nodeOkXpath.getLength()),true);
                result = replace(result,"NBTOTAL",Integer.toString(nodeOkXpath.getLength()),true);
            	setTitle(rb.getString("rechercher.Rechercher") + " " + result ); 
                goTo = (Jdoc.getElementForNode(nodeOkXpath.item(nodeOkXpath.getLength()-1))).debut.getOffset();            
            }
            if (nodeOkXpath.getLength()!= 0) {            
                try {
                    textPane.scrollRectToVisible(textPane.modelToView(doc.getLength()));
                    textPane.scrollRectToVisible(textPane.modelToView(goTo));
                } catch (BadLocationException ex) {
                    ex.printStackTrace();
                    return;
                }
                textPane.setCaretPosition(goTo);
            }        
            else {
                getToolkit().beep();
                setTitle(rb.getString("rechercher.Rechercher"));
            }
        }           
    }
    
    public NodeList getXpathNodeList(String nodePath) {
        org.w3c.dom.Node DomRoot = (org.w3c.dom.Node)Jdoc.DOMdoc.getDocumentElement();
        try {
            return (org.apache.xpath.XPathAPI.selectNodeList(DomRoot, nodePath));
        } 
        catch (TransformerException e) {
            JOptionPane.showMessageDialog(this,e.getLocalizedMessage(),rb.getString("rechercher.ErrXpath"), JOptionPane.INFORMATION_MESSAGE);
            return null;
        }
    }
    
    public void suivant() {
        suivant (textPane.getSelectionStart());
    }
    
    // recherche suivant a partir de rech_pos
    public void suivant(int rech_pos) {
        texteRecherche = tfRechercher.getText();
        if (texteRecherche == null || texteRecherche.length() == 0)
            return;
        int len = texteRecherche.length();
        if (len >= doc.getLength()) {
            getToolkit().beep();
            return;
        }
        if ((rech_pos + len) > doc.getLength()) 
            rech_pos = 0;
        int ind = -1;
        String text;
        try {
            if ((textPane.getSelectionEnd()-textPane.getSelectionStart()) == len)
                if ((!ignorerCasse && doc.getText(rech_pos,len).equals(texteRecherche)) ||
                        (ignorerCasse && doc.getText(rech_pos,len).equalsIgnoreCase(texteRecherche))) 
                rech_pos++;
            for (int i=rech_pos; i<doc.getLength()-len; i++) {
                if (includeComponent(i,len)==(-1)) {
                    text = doc.getText(i, len);
                    if ((!ignorerCasse && text.equals(texteRecherche)) ||
                            (ignorerCasse && text.equalsIgnoreCase(texteRecherche))) {
                        ind = i;
                        break;
                    }
                }
                else i=includeComponent(i,len);      
            }
        } 
        catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return;
        }
        if (ind != -1) {
            textPane.setCaretPosition(ind);
            textPane.moveCaretPosition(ind+len);
        }
        else if (rech_pos != 0) {
                suivant(0);
             }
             else {
                getToolkit().beep();
             }
        toFront();
    }

    public void precedent() {
        precedent (textPane.getSelectionStart());
    }
    
    // recherche precedent a partir de rech_pos
    public void precedent(int rech_pos) {
        texteRecherche = tfRechercher.getText();
        if (texteRecherche == null || texteRecherche.length() == 0)
            return;
        int len = texteRecherche.length();
        if (len >= doc.getLength()) {
            getToolkit().beep();
            return;
        }
        int ind = -1;
        if (rech_pos + len > doc.getLength())
            rech_pos = doc.getLength() - len;
        if (rech_pos < 0)
            rech_pos = 0;
        String text;
        try {
            if ((textPane.getSelectionEnd()-textPane.getSelectionStart()) == len)
                if ((!ignorerCasse && doc.getText(rech_pos,len).equals(texteRecherche)) ||
                        (ignorerCasse && doc.getText(rech_pos,len).equalsIgnoreCase(texteRecherche))) 
                rech_pos--;
            for (int i=rech_pos; i>=0; i--) {
                if (includeComponent(i,len)==(-1)){ 
                    text = doc.getText(i, len);
                    if ((!ignorerCasse && text.equals(texteRecherche)) ||
                            (ignorerCasse && text.equalsIgnoreCase(texteRecherche))) {
                        ind = i;
                        break;
                    }
                }
                else i=includeComponent(i,len)-len+1;    
            }
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return;
        }
        if (ind != -1) {
            textPane.setCaretPosition(ind);
            textPane.moveCaretPosition(ind+len);
        }
        else if (rech_pos != (doc.getLength()-len)) {
                precedent(doc.getLength()-len); 
             }
             else {
                getToolkit().beep(); 
             }
        toFront();     
    }

    public void remplacer() {
        String texteRecherche = tfRechercher.getText();
        String texteRemplacer = tfRemplacer.getText();
        if (textPane.getSelectionStart()==textPane.getSelectionEnd()) return;
        int start = textPane.getSelectionStart();
        int end = textPane.getSelectionEnd();
        try {
            if (!texteRecherche.equals("")){ 
                textPane.setSelectionStart(0);
                textPane.setSelectionEnd(0);
                int lenRech = texteRecherche.length();
                int lenRemp = texteRemplacer.length();
                int fin = end-lenRech+1;
                String text;
                for (int i=start; i<fin; i++) {
                    if ((!ignorerCasse && texteRecherche.equals(doc.getText(i,lenRech))) ||
                           (ignorerCasse &&texteRecherche.equalsIgnoreCase(doc.getText(i,lenRech)))) 
                        {
                        if (includeComponent(i,lenRech)==(-1)){
                            doc.remove(i, lenRech);
                            doc.insertString(i, texteRemplacer, null);
                            fin = fin - lenRech + lenRemp;
                            i+=lenRemp-1;
                        }
                        else i=includeComponent(i,lenRech);
                    }    
                }
                textPane.setSelectionStart(start);
                textPane.setSelectionEnd(fin+lenRech-1);
            }
            else {
                if (start != end)
                    doc.remove(start, end - start);
                doc.insertString(start, texteRemplacer, null);
            }
        }    
        catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            return;
        }
    }

    public void toutRemplacer() {
        if (dansSelection){
             remplacer();
             return;
        }
        texteRecherche = tfRechercher.getText();
        if (texteRecherche == null || texteRecherche.length() == 0)
            return;
        String texteRemplacer = tfRemplacer.getText();
        textPane.setSelectionStart(0);
        textPane.setSelectionEnd(0);
        textPane.debutEditionSpeciale(rb.getString("rechercher.Remplacer"), false);
        int len = texteRecherche.length();
        int ind = -1;
        int i0 = 0;
        int ifin = doc.getLength()-len;
        String text;
        try {
            for (int i=i0; i<ifin; i++) {
                if (includeComponent(i,len)==(-1)){
                    text = doc.getText(i, len);
                    if ((!ignorerCasse && text.equals(texteRecherche)) ||
                            (ignorerCasse && text.equalsIgnoreCase(texteRecherche))) {
                        ind = i;
                        doc.remove(i, len);
                        doc.insertString(i, texteRemplacer, null);
                        ifin = doc.getLength()-len;
                    }
                }
                else i=includeComponent(i,len);    
            }
        } catch (BadLocationException ex) {
            System.err.println("BadLocationException: " + ex.getMessage());
            textPane.finEditionSpeciale();
            return;
        }
        if (ind == -1) {
            getToolkit().beep();
        }
        textPane.finEditionSpeciale();
    }

    public void remplRech() {
        remplacer();
        suivant();
    }
    
    public String getTexteRecherche() {
        return(tfRechercher.getText());
    }
    
    private int includeComponent(int position, int lenght) {
        int posResult = -1;
        for (int i=position; i<lenght+position; i++) {
            Element element = null;
            Component component = null;
            Icon icon = null;
            element = doc.getCharacterElement(i);
            component = StyleConstants.getComponent(element.getAttributes());
            icon = StyleConstants.getIcon(element.getAttributes());
            if ((component != null) || (icon != null)){
                posResult = i;
            }       
        }
        return posResult;
    }
    
    public static String replace(String orig, String strReplace, String strWith, boolean all) {
        if (orig == null || strReplace == null || strReplace.length() == 0 || strWith == null)
            throw new IllegalArgumentException("pas d'arguments.");
        StringBuffer buffOrig = new StringBuffer(orig);
        int i = 0;
        while (i + strReplace.length() <= buffOrig.length()) {
            if (buffOrig.substring(i, i + strReplace.length()).equals(strReplace)) {
                buffOrig.replace(i, i + strReplace.length(), strWith);
                if (!all)
                    break;
                else
                    i += strWith.length();
                }
            else
                i++;
        }
        return buffOrig.toString();
    }
    
}
