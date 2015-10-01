<?php
function exec_enabled() {
  $disabled = explode(',', ini_get('disable_functions'));
  return !in_array('shell_exec', $disabled);
}

var_dump(exec_enabled());
?>