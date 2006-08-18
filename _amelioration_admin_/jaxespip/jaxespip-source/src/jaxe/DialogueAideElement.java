/*
Jaxe - Editeur XML en Java

Copyright (C) 2003 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Rectangle;
import java.awt.Toolkit;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.util.ArrayList;
import java.util.ResourceBundle;

import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JList;
import javax.swing.JPanel;
import javax.swing.JScrollPane;

import org.w3c.dom.Element;

public class DialogueAideElement extends JDialog implements ActionListener {

    private static ResourceBundle rb = JaxeResourceBundle.getRB();
    
    private Element defElement;
    private Config cfg;
    private JLabel labeldoc;
    private JList listeParents;
    private JList listeEnfants;
    private JLabel labelexpr;

    public DialogueAideElement(Element defElement, Config cfg, JFrame frame) {
        super(frame, rb.getString("aide.element") + " " + cfg.nomBalise(defElement), true);
        this.cfg = cfg;
        initialisation(frame);
        remplissage(defElement);
    }
    
    private void initialisation(JFrame frame) {
        JPanel cpane = new JPanel();
        cpane.setLayout(new BoxLayout(cpane, BoxLayout.Y_AXIS));
        setContentPane(cpane);
        
        // description
        JLabel labeldesc = new JLabel(rb.getString("aide.description"));
        labeldesc.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(labeldesc);
        labeldoc = new JLabel("");
        JPanel paneldoc = new JPanel();
        paneldoc.setAlignmentX(Component.LEFT_ALIGNMENT);
        paneldoc.add(labeldoc);
        cpane.add(paneldoc);
        cpane.add(Box.createRigidArea(new Dimension(1, 20)));
        
        // parents
        JLabel labelparents = new JLabel(rb.getString("aide.parents"));
        labelparents.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(labelparents);
        listeParents = new JList();
        //listeParents.setLayoutOrientation(JList.HORIZONTAL_WRAP);  JDK 1.4
        MouseListener listenParents = new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                if (e.getClickCount() == 2) {
                    int index = listeParents.locationToIndex(e.getPoint());
                    if (index != -1) {
                        Element def = cfg.getBaliseDef((String)
                            listeParents.getModel().getElementAt(index));
                        if (def != null)
                            remplissage(def);
                    }
                }
            }
        };
        listeParents.addMouseListener(listenParents);
        JScrollPane panelparents = new JScrollPane(listeParents);
        panelparents.setPreferredSize(new Dimension(250, 150));
        panelparents.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(panelparents);
        cpane.add(Box.createRigidArea(new Dimension(1, 20)));
        
        // enfants
        JLabel labelenfants = new JLabel(rb.getString("aide.enfants"));
        labelenfants.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(labelenfants);
        labelexpr = new JLabel("");
        labelexpr.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(labelexpr);
        listeEnfants = new JList();
        //listeEnfants.setLayoutOrientation(JList.HORIZONTAL_WRAP);  JDK 1.4
        MouseListener listenEnfants = new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                if (e.getClickCount() == 2) {
                    int index = listeEnfants.locationToIndex(e.getPoint());
                    if (index != -1) {
                        Element def = cfg.getBaliseDef((String)
                            listeEnfants.getModel().getElementAt(index));
                        if (def != null)
                            remplissage(def);
                    }
                }
            }
        };
        listeEnfants.addMouseListener(listenEnfants);
        JScrollPane panelenfants = new JScrollPane(listeEnfants);
        panelenfants.setPreferredSize(new Dimension(250, 150));
        panelenfants.setAlignmentX(Component.LEFT_ALIGNMENT);
        cpane.add(panelenfants);
        cpane.add(Box.createRigidArea(new Dimension(1, 20)));
        
        // actions
        JPanel actpane = new JPanel(new FlowLayout());
        actpane.setAlignmentX(Component.LEFT_ALIGNMENT);
        JButton bfermer = new JButton(rb.getString("aide.fermer"));
        bfermer.setActionCommand("fermer");
        bfermer.addActionListener(this);
        actpane.add(bfermer);
        cpane.add(actpane);
        getRootPane().setDefaultButton(bfermer);
        
        if (frame != null) {
            Rectangle r = frame.getBounds();
            setLocation(r.x + r.width/4, r.y + r.height/4);
        } else {
            Dimension screen = Toolkit.getDefaultToolkit().getScreenSize();
            setLocation((screen.width - getSize().width)/3,(screen.height - getSize().height)/3);
        }
    }
    
    public void remplissage(Element defElement) {
        this.defElement = defElement;
        
        setTitle(rb.getString("aide.element") + " " + cfg.nomBalise(defElement));
        
        // description
        String documentation = cfg.documentation(defElement);
        labeldoc.setText(documentation);
        
        // parents
        listeParents.clearSelection();
        ArrayList alparents = cfg.listeParents(defElement);
        listeParents.setListData(alparents.toArray());
        
        // enfants
        labelexpr.setText(versHTML(cfg.expressionReguliere(defElement)));
        listeEnfants.clearSelection();
        ArrayList alenfants = cfg.listeSousbalises(defElement);
        listeEnfants.setListData(alenfants.toArray());
        
        pack();
    }
    
    /**
     * tranformation en HTML (découpage en lignes pour expression régulière)
     */
    protected String versHTML(String s) {
        if (s != null) {
            s = s.trim();
            if (s.length() > 90) {
                int p = 0;
                for (int i=0; i<s.length(); i++) {
                    if (i-p > 80 && (s.charAt(i) == '|' || s.charAt(i) == ',')) {
                        s = s.substring(0,i) + "<br>" + s.substring(i);
                        p = i;
                    }
                }
                s = "<html><body>" + s + "</body></html>";
            }
        }
        return(s);
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("fermer".equals(cmd))
            fermer();
    }
    
    public void fermer() {
        setVisible(false);
        dispose();
    }
    
}
