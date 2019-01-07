<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title><?php if(isset($title)){ echo $title; }?></title>
      <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">

      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      <script src= 'admin/../includes/table2csv.js'></script>
      <link rel="stylesheet" href="style/main.css">
      <?php
         if($user->is_logged_in()){
           ?>

 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Admin Panel (<?php echo htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES); ?>)</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">

        <li><a href='assign_user_to_collection.php'>Assign Collection</a> </li>
        <li><a href='list_of_annotations.php'> Annotations</a></li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="list_of_users.php">View</a></li>
            <li><a href="add_new_user.php">Add</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Collections <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="upload_collections.php">Upload</a></li>
            <li><a href="view_collections.php">View</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Image Labels <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <!-- <li><a href="createImageLabel.php">Add </a></li> -->
            <li><a href="viewImageLabel.php">View </a></li>
          </ul>
        </li>

        <li><a href='list_of_image_level_labels.php'>View Image Level Labels</a></li>
        <li><a href='logout.php'>Logout</a>

      </ul>


    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!--
      <div class="container">
         <div class="row">
            <div class="col-xs-12 col-sm-8  ">
               <h2>Admin Panel : (<?php echo htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES); ?>)</h2>
               <p>
                  <a href='list_of_users.php'>Users</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='add_new_user.php'>Add User</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='assign_user_to_collection.php'>Assign Collection</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='upload_collections.php'>Upload Collections</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='view_collections.php'> Collections</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='list_of_annotations.php'> Annotations</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='createImageLabel.php'> Create Image Label</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='viewImageLabel.php'> View Image Label</a> &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href='logout.php'>Logout</a>
               </p>
            </div>
         </div>
      </div> -->
      <?php
         }
         ?>
   </head>
   <body>
