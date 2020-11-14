<?php
//require_once (dirname(__FILE__) . '/../simplesamlphp/lib/_autoload.php');
require_once (dirname(__FILE__) . '/lib/_autoload.php');
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth();
 
$attributes = $as->getAttributes();
$attributes = array(
'1'=>array('admin'),
'2'=>array('adminketan404')
);
$loginname = $attributes[1][0];
$password = $attributes[2][0];
//echo $loginname;
//echo $password;
 
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title>Index Page</title>
</head>
<body>
    <h2>Index Page</h2>
    <h3>Welcome <strong>Authenticated User</strong>!</h3>
    <h4>Claim list:</h4>
<?php
echo '<pre>';
print_r($attributes);
echo '</pre>';
 
// Get a logout URL
$url = $as->getLogoutURL();
echo '<a href="' . htmlspecialchars($url) . '">Logout</a>';
 

?>
</body>
</html>
