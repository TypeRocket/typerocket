<?php
namespace TypeRocket\Html;

class Generator
{

    /** @var Tag */
    public $tag;

    public function __toString()
    {
        return $this->getString();
    }

    /**
     * Create new Tag
     *
     * @param $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    public function newElement( $tag, array $attributes = [], $text = '' )
    {
        $this->tag = new Tag( $tag, $attributes, $text );

        return $this;
    }

    /**
     * Create new link
     *
     * @param string $text
     * @param string $url
     * @param array $attributes
     *
     * @return $this
     */
    public function newLink( $text = '', $url = '#', array $attributes = [])
    {

        $attributes = array_merge( ['href' => $url], $attributes );

        $this->tag = new Tag( 'a', $attributes, $text );

        return $this;
    }

    /**
     * Create new image
     *
     * @param string $src
     * @param array $attributes
     *
     * @return $this
     */
    public function newImage( $src = '', array $attributes = [])
    {

        $attributes = array_merge( ['src' => $src], $attributes );

        $this->tag = new Tag( 'img', $attributes );

        return $this;
    }

    /**
     * Create new input
     *
     * @param $type
     * @param $name
     * @param $value
     * @param array $attributes
     *
     * @return $this
     */
    public function newInput( $type, $name, $value, array $attributes = [])
    {

        $defaults = ['type' => $type, 'name' => $name, 'value' => $value];

        if (is_null( $name )) {
            unset( $defaults['name'] );
        }

        if (is_null( $value )) {
            unset( $defaults['value'] );
        }

        $attributes = array_merge( $defaults, $attributes );

        $this->tag = new Tag( 'input', $attributes );

        return $this;
    }

    /**
     * Get Tag
     *
     * @return \TypeRocket\Html\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set Tag
     *
     * @param \TypeRocket\Html\Tag $tag
     *
     * @return $this
     */
    public function setTag( Tag $tag )
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get string of tag
     *
     * @return string
     */
    public function getString()
    {
        return $this->tag->getString();
    }

    /**
     * Append inside of tag
     *
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    public function appendInside( $tag, array $attributes = [], $text = '' )
    {

        if ($tag instanceof Generator) {
            $tag = $tag->tag;
        } elseif (is_string( $tag )) {
            $tag = new Tag( $tag, $attributes, $text );
        }

        $this->tag->appendInnerTag( $tag );

        return $this;

    }

    /**
     * Prepend Inside of tag
     *
     * @param string|Tag|Generator $tag
     * @param array $attributes
     * @param string $text
     *
     * @return $this
     */
    public function prependInside( $tag, array $attributes = [], $text = '' )
    {

        if ($tag instanceof Generator) {
            $tag = $tag->tag;
        } elseif (is_string( $tag )) {
            $tag = new Tag( $tag, $attributes, $text );
        }

        $this->tag->prependInnerTag( $tag );

        return $this;

    }

}
