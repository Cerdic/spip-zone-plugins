<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::Gettext
 *
 * PHP versions 4 and 5
 *
 * @category  FileFormats
 * @package   File_Gettext
 * @author    Michael Wallner <mike@php.net>
 * @copyright 2004-2005 Michael Wallner
 * @license   BSD, revised
 * @version   CVS: $Id: PO.php 269167 2008-11-17 13:25:09Z clockwerx $
 * @link      http://pear.php.net/package/File_Gettext
 */

/**
 * Requires File_Gettext
 */
require_once 'File/Gettext.php';

/**
 * File_Gettext_PO
 *
 * GNU PO file reader and writer.
 *
 * @category  FileFormats
 * @package   File_Gettext
 * @author    Michael Wallner <mike@php.net>
 * @copyright 2004-2005 Michael Wallner
 * @license   BSD, revised
 * @link      http://pear.php.net/package/File_Gettext
 */
class File_Gettext_PO extends File_Gettext
{
    /**
     * Constructor
     *
     * @param string $file path to GNU PO file
     *
     * @access  public
     * @return  object      File_Gettext_PO
     */
    function File_Gettext_PO($file = '')
    {
        $this->file = $file;
    }

    /**
     * Load PO file
     *
     * @param string $file File path to load
     *
     * @access  public
     * @return  mixed   Returns true on success or PEAR_Error on failure.
     */
    function load($file = null)
    {
        $this->strings = array();

        if (!isset($file)) {
            $file = $this->file;
        }

        // load file
        if (!$contents = @file($file)) {
            return parent::raiseError($php_errormsg . ' ' . $file);
        }
        
        $state = 'init';
        $entry = array();
        for ($i = 1; $i <= count($contents); $i++) {
            $line = trim($contents[$i-1]);

            if ($line == '') {
                // empty!
                continue;
            }
            if (substr($line, 0, 1) == '#') {
                // comment
                continue;
            }

            $key = '';
            $stuff = $line;
            if (substr($stuff, 0, 1) != '"') {
                $bits = explode(' ' , $line, 2); // @fixme whitespace rules?
                if (count($bits) == 2) {
                    list($key, $stuff) = $bits;
                }
            }

            if (substr($stuff, 0, 1) != '"' || substr($stuff, -1, 1) != '"') {
                return parent::raiseError("Syntax error at line $i: invalid string: $line");
            } else {
                $str = $this->parseString(substr($stuff, 1, -1));
            }

            // for plural msgsstr matching...
            $n = isset($entry['msgstr[]']) ? (count($entry['msgstr[]']) - 1) : 0;
            $nplus = $n + 1;

            switch ($state) {
            case 'init':
                if ($key == 'msgctxt' || $key == 'msgid') {
                    if (isset($entry[$key])) {
                        return parent::raiseError("Syntax error at line $i: got second $key: $line");
                    }
                    $state = $key;
                    $entry[$key] = $str;
                } else {
                    return parent::raiseError("Syntax error at line $i: expected msgctxt or msgid: $line");
                }
                continue;
            case "msgctxt":
                if ($key == '') {
                    $entry[$state] .= $str;
                } else if ($key == 'msgid') {
                    $state = $key;
                    $entry[$key] = $str;
                } else {
                    return parent::raiseError("Syntax error at line $i: expected msgid: $line");
                }
                continue;
            case "msgid":
                if ($key == '') {
                    $entry[$state] .= $str;
                } else if ($key == 'msgid_plural') {
                    $state = $key;
                    $entry[$key] = $str;
                } else if ($key == 'msgstr') {
                    $state = $key;
                    $entry[$key] = $str;
                } else {
                    return parent::raiseError("Syntax error at line $i: expected msgstr: $line");
                }
                continue;
            case "msgid_plural":
                if ($key == '') {
                    $entry[$state] .= $str;
                } else if ($key == "msgstr[$n]") {
                    $state = $key;
                    $entry['msgstr[]'][$n] = $str;
                } else {
                    return parent::raiseError("Syntax error at line $i: expected msgstr[$n]: $line");
                }
                continue;
            case "msgstr[$n]":
                if ($key == '') {
                    $entry['msgstr[]'][$n] .= $str;
                } else if ($key == "msgstr[$nplus]") {
                    $state = $key;
                    $entry['msgstr[]'][$nplus] = $str;
                } else {
                    // Save this entry...
                    $this->storeEntry($entry);

                    // Back up and continue!
                    $state = 'init';
                    $entry = array();
                    $i--;
                }
                continue;
            case "msgstr":
                if ($key == '') {
                    $entry[$state] .= $str;
                } else {
                    // Save this entry...
                    $this->storeEntry($entry);

                    // Back up and continue!
                    $state = 'init';
                    $entry = array();
                    $i--;
                }
                continue;
            default:
                return parent::raiseError("Parse error: unknown state $state at line $i: $line");
            }
        }
        if ($entry) {
            $this->storeEntry($entry);
        }

        // check for meta info
        if (isset($this->strings[''])) {
            $this->meta = parent::meta2array($this->strings['']);
            unset($this->strings['']);
        }

        return true;
    }

    function parseString($str)
    {
        return parent::prepare($str);
    }
    
    function storeEntry($entry)
    {
        $id = $entry['msgid'];
        if (isset($entry['msgctxt'])) {
            $id = $entry['msgctxt'] . "\004" . $id;
        }
        if (isset($entry['msgstr[]'][0])) {
            $str = $entry['msgstr[]'][0]; // @fixme handle plurals
        } else {
            $str = $entry['msgstr'];
        }
        $this->strings[$id] = $str;
    }

    /**
     * Save PO file
     *
     * @param string $file File path to write to
     *
     * @access  public
     * @return  mixed   Returns true on success or PEAR_Error on failure.
     */
    function save($file = null)
    {
        if (!isset($file)) {
            $file = $this->file;
        }

        // open PO file
        if (!is_resource($fh = @fopen($file, 'w'))) {
            return parent::raiseError($php_errormsg . ' ' . $file);
        }
        // lock PO file exclusively
        if (!@flock($fh, LOCK_EX)) {
            @fclose($fh);
            return parent::raiseError($php_errmsg . ' ' . $file);
        }

        // write meta info
        if (count($this->meta)) {
            $meta = 'msgid ""' . "\nmsgstr " . '""' . "\n";
            foreach ($this->meta as $k => $v) {
                $meta .= '"' . $k . ': ' . $v . '\n"' . "\n";
            }
            fwrite($fh, $meta . "\n");
        }
        // write strings
        foreach ($this->strings as $o => $t) {
            if (strpos($o, "\004") !== false) {
                list($ctxt, $id) = explode("\004", $o, 2);
                $chunk = 'msgctxt "' . parent::prepare($ctxt, true) . '"' . "\n";
                $chunk .= 'msgid "'  . parent::prepare($id, true) . '"' . "\n";
            } else {
                $chunk = 'msgid "'  . parent::prepare($o, true) . '"' . "\n";
            }
            $chunk .= 'msgstr "' . parent::prepare($t, true) . '"' . "\n\n";
            fwrite($fh, $chunk);
        }

        //done
        @flock($fh, LOCK_UN);
        @fclose($fh);
        return true;
    }
}
?>
