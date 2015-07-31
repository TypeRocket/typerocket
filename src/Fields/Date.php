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
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
    }

    /**
     * Covert Date to HTML string
     */
    public function getString()
    {
        $name  = $this->getNameAttributeString();
        $this->removeAttribute( 'name' );
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'raw');

        if ( is_callable($sanitize)) {
            $value = esc_attr( call_user_func($sanitize, $value ) );
        }

        $this->appendStringToAttribute( 'class', ' date-picker' );
        $input = new Generator();

        return $input->newInput( 'text', $name, $value, $this->getAttributes() )->getString();
    }

}
