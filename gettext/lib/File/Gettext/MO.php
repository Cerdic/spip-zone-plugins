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
 * @version   CVS: $Id: MO.php 269167 2008-11-17 13:25:09Z clockwerx $
 * @link      http://pear.php.net/package/File_Gettext
 */

/**
 * Requires File_Gettext
 */
require_once 'File/Gettext.php';

/**
 * File_Gettext_MO
 *
 * GNU MO file reader and writer.
 *
 * @category  FileFormats
 * @package   File_Gettext
 * @author    Michael Wallner <mike@php.net>
 * @copyright 2004-2005 Michael Wallner
 * @license   BSD, revised
 * @link      http://pear.php.net/package/File_Gettext
 */
class File_Gettext_MO extends File_Gettext
{
    /**
     * file handle
     *
     * @access  private
     * @var     resource
     */
    var $_handle = null;

    /**
     * big endianess
     *
     * Whether to write with big endian byte order.
     *
     * @access  public
     * @var     bool
     */
    var $writeBigEndian = false;

    /**
     * Constructor
     *
     * @param string $file path to GNU MO file
     *
     * @access  public
     * @return  object      File_Gettext_MO
     */
    function File_Gettext_MO($file = '')
    {
        $this->file = $file;
    }

    /**
     * _read
     *
     * @param int $bytes Bytes to read
     *
     * @access  private
     * @return  mixed
     */
    function _read($bytes = 1)
    {
        if (0 < $bytes = abs($bytes)) {
            return fread($this->_handle, $bytes);
        }
        return null;
    }

    /**
     * _readInt
     *
     * @param bool $bigendian Is the data an unsigned long?
     *
     * @see     http://au.php.net/manual/en/function.pack.php
     * @access  private
     * @return  int
     */
    function _readInt($bigendian = false)
    {
        return current($array = unpack($bigendian ? 'N' : 'V', $this->_read(4)));
    }

    /**
     * _writeInt
     *
     * @param int $int Int to write
     *
     * @access  private
     * @return  int
     */
    function _writeInt($int)
    {
        return $this->_write(pack($this->writeBigEndian ? 'N' : 'V', (int) $int));
    }

    /**
     * _write
     *
     * @param string $data Data to write to file
     *
     * @access  private
     * @return  int
     */
    function _write($data)
    {
        return fwrite($this->_handle, $data);
    }

    /**
     * _writeStr
     *
     * @param string $string String to write
     *
     * @access  private
     * @return  int
     */
    function _writeStr($string)
    {
        return $this->_write($string . "\0");
    }

    /**
     * Reads a series of one or more null-terminated strings from a given
     * location in the source file. Note that MO files optimize plural pairs
     * by storing them together under the same index entry.
     *
     * @param array $params associative array with offset and length
     *                              of the string
     *
     * @access  private
     * @return  string
     */
    private function _readStrings($params)
    {
        fseek($this->_handle, $params['offset']);
        $strings = $this->_read($params['length']);
        return explode("\x00", $strings);
    }

