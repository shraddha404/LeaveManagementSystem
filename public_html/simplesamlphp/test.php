<?php
echo "xxx";
exit;
$auth = new SimpleSAML_Auth_Simple('default-sp');
if (!$auth->isAuthenticated()) {
    /* Show login link. */
    print('<a href="/login">Login</a>');
}

?>
