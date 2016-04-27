<?php

namespace TypeRocket\Traits;

trait FormConnectorTrait
{
    protected $populate = true;
    protected $group = null;
    protected $sub = null;
    protected $settings = [];

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