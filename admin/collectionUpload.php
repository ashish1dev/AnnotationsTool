<?php
ini_set( 'memory_limit', '1024M' );
ini_set('upload_max_filesize', '1024M');
ini_set('post_max_size', '1024M');
ini_set('max_input_time', 3600);
ini_set('max_execution_time', 3600);

$currentDir = getcwd();

$uploadDirectory = "/../Images";

$location = "localhost";
$user = "root";
$pass = "";

function generateThumbNailForAllImagesInThisFolder($directory, $unippedFolderName, $collectionID, $db){

  $dir_Url =  $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
  $destfolder = $directory."/thumbnail/";

  echo '<br/>directory = '.$directory."<br/>";
  $childDirectoryPathName = '';
  if(strrpos($directory,$unippedFolderName) >0){
    $childDirectoryPathName = substr($directory, strrpos($directory,$unippedFolderName) + strlen($unippedFolderName)+1 );
    echo "<br/> childDirectoryPathName = ".$childDirectoryPathName ."<br/>";
    if($childDirectoryPathName=='thumbnail'){
      $childDirectoryPathName = "";
    }
  }

  if (file_exists($destfolder)) {
    echo "<br/>The file $destfolder exists";
  } else {
    echo "<br/>The file $destfolder does not exist";
    mkdir ($destfolder, 0755);
  }

  $dh  = opendir($directory);
  $count = 0;
  //for parent level directory
  while (false !== ($fileName = readdir($dh))) {
      $ext = substr($fileName, strrpos($fileName, '.') + 1);
      if(in_array($ext, array("jpg","jpeg","png","gif","bmp"))){
        $files1[] = $fileName;
        $src = $directory.'/'.$fileName;
        $dest = $destfolder.'/thumbnail_'.$fileName;;//$currentDir.$uploadDirectory."/thumbnail/".'thumbnail_'.$fileName;
        $desired_width = 75;

        $thumbnailPath = $dest;
        $imagePath = $src;
        $thumbnailName= '/thumbnail_'.$fileName;
        createAnEntryInAnnotationsDirListTxtFile($unippedFolderName,$childDirectoryPathName,$fileName, $collectionID);
        insertImageDetailsInDB($fileName, $imagePath, $thumbnailName, $thumbnailPath, $collectionID, $db);

        make_thumb($src, $dest, $desired_width);

        if($count == 0){
          updateCollectionRecordWithImageName($collectionID, $fileName , $db);
        }
        $count++;
      }
  }
}

function imagecreatefrombmp($p_sFile)
  {
      echo $p_sFile;
      //    Load the image into a string
      $file    =    fopen($p_sFile,"rb");
      $read    =    fread($file,10);
      while(!feof($file)&&($read<>""))
          $read    .=    fread($file,1024);

      $temp    =    unpack("H*",$read);
      $hex    =    $temp[1];
      $header    =    substr($hex,0,108);

      //    Process the header
      //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
      if (substr($header,0,4)=="424d")
      {
          //    Cut it in parts of 2 bytes
          $header_parts    =    str_split($header,2);

          //    Get the width        4 bytes
          $width            =    hexdec($header_parts[19].$header_parts[18]);

          //    Get the height        4 bytes
          $height            =    hexdec($header_parts[23].$header_parts[22]);

          //    Unset the header params
          unset($header_parts);
      }

      //    Define starting X and Y
      $x                =    0;
      $y                =    1;

      //    Create newimage
      $image            =    imagecreatetruecolor($width,$height);

      //    Grab the body from the image
      $body            =    substr($hex,108);

      //    Calculate if padding at the end-line is needed
      //    Divided by two to keep overview.
      //    1 byte = 2 HEX-chars
      $body_size        =    (strlen($body)/2);
      $header_size    =    ($width*$height);

      //    Use end-line padding? Only when needed
      $usePadding        =    ($body_size>($header_size*3)+4);

      //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
      //    Calculate the next DWORD-position in the body
      for ($i=0;$i<$body_size;$i+=3)
      {
          //    Calculate line-ending and padding
          if ($x>=$width)
          {
              //    If padding needed, ignore image-padding
              //    Shift i to the ending of the current 32-bit-block
              if ($usePadding)
                  $i    +=    $width%4;

              //    Reset horizontal position
              $x    =    0;

              //    Raise the height-position (bottom-up)
              $y++;

              //    Reached the image-height? Break the for-loop
              if ($y>$height)
                  break;
          }

          //    Calculation of the RGB-pixel (defined as BGR in image-data)
          //    Define $i_pos as absolute position in the body
          $i_pos    =    $i*2;
          $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
          $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
          $b        =    hexdec($body[$i_pos].$body[$i_pos+1]);

          //    Calculate and draw the pixel
          $color    =    imagecolorallocate($image,$r,$g,$b);
          imagesetpixel($image,$x,$height-$y,$color);

          //    Raise the horizontal position
          $x++;
      }

      //    Unset the body / free the memory
      unset($body);

      //    Return image-object
      return $image;
  }



