<?php
require_once('functions.php');

session_destroy();
header('Location: index.php');
alert_message('success', 'Logged out successfully.');
exit;
