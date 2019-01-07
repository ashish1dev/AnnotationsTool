<?php
require('includes/config.php');
//if not logged in redirect to login page
if (!$user->is_logged_in()) {
    header('Location: login.php');
    exit();
}

//define page title
$title = 'Create Image Label Field';

//include header template
require('layout/header.php');

$location = "localhost";
$user     = "root";
$pass     = "";

//Database Selection
$link = mysql_connect($location, $user, $pass);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db('labelme', $link) or die(mysql_error());

function create_DB_entry_for_ImageLabel($collectionID, $fieldType, $fieldName, $fieldValue, $db)
{

    // fieldType = ['1','1','0'];
    // fieldName = ['Main Type','Second Type','Description'];
    // fieldValue = ['Single panel, Multi Panel','Line Chart, Pie Chart, Bar Chart','',];
    try {
        //insert into database with a prepared statement
        $stmt = $db->prepare('INSERT INTO imageLabel (collectionID, fieldType, fieldName, fieldValue) VALUES (:collectionID, :fieldType, :fieldName, :fieldValue)');
        $stmt->execute(array(
            ':collectionID' => $collectionID,
            'fieldType' => $fieldType,
            'fieldName' => $fieldName,
            'fieldValue' => $fieldValue
        ));

        // $id = $db->lastInsertId('id');
        $successMsg = "Record added successfully !";
        // return $id;
        //else catch the exception and show the error.
    }
    catch (PDOException $e) {
        $error[] = $e->getMessage();
        echo $error[0];
    }
}

if (isset($_GET['collection_id'])) {
    $collectionID = $_GET['collection_id'];
    // echo $collectionID;
}

function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}

//if form has been submitted process it
if (isset($_POST['submit'])) {

    // foreach ($_POST as $key => $value) {
    //     if (htmlspecialchars($key) != 'type' && htmlspecialchars($key) != 'submit')
    //         echo "" . htmlspecialchars($key) . " => " . htmlspecialchars($value) . "<br>";
    //     if (contains("textField_", htmlspecialchars($key))) {
    //         $fieldType  = '0';
    //         $fieldName  = htmlspecialchars($value);
    //         $fieldValue = "";
    //         create_DB_entry_for_ImageLabel($collectionID, $fieldType, $fieldName, $fieldValue, $db);
    //     } else if (contains("multiValuedField_", htmlspecialchars($key))) {
    //         $fieldType  = '1';
    //         $fieldName  = htmlspecialchars($value);
    //         $fieldValue = htmlspecialchars($value);
    //         create_DB_entry_for_ImageLabel($collectionID, $fieldType, $fieldName, $fieldValue, $db);
    //     }
    // }
    print_r($_POST);
    if (isset($_POST['textField_0'])){
      $fieldType  = '0';
      $fieldName  = $_POST['textField_0'];
      $fieldValue = "";
      create_DB_entry_for_ImageLabel($collectionID, $fieldType, $fieldName, $fieldValue, $db);
    }

    if (isset($_POST['multiValuedField_0'])){
      $fieldType  = '1';
      $fieldName  = $_POST['multiValuedField_0'];
      $fieldValue  = $_POST['multiValuedFieldValues_0'];
      create_DB_entry_for_ImageLabel($collectionID, $fieldType, $fieldName, $fieldValue, $db);
    }

    // if (!isset($_POST['username'])) $error[] = "Please fill out all fields";
    // if (!isset($_POST['email'])) $error[] = "Please fill out all fields";
    // if (!isset($_POST['password'])) $error[] = "Please fill out all fields";
}

$collectionsQuery   = "SELECT * FROM collections where id='" . $collectionID . "'";
// echo $collectionsQuery;
$collectionsResults = mysql_query($collectionsQuery);


