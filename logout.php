THIS SHOULD BE A LINTER ERROR<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
