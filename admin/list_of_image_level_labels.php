<?php
 require('includes/config.php');
 // error_reporting( error_reporting() );
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

   function display_dropdown($results,$collection_id){
     echo "<div align='center' style='margin-right: auto;  margin-left: auto;  max-width: 500px;clear:both;  '>";
     echo '<label>Choose a collection : &nbsp;</label>';
     echo "<select  id='collection_dropdown' name='collection_dropdown' >";
     echo "<option id='-1' selected=true value= 'select' >"."select"."</option>";

     while ($row = mysql_fetch_array($results)) {
       if($row['id'] == $collection_id){
         echo "<option id='".$row['id']."' selected=true value= '".$row['id']."' >".$row['id'] ." - ",$row['name']."</option>";
       }else{
         echo "<option id='".$row['id']."' value= '".$row['id']."' >".$row['id'] ." - ",$row['name']."</option>";
       }
     }
     echo "</select>";
     echo "<br/><button style='clear:both;' class='btn btn-primary pull-right' onClick='loadCollectionImages();'> Load Collection Image</button>";
     echo "</div>";

   }

   if(!isset($_GET['collection_id']) || (!isset($_GET['page']))){
     if(isset($_GET['collection_id']))
       $collection_id = $_GET['collection_id'];


     $query="SELECT distinct collections.id,name FROM collections";
     // echo $query;

     $results = mysql_query($query);
      if(isset($_GET['collection_id']))
        display_dropdown($results,$_GET['collection_id']);
      else
        display_dropdown($results,null);


   }

    if(isset($_GET['collection_id'])){
      $query="SELECT * FROM imageLevelValuesPerCollection
               JOIN collections on imageLevelValuesPerCollection.collection_id = collections.id
               JOIN images on imageLevelValuesPerCollection.image_id = images.imageID
               LEFT JOIN imageLabel on imageLabel.imageLabel_ID = imageLevelValuesPerCollection.field_id
               WHERE collections.id = ".$collection_id ;

      //echo $query."<br/>";
      $results_imageLevelsValues = mysql_query($query);
      $query_imageLabel="SELECT * FROM imageLabel WHERE collectionID = ".$collection_id ." ";
      $results_imageLabel = mysql_query($query_imageLabel);

      if (!$results_imageLevelsValues) { // add this check.
          die('Invalid query: ' . mysql_error());
      }

      if (!$results_imageLabel) { // add this check.
          die('Invalid query: ' . mysql_error());
      }

      $fieldTable = array();

      while ($row = mysql_fetch_array($results_imageLabel)) {
          // print_r ($row);
          // echo $row['imageLabel_ID'].",".$row["fieldName"]."<br/>";
          $fieldTable[$row['imageLabel_ID']] = $row["fieldName"];
      }
      $fieldTable_keys = array_keys($fieldTable);
      // print_r($fieldTable_keys);

      $numberOfRows = mysql_num_rows($results_imageLevelsValues);
      //echo "numberOfRows = ".$numberOfRows;
    }


   ?>

   <script>
   function downloadCSV(){
     $("table").first().table2csv();
   }
  </script>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-8">
        <?php  if(isset($_GET['collection_id']) && $numberOfRows>0){?>
         <h2>Image Level Values :</h2>
         <input type='button' class='btn btn-primary pull-right' onClick='downloadCSV();' value='Export CSV'/>
         <br/>
       <?php } ?>

               <?php
                 if(isset($_GET['collection_id']) && $numberOfRows>0){
                    $previous_image_id = '';
                    $rowStr = null;
                    $allRows = '';
                    $isSameAsPrevious = false;
                    $maxFieldValuePair = 0;
                    $image_records = array();
                    while ($row = mysql_fetch_array($results_imageLevelsValues)) {
                        $image_id = $row['image_id'];
                        $collection_id = $row['collection_id'];
                        $imageName = $row['imageName'];
                        $field_id = $row["imageLabel_ID"];
                        $field_value = $row["field_value"];
                        $image_records[$image_id][$field_id] = $field_value;
                    }
                      $i =0 ;
                      $array_keys = array_keys($image_records);
                      $body_rows_multiple = "";
                      $body_row = "";
                      for ($i=0; $i< count($image_records); $i++){

                            $body_row = "<td>".$array_keys[$i]."</td>";
                            for ($j=0; $j< count($fieldTable_keys); $j++){

                              $value = isset($image_records[$array_keys[$i]][$fieldTable_keys[$j]]) ? $image_records[$array_keys[$i]][$fieldTable_keys[$j]] : '';
                                if(!$value){
                                    $body_row .= "<td>  </td>";
                                }
                                else {
                                  $body_row .= "<td>" . $value."</td>";
                                }
                            }
                            $body_rows_multiple .="<tr>".$body_row."</tr>";
                      }
                      $header = "<tr><th>"."Image ID"."</th>";
                      for ($j=0; $j< count($fieldTable_keys); $j++){
                        if($fieldTable[$fieldTable_keys[$j]])
                          $header .="<th>".$fieldTable[$fieldTable_keys[$j]]."</th>";
                      }
                      $header .="</tr>";
                      ?>
                      <table class='table col-xs-6' border='1' >
                         <tbody>
                           <?php
                           echo  $header.$body_rows_multiple;
                           ?>
                      </tbody>
                    </table>
                    <?php
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


   <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
   <script>
   function loadCollectionImages(){
     var value = $('#collection_dropdown option:selected').val();
     console.log(value);
     if(value != 'select')
        location.href='list_of_image_level_labels.php?collection_id='+value;
   }

   </script>
