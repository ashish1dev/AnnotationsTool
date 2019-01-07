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

   $query="SELECT * FROM annotations order by created_at DESC";
   $results = mysql_query($query);

   if (!$results) { // add this check.
       die('Invalid query: ' . mysql_error());
   }


   ?>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8  ">
         <h2>List of Annotation:</h2>
         <table class='table col-xs-6' border='1' >
            <tr>
            <th>id</th>
            <th>name </th>
            <th>attributes </th>
            <th>occluded </th>
            <th>username </th>
            <th>Collection Name</th>
            <th>dirName </th>
            <th>imageName </th>
            <th>Open Image as Admin </th>
          </tr>
            <tbody>
               <?php
                  while ($row = mysql_fetch_array($results)) {
                      echo '<tr>';
                      // foreach($row as $field) {
                      //     echo '<td>' . htmlspecialchars($field['']) . '</td>';
                      // }
                      echo '<td>' .$row['id'].'</td>';
                      echo '<td>' .$row['name'].'</td>';
                      echo '<td>' .$row['attributes'].'</td>';
                      echo '<td>' .$row['occluded'].'</td>';
                      echo '<td>' .$row['username'].'</td>';
                      echo '<td>' .$row['collectionName'].'</td>';
                      echo '<td>' .$row['dirName'].'</td>';
                      echo '<td>' .$row['imageName'].'</td>';
                      //echo '<td>' ."<a href='' target='_blank'>".'View as Admin'.'</a>'.'</td>';
                      $folderName = $row['dirName'];
                      $imageName = $row['imageName'];
                       echo '<td>'."<a target='_blank' href='http://localhost/labelme/tool.html?collection=".$row['collectionName']."&mode=c&folder=".$folderName."&image=".$imageName."&username=".$_SESSION['admin_username']."'>View As Admin</a>".'</td>';

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
