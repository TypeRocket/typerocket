<?php
namespace TypeRocket\Fields;

use \TypeRocket\Sanitize;

class WordPressEditor extends Field implements ScriptField
{

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'wp_editor' );
    }

    public function enqueueScripts() {
        wp_enqueue_media();
    }

    /**
     * Covert WordPress Editor to HTML string
     */
    public function getString()
    {
        $this->setAttribute('name', $this->getNameAttributeString());
        $value    = Sanitize::editor( $this->getValue() );
        $settings = $this->getSetting('options', array());

        $override = array(
            'textarea_name' => $this->getAttribute('name')
        );

        $defaults = array(
            'textarea_rows' => 10,
            'teeny'         => true,
            'tinymce'       => array( 'plugins' => 'wordpress' )
        );

        $settings = array_merge( $defaults, $settings, $override );

        ob_start();
        wp_editor( $value, 'wp_editor_' . $this->getName(), $settings );
        $html = ob_get_clean();

        return $html;
    }

}