    /**
     * Load MO file
     *
     * @param string $file File path to load
     *
     * @access   public
     * @return   mixed   Returns true on success or PEAR_Error on failure.
     */
    function load($file = null)
    {
        $this->strings = array();

        if (!isset($file)) {
            $file = $this->file;
        }

        // open MO file
        if (!is_resource($this->_handle = @fopen($file, 'rb'))) {
            return parent::raiseError($php_errormsg . ' ' . $file);
        }
        // lock MO file shared
        if (!@flock($this->_handle, LOCK_SH)) {
            @fclose($this->_handle);
            return parent::raiseError($php_errormsg . ' ' . $file);
        }

        // read (part of) magic number from MO file header and define endianess
        switch ($magic = current($array = unpack('c', $this->_read(4))))
        {
        case -34:
            $be = false;
            break;

        case -107:
            $be = true;
            break;

        default:
            return parent::raiseError("No GNU mo file: $file (magic: $magic)");
        }

        // check file format revision - we currently only support 0
        if (0 !== ($_rev = $this->_readInt($be))) {
            return parent::raiseError('Invalid file format revision: ' . $_rev);
        }

        // count of strings in this file
        $count = $this->_readInt($be);

        // offset of hashing table of the msgids
        $offset_original = $this->_readInt($be);
        // offset of hashing table of the msgstrs
        $offset_translat = $this->_readInt($be);

        // move to msgid hash table
        fseek($this->_handle, $offset_original);
        // read lengths and offsets of msgids
        $original = array();
        for ($i = 0; $i < $count; $i++) {
            $original[$i] = array(
                'length' => $this->_readInt($be),
                'offset' => $this->_readInt($be)
            );
        }

        // move to msgstr hash table
        fseek($this->_handle, $offset_translat);
        // read lengths and offsets of msgstrs
        $translat = array();
        for ($i = 0; $i < $count; $i++) {
            $translat[$i] = array(
                'length' => $this->_readInt($be),
                'offset' => $this->_readInt($be)
            );
        }

        // read all
        for ($i = 0; $i < $count; $i++) {
            $pairs = array_combine(
                $this->_readStrings($original[$i]),
                $this->_readStrings($translat[$i]));
            foreach ($pairs as $origStr => $translatedStr) {
                $this->strings[$origStr] = $translatedStr;
            }
        }

        // done
        @flock($this->_handle, LOCK_UN);
        @fclose($this->_handle);
        $this->_handle = null;

        // check for meta info
        if (isset($this->strings[''])) {
            $this->meta = parent::meta2array($this->strings['']);
            unset($this->strings['']);
        }

        return true;
    }

    /**
     * Save MO file
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

        // open MO file
        if (!is_resource($this->_handle = @fopen($file, 'wb'))) {
            return parent::raiseError($php_errormsg . ' ' . $file);
        }
        // lock MO file exclusively
        if (!@flock($this->_handle, LOCK_EX)) {
            @fclose($this->_handle);
            return parent::raiseError($php_errormsg . ' ' . $file);
        }

        // write magic number
        if ($this->writeBigEndian) {
            $this->_write(pack('c*', 0x95, 0x04, 0x12, 0xde));
        } else {
            $this->_write(pack('c*', 0xde, 0x12, 0x04, 0x95));
        }

        // write file format revision
        $this->_writeInt(0);

        $count = count($this->strings) + ($meta = (count($this->meta) ? 1 : 0));
        // write count of strings
        $this->_writeInt($count);

        $offset = 28;
        // write offset of orig. strings hash table
        $this->_writeInt($offset);

        $offset += ($count * 8);
        // write offset transl. strings hash table
        $this->_writeInt($offset);

        // write size of hash table (we currently ommit the hash table)
        $this->_writeInt(0);

        $offset += ($count * 8);
        // write offset of hash table
        $this->_writeInt($offset);

        // unshift meta info
        if ($meta) {
            $meta = '';
            foreach ($this->meta as $key => $val) {
                $meta .= $key . ': ' . $val . "\n";
            }
            $strings = array('' => $meta) + $this->strings;
        } else {
            $strings = $this->strings;
        }

        // write offsets for original strings
        foreach (array_keys($strings) as $o) {
            $len = strlen($o);
            $this->_writeInt($len);
            $this->_writeInt($offset);
            $offset += $len + 1;
        }

        // write offsets for translated strings
        foreach ($strings as $t) {
            $len = strlen($t);
            $this->_writeInt($len);
            $this->_writeInt($offset);
            $offset += $len + 1;
        }

        // write original strings
        foreach (array_keys($strings) as $o) {
            $this->_writeStr($o);
        }

        // write translated strings
        foreach ($strings as $t) {
            $this->_writeStr($t);
        }

        // done
        @flock($this->_handle, LOCK_UN);
        @fclose($this->_handle);
        return true;
    }
}
?>
