/**
 * (c) Lexis Nexis Deutschland, 2004
 */
package jaxe;

import java.util.EventObject;


/**
 * EditEvent for changes in the document
 * @author Kykal
 */
public class JaxeEditEvent extends EventObject {

    /**
     * Creates an event with JaxeElements
     * @param source Eventsource
     * @param offs Offset in document
     * @param e JaxeElement
     */
    public JaxeEditEvent(Object source,JaxeElement e) {
        super(source);
        _offs = 0;
        _je = e;
        _text = null;
        _consume = false;
    }

    /**
     * Creates an event with plain text
     * @param source Eventsource
     * @param offs Offset in document
     * @param text Text
     */
    public JaxeEditEvent(Object source, int offs,String text) {
        super(source);
        _offs = offs;
        _je = null;
        _text = text;
        _consume = false;
    }
    
    /**
     * Returns the JaxeElement or null if event is used with text
     * @return JaxeElement
     */
    public JaxeElement getJaxeElement() {
        return _je;
    }
    
    /**
     * Returns the offset in the document
     * @return Offset
     */
    public int getOffset() {
        return _offs;
    }
    
    /**
     * Status, if the event hast been uses;
     * @return true, if used
     */
    public boolean isConsumed() {
        return _consume;
    }
    
    /**
     * Sets the used status to true 
     */
    public void consume() {
        _consume = true;
    }
    
    /**
     * Returns the text or null is event is used with JaxeElement
     * @return Text
     */
    public String getText() {
        return _text;
    }
    
    /**
     * toString
     */
    public String toString() {
        return getClass() + " je: " + _je + "  offset: " + _offs + "  text: " + _text;
    }
    
    /**
     * Offset in document
     */
    private int _offs;
    /**
     * JaxeElement
     */
    private JaxeElement _je;
    /**
     * Text
     */
    private String _text;
    /**
     * Has event been used?
     */
    private boolean _consume;
}
