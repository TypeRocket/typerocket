<?php

namespace TypeRocket\Layout;

use TypeRocket\Html\Generator;
use TypeRocket\Page;

class Tables
{
    public $results;
    public $columns;
    public $page = null;
    public $settings = ['update_column' => 'id'];

    /**
     * @param array $results
     *
     * @return $this
     */
    public function setResults( array $results )
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Tables
     */
    public function setColumns( array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function setPage( Page $page) {
        $this->page = $page;

        return $this;
    }

    public function setUpdateColumn($name)
    {
        $this->settings['update_column'] = strtolower($name);

        return $this;
    }

    /**
     * Render table
     */
    public function render()
    {
        $results = $this->results;
        $columns = $this->columns;
        $table = new Generator();
        $head = new Generator();
        $body = new Generator();
        $foot = new Generator();

        if( empty($columns) ) {
            $columns = array_keys(get_object_vars($results[0]));
        }

        $table->newElement('table', ['class' => 'tr-list-table wp-list-table widefat striped']);
        $head->newElement('thead');
        $body->newElement('tbody', ['class' => 'the-list']);
        $foot->newElement('tfoot');

        $th_row = new Generator();
        $th_row->newElement('tr', ['class' => 'manage-column']);
        foreach ( $columns as $column => $label ) {
            $th = new Generator();

            if( is_string($label) ) {
                $th->newElement('th', [], ucfirst($label));
            } else {
                $th->newElement('th', [], $column);
            }

            $th_row->appendInside($th);
        }
        $head->appendInside($th_row);
        $foot->appendInside($th_row);

        foreach ( $results as $result ) {
            $td_row = new Generator();
            $td_row->newElement('tr', ['class' => 'manage-column']);
            foreach($columns as $column => $label ) {

                if( ! is_string($column) ) {
                    $column = $label;
                }

                $value = $result->$column;

                if( $this->page instanceof Page && $column == $this->settings['update_column'] && !empty($this->page->pages) ) {
                    foreach ($this->page->pages as $page) {
                        if( $page->action == 'update' ) {
                            $url   = admin_url() . 'admin.php?page=' . $page->getSlug() . '&item_id=' . (int) $result->id;
                            $value = "<a href=\"{$url}\">{$value}</a>";
                            break;
                        }
                    }
                }

                $td = new Generator();
                $td->newElement('td', [], $value);
                $td_row->appendInside($td);
            }
            $body->appendInside($td_row);
        }

        $table->appendInside('thead', [], $head );
        $table->appendInside('tbody', [], $body );
        $table->appendInside('tfoot', [], $foot );

        echo $table;

    }

}