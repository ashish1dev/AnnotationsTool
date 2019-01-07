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

   $query="SELECT * FROM imageLabel   INNER JOIN   collections   ON imageLabel.collectionID = collections.ID";
   $results = mysql_query($query);

   if (!$results) { // add this check.
       die('Invalid query: ' . mysql_error());
   }


   ?>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8  ">
         <h2>List of Image Label(s):</h2>
         <table class='table col-xs-6' border='1' >
            <tr>
              <th>Image Label ID </th>
              <th>collection ID </th>
              <th>Name</th>
              <th>Field Type </th>
              <th>Field Name </th>
              <th>Field Value </th>
              <!-- <th></th> -->
            </tr>
            <tbody>
               <?php
                  while ($row = mysql_fetch_array($results)) {
                      echo '<tr>';
                        echo '<td>' .$row['imageLabel_ID'].'</td>';
                        echo '<td>' .$row['collectionID'].'</td>';
                        echo '<td>' .$row['name'].'</td>';
                        echo '<td>' .$row['fieldType'].'</td>';
                        echo '<td>' .$row['fieldName'].'</td>';
                        echo '<td>' .$row['fieldValue'].'</td>';
                      ?>
                      <!-- <td> <button type="button" class="btn btn-primary" onClick='deleteImagelabel();'>delete</button></td> -->

                      <?php
                      echo '</tr>';
                  }
                  ?>
            </tbody>
        </table>
        <br/>
      </div>
   </div>
</div>

<script>
function deleteImagelabel(){
  alert('deleteImagelabel...');
}
</script>
<?php
   //include header template
   require('layout/footer.php');
   ?>
