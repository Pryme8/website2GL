<?php
$str_json = file_get_contents('php://input'); 
$data = json_decode($str_json, true);
echo file_get_contents($data['url']);
?>