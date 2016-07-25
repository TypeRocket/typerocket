<?php
namespace TypeRocket\Fields;

use \TypeRocket\Form,
    \TypeRocket\Sanitize,
    \Typerocket\Traits\FormConnectorTrait;

abstract class Field
{
    use FormConnectorTrait;

    private $name = null;
    private $type = null;
    private $attr = [];

    /** @var Form */
    private $form = null;
    private $prefix = 'tr';
    private $helper = null;
    private $label = false;

    /**
     * When instancing a Field use reflection to connect the Form
     *
     * @param string $name the name of the field
     * @param array $attr the html attributes
     * @param array $settings the settings of the field
     * @param bool|true $label show the label
     *
     * @internal A Form must be passed for the field to work
     */
    public function __construct( $name, array $attr = [], array $settings = [], $label = true )
    {
        $args = func_get_args();
        $this->init();
        $setup = new \ReflectionMethod( $this, 'setup' );
        $setup->setAccessible(true);

        $args = $this->assignAutoArgs($args);

        if ($this instanceof ScriptField) {
            $this->enqueueScripts();
        }

        $setup->invokeArgs( $this, $args );
        $setup->setAccessible(false);
    }

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Get Field object as string
     *
     * @return string
     */
    public function __toString()
    {
        $form = $this->getForm();
        if($form instanceof Form) {
            $string = $this->getForm()->getFromFieldString($this);
        } else {
            $string = $this->getString();
        }

        return $string;
    }

    /**
     * Require Form
     *
     * @param $args
     *
     * @return mixed
     */
    private function assignAutoArgs($args) {
        foreach ($args as $key => $arg) {
            if ($arg instanceof Form) {
                $this->configureToForm( $arg );
                unset( $args[$key] );
                return $args;
            }
        }

        die('TypeRocket: A field does not have a From connected to it.');
    }

    /**
     * Invoked by Reflection in constructor
     *
     * @param string $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return $this
     */
    private function setup( $name, array $attr = [], array $settings = [], $label = true )
    {
        $this->settings = $settings;
        $this->label    = $label;
        $this->attr     = $attr;
        $this->setName( $name );

        if (empty( $settings['label'] )) {
            $this->settings['label'] = $name;
        }

        return $this;

    }

    /**
     * Init is normally used to setup initial configuration like a
     * constructor does.
     *
     * @return mixed
     */
    abstract protected function init();

    /**
     * Setup to use with a Form.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function configureToForm( Form $form )
    {
        $this->form = clone $form;
        $this->setGroup( $this->form->getGroup() );
        $this->setSub( $this->form->getSub() );
        $this->itemId = $this->form->getItemId();
        $this->resource = $this->form->getResource();
        $this->action = $this->form->getAction();
        $this->model = $this->form->getModel();
        $this->setPopulate( $this->form->getPopulate() );

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set Attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes( array $attributes )
    {
        $this->attr = $attributes;

        return $this;
    }

    /**
     * Get Attribute by key
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attr;
    }

    /**
     * Set Attribute by key
     *
     * @param $key
     * @param $value
     *
     * @return Field $this
     */
    public function setAttribute( $key, $value )
    {
        $this->attr[(string) $key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return null
     */
    public function getAttribute( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->attr )) {
            return $default;
        }

        return $this->attr[$key];
    }

    /**
     * Set Help Text
     *
     * @param string $value help text
     *
     * @return Field $this
     */
    public function setHelp( $value )
    {
        $this->settings['help'] = (string) $value;

        return $this;
    }

    /**
     *
     * Get Help Text
     *
     * @return string help text
     */
    public function getHelp()
    {
        return $this->settings['help'];
    }

    /**
     * Remove Attribute by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeAttribute( $key )
    {

        if (array_key_exists( $key, $this->attr )) {
            unset( $this->attr[$key] );
        }

        return $this;
    }

    /**
     * Append a string to an attribute
     *
     * @param string $key the attribute if set
     * @param string $text the string to append
     * @param string $separator separate stings by this
     *
     * @return $this
     */
    public function appendStringToAttribute( $key, $text, $separator = ' ' )
    {

        if (array_key_exists( $key, $this->attr )) {
            $text = $this->attr[$key] . $separator . (string) $text;
        }

        $this->attr[$key] = $text;

        return $this;
    }

    /**
     * Generate the string needed for the html name attribute. This
     * does not set the name attribute.
     *
     * @return string
     */
    public function getNameAttributeString()
    {
        return $this->prefix .$this->getBrackets();
    }

    /**
     * Set the type of Field. This is not always the input type. For
     * example in custom fields. Text Field is the only that does.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType( $type )
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Get Type
     *
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name of field. Not the same as the html name attribute.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = Sanitize::underscore( $name );

        return $this;
    }

    /**
     * Get name of field. Not the same as the html name attribute.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Label Text
     *
     * Set the label text to be used
     *
     * @param string $value
     *
     * @return $this
     */
    public function setLabel( $value )
    {

        $this->settings['label'] = $value;

        return $this;
    }

    /**
     * Get Label
     *
     * This is not the label text but the label setting. Whether it
     * should be displayed.
     *
     * @return bool
     */
    public function getLabel()
    {
        if ( ! array_key_exists( 'label', $this->settings )) {
            return null;
        }

        return $this->settings['label'];
    }

    /**
     * Set whether label should be displayed
     *
     * @param $label
     *
     * @return $this
     */
    public function setLabelOption( $label )
    {
        $this->label = (bool) $label;

        return $this;
    }

    /**
     * Get Label Option
     *
     * This is not the label text but the label setting. Whether it
     * should be displayed.
     *
     * @return bool
     */
    function getLabelOption()
    {
        return $this->label;
    }

    /**
     * Get the prefix
     *
     * @return null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }


    /**
     * Get the value from the database
     *
     * @return null|string return the fields value
     */
    public function getValue()
    {

        if ($this->populate == false) {
            return null;
        }

        $value = $this->form->getModel()->getFieldValue($this);

        return $value;
    }

    /**
     * Sanitize field value
     *
     * @param $value
     * @param null $default
     *
     * @return mixed
     */
    protected function sanitize( $value, $default = null )
    {
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', $default );

        if ( is_callable($sanitize)) {
            $value = call_user_func($sanitize, $value);
        }

        return $value;
    }

    /**
     * Get bracket syntax used to name the input and get the
     * value from the database using the GetValue class.
     *
     * @return string format [group][name][sub]
     */
    public function getBrackets()
    {
        return $this->getBracketsFromDots();
    }

    /**
     * Get the dot syntax
     *
     * @return null|string
     */
    public function getDots()
    {

        $dots = $this->name;

        if(!empty($this->group)) {
            $dots = $this->group . '.' . $dots;
        }

        if(!empty($this->sub)) {
            $dots .= '.' . $this->sub;
        }

        return $dots;
    }

    public function getBracketsFromDots()
    {
        $dots = $this->getDots();
        $array = explode('.', $dots);
        $brackets = array_map(function($item) { return "[{$item}]"; }, $array);

        return implode('', $brackets);
    }

    /**
     * Set the field debugger helper for the front-end
     *
     * @param null $helper
     *
     * @return $this
     */
    public function setDebugHelperFunction( $helper = null ) {
        $this->helper = $helper;

        return $this;
    }

    /**
     * Get the field debugger helper for the front-end
     *
     * @return null
     */
    public function getDebugHelperFunction() {
        return $this->helper;
    }

    /**
     * Configure in all concrete Field classes
     *
     * @return string
     */
    abstract public function getString();

}