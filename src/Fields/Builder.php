<?php

namespace TypeRocket\Fields;

use TypeRocket\Buffer;

class Builder extends Matrix
{

    public function getSstring()
    {
        $buffer = new Buffer();
        $buffer->startBuffer();
        ?>

        <div class="tr-builder">

            <ul class="tr-components">
               <li>
                    <p>Builder</p>
               </li>
            </ul>

            <div class="tr-frame-fields">

            </div>

        </div>

        <?php
        $buffer->indexBuffer('main');
        return $buffer->getBuffer('main');
    }

}