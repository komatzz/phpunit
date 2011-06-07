<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Framework
 * @author     Bernhard Schussek <bschussek@gmail.com>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

/**
 * Compares objects for equality.
 *
 * @package    PHPUnit
 * @subpackage Framework_Comparator
 * @author     Bernhard Schussek <bschussek@gmail.com>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.6.0
 */
class PHPUnit_Framework_Comparator_Object extends PHPUnit_Framework_Comparator_Array
{
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param  mixed $a The first value to compare
     * @param  mixed $b The second value to compare
     * @return boolean
     * @since  Method available since Release 3.6.0
     */
    public function accepts($a, $b)
    {
        return is_object($a) && is_object($b);
    }

    /**
     * Asserts that two values are equal.
     *
     * @param  mixed $a The first value to compare
     * @param  mixed $b The second value to compare
     * @param  float $delta The allowed numerical distance between two values to
     *                      consider them equal
     * @param  bool  $canonicalize If set to TRUE, arrays are sorted before
     *                             comparison
     * @param  bool  $ignoreCase If set to TRUE, upper- and lowercasing is
     *                           ignored when comparing string values
     * @throws PHPUnit_Framework_ComparisonFailure Thrown when the comparison
     *                           fails. Contains information about the
     *                           specific errors that lead to the failure.
     * @since  Method available since Release 3.6.0
     */
    public function assertEquals($a, $b, $delta = 0, $canonicalize = FALSE, $ignoreCase = FALSE, array &$processed = array())
    {
        if (get_class($b) !== get_class($a)) {
            throw new PHPUnit_Framework_ComparisonFailure($a, $b);
        }

        // don't compare twice to allow for cyclic dependencies
        if (in_array(array($b, $a), $processed, true) || in_array(array($a, $b), $processed, true)) {
            return;
        }

        $processed[] = array($b, $a);

        // don't compare objects if they are identical
        // this helps to avoid the error "maximum function nesting level reached"
        // CAUTION: this conditional clause is not tested
        if ($b !== $a) {
            parent::assertEquals($this->toArray($a), $this->toArray($b), $delta, $canonicalize, $ignoreCase, $processed);
        }
    }

    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @param  object $object
     * @return array
     * @since  Method available since Release 3.6.0
     */
    protected function toArray($object)
    {
        $array = array();

        foreach ((array)$object as $key => $value) {
            // properties are transformed to keys in the following way:

            // private   $property => "\0Classname\0property"
            // protected $property => "\0*\0property"
            // public    $property => "property"

            if (preg_match('/^\0.+\0(.+)$/', $key, $matches)) {
                $key = $matches[1];
            }

            $array[$key] = $value;
        }

        return $array;
    }
}