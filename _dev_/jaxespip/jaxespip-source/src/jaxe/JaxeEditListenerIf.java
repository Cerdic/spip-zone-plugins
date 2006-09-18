/**
 * (c) Lexis Nexis Deutschland, 2004
 */
package jaxe;

import javax.swing.text.Position;


/**
 * Listener for EditEvents
 * @author Kykal
 */
public interface JaxeEditListenerIf {
    
    /**
     * Method is called before inserting JaxeElements into the document
     * @param e
     */
    public Position prepareAddedElement(Position pos);
    
    /**
     * Method is called when inserting JaxeElements into the document
     * @param e
     */
    public Position elementAdded(JaxeEditEvent e, Position pos);
    
    /**
     * Method is called when text is inserted into the document
     * @param e
     */
    public void textAdded(JaxeEditEvent e);
    
    /**
     * Method is called when JaxeElements are removed in the document
     * @param e
     */
    public void elementRemoved(JaxeEditEvent e);
    
    /**
     * Method is called when text is removed in the document
     * @param e
     */
    public void textRemoved(JaxeEditEvent e);
}
