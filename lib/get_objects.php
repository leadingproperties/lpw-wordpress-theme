<?php
$objects = new LP_ObjectList();
header('Content-Type: application/json');
echo $objects->get_json_objects();
