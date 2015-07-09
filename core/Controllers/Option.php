<?php

namespace TypeRocket\Controllers;

class Option extends Controller
{

    function validate()
    {
        parent::validate();
        $this->valid = apply_filters( 'tr_option_validate', $this->valid, $this );

        if( ! current_user_can( 'manage_options')) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        if( ! check_ajax_referer( 'form_' . TR_SEED, '_tr_nonce_form', false )) {
            $this->valid = false;
            $this->response['message'] = 'Invalid CSRF Token';
        }

        return $this->valid;
    }

    function sanitize()
    {
        parent::sanitize();
        $this->fields = apply_filters( 'tr_option_sanitize', $_POST['tr'], $this );
    }

    function save( $item_id, $action = 'update' )
    {
        parent::save( $item_id, $action );

        return $this;
    }

    protected function update()
    {
        if (is_array( $this->fields )) :
            foreach ($this->fields as $key => $value) :

                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_meta = get_option( $key );

                if (( isset( $value ) && $value !== "" ) && $current_meta !== $value) :
                    update_option( $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_meta ) || $current_meta === "" )) :
                    delete_option( $key );
                endif;

            endforeach;
        endif;
    }

    protected function create()
    {
        $this->update();
    }

}

//
//if (!empty($_POST['title']) && !empty($_POST['email'])) {
//    $args = [$_POST['title'], $_POST['content'], json_encode($_POST), 'article', 'published', $args[2]['id']];
//
//    $db = (new Treenode\Service\Database());
//    $db->link();
//    $db->query('UPDATE nodes SET title=?, content=?, meta=?, kind=?, status=? WHERE id=?', $args);
//    echo json_encode(['errors' => false, 'message' => 'Updated node!']);
//} else {
//    echo json_encode([
//        'errors' => true,
//        'message' => 'Fields required!',
//        'required' => ['title' => 'You need a name', 'email' => 'You need an email']
//    ]);
//}