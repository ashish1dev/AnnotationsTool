<?php
 require('includes/config.php');


$form_data = array(); //Pass back the data to `form.php`

// var_dump($_POST);
/* Validate the form on the server side */
if (!empty($_POST['objEnter'])) {
    $form_data['objEnter'] = $_POST['objEnter'];
}

    $form_data['attributes'] = $_POST['attributes'];


if (!empty($_POST['occluded'])) {
    $form_data['occluded'] = $_POST['occluded'];
}

if (!empty($_POST['username'])) {
    $form_data['username'] = $_POST['username'];
}

if (!empty($_POST['dirName'])) {
    $form_data['dirName'] = $_POST['dirName'];
}
if (!empty($_POST['imageName'])) {
    $form_data['imageName'] = $_POST['imageName'];
}

$form_data['collectionName'] = $_POST['collectionName'];


header('Content-Type: application/json');

try {

  //insert into database with a prepared statement
  // INSERT INTO `annotations`(`id`, `name`, `attributes`, `occluded`, `username`, `dirName`, `imageName`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7])
  $stmt = $db->prepare('INSERT INTO annotations ( name, attributes, occluded, username, dirName, collectionName, imageName) VALUES ( :name, :attributes, :occluded, :username, :dirName, :collectionName, :imageName)');
  $stmt->execute(array(
    'name' => $form_data['objEnter'],
    'attributes'=> $form_data['attributes'],
    'occluded'=> $form_data['occluded'],
    'username'=> $form_data['username'],
    'dirName'=> $form_data['dirName'],
    'collectionName' => $form_data['collectionName'],
    'imageName'=> $form_data['imageName']
  ));
  $id = $db->lastInsertId('id');
  $form_data['status'] ='success';
  echo json_encode($form_data);

//else catch the exception and show the error.
} catch(PDOException $e) {
    $form_data['status'] ='failure';
    $error[] = $e->getMessage();
    echo json_encode($form_data);
}



?>
