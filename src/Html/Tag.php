<?php
namespace TypeRocket\Html;

class Tag
{

    private $tag;
    private $attributes;
    private $text;

    /** @var  TagCollection */
    private $innerHtmlTags;
    private $specialTags = array( 'img' => true, 'input' => true, 'hr' => true, 'br' => true );
    private $specialTag = false;


    /**
     * Html constructor.
     *
     * @param $tag
     * @param $attributes
     * @param string $text
     */
    public function __construct( $tag, $attributes, $text = '')
    {
        $this->setTag( $tag );
        $this->setAttributes( $attributes );
        $this->setText( $text );
        $this->setInnerHtmlTags( new TagCollection() );

        if( array_key_exists($this->tag, $this->specialTags) ) {
            $this->specialTag = true;
        }

        return $this;
    }

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    public function __toString()
    {
        return $this->getString();
    }

    /**
     * Get tag
     *
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Get attributes
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get Special Tag
     *
     * @return bool
     */
    public function getSpecialTag()
    {
        return $this->specialTag;
    }

    /**
     * Get attribute
     *
     * @param $attribute
     *
     * @return null
     */
    public function getAttributeValue( $attribute )
    {
        $value = null;

        if (array_key_exists( $attribute, $this->attributes )) {
            $value = $this->attributes[$attribute];
        }

        return $value;
    }

    /**
     * Get inner HTML tags
     */
    public function getInnerHtmlTags()
    {
        $this->innerHtmlTags;
    }

    /**
     * Set tag text
     *
     * @param $text
     *
     * @return $this
     */
    public function setText( $text )
    {
        $this->text = (string) $text;

        return $this;
    }

    /**
     * Set Tag name
     *
     * @param $tag
     *
     * @return $this
     */
    public function setTag( $tag )
    {
        if (is_string( $tag )) {
            $this->tag = $tag;
        }

        return $this;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes( array $attributes )
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set attribute
     *
     * @param $attribute
     * @param $value
     *
     * @return $this
     */
    public function setAttribute( $attribute, $value )
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * Update attributes and merge them
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function updateAttributes( array $attributes )
    {
        $this->attributes = array_merge( $this->attributes, $attributes );

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param $attribute
     *
     * @return $this
     */
    public function removeAttribute( $attribute )
    {
        if (array_key_exists( $attribute, $this->attributes )) {
            unset( $this->attributes[$attribute] );
        }

        return $this;
    }

    /**
     * Append Inner Tag
     *
     * @param \TypeRocket\Html\Tag $tag
     *
     * @return $this
     */
    public function appendInnerTag( Tag $tag )
    {
        $this->innerHtmlTags->append( $tag );

        return $this;
    }

    /**
     * Prepend inner tag
     *
     * @param \TypeRocket\Html\Tag $tag
     *
     * @return $this
     */
    public function prependInnerTag( Tag $tag )
    {
        $this->innerHtmlTags->prepend( $tag );

        return $this;
    }

    /**
     * @param TagCollection $collection
     *
     * @return $this
     */
    public function setInnerHtmlTags( TagCollection $collection )
    {
        $this->innerHtmlTags = $collection;

        return $this;
    }

    /**
     * Get string
     *
     * @return string
     */
    public function getString() {

        $openTag = $this->getStringOpenTag();
        $closeTag = $this->getStringCloseTag();
        $innerHtmlWithTags = $this->getStringInnerHtmlWithTags();

        return $openTag.$innerHtmlWithTags.$closeTag;

    }

    /**
     * Get the opening tag in string form
     *
     * @return string
     */
    public function getStringOpenTag() {
        $openTag = "<{$this->tag}";

        foreach($this->attributes as $attribute => $value) {
            $openTag .= " {$attribute}=\"{$value}\"";
        }

        if( $this->specialTag ) {
            $openTag .= " />";
        } else {
            $openTag .= ">";
        }

        return $openTag;
    }

    /**
     * Get the closing tag as string
     *
     * @return string
     */
    public function getStringCloseTag() {
        $closeTag = '';

        if( ! $this->specialTag ) {
            $closeTag = "</{$this->tag}>";
        }

        return $closeTag;
    }

    /**
     * Get the string with inner HTML
     *
     * @return string
     */
    public function getStringInnerHtmlWithTags() {
        $innerHtmlTags = $innerHtml ='';

        if( ! $this->specialTag ) {
            $innerHtml = $this->text;
            foreach($this->innerHtmlTags as $tag) {
                $innerHtmlTags .= $tag->getString();
            }
        }

        return $innerHtml.$innerHtmlTags;
    }

}
