<?php
 require('includes/config.php');
   //if not logged in redirect to login page
   if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

   //define page title
   $title = 'Admin Page';


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
         <h2>List of Users:</h2>
         <table class='table col-xs-6' border='1' >
            <tr>Username </tr>
            <tbody>
               <?php
                  while ($row = mysql_fetch_array($results)) {
                      echo '<tr>';
                      // foreach($row as $field) {
                      //     echo '<td>' . htmlspecialchars($field['']) . '</td>';
                      // }
                      echo '<td>' .$row['username'].'</td>';
                      echo '</tr>';
                  }
                  ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<?php
   //include header template
   require('layout/footer.php');
   ?>
