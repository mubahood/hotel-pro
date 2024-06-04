<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function text_input($param){
    return "some data";
}