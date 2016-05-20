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
        $option = ! is_null($option) ? $option : $default;

        $generator  = new Generator();
        $generator->newElement( 'select', $this->getAttributes() );

        foreach ($this->options as $key => $value) {

            if( is_array($value) ) {

                $optgroup  = new Generator();
                $optgroup->newElement( 'optgroup', ['label' => $key] );

                foreach($value as $k => $v) {
                    $attr['value'] = $v;
                    if ( $option == $v && isset($option) ) {
                        $attr['selected'] = 'selected';
                    } else {
                        unset( $attr['selected'] );
                    }

                    $optgroup->appendInside( 'option', $attr, (string) $k );
                }

                $generator->appendInside( $optgroup );

            } else {
                $attr['value'] = $value;
                if ( $option == $value && isset($option) ) {
                    $attr['selected'] = 'selected';
                } else {
                    unset( $attr['selected'] );
                }

                $generator->appendInside( 'option', $attr, (string) $key );
            }

        }

        return $generator->getString();
    }

}