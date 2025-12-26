<?php



function redirect($path) {
     header('Location: ' . APP_URL . '/' . ltrim($path, '/')); 
     die;
 }
 
 function back() {
    header("Location: {$_SERVER['HTTP_REFERER']}");
 }