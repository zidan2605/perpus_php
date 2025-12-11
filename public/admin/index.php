<?php
// Redirect to actual admin folder
header('Location: ../../admin/index_admin.php' . (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
?>


