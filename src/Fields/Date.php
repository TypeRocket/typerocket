<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

class Date extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'date' );
    }

    public function enqueueScripts() {
        wp_enqueue_script( 'jquery-ui-datepicker', ['jquery'], '1.0', true );
    }

    /**
     * Covert Date to HTML string
     */
    public function getString()
    {
        $name  = $this->getNameAttributeString();
        $this->removeAttribute( 'name' );
        $value = $this->getValue();
        $value = esc_attr( $this->sanitize($value, 'raw') );

        $this->appendStringToAttribute( 'class', ' date-picker' );
        $input = new Generator();

        return $input->newInput( 'text', $name, $value, $this->getAttributes() )->getString();
    }

}
