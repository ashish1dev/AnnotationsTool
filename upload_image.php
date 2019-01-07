<?php require('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Members Page';

//include header template
require('layout/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload with PHP</title>
</head>
<body>

  <div class="container">

  	<div class="row">

  	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">


        </div>
      </div>
    </div>




    <div class="container">

    	<div class="row">

    	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    		  <form action="fileUpload.php" method="post" enctype="multipart/form-data">
    				<h2>Upload an Image:</h2>
    				<p><a href='./'>Back to home page</a></p>
    				<hr>


    				<div class="form-group">
    				  <input type="file" name="myfile" class="btn btn-default" id="fileToUpload">
            </div>


    				<div class="form-group">
    				    <input type="submit" name="submit"  class="btn btn-primary"   value="Upload File Now" >
      			</div>

    			</form>
    		</div>
    	</div>
    </div>

</body>
</html>


<?php
//include header template
require('layout/footer.php');
?>
