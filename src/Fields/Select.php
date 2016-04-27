<?php
namespace TypeRocket\Fields;

use TypeRocket\Traits\OptionsTrait;
use \TypeRocket\Html\Generator;

class Select extends Field
{

    use OptionsTrait;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'select' );
    }

    /**
     * Covert Select to HTML string
     */
    public function getString()
    {
        $default = $this->getSetting('default');
        $this->setAttribute('name', $this->getNameAttributeString());
        $option = $this->getValue();
        $option = ! is_null($option) ? $this->getValue() : $default;

        $generator  = new Generator();
        $generator->newElement( 'select', $this->getAttributes() );

        foreach ($this->options as $key => $value) {

            $attr['value'] = $value;
            if ($option === $value) {
                $attr['selected'] = 'selected';
            } else {
                unset( $attr['selected'] );
            }

            $generator->appendInside( 'option', $attr, (string) $key );

        }

        return $generator->getString();
    }

}