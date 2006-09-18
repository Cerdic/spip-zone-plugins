/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.Rectangle;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.util.ArrayList;
import java.util.Hashtable;

import javax.swing.Action;
import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JComponent;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;
import javax.swing.text.DefaultEditorKit;
import javax.swing.text.JTextComponent;

import org.w3c.dom.DOMException;
import org.w3c.dom.Element;

/**
 * Dialogue de modification des attributs d'un élément
 */
public class DialogueAttributs extends JDialog implements ActionListener, KeyListener {

    JComponent[] champs;
    String[] titres;
    String[] defauts;
    boolean valide = false;
    Element defbalise;
    Element el;
    JFrame jframe;
    JaxeDocument doc;
    
    public DialogueAttributs(JFrame jframe, JaxeDocument doc, String titre, Element defbalise, Element el) {
        super(jframe, titre, true);
        this.jframe = jframe;
        this.doc = doc;
        this.defbalise = defbalise;
        this.el = el;
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        int natt = latt.size();
        titres = new String[natt];
        champs = new JComponent[natt];
        defauts = new String[natt];
        for (int i=0; i<natt; i++) {
            Element att = (Element)latt.get(i);
            titres[i] = doc.cfg.nomAttribut(att);
            String elval = el.getAttribute(titres[i]);
            defauts[i] = doc.cfg.valeurParDefaut(att);
            if ("".equals(elval) && defauts[i] != null && el.getAttributeNode(titres[i]) == null)
                elval = defauts[i];
            String[] lval = doc.cfg.listeValeurs(att);
            if (lval != null && lval.length > 0) {
                JComboBox popup = new JComboBox();
                champs[i] = popup;
                if (!doc.cfg.estObligatoire(att) && defauts[i] == null)
                    popup.addItem("");
                for (int j=0; j<lval.length; j++) {
                    String sval = lval[j];
                    popup.addItem(sval);
                    if (sval.equals(elval))
                        popup.setSelectedItem(sval);
                }
            } else {
                champs[i] = new JTextField(elval, 40);
                ((JTextField)champs[i]).addKeyListener(new KeyAdapter() {
                    public void keyPressed(KeyEvent evt) {
                        if (evt.getKeyCode() == KeyEvent.VK_ESCAPE)
                            actionAnnuler();
                    }
                });
            }
        }
        JPanel cpane = new JPanel(new BorderLayout());
        setContentPane(cpane);
        JPanel chpane = new JPanel(new BorderLayout());
        JPanel qpane = new JPanel(new GridLayout(titres.length, 1));
        for (int i=0; i<titres.length; i++) {
            JLabel label = new JLabel(titres[i]);
            Element att = (Element)latt.get(i);
            if (doc.cfg.estObligatoire(att))
                label.setForeground(new Color(150, 0, 0)); // rouge foncé
            else
                label.setForeground(new Color(0, 100, 0)); // vert foncé
            qpane.add(label);
        }
        qpane.setBorder(BorderFactory.createEmptyBorder(0, 5, 0, 5));
        JPanel tfpane = new JPanel(new GridLayout(champs.length, 1));
        for (int i=0; i<champs.length; i++) {
            tfpane.add(champs[i]);
        }
        chpane.add(qpane, BorderLayout.CENTER);
        chpane.add(tfpane, BorderLayout.EAST);
        cpane.add(chpane, BorderLayout.CENTER);
        JPanel bpane = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        JButton boutonAnnuler = new JButton(JaxeResourceBundle.getRB().getString("bouton.Annuler"));
        boutonAnnuler.addActionListener(this);
        boutonAnnuler.setActionCommand("Annuler");
        bpane.add(boutonAnnuler);
        JButton boutonOK = new JButton(JaxeResourceBundle.getRB().getString("bouton.OK"));
        boutonOK.addActionListener(this);
        boutonOK.setActionCommand("OK");
        bpane.add(boutonOK);
        cpane.add(bpane, BorderLayout.SOUTH);
        getRootPane().setDefaultButton(boutonOK);
        cpane.setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));
        JTextField atf = null;
        for (int i=0; i<natt; i++)
            if (champs[i] instanceof JTextField)
                atf = (JTextField)champs[i];
        if (atf != null) {
            createActionTable(atf);
            //addMenus();
        }
        addKeyListener(this);
        pack();
        addWindowListener(new WindowAdapter() {
            boolean gotFocus = false;
            public void windowActivated(WindowEvent we) {
                // Once window gets focus, set initial focus
                if (!gotFocus) {
                    champs[0].requestFocus();
                    gotFocus = true;
                }
            }
        });
        if (jframe != null) {
            Rectangle r = jframe.getBounds();
            setLocation(r.x + r.width/4, r.y + r.height/4);
        } else
            setLocation(400, 400);
    }
    
    /*protected void addMenus() {
        JMenuBar mainMenuBar = new JMenuBar();
    	JMenu editMenu = new JMenu("Edition");
        editMenu.add(getActionByName(DefaultEditorKit.cutAction));
        JMenuItem miCopy = editMenu.add(getActionByName(DefaultEditorKit.copyAction));
        miCopy.setAccelerator(KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_C, java.awt.Event.META_MASK));
        editMenu.add(getActionByName(DefaultEditorKit.pasteAction));
        editMenu.addSeparator();
        editMenu.add(getActionByName(DefaultEditorKit.selectAllAction));
        mainMenuBar.add(editMenu);
        setJMenuBar(mainMenuBar);
    }*/
    
    Hashtable actions;
    private void createActionTable(JTextComponent textComponent) {
        actions = new Hashtable();
        Action[] actionsArray = textComponent.getActions();
        for (int i = 0; i < actionsArray.length; i++) {
            Action a = actionsArray[i];
            actions.put(a.getValue(Action.NAME), a);
        }
    }
    private Action getActionByName(String name) {
        return (Action)(actions.get(name));
    }

    public void keyPressed(KeyEvent e) {
        if (e.isMetaDown()/* || e.isControlDown()*/) {
            //System.out.println("cmd-"+e.getKeyChar());
            int modifiers = 0;
            if (e.isMetaDown())
                modifiers = ActionEvent.META_MASK;
            if ('C' == e.getKeyChar()) {
                //if (e.isControlDown())
                //    modifiers = ActionEvent.CTRL_MASK;
                ActionEvent ae = new ActionEvent(this, ActionEvent.ACTION_PERFORMED, "copy", modifiers);
                getActionByName(DefaultEditorKit.copyAction).actionPerformed(ae);
            }
            if ('X' == e.getKeyChar()) {
                ActionEvent ae = new ActionEvent(this, ActionEvent.ACTION_PERFORMED, "cut", modifiers);
                getActionByName(DefaultEditorKit.cutAction).actionPerformed(ae);
            }
            if ('V' == e.getKeyChar()) {
                ActionEvent ae = new ActionEvent(this, ActionEvent.ACTION_PERFORMED, "paste", modifiers);
                getActionByName(DefaultEditorKit.pasteAction).actionPerformed(ae);
            }
        }
    }
    
    public void keyReleased(KeyEvent e) {
    }
    
    public void keyTyped(KeyEvent e) {
    }
    
    public boolean afficher() {
        show();
        return(valide);
    }

    public String[] lireReponses() {
        String[] rep = new String[champs.length];
        for (int i=0; i<champs.length; i++) {
            if (champs[i] instanceof JTextComponent)
                rep[i] = ((JTextComponent)champs[i]).getText();
            else if (champs[i] instanceof JComboBox)
                rep[i] = (String)((JComboBox)champs[i]).getSelectedItem();
            else
                rep[i] = null;
        }
        return(rep);
    }
    
    public void enregistrerReponses() {
        String[] rep = lireReponses();
        try {
            for (int i=0; i<rep.length; i++)
                if (rep[i] != null) {
                    if ("".equals(rep[i]) && !"".equals(el.getAttribute(titres[i])) &&
                            !el.getAttribute(titres[i]).equals(defauts[i]))
                        el.removeAttribute(titres[i]);
                    else if (rep[i].equals(defauts[i]))
                        el.removeAttribute(titres[i]);
                    else if (!"".equals(rep[i]) || defauts[i] != null)
                        el.setAttribute(titres[i], rep[i]);
                }
            doc.modif = true;
        } catch (DOMException ex) {
            System.err.println("DOMException: " + ex.getMessage());
            return;
        }
    }
    
    protected boolean checkAtt() {
        String[] rep = lireReponses();
        ArrayList latt = doc.cfg.listeAttributs(defbalise);
        int natt = latt.size();
        for (int i=0; i<natt; i++) {
            Element att = (Element)latt.get(i);
            if (doc.cfg.estObligatoire(att) && (rep[i] == null ||
                "".equals(rep[i]))) {
                getToolkit().beep();
                if (champs[i] instanceof JTextComponent)
                    ((JTextComponent)champs[i]).selectAll();
                return false;
            }
        }
        return true;
    }
    
    public void actionOK() {
        if (checkAtt()) {
            valide = true;
            setVisible(false);
        }
    }
    
    public void actionAnnuler() {
        valide = false;
        setVisible(false);
    }
    
    public void actionPerformed(ActionEvent e) {
        String cmd = e.getActionCommand();
        if ("OK".equals(cmd))
            actionOK();
        else if ("Annuler".equals(cmd))
            actionAnnuler();
    }

}
