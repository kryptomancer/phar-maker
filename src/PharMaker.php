<?php
/**
 * @link      https://github.com/drabajs/phar-maker
 * @copyright (c) 2014, Jakub Drabik
 * @license   MIT Licence
 */

namespace Drabajs\PharMaker;

use Phar;

class PharMaker {
    
    protected $source;
    protected $target;
    
    protected $minified = FALSE;


    protected $options = array(
        'cache-dir' => '.cache'
    );
    
    public function __construct($source, $target, $options = array()) {
        
        if (!Phar::canWrite()) {
            throw new \UnexpectedValueException("Creating phar archive is disabled by the php.ini setting phar.readonly.");
        }
        
        if (!file_exists($source)) {
            throw new \InvalidArgumentException("Source file or directory does not exist.");
        }
        
        $this->source = $source;
        $this->target = $target;
        $this->options = array_merge($this->options, $options);
    }
    
    public function minify(IPhpMinifier $minifier = NULL) {
        if (!$minifier) {
            $minifier = new PhpMinifier;
        }
        
        $minifier->setSource($this->source);
        $minifier->setTarget($this->options['cache-dir']);
        $minifier->run();
        $this->minified = TRUE;
    }
    
    public function makePhar($compression = Phar::GZ) {
        $source = $this->minified ? $this->options['cache-dir'] : $this->source;
        
        $phar = new Phar($this->target);

        $phar->buildFromDirectory($source);

        $phar->setStub("<?php
        Phar::mapPhar('" . $this->target . "');
        require 'phar://" . $this->target ."/loader.php';
        __HALT_COMPILER();");

        $phar->compressFiles($compression);
        
    }
}
