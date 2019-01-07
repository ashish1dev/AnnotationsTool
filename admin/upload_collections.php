<?php require('includes/config.php');
   ini_set( 'memory_limit', '128M' );
   ini_set('upload_max_filesize', '128M');
   ini_set('post_max_size', '128M');
   ini_set('max_input_time', 3600);
   ini_set('max_execution_time', 3600);

   //phpinfo();

   //if not logged in redirect to login page
   if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

   //define page title
   $title = 'Admin Page';


   $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   //include header template
   require('layout/header.php');
   $location = "localhost";
   $user = "root";
   $pass = "";

   //Database Selection
   $link = mysql_connect($location, $user, $pass);
   if (!$link) {
       die('Could not connect: ' . mysql_error());
   }

   mysql_select_db('labelme', $link) or die(mysql_error());

   $query="SELECT * FROM members";
   $results = mysql_query($query);

   if (!$results) { // add this check.
       die('Invalid query: ' . mysql_error());
   }
   ?>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8  ">
         <form action="collectionUpload.php" method="post" enctype="multipart/form-data">
            <h2>Upload A Collection (Zipped Dataset):</h2>
            <div class="form-group">
               <input type="file" name="myfile" class="btn btn-default" id="collectionToUpload">
            </div>
            <div class="form-group">
               <input type="submit" name="submit"  class="btn btn-primary"   value="Upload Image Collection Now" >
            </div>
         </form>
      </div>
   </div>
</div>
<?php
   //include footer template
   require('layout/footer.php');
   ?>
