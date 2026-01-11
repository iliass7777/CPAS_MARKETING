<?php
$host = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
// domain name
$host .= $_SERVER['HTTP_HOST'];
// request uri
$uri = $_SERVER['REQUEST_URI'];
// base url
$base_url = $host . dirname($uri) . "/";
?>