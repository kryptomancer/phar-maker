<?php
/**
 * @link      https://github.com/drabajs/phar-maker
 * @copyright (c) 2014, Jakub Drabik
 * @license   MIT Licence
 */

namespace Drabajs\PharMaker;

interface IPhpMinifier {
    public function run();
    public function setSource($source);
    public function setTarget($target);
}
