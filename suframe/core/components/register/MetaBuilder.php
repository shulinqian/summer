<?php
namespace suframe\core\components\register;

class MetaBuilder{

    protected $layout;
    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    public function build($args){
        $layout = file_get_contents($this->layout);
        return $layout;
    }

}