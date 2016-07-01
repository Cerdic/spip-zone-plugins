<?php
/**
 * This file is part of AutoEmbed.
 * http://autoembed.com
 *
 * $Id: AutoEmbed.class.php 214 2010-02-25 15:56:12Z phpuser $
 *
 * AutoEmbed is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AutoEmbed is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with AutoEmbed.  If not, see <http://www.gnu.org/licenses/>.
 */

class AutoEmbed {

  const AE_TAG = '<!-- Generated by AutoEmbed (http://autoembed.com) -->';

  private $_media_id;
  private $_stub;
  private $_object_attribs;
  private $_object_params;

  /**
   * AutoEmbed Constructor
   *
   * @return object - AutoEmbed object
   */
  public function __construct() {
    global $AutoEmbed_stubs;
    
    include_once 'stubs.php';
  }

  /**
   * Parse given URL
   *
   * @param $url string - href to check for embeded video
   *
   * @return boolean - whether or not the url contains valid/supported video
   */
  public function parseUrl($url) {
    global $AutoEmbed_stubs;

	$url = preg_replace(',^https:,', 'http:', $url);

    foreach ($AutoEmbed_stubs as $stub) { 
      if ( preg_match('~'.$stub['url-match'].'~imu', $url, $match) ) {
        $this->_stub = $stub;
        
        if ( isset($stub['fetch-match'] ) ) {
          return $this->_parseLink($url);

        } else {
          $this->_media_id = $match;
          $this->_setDefaultParams();
          return true;
        }
      }
    }

    unset($stub);
    return false;
  }

  /**
   * Create the embed code for a local file
   *
   * @param $file string - the file we are wanting to embed
   *
   * @return boolean - whether or not the url contains valid/supported video
   */
  public function embedLocal($file) {
    return $this->parseUrl("__local__$file");
  }

  /**
   * Returns info about the stub
   *
   * @param string $property - (optional) the specific
   *           property of the stub to be returned.  If 
   *           ommited, array of all properties are returned
   *
   * @return mixed - details about the stub 
   */
  public function getStub($property = null) {
    return isset($property) ? $this->_stub[$property] : $this->_stub;
  }

  /**
   * Return object params about the video metadata
   *
   * @return array - object params
   */
  public function getObjectParams() {
    return $this->_object_params;
  }

  /**
   * Return object attribute
   *
   * @return array - object attribute
   */
  public function getObjectAttribs() {
    return $this->_object_attribs;
  }

  /**
   * Convert the url to an embedable tag
   *
   * return string - the embed html
   */
  public function getEmbedCode() {
    if ( isset($this->_stub['iframe-player']) )
    {
      return $this->_buildiFrame();
    }
    return $this->_buildObject();
  }

  /**
   * Return a thumbnail for the embeded video
   *
   * return string - the thumbnail href
   */
  public function getImageURL() {
    if (!isset($this->_stub['image-src'])) return false;

    $thumb = $this->_stub['image-src'];

    for ($i=1; $i<=count($this->_media_id); $i++) {
      $thumb = str_ireplace('$'.$i, $this->_media_id[$i - 1], $thumb);
    }

    return $thumb;
  }

  /**
   * Set the height of the object
   * 
   * @param mixed - height to set the object to
   *
   * @return boolean - true if the value was set, false
   *                   if parseURL hasn't been called yet
   */
  public function setHeight($height) {
    return $this->setObjectAttrib('height', $height);
  }

  /**
   * Set the width of the object
   * 
   * @param mixed - width to set the object to
   *
   * @return boolean - true if the value was set, false
   *                   if parseURL hasn't been called yet
   */
  public function setWidth($width) {
    return $this->setObjectAttrib('width', $width);
  }

  /**
   * Override a default param value for both the object
   * and flash param list
   *
   * @param $param mixed - the name of the param to be set
   *                       or an array of multiple params to set
   * @param $value string - (optional) the value to set the param to
   *                        if only one param is being set
   *
   * @return boolean - true if the value was set, false
   *                   if parseURL hasn't been called yet
   */
  public function setParam($param, $value = null) {
    return $this->setObjectParam($param, $value);
  }

