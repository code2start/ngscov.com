<?php

//$output = '';
$upload = 'err';
$allowTypes = array(
    'application/zip', 'application/x-zip-compressed',
    'multipart/x-zip', 'application/x-compressed', 'application/fastq.gz', 'application/fastq'
);

if (isset($_FILES['file']['name'][0])) {
    $adapter = $_POST['adapter'];
    $project = $_POST['project'];
    $time = date('y-m-d-h-i-s');
    $path = "data/$time";
    mkdir($path, 0777, true);
    foreach ($_FILES['file']['name'] as $keys => $values) {
        //$upload = $path . $values;
        //move_uploaded_file($_FILES['file']['tmp_name'][$keys], $path . $values);
        $dist = $path . '/' . $values;
        if (move_uploaded_file($_FILES['file']['tmp_name'][$keys], $dist)) {
            //$upload = $path . $values;
            //$ext = pathinfo($dist, PATHINFO_EXTENSION);
            $ext = '_R1_001.fastq.gz';
            exec("./Phase3.sh $ext $project $path $adapter");
            $upload = 'ok';
        }
    }
}


echo $upload;
