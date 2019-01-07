<?php require('includes/config.php');
   //if not logged in redirect to login page
   if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

   //include header template
   require('layout/header.php');

   function checkIfExists($collection_id,$member_id ){
     $location = "localhost";
     $user = "root";
     $pass = "";

     //Database Selection
     $link = mysql_connect($location, $user, $pass);
     if (!$link) {
         die('Could not connect: ' . mysql_error());
     }
     mysql_select_db('labelme', $link) or die(mysql_error());
     $query="SELECT * FROM member_collection where member_id = '".$member_id."' and collection_id = '".$collection_id."' ";
     $results = mysql_query($query);

     if (!$results) { // add this check.
         die('Invalid query: ' . mysql_error());
     }

     $total_rows = mysql_num_rows($results);
    // echo "Total rows: " . $total_rows;
     if($total_rows>0)
      return true;

     return false;
   }

   function findAllRecordsForCollectionName($collectionName){

     $location = "localhost";
     $user = "root";
     $pass = "";

     //Database Selection
     $link = mysql_connect($location, $user, $pass);
     if (!$link) {
         die('Could not connect: ' . mysql_error());
     }
     mysql_select_db('labelme', $link) or die(mysql_error());

      $query="SELECT id FROM collections where name  LIKE '%".$collectionName."/%'";
      $results = mysql_query($query);

      if (!$results) { // add this check.
          die('Invalid query: ' . mysql_error());
      }
      $collectionIDArray = array();
      while ($row = mysql_fetch_array($results)) {
          array_push($collectionIDArray, $row['id']);
      }
      return $collectionIDArray;
   }

   function insertInTable($collection_id,$member_id, $db) {

       try {
           //insert into database with a prepared statement
           $stmt = $db->prepare('INSERT INTO member_collection (member_id,collection_id) VALUES (:member_id, :collection_id)');
           $stmt->execute(array(
             ':member_id' => $member_id,
             ':collection_id' => $collection_id
           ));
           $id = $db->lastInsertId('id');
           $successMsg = "Record added successfully !";

           //else catch the exception and show the error.
         } catch(PDOException $e) {
             $error[] = $e->getMessage();
             echo $error[0];
         }

   }

   //if form has been submitted process it
   if(isset($_POST['submit'])){

       if (!isset($_POST['member_id'])) $error[] = "Please fill out all fields";
       if (!isset($_POST['collection_id'])) $error[] = "Please fill out all fields";

      $member_id = $_POST['member_id'];
      $collection_id = $_POST['collection_id'];
       if($collection_id != '-1' && $member_id != '-1'){
          // echo $member_id;
          // echo "collection_id = ";
          // echo $collection_id;
          // echo "<br/> ";
          $pos = strpos($collection_id,'ALL_');
          //echo $pos;
          if($pos === 0){
              // echo "pos is >=0 ";
              $collectionName = substr($collection_id, $pos+4);
              // echo "found all for : ".$collectionName.";";
              $collectionIDArray = findAllRecordsForCollectionName($collectionName);
              foreach ($collectionIDArray as $collectionID) {
                $alreadyExists = checkIfExists($collectionID,$member_id);
                if($alreadyExists == false){
                    insertInTable($collectionID, $member_id, $db);
                }
              }
          }
          elseif($pos === false){
            // echo "pos is false";
            $alreadyExists = checkIfExists($collection_id,$member_id);
            if($alreadyExists == false){
                insertInTable($collection_id, $member_id, $db);
            }
          }


          // if($pos==0){
          //   echo "pos=".$pos."<br/>----<br/>";
          //
          //   $collectionName = substr($collection_id, $pos+4);
          //   echo "found all for : ".$collectionName.";";
          //   $collectionIDArray = findAllRecordsForCollectionName($collectionName);
          //
          //   foreach ($collectionIDArray as $collectionID) {
          //     insertInTable($collectionID, $member_id, $db);
          //   }
          // }else{
          //   insertInTable($collection_id, $member_id, $db);
          // }
        }else{
            $errorMsg =  "Please select from both username and collection names.";
        }
  }

   //define page title
   $title = 'Admin Page';

   $location = "localhost";
   $user = "root";
   $pass = "";

   //Database Selection
   $link = mysql_connect($location, $user, $pass);
   if (!$link) {
       die('Could not connect: ' . mysql_error());
   }

   mysql_select_db('labelme', $link) or die(mysql_error());

   $userQuery="SELECT * FROM members";
   $userResults = mysql_query($userQuery);

   $collectionsQuery="SELECT * FROM collections";
   $collectionsResults = mysql_query($collectionsQuery);

   if (!$userResults) { // add this check.
       die('Invalid query: ' . mysql_error());
   }

   if (!$collectionsQuery) { // add this check.
       die('Invalid query: ' . mysql_error());
   }

    //read all assigned user and colllection names;
    $member_collection_query="SELECT * FROM member_collection JOIN members ON member_collection.member_id = members.memberID JOIN collections ON member_collection.collection_id = collections.id";
    $member_collection_results = mysql_query($member_collection_query);

    if (!$member_collection_results) { // add this check.
        die('Invalid query: ' . mysql_error());
    }


   ?>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8"  >
         <h2>Assign Collection to User:</h2>
         <form role="form" method="post" action="" autocomplete="off"  >
            <?php if(isset($errorMsg)){?>
             <h3><span class="form-control label label-danger"><?php echo $errorMsg; ?></span></h3>
            <?php } ?>
            <?php if(isset($successMsg)){?>
             <h3><span class="form-control label label-success"><?php echo $successMsg; ?></span></h3>
            <?php } ?>
            <div class="input-group">
               <span class="form-control label label-primary">UserName</span>
               <select class="form-control" id="member_id" name ="member_id">
                 <option id='-1' value ='-1' >Select...</option>
                 <?php
                    while ($row = mysql_fetch_array($userResults)) {
                         echo "<option id = '".$row['memberID']."' value = '".$row['memberID']."'>" .$row['username']."</option>";
                    }
                ?>
               </select>


               <span class="input-group-addon">-</span>
               <span class="form-control label label-primary">Collection Name</span>
               <select class="form-control" id="collection_id" name ="collection_id">
                 <option id='-1' value ='-1'>Select...</option>
                 <?php

                  $collectionNameArray = array();
                  while ($row = mysql_fetch_array($collectionsResults)) {

                      $pos = strpos($row['name'], '/');

                      if($pos != false){
                        echo "sub collection exists";
                        $collectionName  = substr($row['name'],0,$pos);
                        // $subName = substr($row['name'],$pos+1);
                        echo $collectionName;
                        echo '1';
                        if(!in_array($collectionName,$collectionNameArray )){
                            array_push($collectionNameArray, $collectionName);
                            echo "<option id = 'ALL_".$collectionName."'  value = 'ALL_".$collectionName."'>" .'ALL_'.$collectionName."</option>";

                        }

                      }
                      echo "<option id = '".$row['id']."'  value = '".$row['id']."'>" .$row['name']."</option>";
                  }


                  ?>
               </select>
               <!-- <span class="input-group-addon">-</span>
               <span class="form-control label label-primary">Sub Collection Name</span>
               <select class="form-control" id="sub_collection_id" name ="sub_collection_id">
                 <option id='-1' value ='-1'>Select...</option>
                 <?php

                  // foreach ($subCollectionName as $value) {
                  //      echo "<option id = '".$value."'  value = '".$value."'>". $value."</option>";
                  // }

                  ?>
               </select> -->


            </div>
            <br/>
            <input type="submit" name="submit" class="btn btn-default pull-right" value="Submit Button">
         </form>
      </div>
   </div>
</div>


<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8  ">
         <h2>Assigned Collection Names:</h2>
         <table class='table col-xs-6' border='1' >
           <th> Username </th>
            <th>Collection Names </th>
            <tbody>
               <?php
                  while ($row = mysql_fetch_array($member_collection_results)) {
                    	$folderName = str_replace('@*@','/',$row['name']);
                      echo '<tr>';
                      echo '<td>' .$row['username'].'</td>';
                      echo '<td>' .$folderName.'</td>';
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
