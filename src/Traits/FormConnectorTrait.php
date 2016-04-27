<?php

namespace TypeRocket\Traits;

trait FormConnectorTrait
{

    private $resource = null;
    private $action = null;
    private $itemId = null;

    /** @var \TypeRocket\Models\Model $model */
    private $model = null;

    private $populate = true;
    private $group = null;
    private $sub = null;
    private $settings = [];

    /**
     * Get controller
     *
     * @return null|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set Action
     *
     * @return null|string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get Item ID
     *
     * @return null|string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Get Model
     *
     * @return \TypeRocket\Models\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set Group into dot notation
     *
     * @param $group
     *
     * @return $this
     */
    public function setGroup( $group )
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get Group
     *
     * @return null|string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set Sub Group into dot notation
     *
     * @param $sub
     *
     * @return $this
     */
    public function setSub( $sub )
    {
        $this->sub = $sub;

        return $this;
    }

    /**
     * Get Sub Group
     *
     * @return null
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * Set whether to populate Field from database. If set to false fields will
     * always be left empty and with their default values.
     *
     * @param $populate
     *
     * @return $this
     */
    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    /**
     * Get populate
     *
     * @return bool
     */
    public function getPopulate()
    {
        return $this->populate;
    }

    /**
     * Set From settings
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get Form settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set Form setting by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setSetting( $key, $value )
    {
        $this->settings[$key] = $value;

        return $this;
    }

    /**
     * Get From setting by key
     *
     * @param $key
     * @param null $default default value to return if none
     *
     * @return null
     */
    public function getSetting( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->settings )) {
            return $default;
        }

        return $this->settings[$key];
    }

    /**
     * Remove setting bby key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeSetting( $key )
    {
        if (array_key_exists( $key, $this->settings )) {
            unset( $this->settings[$key] );
        }

        return $this;
    }

    /**
     * Render Setting
     *
     * By setting render to 'raw' the form will not add any special html wrappers.
     * You have more control of the design when render is set to raw.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRenderSetting( $value )
    {
        $this->settings['render'] = $value;

        return $this;
    }

    /**
     * Get render mode
     *
     * @return null
     */
    public function getRenderSetting()
    {
        if ( ! array_key_exists( 'render', $this->settings )) {
            return null;
        }

        return $this->settings['render'];
    }
}