<?php
require("classes/SimpleImage.php");

if (isset($_FILES['file']['name'])){

    $files= array();
        
    foreach($_FILES['file']['name'] as $key=>$val){

        $file_name = $_FILES['file']['name'][$key];
        // get file extension
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
        // get filename without extension
        $filenamewithoutextension = pathinfo($file_name, PATHINFO_FILENAME);
    
        if (!file_exists(getcwd(). '/uploads')) {
            mkdir(getcwd(). '/uploads', 0777);
        }
    
        $date = date('dmYHis');
        $no_spaces_filename = str_replace(" ","_",$filenamewithoutextension);
    
        $mimeImg = array ('image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml');
        $mimeFiles = array ('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/pdf', 'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/vnd.rar', 'application/rtf', 'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/zip', 'application/x-7z-compressed', 'application/x-rar-compressed', 'application/octet-stream',
                            'multipart/x-zip', 'application/x-rar', 'text/rtf');
        $imageExt = array('gif', 'png', 'jpg', 'jpeg');    
        $fileExt = array('pdf', 'rtf', 'doc', 'ppt', 'pptx', 'docx', 'otd', 'xls', 'xlsx', 'zip', 'rar');
    
        if (in_array($ext, $imageExt)) {
            $dataType = '__img__';
        }elseif (in_array($ext, $fileExt)){
            $dataType = '__file__';
        }else{
            // Isto za greške (imati na umu da će se prevoditi)
            die;
        }
     
        $filename_to_store = $dataType.$date.'_'.$no_spaces_filename.'.'.$ext; 

        if ($dataType = '__img__' || $dataType = '__file__'){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);    

            if(empty($finfo) || empty($_FILES['file']['tmp_name'][$key])){
                die;
            }else{
                $mime = finfo_file($finfo, $_FILES['file']['tmp_name'][$key]);
            }
            
        }else{
            // Fali greška
            die;
        }

        if($dataType = '__img__' && in_array($mime, $mimeImg)){
            $thumbnail_name_to_store = 'thumb_'.$date.'_'.$no_spaces_filename.'.'.$ext;
            $image = new \claviska\SimpleImage();
                $image
                    ->fromFile($_FILES["file"]["tmp_name"][$key])
                    ->thumbnail(300, 200)
                    ->toFile('uploads/'. $thumbnail_name_to_store);
            move_uploaded_file($_FILES['file']['tmp_name'][$key], getcwd(). '/uploads/'.$filename_to_store);

            $files[] = 'uploads/' . $thumbnail_name_to_store;
        }
        elseif ($dataType = '__file__' &&in_array($mime,$mimeFiles)){ 
            move_uploaded_file($_FILES['file']['tmp_name'][$key], getcwd(). '/uploads/'.$filename_to_store);
            
            // Vraćanje putanje do slike nazad (pomjereno ovdje, jer ako je error, nema šta dodati)
            $files[] = 'uploads/' . $filename_to_store;
        }else{
            // Imati u planu da će se greške prevoditi (samo imati na umu trenutno)
            die;
        }

    }
    echo json_encode($files);
    die;
    
}else{
    // Isto za greške
    die;
}


    // If else provjera za tip fajla /
    // __img__ za slike (pronaci standardne ekstenzije za slike) /
    // __file__ za fajlove (pronaci  standardne ekstenzije za Word, Excel i PDF fajlove) /
    // Ako nije nijedno ni drugo obustavi upload /