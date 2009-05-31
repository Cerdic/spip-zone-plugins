<?php

require_once 'HTMLPurifier/Printer.php';

class HTMLPurifier_Printer_ConfigForm extends HTMLPurifier_Printer
{
    
    /**
     * Printers for specific fields
     * @protected
     */
    var $fields = array();
    
    /**
     * Documentation URL, can have fragment tagged on end
     * @protected
     */
    var $docURL;
    
    /**
     * Name of form element to stuff config in
     * @protected
     */
    var $name;
    
    /**
     * @param $name Form element name for directives to be stuffed into
     * @param $doc_url String documentation URL, will have fragment tagged on
     */
    function HTMLPurifier_Printer_ConfigForm($name, $doc_url = null) {
        parent::HTMLPurifier_Printer();
        $this->docURL = $doc_url;
        $this->name   = $name;
        $this->fields['default']    = new HTMLPurifier_Printer_ConfigForm_default();
        $this->fields['bool']       = new HTMLPurifier_Printer_ConfigForm_bool();
    }
    
    /**
     * Returns HTML output for a configuration form
     * @param $config Configuration object of current form state
     * @param $ns Optional namespace(s) to restrict form to
     */
    function render($config, $ns = true) {
        $this->config = $config;
        if ($ns === true) {
            $all = $config->getAll();
        } else {
            if (is_string($ns)) $ns = array($ns);
            foreach ($ns as $n) {
                $all = array($n => $config->getBatch($n));
            }
        }
        $ret = '';
        $ret .= $this->start('table', array('class' => 'hp-config'));
        $ret .= $this->start('thead');
        $ret .= $this->start('tr');
            $ret .= $this->element('th', 'Directive');
            $ret .= $this->element('th', 'Value');
        $ret .= $this->end('tr');
        $ret .= $this->end('thead');
        foreach ($all as $ns => $directives) {
            $ret .= $this->renderNamespace($ns, $directives);
        }
        $ret .= $this->start('tfoot');
        $ret .= $this->start('tr');
            $ret .= $this->start('td', array('colspan' => 2, 'class' => 'controls'));
                $ret .= '<input type="submit" value="Submit" /> [<a href="?">Reset</a>]';
            $ret .= $this->end('td');
        $ret .= $this->end('tr');
        $ret .= $this->end('tfoot');
        $ret .= $this->end('table');
        return $ret;
    }
    
    /**
     * Renders a single namespace
     * @param $ns String namespace name
     * @param $directive Associative array of directives to values
     * @protected
     */
    function renderNamespace($ns, $directives) {
        $ret = '';
        $ret .= $this->start('tbody', array('class' => 'namespace'));
        $ret .= $this->start('tr');
            $ret .= $this->element('th', $ns, array('colspan' => 2));
        $ret .= $this->end('tr');
        $ret .= $this->end('tbody');
        $ret .= $this->start('tbody');
        foreach ($directives as $directive => $value) {
            $ret .= $this->start('tr');
            $ret .= $this->start('th');
            if ($this->docURL) {
                $url = str_replace('%s', urlencode("$ns.$directive"), $this->docURL);
                $ret .= $this->start('a', array('href' => $url));
            }
                $ret .= $this->element(
                    'label',
                    "%$ns.$directive",
                    // component printers must create an element with this id
                    array('for' => "{$this->name}:$ns.$directive")
                );
            if ($this->docURL) $ret .= $this->end('a');
            $ret .= $this->end('th');
            
            $ret .= $this->start('td');
                $def = $this->config->def->info[$ns][$directive];
                $type = $def->type;
                if (!isset($this->fields[$type])) $type = 'default';
                $type_obj = $this->fields[$type];
                if ($def->allow_null) {
                    $type_obj = new HTMLPurifier_Printer_ConfigForm_NullDecorator($type_obj);
                }
                $ret .= $type_obj->render($ns, $directive, $value, $this->name, $this->config);
            $ret .= $this->end('td');
            $ret .= $this->end('tr');
        }
        $ret .= $this->end('tbody');
        return $ret;
    }
    
}

