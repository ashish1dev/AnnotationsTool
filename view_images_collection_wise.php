<?php
require('includes/config.php');
ini_set('memory_limit', '128M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 3600);
ini_set('max_execution_time', 3600);


//if not logged in redirect to login page
if (!$user->is_logged_in()) {
    header('Location: login.php');
    exit();
}


//define page title
$title = 'View Images';

$actual_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//include header template
require('layout/header.php');
$location = "localhost";
$user     = "root";
$pass     = "";

if(isset($_GET['status'])){
  echo "<h2 style='color:green;'>Image label Saved successfully !</h2>";
}

//Database Selection
$link = mysql_connect($location, $user, $pass);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db('labelme', $link) or die(mysql_error());

$query   = "SELECT distinct collections.id,name FROM collections JOIN member_collection ON collections.id = member_collection.collection_id";
$results = mysql_query($query);

if (!$results) { // add this check.
    die('Invalid query: ' . mysql_error());
}

function get_list_of_image_labels($image_id){
  $query="SELECT * FROM imageLevelValuesPerCollection
           JOIN collections on imageLevelValuesPerCollection.collection_id = collections.id
           JOIN images on imageLevelValuesPerCollection.image_id = images.imageID
           where imageLevelValuesPerCollection.image_id = '".$image_id."'
           order by collection_id, image_id";

           // echo $query;

  $results = mysql_query($query);
  $str = "<p style='font-size:70%;'>";
  // $row = mysql_fetch_row($results);
  while ($row = mysql_fetch_array($results)) {
    if($row['field_value']!="")
      $str .="<b>".$row['field_name']." :</b> ".$row['field_value']."; ";
  }
  $str .= '<p></b>';
  return $str;
}

function display_dropdown($results, $collection_id) {
    echo "<div align='center' style='margin-right: auto;  margin-left: auto;  max-width: 700px;clear:both;  '>";
    echo '<label>Choose a collection : &nbsp;</label>';
    echo "<select  id='collection_dropdown' name='collection_dropdown' >";
    echo "<option id='-1' selected=true value= 'select' >"."select"."</option>";

    while ($row = mysql_fetch_array($results)) {
        if ($row['id'] == $collection_id) {
            echo "<option id='" . $row['id'] . "' selected=true value= '" . $row['id'] . "' >" . $row['id'] . " - ", $row['name'] . "</option>";
        } else {
            echo "<option id='" . $row['id'] . "' value= '" . $row['id'] . "' >" . $row['id'] . " - ", $row['name'] . "</option>";
        }
    }
    echo "</select>";
    echo "<button style='float:right;' class='btn btn-primary' onClick='loadCollectionImages();'> Load Collection Image</button>";
    echo "</div>";

}

if (!isset($_GET['collection_id']) || (!isset($_GET['page']))) {
    display_dropdown($results, null);

} else {

    $collection_id = $_GET['collection_id'];
    display_dropdown($results, $collection_id);
    echo '<b>&nbsp; &nbsp;  show images of collection_id : ' . $collection_id;

    $page = $_GET['page'];
    echo ",<br/> &nbsp; &nbsp; viewing page : " . $page."</b>";
    $number_of_images_per_page = 16;

    $query_imageLabel   = "SELECT * FROM imageLabel where collectionID = '" . $collection_id . "'";
    $results_imageLabel = mysql_query($query_imageLabel);
    if (!$results_imageLabel) { // add this check.
        die('Invalid query: ' . mysql_error());
    }


    $start_id = ($page - 1) * $number_of_images_per_page;

    // echo 'start_id = '.$start_id;
    // echo 'end_id = '.$end_id;
    $query_images   = "SELECT * FROM images where collectionID = '" . $collection_id . "' LIMIT " . $start_id . "," . $number_of_images_per_page;
    $results_images = mysql_query($query_images);
    if (!$results_images) { // add this check.
        die('Invalid query: ' . mysql_error());
    }

    //
    // echo "<br/>";
    $htmlElement = "<div>";
    // $htmlElement = $htmlElement . "<form action='submit_image_label_values.php' method='POST'>";

    while ($row = mysql_fetch_array($results_imageLabel)) {
        // echo $row['fieldType'] . "," . $row["fieldName"] . "," . $row['fieldValue'];
        // echo "<br/>";
        if ($row['fieldType'] == '0') {
            $htmlElement = $htmlElement . "<br/><label id='".$row['fieldName']."' name='".$row['fieldName']."' value='".$row['fieldName']."' >".$row['fieldName']."</label>";
            $htmlElement = $htmlElement . "<input type='text' id='".$row['fieldName']."' name='".$row['fieldName']."'/>";
        } else if ($row['fieldType'] == '1') {
            $arr         = explode(",", $row['fieldValue']);
            $htmlElement = $htmlElement . "<br/><label id='".$row['fieldName']."' name='".$row['fieldName'] ."' value='" . $row['fieldName'] . "' >" . $row['fieldName'] . "</label>";
            $htmlElement = $htmlElement . "<select id='".$row['fieldName']."' name='".$row['fieldName']."'>";
            foreach ($arr as &$value) {
                $value       = str_replace(",","",$value);
                $value       = trim($value);
                $htmlElement = $htmlElement . "<option>" . $value . "</option>";
            }
            $htmlElement = $htmlElement . "</select>";
        }
    }
    if (mysql_num_rows($results_imageLabel) > 0) {
        $htmlElement = $htmlElement . "<input type='text' id='collection_id' name='collection_id' style='visibility:hidden;' value='".$collection_id."'/>";
        $htmlElement = $htmlElement . "<input type='text' id='page' name='page' style='visibility:hidden;' value='".$page."'/>";

        $htmlElement = $htmlElement . "<input type='submit' name='submit' class='btn btn-default pull-right' value='Submit'/>";
    }
    $htmlElement = $htmlElement . "</form>";
    $htmlElement = $htmlElement . "</div>";

    echo "<br/>";
    echo "<div align='center'>";
    if ($page > 1) {
        echo "<a class='previous' href='http://localhost/labelme/view_images_collection_wise.php?collection_id=" . $collection_id . "&page=" . ($page - 1) . "'>&#8249; PREV </a>";
    }
    echo "<a class='next' href='http://localhost/labelme/view_images_collection_wise.php?collection_id=" . $collection_id . "&page=" . ($page + 1) . "'>NEXT &#8250; </a>";
    echo "</div>";
    echo "<section class='container'>";
    echo "<form action='submit_image_label_values.php' method='POST'>";
    while ($row = mysql_fetch_array($results_images)) {
        //  echo $row['thumbnailPath'];
        $imageID = $row['imageID'];
        $position = strpos($row['thumbnailPath'], 'htdocs/') + 7;
        $path     = substr($row['thumbnailPath'], $position);
        $imgSrc   = "http://localhost/" . $path;

        $position     = strpos($row['thumbnailName'], '_') + 1;
        $justFileName = substr($row['thumbnailName'], $position);

        $imageLabels = get_list_of_image_labels($imageID);
        echo "<div class='one center' style='margin:0px;padding:5px'>";
        // echo ""
        echo "<img   src='" . $imgSrc . "' width='80px'   />";
        // echo "<br/>";
        echo "<p style='margin:0px;padding:5px' >".$imageLabels."</p>";
        echo "<input  style='font-size:70%;' class='bottom' type='checkbox' id='imageID_".$imageID."' name='imageID_".$imageID."'  value='" . $justFileName . "'/> &nbsp;" . $justFileName;
        echo "</div>";
    }
    echo "</section>";

    echo "<section class='containerRight'>";
    echo "Form";
    echo $htmlElement;
    echo "</section>";

    $currentDir      = getcwd();
    //   $uploadDirectory = "/../Images/CollectionsDataset";
    $uploadDirectory = "/Images";


    $path        = $currentDir . $uploadDirectory;
    $directories = glob($path . '/*', GLOB_ONLYDIR);
    //echo $actual_link . "<br/>";
    $dir_Url     = $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
    //echo $dir_Url . "<br/>";



}



//include header template
require('layout/footer.php');
?>

   <style>
     .container {
         width: 75%;
         /* height: 200px; */
         /* background: aqua; */
         margin: auto;
         float:left;
         padding: 10px;
     }
     .containerRight {
         width: 20%;
         margin: auto;
         /* height: 200px; */
         background: rgb(211,211,211);
         float:left;
         margin: auto;
         padding: 10px;
     }
     .one {
         width: 25%;
         height: 150px;
         /* background: red; */
         border: 1px black solid;
         float: left;
     }
     .two {
         margin-left: 15%;
         height: 200px;
         background: black;
     }
     .bottom{
       vertical-align: text-bottom;
     }
     .center {
        margin: auto;
        text-align: center;
        padding: 20px 0;
        /* width: 50%; */
        /* border: 3px solid green; */
        /* padding: 1px; */
    }
    .previous {
        background-color: #4CAF50;
        color: black;
    }

    .next {
        background-color: #4CAF50;
        color: white;
    }
    a {
        text-decoration: none;
        display: inline-block;
        padding: 8px 16px;
    }

    a:hover {
        background-color: #ddd;
        color: black;
    }
   </style>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
   <script>
   function loadCollectionImages(){
     var value = $('#collection_dropdown option:selected').val();
     console.log(value);
     if(value !='select')
      location.href='view_images_collection_wise.php?collection_id='+value+"&page="+1;
   }
   </script>
