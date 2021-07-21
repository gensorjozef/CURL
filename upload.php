<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "Classes/helper/Database.php";

$connection = (new Database())->getConnection();

$connection->query('SET foreign_key_checks = 0');
$statement = $connection->prepare("TRUNCATE TABLE student_action");
$statement->execute();
$statement = $connection->prepare("TRUNCATE TABLE lectures");
$statement->execute();
$ch = curl_init();


$conn = (new Database())->getConnection();
curl_setopt($ch, CURLOPT_URL, "https://github.com/apps4webte/curldata2021");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
if(curl_error($ch)) {
    die('chyba');
}

preg_match_all("!>[^\s]*?Te2.csv</a>!", $result, $filesPaths);
$filesPaths = $filesPaths[0];


foreach ($filesPaths as $filePath) {
    $filePath = substr($filePath, 1);
    $filePath = substr($filePath, 0, -4);

    curl_setopt($ch, CURLOPT_URL, "https://raw.githubusercontent.com/apps4webte/curldata2021/main/$filePath");
    $csv[] = iconv('UTF-16LE', 'UTF-8' , curl_exec($ch));
    if(curl_error($ch)) {
        die('chyba2');
    }

//        $lines = explode(PHP_EOL, $csv);
//

//        foreach ($lines as $index => $line){
//
//            echo "skuska";
//            if (($index > 0)){
//                $lineArray[] = str_getcsv($line,"\t");
//            $name = $lineArray[0];
//            $action = $lineArray[1];
//            $timestamp = $lineArray[2];
//            $stm->execute([1, $name, $action, $timestamp]);
//            }
//
//        }
//
//
//
//
//    }
}
$counter = 0;
foreach ($csv as $lecture => $file){
    $lines = explode(PHP_EOL, $file);
    $counter += sizeof($lines);
    $lecture_name = explode("_", $filesPaths[$lecture],-2);
    $name = $lecture_name[0];
    $name = ltrim($name,'>');
    $sql_lecture = "INSERT INTO lectures (timestamp_lecture) VALUES (?)";
    $stm_lecture = $conn->prepare($sql_lecture);
    $stm_lecture->execute([$name]);
    $id_lecture = $conn->lastInsertId();



    $sql_student = "INSERT INTO student_action (lecture_id, name, action, timestamp ) VALUES (?,?,?,?)";
    $stm_student = $conn->prepare($sql_student);
    foreach ($lines as $index => $line){
        if (($index > 0)){
            $lineArray = str_getcsv($line,"\t");
            if (sizeof($lineArray) == 3){
                $student_name = $lineArray[0];
                $action = $lineArray[1];

                DateTime::createFromFormat('d/m/Y, H:i:s',  $lineArray[2]) ? $date_format = 'd/m/Y, H:i:s' : $date_format = 'd/m/Y, H:i:s A';
                $timestamp = DateTime::createFromFormat($date_format,  $lineArray[2])->format('Y-m-d H:i:s');
                $stm_student->execute([$id_lecture, $student_name, $action, $timestamp]);
            }


        }

    }




}
curl_close($ch);




$set_sql = "SELECT id FROM lectures";
$stm_lecture_set = $conn->prepare($set_sql);
$stm_lecture_set->execute();
$lectures_set = $stm_lecture_set->fetchAll(PDO::FETCH_ASSOC);
foreach ($lectures_set as $set){
    $sql_lecture_end = "SELECT MAX(timestamp) FROM student_action WHERE lecture_id = ? AND action = 'Left'";
    $stm_lecture_end = $conn->prepare($sql_lecture_end);
    $stm_lecture_end->bindValue(1,$set["id"]);
    $stm_lecture_end->execute();
    $lecture_end = $stm_lecture_end->fetch(PDO::FETCH_ASSOC);
    $nazov = $lecture_end["MAX(timestamp)"];

    $sql_lecture_end_set = "UPDATE lectures SET lecture_end= ? WHERE id= ?";
    $stm_lecture_end_set = $conn->prepare($sql_lecture_end_set);
    $stm_lecture_end_set->bindValue(1,$nazov);
    $stm_lecture_end_set->bindValue(2,$set["id"]);
    $stm_lecture_end_set->execute();
}
echo json_encode(["status" => "success", "msg" => "Pridaných " . $counter . " záznamov"]);
die();











