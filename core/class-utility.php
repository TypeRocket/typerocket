<?php

class tr_utility extends tr_base {

  public $buffering = false;
  public $buffer = array();

  function buffer($index = null) {

    $this->sanitize_string($index);

    if($this->buffering === false ) {
      if(isset($index) && $index !== '') {
        die('Starting buffer... Index when the buffer ends.');
      }
      ob_start();
      $this->buffering = true;
    }
    else {
      $this->check($index, 'Ending buffer... add an index.');
      $data = ob_get_clean();
      $this->buffer[$index] = $data;
      $this->buffering = false;
    }

  }

}