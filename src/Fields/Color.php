<?php
namespace TypeRocket\Fields;

use \TypeRocket\Config,
    \TypeRocket\Html\Generator,
    \TypeRocket\Sanitize;

class Color extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'color' );
    }

    /**
     * Get the scripts
     */
    public function enqueueScripts() {
        wp_enqueue_script( 'wp-color-picker'  );
    }

    /**
     * Covert Color to HTML string
     */
    public function getString()
    {
        $name  = $this->getNameAttributeString();
        $value = Sanitize::hex( $this->getValue() );
        $this->removeAttribute( 'name' );
        $this->appendStringToAttribute( 'class', ' color-picker' );
        $palette = 'tr_' . uniqid() . '_color_picker';
        $this->setAttribute('id', $palette);
        $obj = $this;

        $callback = \Closure::bind(function() use ($palette, $obj) {
            wp_localize_script( 'typerocket-scripts', $palette . '_color_palette', $this->getSetting( 'palette' ) );
        }, $this);

        add_action('admin_footer', $callback, 999999999999 );

        if( Config::getFrontend() ) {
            add_action('wp_footer', $callback, 999999999999 );
        }

        if ( $this->getSetting( 'palette' ) ) {
            $this->setAttribute( 'data-default-color', $this->getSetting( 'palette' )[0] );
        }

        $input = new Generator();

        return $input->newInput( 'text', $name, $value, $this->getAttributes() )->getString();
    }

    /**
     * Set color palette
     *
     * Use 6 character hex only eg. [ '#222222', '#000000' ]
     *
     * @param array $palette set the color palette
     *
     * @return $this
     */
    public function setPalette( array $palette ) {
        if( ! empty( $palette) ) {
            $this->setSetting('palette', $palette );
        }

        return $this;
    }

}