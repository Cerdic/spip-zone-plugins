<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfYamlInline.php');

/**
 * sfYamlDumper dumps PHP variables to YAML strings.
 *
 * @package    symfony
 * @subpackage yaml
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfYamlDumper.class.php 10575 2008-08-01 13:08:42Z nicolas $
 */
class sfYamlDumper
{
  protected $indentation;

  /**
   * Set indentation on creation.
   * @fork correction pour coincider avec le fonctionnement de symfony v4
   *       pas de risque car cette librairie n'évoluera plus
   *
   * @param  integer $indent The amount of spaces to use for indentation of nested nodes.
   */
  public function __construct($indentation = 2)
  {
    if ($indentation < 1) {
      $this->indentation = 2;
    } else {
      $this->indentation = $indentation;
    }

  }

  /**
   * Dumps a PHP value to YAML.
   *
   * @param  mixed   $input  The PHP value
   * @param  integer $inline The level where you switch to inline YAML
   * @param  integer $indent The level o indentation (used internally)
   *
   * @return string  The YAML representation of the PHP value
   */
  public function dump($input, $inline = 0, $indent = 0)
  {
    $output = '';
    $prefix = $indent ? str_repeat(' ', $indent) : '';

    if ($inline <= 0 || !is_array($input) || empty($input))
    {
      $output .= $prefix.sfYamlInline::dump($input);
    }
    else
    {
      $isAHash = array_keys($input) !== range(0, count($input) - 1);

      foreach ($input as $key => $value)
      {
        $willBeInlined = $inline - 1 <= 0 || !is_array($value) || empty($value);

        $output .= sprintf('%s%s%s%s',
          $prefix,
          $isAHash ? sfYamlInline::dump($key).':' : '-',
          $willBeInlined ? ' ' : "\n",
          $this->dump($value, $inline - 1, $willBeInlined ? 0 : $indent + $this->indentation)
        ).($willBeInlined ? "\n" : '');
      }
    }

    return $output;
  }
}