function getListOfAllDirectories($parentDirectory){
  echo "<br/> parentDirectory = ".$parentDirectory;
  echo "<br/>Getting list of all directories : <br/>";
  //path to directory to scan
  $directory = $parentDirectory;

  //get all files in specified directory
  //$files = glob($directory . "*");

  $directories = glob($directory . '/*' , GLOB_ONLYDIR);
  //print each file name
  foreach($directories as $dir)
  {
    echo '<br/>'.$dir;
   // //check to see if the file is a folder/directory
   // if(is_dir($dir))
   // {
   //  echo $dir;
   // }
  }
}

function make_thumb($src, $dest, $desired_width) {

	/* read the source image */
	// $source_image = imagecreatefromjpeg($src);

  // echo '<br/>src = '.$src;
  //echo '<br/>dest = '.$dest;

  echo is_dir($src);

  if(is_dir($src)==1){
    return;
  }else{
    $ext = pathinfo($src, PATHINFO_EXTENSION);
    $source_image = null;
    if ($ext == "png") {
        $source_image = imagecreatefrompng($src);
    } elseif  ($ext ==  "gif") {
        $source_image = imagecreatefromgif($src);
    } elseif ($ext == "jpg") {
        $source_image = imagecreatefromjpeg($src);
    } elseif ($ext == "jpeg") {
            $source_image = imagecreatefromjpeg($src);
    } elseif ($ext == "bmp") {
        $source_image = imagecreatefrombmp($src);
    }

    if($source_image!=null){

      	$width = imagesx($source_image);
      	$height = imagesy($source_image);

      	/* find the "desired height" of this thumbnail, relative to the desired width  */
      	$desired_height = floor($height * ($desired_width / $width));

      	/* create a new, "virtual" image */
      	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);

      	/* copy source image at a resized size */
      	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

      	/* create the physical thumbnail image to its destination */
      	imagejpeg($virtual_image, $dest);
    }

  }

}

require('includes/config.php');


function insertCollectionName($collectionName, $db){

  try {
      //insert into database with a prepared statement
      $stmt = $db->prepare('INSERT INTO collections (name) VALUES (:name)');
      $stmt->execute(array(
        ':name' => $collectionName
      ));
      $id = $db->lastInsertId('id');
      $successMsg = "Record added successfully !";
      return $id;
      //else catch the exception and show the error.
    } catch(PDOException $e) {
        $error[] = $e->getMessage();
        echo $error[0];
    }
}

function updateCollectionRecordWithImageName($id, $firstImageName,$db){

  //$collectionName= str_replace('/','@*@',$collectionName);

  try {
      //insert into database with a prepared statement
      //UPDATE members SET resetToken = :token, resetComplete='No' WHERE email = :email
      $stmt = $db->prepare('UPDATE collections SET firstImageName = :firstImageName WHERE id = :id');
      $stmt->execute(array(
        ':id' => $id,
        ':firstImageName' => $firstImageName,
      ));

      $successMsg = "Record added successfully !";

      //else catch the exception and show the error.
    } catch(PDOException $e) {
        $error[] = $e->getMessage();
        echo $error[0];
    }
}

function createDirInAnnotationFolder($collectionName){
  $dir = '../Annotations/'.$collectionName;
  echo '<br/>'.$dir;
  if (!file_exists($dir)) {
    echo '<br/>making new directory.';
    mkdir($dir, 0777, true);
  }

  $dir = '../Masks/'.$collectionName;
  echo '<br/>'.$dir;
  if (!file_exists($dir)) {
    echo '<br/>making new directory.';
    mkdir($dir, 0777, true);
  }


    $dir = '../Scribbles/'.$collectionName;
    echo '<br/>'.$dir;
    if (!file_exists($dir)) {
      echo '<br/>making new directory.';
      mkdir($dir, 0777, true);
    }
}

function insertImageDetailsInDB($fileName, $imagePath, $thumbnailName, $thumbnailPath, $collectionID, $db){

    try {
        //insert into database with a prepared statement
        $stmt = $db->prepare('INSERT INTO images (imageName, imagePath, thumbnailName, thumbnailPath, collectionID) VALUES (:imageName, :imagePath, :thumbnailName, :thumbnailPath, :collectionID)');
        $stmt->execute(array(
          ':imageName' => $fileName,
          ':imagePath' => $imagePath,
          ':thumbnailName' => $thumbnailName,
          ':thumbnailPath'=> $thumbnailPath,
          ':collectionID'=> $collectionID
        ));
        $id = $db->lastInsertId('id');
        $successMsg = "Record added successfully !";
        return $id;
        //else catch the exception and show the error.
      } catch(PDOException $e) {
          $error[] = $e->getMessage();
          echo $error[0];
      }
}

