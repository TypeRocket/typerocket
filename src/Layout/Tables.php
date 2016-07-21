<?php

namespace TypeRocket\Layout;

use TypeRocket\Config;
use TypeRocket\Html\Generator;
use TypeRocket\Models\SchemaModel;
use TypeRocket\Page;

class Tables
{
    public $results;
    public $columns;
    public $count;
    public $model;
    public $primary = 'id';

    /** @var null|Page  */
    public $page = null;
    public $paged = 1;
    public $limit;
    public $offset = 0;
    public $settings = ['update_column' => 'id'];

    public function __construct( SchemaModel $model, $limit = 25 )
    {
        $this->limit = $limit;
        $this->model = clone $model;
        $this->count = $model->findAll()->count();
        $this->paged = !empty($_GET['paged']) ? (int) $_GET['paged'] : 1;

        $this->offset = ( $this->paged - 1 ) * $this->limit;
    }

    /**
     * Set table limit
     *
     * @param $limit
     *
     * @return $this
     */
    public function setLimit( $limit ) {
        $this->limit = (int) $limit;
        $this->offset = ( $this->paged - 1 ) * $this->limit;

        return $this;
    }

    /**
     * @param $primary
     * @param array $columns
     *
     * @return \TypeRocket\Layout\Tables
     */
    public function setColumns( $primary, array $columns)
    {
        $this->primary = $primary;
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param \TypeRocket\Page $page
     *
     * @return $this
     */
    public function setPage( Page $page) {
        $this->page = $page;

        return $this;
    }

    /**
     * Render table
     */
    public function table()
    {
        $results = $this->model->findAll()->take($this->limit, $this->offset)->get();
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
        foreach ( $columns as $column => $data ) {
            $th = new Generator();
            $classes = null;
            if($this->primary == $column) {
                $classes = 'column-primary';
            }

            if( ! is_string($column) ) {
                $th->newElement('th', ['class' => $classes], ucfirst($data));
            } else {
                $th->newElement('th', ['class' => $classes], $data['label']);
            }

            $th_row->appendInside($th);
        }
        $head->appendInside($th_row);
        $foot->appendInside($th_row);

        if( !empty($results)) {
            foreach ($results as $result) {
                $td_row = new Generator();
                $row_id = 'result-row-' . $result->id;
                $td_row->newElement('tr', ['class' => 'manage-column', 'id' => $row_id]);
                foreach ($columns as $column => $data) {
                    $show_url = $edit_url = $delete_url = '';

                    // get columns if none set
                    if ( ! is_string($column)) {
                        $column = $data;
                    }

                    $text = $result->$column;

                    if ($this->page instanceof Page && ! empty($this->page->pages)) {
                        foreach ($this->page->pages as $page) {
                            /** @var Page $page */
                            if ($page->action == 'edit') {
                                $edit_url = $page->getUrl(['item_id' => (int)$result->id]);
                            }

                            if ($page->action == 'show') {
                                $show_url = $page->getUrl(['item_id' => (int)$result->id]);
                            }

                            if ($page->action == 'delete') {
                                $delete_url = $page->getUrl(['item_id' => (int)$result->id]);
                            }
                        }

                        if ( ! empty($data['actions'])) {
                            $text = "<strong><a href=\"{$edit_url}\">{$text}</a></strong>";
                            $text .= "<div class=\"row-actions\">";
                            foreach ($data['actions'] as $index => $action) {

                                if ($index > 0) {
                                    $text .= ' | ';
                                }

                                switch ($action) {
                                    case 'edit' :
                                        $text .= "<span class=\"edit\"><a href=\"{$edit_url}\">Edit</a></span>";
                                        break;
                                    case 'delete' :
                                        $delete_url = wp_nonce_url($delete_url, 'form_' . Config::getSeed(),
                                            '_tr_nonce_form');
                                        $text .= "<span class=\"delete\"><a data-target=\"#{$row_id}\" class=\"tr-delete-row-rest-button\" href=\"{$delete_url}\">Delete</a></span>";
                                        break;
                                    case 'view' :
                                        $text .= "<span class=\"view\"><a href=\"{$show_url}\">View</a></span>";
                                }
                            }
                            $text .= "</div>";
                        }
                    }

                    $classes = null;
                    if($this->primary == $column) {
                        $classes = 'column-primary';
                        $text .= "<button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">Show more details</span></button>";
                    }

                    $td = new Generator();
                    $td->newElement('td', ['class' => $classes], $text);
                    $td_row->appendInside($td);
                }
                $body->appendInside($td_row);
            }
        } else {
            $td_row = new Generator();
            $td_row->newElement('tr', [], '<td>No results.</td>');
            $body->appendInside($td_row);
        }

        $table->appendInside('thead', [], $head );
        $table->appendInside('tbody', [], $body );
        $table->appendInside('tfoot', [], $foot );

        echo $table;

    }

    public function pagination()
    {
        $pages = ceil($this->count / $this->limit);
        $item_word = 'items';

        if($this->count < 2) {
            $item_word = 'item';
        }

        $page = $this->paged;
        $previous_page = $this->paged - 1;
        $next_page = $this->paged + 1;

        if($this->page instanceof Page) {
            $next = $this->page->getUrl(['paged' => (int) $next_page]);
            $prev = $this->page->getUrl(['paged' => (int) $previous_page]);
        } else {
            parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query);
            $query_next = array_merge($query, ['paged' => (int) $next_page]);
            $query_prev = array_merge($query, ['paged' => (int) $previous_page]);
            $next = $_SERVER['PHP_SELF'] . '?' . http_build_query($query_next);
            $prev = $_SERVER['PHP_SELF'] . '?' . http_build_query($query_prev);
        }

        echo "<div class=\"tablenav bottom\">";
        echo "<div class=\"tablenav-pages\">
        <span class=\"displaying-num\">{$this->count} {$item_word}</span>
        <span class=\"pagination-links\">";
        if( $page < 2 ) {
            echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">&lsaquo;</span>";
        } else {
            echo "<a class=\"prev-page\" href=\"{$prev}\" aria-hidden=\"true\">&lsaquo;</a>";
        }
        echo " <span id=\"table-paging\" class=\"paging-input\">{$page} of <span class=\"total-pages\">{$pages}</span></span> ";
        if( $page < $pages ) {
            echo "<a class=\"next-page\" href=\"{$next}\"><span class=\"screen-reader-text\">Next page</span><span aria-hidden=\"true\">&rsaquo;</span></a>";
        } else {
            echo "<span class=\"tablenav-pages-navspan\" aria-hidden=\"true\">&rsaquo;</span>";
        }
        echo "</span></div></div>";
    }

}