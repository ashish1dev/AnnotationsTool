<?php require('includes/config.php');
?>

<script type="text/javascript" src="./annotationTools/js/sign_in.js" ></script>
<script type="text/javascript" src="./annotationTools/js/globals.js" ></script>
<script type="text/javascript" src="./annotationTools/js/browser.js" ></script>

<?php

//logout
$user->logout();
//logged in return to index page
header('Location: index.php');
exit;
?>