/**
 * Printer decorator for directives that accept null
 */
class HTMLPurifier_Printer_ConfigForm_NullDecorator extends HTMLPurifier_Printer {
    /**
     * Printer being decorated
     */
    var $obj;
    /**
     * @param $obj Printer to decorate
     */
    function HTMLPurifier_Printer_ConfigForm_NullDecorator($obj) {
        parent::HTMLPurifier_Printer();
        $this->obj = $obj;
    }
    function render($ns, $directive, $value, $name, $config) {
        $ret = '';
        $ret .= $this->start('label', array('for' => "$name:Null_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' Null/Disabled');
        $ret .= $this->end('label');
        $attr = array(
            'type' => 'checkbox',
            'value' => '1',
            'class' => 'null-toggle',
            'name' => "$name:Null_$ns.$directive",
            'id' => "$name:Null_$ns.$directive",
            'onclick' => "toggleWriteability('$name:$ns.$directive',checked)" // INLINE JAVASCRIPT!!!!
        );
        if ($value === null) $attr['checked'] = 'checked';
        $ret .= $this->elementEmpty('input', $attr);
        $ret .= $this->text(' or ');
        $ret .= $this->elementEmpty('br');
        $ret .= $this->obj->render($ns, $directive, $value, $name, $config);
        return $ret;
    }
}

/**
 * Swiss-army knife configuration form field printer
 */
class HTMLPurifier_Printer_ConfigForm_default extends HTMLPurifier_Printer {
    function render($ns, $directive, $value, $name, $config) {
        // this should probably be split up a little
        $ret = '';
        $def = $config->def->info[$ns][$directive];
        if (is_array($value)) {
            switch ($def->type) {
                case 'lookup':
                    $array = $value;
                    $value = array();
                    foreach ($array as $val => $b) {
                        $value[] = $val;
                    }
                case 'list':
                    $value = implode(',', $value);
                    break;
                case 'hash':
                    $nvalue = '';
                    foreach ($value as $i => $v) {
                        $nvalue .= "$i:$v,";
                    }
                    $value = $nvalue;
                    break;
                default:
                    $value = '';
            }
        }
        if ($def->type === 'mixed') {
            return 'Not supported';
            $value = serialize($value);
        }
        $attr = array(
            'type' => 'text',
            'name' => "$name"."[$ns.$directive]",
            'id' => "$name:$ns.$directive"
        );
        if ($value === null) $attr['disabled'] = 'disabled';
        if (is_array($def->allowed)) {
            $ret .= $this->start('select', $attr);
            foreach ($def->allowed as $val => $b) {
                $attr = array();
                if ($value == $val) $attr['selected'] = 'selected';
                $ret .= $this->element('option', $val, $attr);
            }
            $ret .= $this->end('select');
        } else {
            $attr['value'] = $value;
            $ret .= $this->elementEmpty('input', $attr);
        }
        return $ret;
    }
}

/**
 * Bool form field printer
 */
class HTMLPurifier_Printer_ConfigForm_bool extends HTMLPurifier_Printer {
    function render($ns, $directive, $value, $name, $config) {
        $ret = '';
        
        $ret .= $this->start('div', array('id' => "$name:$ns.$directive"));
        
        $ret .= $this->start('label', array('for' => "$name:Yes_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' Yes');
        $ret .= $this->end('label');
        
        $attr = array(
            'type' => 'radio',
            'name' => "$name"."[$ns.$directive]",
            'id' => "$name:Yes_$ns.$directive",
            'value' => '1'
        );
        if ($value) $attr['checked'] = 'checked';
        $ret .= $this->elementEmpty('input', $attr);
        
        $ret .= $this->start('label', array('for' => "$name:No_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' No');
        $ret .= $this->end('label');
        
        $attr = array(
            'type' => 'radio',
            'name' => "$name"."[$ns.$directive]",
            'id' => "$name:No_$ns.$directive",
            'value' => '0'
        );
        if (!$value) $attr['checked'] = 'checked';
        $ret .= $this->elementEmpty('input', $attr);
                
        $ret .= $this->end('div');
        
        return $ret;
    }
}

?>