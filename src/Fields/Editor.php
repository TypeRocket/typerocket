<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator,
    \TypeRocket\Sanitize as Sanitize,
    \TypeRocket\Config as Config;

class Editor extends Textarea
{

    function __construct()
    {
        $paths = Config::getPaths();
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-editor', $paths['urls']['assets'] . '/js/redactor.min.js', array( 'jquery' ), '1.0',
            true );
    }

    function getString()
    {
        $max = '';
        $generator = new Generator();
        $value = $this->getValue();
        $this->appendStringToAttribute('class', ' typerocket-editor ');
        $sanitize = "Sanitize::" . $this->getSetting('sanitize', 'editor');

        if ( is_callable($sanitize)) {
            $value = call_user_func($sanitize, $value );
        }

        $maxLength = $this->getAttribute('maxlength');

        if ( $maxLength != null && $maxLength > 0) {
            $left = (int) $maxLength - strlen( utf8_decode( $value ) );
            $max = new Generator();
            $max->newElement('p', array('class' => 'tr-maxlength'), 'Characters left: ')->appendInside('span', array(), $left);
            $max = $max->getString();
        }

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}