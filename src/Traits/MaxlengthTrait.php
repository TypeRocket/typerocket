<?php

namespace TypeRocket\Traits;

use TypeRocket\Html\Generator;

trait MaxlengthTrait
{

    /**
     * Get the max length for text type fields
     *
     * @param $value
     * @param $maxLength
     *
     * @return string|\TypeRocket\Html\Generator
     */
    public function getMaxlength( $value, $maxLength )
    {
        if ( $maxLength != null && $maxLength > 0) {
            $left = (int) $maxLength - mb_strlen( $value );
            $max = new Generator();
            $max->newElement('p', ['class' => 'tr-maxlength'], 'Characters left: ')->appendInside('span', [], $left);
            $max = $max->getString();
        } else {
            $max = '';
        }

        return $max;
    }

}