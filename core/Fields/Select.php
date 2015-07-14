<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Select extends Field
{

    public $options = array();

    public function __construct()
    {
        $this->setType('select');
    }

    function getString()
    {
        $default = $this->getSetting('default');
        $option = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;

        $generator  = new Generator();
        $generator->newElement( 'select', $this->getAttributes() );

        foreach ($this->options as $key => $value) {

            $attr['value'] = $value;
            if ($option == $value) {
                $attr['selected'] = 'selected';
            } else {
                unset( $attr['selected'] );
            }

            $generator->appendInside( 'option', $attr, (string) $key );

        }

        return $generator->getString();
    }

}