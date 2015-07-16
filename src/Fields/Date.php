<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Date extends Field implements FieldScript
{

    public function init()
    {
        $this->setType( 'date' );
    }

    public function enqueueScripts() {
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
    }

    public function getString()
    {
        $name  = $this->getAttribute( 'name' );
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'attribute');

        if ( is_callable($sanitize)) {
            $value = esc_attr( call_user_func($sanitize, $value ) );
        }

        $this->appendStringToAttribute( 'class', ' date-picker' );
        $this->removeAttribute( 'name' );

        $input = new Generator();

        return $input->newInput( 'text', $name, $value, $this->getAttributes() )->getString();
    }

}
