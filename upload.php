<?php
    error_reporting(0);
    $file_path = "images/";
  
//
//     $file_path = $file_path . basename( $_FILES['profile_picture']['name']);
//     if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
//         echo "success";
//     } else{
//         echo "fail";
//     }
 
 
    
    $ctr=0;
    $count = $_POST["ctr"];
    
    for ($i=0;$i<$count;$i++){
        //$temp = explode(".",$_FILES["profile_picture".$i]["name"]);    
//        $newfilename = "PROF-".$i.".".$temp[1];
//    
//        $upload = move_uploaded_file($_FILES["profile_picture".$i]["tmp_name"], $file_path . $newfilename);
//        
//        if ($upload){
//            $ctr++;
//        } 
       
        $temp = explode('.',$_FILES['profile_picture'.$i]['name']);
        $newfilename = 'PROF-'.$i.'.'.$temp[1];
        $path = $file_path . $newfilename; //basename( $_FILES['profile_picture'.$i]['name']);
        $upload = move_uploaded_file($_FILES['profile_picture'.$i]['tmp_name'], $path);
        if($upload) {
            echo "success";
        } 
    }
   
    echo $count;
       



/**

 * <?php
 * $target_path = "user_uploaded_photos/";
 * for($i=0;$i<count($_FILES["image"]["name"]);$i++){
 * $fileData = pathinfo(basename($_FILES["image"]["name"][$i]));
 * $username = $_POST['username'];
 * print_r($fileData);
 * $date = date('Y_m_d_H_i_s');
 * $newfilename = $username.$i.$date.".".$fileData['extension'];
 * if (move_uploaded_file($_FILES["image"]["tmp_name"][$i], $target_path."/".$newfilename))
 * {
 *     echo "The image {$_FILES['image']['name'][$i]} was successfully uploaded and added to the gallery<br />";
 * }
 * else
 * {
 *  echo "There was an error uploading the file {$_FILES['image']['name'][$i]}, please try again!<br />";
 * }
 * } $location = $_POST['location'];
 * //other fields and database operations ommitted
 */
?>