function createAnEntryInAnnotationsDirListTxtFile($collectionName,$subDirectoryName,$imageName, $collectionID){
  // if($subDirectoryName!='')
  //   $newFileName = '../annotationCache/DirLists/'.addslashes($collectionName.'/'.$subDirectoryName).'.txt';
  // else
  //   $newFileName = '../annotationCache/DirLists/'.$collectionName.'.txt';


  $newFileName = '../annotationCache/DirLists/'.$collectionID.'.txt';

  echo '<br/>'.'newFileName = '.$newFileName;

  if($subDirectoryName!='')
    $newFileContent = ''.$collectionName.'/'.$subDirectoryName.','.$imageName."\n";
  else
    $newFileContent = ''.$collectionName.','.$imageName."\n";

      echo '<br/>making new file.';
      if (file_put_contents($newFileName, $newFileContent,FILE_APPEND | LOCK_EX) !== false) {
      echo "<br/>File created (" . basename($newFileName) . ")";
    } else {
        echo "<br/>Cannot create file (" . basename($newFileName) . ")";
    }

}

    $currentDir = getcwd();
    //  $uploadDirectory = "/CollectionsDataset/";
    //  $uploadDirectory = "/../Images/CollectionsDataset/";
    $uploadDirectory = "/../Images/";

    $errors = []; // Store all foreseen and unforseen errors here

    $fileExtensions = ['zip','rar']; // Get all the file extensions

    $fileName = $_FILES['myfile']['name'];
    $fileSize = $_FILES['myfile']['size'];
    $fileTmpName  = $_FILES['myfile']['tmp_name'];
    $fileType = $_FILES['myfile']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

    $uploadPath = $currentDir . $uploadDirectory . basename($fileName);

    echo '<br/>'.$uploadPath;

    if (isset($_POST['submit'])) {

        if (! in_array($fileExtension,$fileExtensions)) {
            $errors[] = "This file extension is not allowed. Please upload a ZIP or RAR file";
        }

        if ($fileSize > 1024000000) {
            $errors[] = "This file is more than 1GB. Sorry, it has to be less than or equal to 2MB";
        }

        if (empty($errors)) {
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

            if ($didUpload) {
                echo "The file " . basename($fileName) . " has been uploaded";

              // assuming file.zip is in the same directory as the executing script.
              $file = $fileName;

              // get the absolute path to $file
              $path = pathinfo(realpath($file), PATHINFO_DIRNAME);

              $zip = new ZipArchive;
              $res = $zip->open($uploadPath);
              if ($res === TRUE) {
              // extract it to the path we determined above
              $zip->extractTo($currentDir.$uploadDirectory);
              // echo "<br/>zip = ";
              // print_r($zip);
              echo "<br/> $file extracted to $uploadPath <br/>";
              echo "<br/>$zip->numFiles = ".$zip->numFiles;
              if($zip->numFiles>0){
                $pos = strpos($zip->getNameIndex(0),"/");
                $unippedFolderName = substr($zip->getNameIndex(0),0,$pos);
                echo "<br/>".$unippedFolderName;
                echo "<br/>----------<br/>";

                $directory = $currentDir.$uploadDirectory.$unippedFolderName;

                // getListOfAllDirectories($directory);
                $directories = glob($directory . '/*' , GLOB_ONLYDIR);

                if(count($directories)==0){
                  echo "<br/>extracting images of single folder...";
                  $unippedFolderName = addslashes($unippedFolderName);
                  $collectionID = insertCollectionName($unippedFolderName,$db);

                  createDirInAnnotationFolder($unippedFolderName);

                  generateThumbNailForAllImagesInThisFolder($directory, $unippedFolderName, $collectionID, $db);
                }
                else{
                    echo "extracting images of multiple folder...";

                    foreach($directories as $dir)
                    {
                      $pos = strrpos($dir, $unippedFolderName);
                      $collectionName = substr($dir, $pos);
                      $collectionName = addslashes($collectionName);
                      $collectionID = insertCollectionName($collectionName,$db);

                      createDirInAnnotationFolder($collectionName);

                      echo '<br/>'.$dir;
                      generateThumbNailForAllImagesInThisFolder($dir, $unippedFolderName, $collectionID, $db);
                    }
                }
              }

                $zip->close();


              } else {
                echo "<br/>Error ! I couldn't open $file";
              }
            } else {
                echo "An error occurred somewhere. Try again or contact the admin";
            }
        } else {
            foreach ($errors as $error) {
                echo $error . "These are the errors" . "\n";
            }
        }

         header("Location: http://localhost/labelme/admin/upload_collections.php");
         // die();
    }


?>