  /**
   * Override a default object param value
   *
   * @param $param mixed - the name of the param to be set
   *                       or an array of multiple params to set
   * @param $value string - (optional) the value to set the param to
   *                        if only one param is being set
   *
   * @return boolean - true if the value was set, false
   *                   if parseURL hasn't been called yet
   */
  public function setObjectParam($param, $value = null) {
    if (!is_array($this->_object_params)) return false;

    if ( is_array($param) ) {
      foreach ($param as $p => $v) {
        $this->_object_params[$p] = $v;
      }

    } else {
      $this->_object_params[$param] = $value;
    }

    return true;
  }

  /**
   * Override a default object attribute value
   *
   * @param $param mixed - the name of the attribute to be set
   *                       or an array of multiple attribs to be set
   * @param $value string - (optional) the value to set the param to
   *                        if only one param is being set
   *
   * @return boolean - true if the value was set, false
   *                   if parseURL hasn't been called yet
   */
  public function setObjectAttrib($param, $value = null) {
    if (!is_array($this->_object_attribs)) return false;

    if ( is_array($param) ) {
      foreach ($param as $p => $v) {
        $this->_object_attribs[$p] = $v;
      }

    } else {
      $this->_object_attribs[$param] = $value;
    }

    return true;
  }

  /**
   * Attempt to parse the embed id from a given URL
   */ 
  private function _parseLink($url) {
    $source = preg_replace('/[^(\x20-\x7F)]*/','', file_get_contents($url));
    // echo $this->_stub['fetch-match'];
    if ( preg_match('~'.$this->_stub['fetch-match'].'~imu', $source, $match) ) {
      $this->_media_id = $match;
      $this->_setDefaultParams();
      return true;
    }

    return false;
  }

  /**
   * Build a generic object skeleton 
   */
  private function _buildObject() {

    $object_attribs = $object_params = '';

    foreach ($this->_object_attribs as $param => $value) {
      $object_attribs .= '  ' . $param . '="' . $value . '"';    
    }

    foreach ($this->_object_params as $param => $value) {
      $object_params .= '<param name="' . $param . '" value="' . $value . '" />';
    }

    return sprintf("<object %s> %s  %s</object>", $object_attribs, $object_params, self::AE_TAG);
  }

  /**
   * Build an iFrame player
   */
  private function _buildiFrame() {
    $source = $this->_stub['iframe-player'];
    
    for ($i=1; $i<=count($this->_media_id); $i++) {
      $source = str_ireplace('$'.$i, $this->_media_id[$i - 1], $source);
    }
    
    $width = $this->_object_attribs['width'];
    $height = $this->_object_attribs['height'];

    $source = preg_replace(',^http:,', 'https:', $source);

    return sprintf('<iframe type="text/html" width="%s" height="%s" src="%s" frameborder="0" allowFullScreen></iframe>', $width, $height, $source);
  }

  /**
   * Set the default params for the type of
   * stub we are working with
   */
  private function _setDefaultParams() {

    $source = $this->_stub['embed-src'];
    $flashvars = (isset($this->_stub['flashvars']))? $this->_stub['flashvars'] : null;

    for ($i=1; $i<=count($this->_media_id); $i++) {
      $source = str_ireplace('$'.$i, $this->_media_id[$i - 1], $source);
      $flashvars = str_ireplace('$'.$i, $this->_media_id[$i - 1], $flashvars);
    }

    $source = htmlspecialchars($source, ENT_QUOTES, null, false);
    $flashvars = htmlspecialchars($flashvars, ENT_QUOTES, null, false);

    $this->_object_params = array(
            'movie' => $source,
            'quality' => 'high',
            'allowFullScreen' => 'true',
            'allowScriptAccess' => 'always',
            'pluginspage' => 'http://www.macromedia.com/go/getflashplayer',
            'autoplay' => 'false',
            'autostart' => 'false',
            'flashvars' => $flashvars,
           );

    $this->_object_attribs = array(
            'type' => 'application/x-shockwave-flash',
            'data' => $source,
            'width' => $this->_stub['embed-width'],
            'height' => $this->_stub['embed-height'],
           );
  }

}

?>
