<?php

require('includes/config.php');
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }


   $location = "localhost";
   $user = "root";
   $pass = "";

   //Database Selection
   $link = mysql_connect($location, $user, $pass);
   if (!$link) {
       die('Could not connect: ' . mysql_error());
   }

    mysql_select_db('labelme', $link) or die(mysql_error());


function getFieldID($imageID,$collection_id, $fieldName, $fieldValue){
  $query="SELECT * FROM imageLabel WHERE collectionID = '".$collection_id ."' and fieldName = '". $fieldName."'";
  //echo $query."<br/>";
  // $results_imageLabel = mysql_query($query);
  // $row = mysql_fetch_assoc($results_imageLabel);

  $result = mysql_query($query) or die(mysql_error());
  $row = mysql_fetch_assoc($result);
//  print_r($row);
//  echo $row. "<br/>";
  return $row['imageLabel_ID'];

}

function insertInTable_imageLevelValuesPerCollection($imageID, $fieldName, $fieldValue, $collection_id, $db) {

    // echo $imageID.",".$fieldName.",".$fieldValue.",".$_SESSION['username'].",".$collection_id ;

    // echo "<br/>";
    try {

      $field_id = getFieldID($imageID,$collection_id, $fieldName, $fieldValue);
      // echo "field_id = ".$field_id."<br/>";
      $username = $_SESSION['username'];
        //insert into database with a prepared statement
        $stmt = $db->prepare('INSERT INTO imageLevelValuesPerCollection (field_id,field_name, image_id, field_value, collection_id, username) VALUES (:field_id, :fieldName, :imageID, :fieldValue, :collection_id,:username)');
        $stmt->execute(array(
          'field_id' =>$field_id,
          ':fieldName' =>  $fieldName,
          ':imageID' => $imageID,
          ':fieldValue' => $fieldValue,
          ':collection_id' => $collection_id,
          ':username' => $username
        ));
        $id = $db->lastInsertId('id');
        // $successMsg = "Record added successfully !";

        //else catch the exception and show the error.
      } catch(PDOException $e) {
          $error[] = $e->getMessage();
          echo $error[0];
      }
}

$imageIDArray = array();
$fieldNameArray = array();
$fieldValueArray = array();

foreach($_POST as $key=>$value){
  if($key!='submit'){
    // echo $key.",". $value;
    // echo "<br/>";

    if (strpos($key, 'imageID_') !== false) {
        // echo 'true';
        $key = str_replace("imageID_","",$key);
        array_push($imageIDArray,  $key);
    }
    else if (strpos($key, 'collection_id') !== false) {
        // echo 'true';
        $collection_id = $value;
    }
    else if (strpos($key, 'page') !== false) {
          // echo 'true';
          $page = $value;
      }else{
        $key = str_replace("_"," ",$key);
        array_push($fieldNameArray,  $key);
        array_push($fieldValueArray,  $value);
    }
  }
}

foreach($imageIDArray as $imageID){
  $index = 0;
  foreach($fieldNameArray as $fieldName){
    insertInTable_imageLevelValuesPerCollection($imageID,$fieldName, $fieldValueArray[$index], $collection_id, $db);
    $index++;
  }
}

    header("Location: http://localhost/labelme/view_images_collection_wise.php?status=success&collection_id=".$collection_id."&page=".$page);


?>
