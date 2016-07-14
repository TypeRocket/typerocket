<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config,
    \TypeRocket\Traits\MaxlengthTrait;

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

    /**
     * Get the scripts
     */
    public function enqueueScripts() {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-editor', $assets . '/js/redactor.min.js', ['jquery'], '1.0', true );
    }

    /**
     * Covert Editor to HTML string
     */
    public function getString()
    {
        $generator = new Generator();
        $this->setAttribute('name', $this->getNameAttributeString());
        $value = $this->getValue();
        $this->appendStringToAttribute('class', ' typerocket-editor ');
        $value = esc_attr( $this->sanitize($value, 'editor') );

        $max = $this->getMaxlength( $value,  $this->getAttribute('maxlength'));

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}
