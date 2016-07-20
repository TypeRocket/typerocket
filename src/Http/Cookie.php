<?php

namespace TypeRocket\Http;

use TypeRocket\Sanitize;

class Cookie
{
    public function setTransient( $name, $data, $time = MINUTE_IN_SECONDS ) {
        $cookie_id = Sanitize::underscore( uniqid() . time() . uniqid() );
        $this->set($name, $cookie_id);
        set_transient( $name . '_' . $cookie_id, $data, $time );

        return $this;
    }

    public function getTransient( $name, $delete = true ) {
        $data = null;

        if( $_COOKIE[$name] ) {
            $id   = Sanitize::underscore($_COOKIE[$name]);
            $data = get_transient($name . '_' . $id);

            if($delete) {
                delete_transient($name . '_' . $id);
            }

            if (!headers_sent()) {
                $this->delete($name);
            }
        }

        return $data;
    }

    public function set( $name, $data, $time = MINUTE_IN_SECONDS ) {
        setcookie($name, $data, time() + $time, '/', null, isset($_SERVER["HTTPS"]), true);

        return $this;
    }

    public function delete( $name ) {
        setcookie($name, "", time() - 36000);

        return $this;
    }

    public function get( $name ) {
        $data = null;

        if( !empty($_COOKIE[$name]) ) {
            $data = $_COOKIE[$name];
        }

        return $data;
    }
}