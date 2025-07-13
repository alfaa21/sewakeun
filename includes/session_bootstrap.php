<?php
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 86400);
    session_set_cookie_params(86400, "/");
    session_start();
}
?> 