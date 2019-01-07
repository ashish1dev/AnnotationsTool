<?php require('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Members Page';

//include header template
require('layout/header.php');
?>

<script type="text/javascript" src="./annotationTools/js/sign_in.js" ></script>
<script type="text/javascript" src="./annotationTools/js/globals.js" ></script>
<script type="text/javascript" src="./annotationTools/js/browser.js" ></script>


<div class="container">

	<div class="row">

	</div>

	<?php


	   $location = "localhost";
	   $user = "root";
	   $pass = "";

	   //Database Selection
	   $link = mysql_connect($location, $user, $pass);
	   if (!$link) {
	       die('Could not connect: ' . mysql_error());
	   }

	    mysql_select_db('labelme', $link) or die(mysql_error());

	   // $query="SELECT * FROM members";
	   // $results = mysql_query($query);

		 $member_collection_query="SELECT DISTINCT collections.id as ID, name, firstImageName FROM member_collection  JOIN members ON member_collection.member_id = members.memberID   JOIN collections ON member_collection.collection_id = collections.id "; //WHERE members.username = '".$_SESSION['username']."'";
		 $member_collection_results = mysql_query($member_collection_query);

	   if (!$member_collection_results) { // add this check.
	       die('Invalid query: ' . mysql_error());
	   }
	   ?>

	         <h2>List of Approved Collections for you :</h2>
	         <table class='table col-xs-6' border='1' >
						 <th>Collection ID</th>
	            <th>Approved Collections  </th>
							<th>Link  </th>
	            <tbody>
	               <?php
	                  while ($row = mysql_fetch_array($member_collection_results)) {
	                      echo '<tr>';
									      // foreach($row as $field) {
	                      //     echo '<td>' . htmlspecialchars($field['']) . '</td>';
	                      // }
												echo "<td>".$row['ID']."</td>";
												$folderName = str_replace('@*@','/',$row['name']);
	                      echo '<td>' .$folderName.'</td>';
												$imageName = $row['firstImageName'];

												echo '<td>'."<a target='_blank' href='http://localhost/labelme/tool.html?collection=".$row['ID']."&mode=c&folder=".$folderName."&image=".$imageName."&username=".$_SESSION['username']."'>Open Label Me</a>".'</td>';
	                      echo '</tr>';
	                  }
	                  ?>
	            </tbody>
	         </table>

</div>

<script type="text/javascript">
	var username = "<?php echo 	$_SESSION['username']; ?>";
	  setCookie("username",username);

</script>

<?php
//include header template
require('layout/footer.php');
?>