if (!isset($_GET['collection_id'])) {
    echo "collection id not set";
?>
    <script>
      alert("error: collection id not set");
     </script>
     <?php
}
?>
  <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-10 ">
            <hr>
            <form id="fieldForm" name="fieldForm" role="form" method="post" action="" autocomplete="off">
               <h2> New Image Label Field : </h2>
               </p>
               <hr>

             <div class="col-sm-10">
                  <h4><label>Collection Id: </label>
                   <?php
$row = mysql_fetch_array($collectionsResults);
echo " " . $row['id'] . " <br/><label> Name : </label> " . $row['name'] . "</h4>";
?>
            </div>

             <div class="row">
               <div class="col-sm-4" style='color:blue;'>
                  <label><i>*****Add field by choosing Type from this dropdown*****</i></label>
                  <br/><label> Type of Field</label>
                   <select class="form-control" id="type" name ="type">
                      <option value="default" selected="selected">select...</option>
                      <option id='0' value ='0'>0 (dropdown field)</option>
                      <option id='1' value ='1'>1 (text field)</option>
                   </select>

               </div>
               <input type="button" value="Reset" class="btn btn-warning pull-right col-sm-1" onClick="refreshPage();"/>
             </div>

               <div id = 'dynamicSection' class="row form-group" name = 'dynamicSection'>
               </div>

              <div class="row">
                  <div class="col-xs-6 col-md-4">
                     <input type="submit" name="submit" value="Submit" class="pull-right btn btn-primary btn-block btn-lg " tabindex="5">
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>

<script>

var textFieldCount = 0;
var multiValuedFieldCount = 0;
var multiValuedFieldValuesCount = 0;

function resetTypeDropdown(){
  $("#type").val('default');
}

$("#type").change(function(){

    var selected = $('#type option:selected').val();
    // alert(selected);
    if(selected == 0){

        var newDiv = $("<div id='div_"+multiValuedFieldCount + "' class='col-sm-4'  >Field Name </div>");
        newDiv.appendTo("#dynamicSection");
        var multiValuedField = $("<input type='text' placeholder='field names' />")
          .attr("id", "multiValuedField_"+multiValuedFieldCount)
          .attr("name", "multiValuedField_"+multiValuedFieldCount)
          .attr("required","required")
          .attr("class","  col-sm-6 input-lg");

        $('#div_'+multiValuedFieldCount).append(multiValuedField);
        multiValuedField.appendTo("#dynamicSection");

        var newDiv = $("<div id='div_"+multiValuedFieldValuesCount + "' class='col-sm-4'>Field Values </div>");
        newDiv.appendTo("#dynamicSection");
        var multiValuedFieldValues = $("<input type='textbox' placeholder='Comma seperated field values' />")
          .attr("id", "multiValuedFieldValues_"+multiValuedFieldValuesCount)
          .attr("name", "multiValuedFieldValues_"+multiValuedFieldValuesCount)
          .attr("required","required")
          .attr("class","  col-sm-6 input-lg");

        $('#div_'+multiValuedFieldValuesCount).append(multiValuedFieldValues);

        multiValuedFieldValues.appendTo("#dynamicSection");

        multiValuedFieldCount = multiValuedFieldCount + 1;
        multiValuedFieldValuesCount = multiValuedFieldValuesCount + 1;

    }else if(selected == 1){

        var newDiv = $("<div id='div_"+textFieldCount + "' class='col-sm-5'>Text Field Name</div>");
        newDiv.appendTo("#dynamicSection");

        var textField = $("<input type='text' value='' />")
          .attr("id", "textField_"+textFieldCount)
          .attr("name", "textField_"+textFieldCount)
          .attr("required","required")
          .attr("class","form-control input-lg");
        $('#div_'+textFieldCount).append(textField);

        textFieldCount = textFieldCount + 1;
    }

    // resetTypeDropdown();
    $("#type").attr("readonly",true);
    $("#type").attr("disabled","disabled");
});

function refreshPage(){
 location.reload();
}
</script>
<?php
//include header template
require('layout/footer.php');
?>
