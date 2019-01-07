<?php require('includes/config.php');
   //if not logged in redirect to login page
   if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

   //define page title
   $title = 'Admin Page';


   //include header template
   require('layout/header.php');
   ?>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8  ">
      </div>
   </div>
</div>
<?php
   //include header template
   require('layout/footer.php');
   ?>
