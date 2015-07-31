<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

class Text extends Field
{

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'text' );
    }

    /**
     * Covert Test to HTML string
     */
    public function getString()
    {
        $max = '';
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'raw');

        if ( is_callable($sanitize)) {
            $value = esc_attr( call_user_func($sanitize, $value ) );
        }

        $maxLength = $this->getAttribute('maxlength');

        if ($maxLength != null && $maxLength > 0) {
            $left = (int) $maxLength - strlen( utf8_decode( $value ) );
            $max = new Generator();
            $max->newElement('p', array('class' => 'tr-maxlength'), 'Characters left: ')->appendInside('span', array(), $left);
            $max = $max->getString();
        }

        return $input->newInput($this->getType(), $name, $value, $this->getAttributes() )->getString() . $max;
    }

}