<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;
use TypeRocket\Traits\MaxlengthTrait;

class Editor extends Textarea implements ScriptField
{
    use MaxlengthTrait;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'editor' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-editor', $assets . '/js/redactor.min.js', array( 'jquery' ), '1.0', true );
    }

    /**
     * Covert Editor to HTML string
     */
    public function getString()
    {
        $generator = new Generator();
        $value = $this->getValue();
        $this->setAttribute('name', $this->getNameAttributeString());
        $this->appendStringToAttribute('class', ' typerocket-editor ');
        $value = esc_attr( $this->sanitize($value, 'editor') );

        $max = $this->getMaxlength( $value,  $this->getAttribute('maxlength'));

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}