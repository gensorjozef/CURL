<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "Classes/helper/Database.php";
$conn = (new Database())->getConnection();
function time_diff_minutes($start, $end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = $start->diff($end);
    return ceil($interval->format('%h')*60 + $interval->format('%i') + $interval->format('%s')/60);
}
$count_column = 0;
$students_array = array();
$sql = "SELECT DISTINCT name FROM student_action";
$stm = $conn->prepare($sql);
$stm->execute();
$students = $stm->fetchAll(PDO::FETCH_ASSOC);
foreach ($students as $student){
    array_push($students_array, $student["name"]);
}
$sql_lectures = "SELECT * FROM lectures";
$stm_lectures = $conn->prepare($sql_lectures);
$stm_lectures->execute();
$lectures = $stm_lectures->fetchAll(PDO::FETCH_ASSOC);

//$sql = "SELECT * FROM student_action left join lectures on student_action.lecture_id = lectures.id";
//$stm = $conn->prepare($sql);
//$stm->execute();
//$students = $stm->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <title>Zadanie 4</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <script>
        function UpdateDB(){
            alert("Vkladanie záznamov");
            fetch('./upload.php')
                .then(
                    function(response) {
                        if (response.status !== 200) {
                            console.log('Looks like there was a problem. Status Code: ' +
                                response.status);
                            return;
                        }

                        // Examine the text in the response
                        response.json().then(function(data) {
                            alert(data.msg);
                            window.location.href = "index.php";
                        });
                    }
                )
                .catch(function(err) {
                    console.log('Fetch Error :-S', err);
                });
        }
    </script>
</head>
<body>

<div class="card bg-light">
    <article class="card-body mx-auto" style="max-width: 1000px;">
        <div class="col text-center">
            <button type="button" class="btn btn-danger center" onclick="UpdateDB()"> Aktualizovanie DB  </button>
        </div>
        <p class="text-center">Pozor všetky údaje v DB budu vymazané a nahradené údajmi z <a href="https://github.com/apps4webte/curldata2021">repozitáru</a>!</p>
        <br>

        <h4 class="card-title mt-3 text-center">Tabulka dochádzky Webtech2</h4>
        <table id="myTable2" class="display">
            <thead>
            <tr>
                <th>Meno</th>
                <?php
                foreach ($lectures as $lecture) {
                    $count_column += 1;
                    $name_lecture = $lecture['timestamp_lecture'];
                    $name_lecture = str_replace('00:00:00', '', $name_lecture);
                    echo "<th>" . "{$name_lecture}" . "</th>";
                }
                ?>
                <th>Počet účastí</th>
                <th>Počet minút</th>
            </tr>
            </thead>
            <tbody>
        <?php
            foreach ($students_array as $student_name){
                echo '<tr>';
                $name_swap =  explode(" ", $student_name);
                $name_swaped = $name_swap[1] . " " . $name_swap[0];
                echo '<td>' . "{$name_swaped}"  . '</td>';
                $total_time = 0;
                $ucast = 0;
                foreach ($lectures as $lecture){
                    $sql_select = "SELECT * FROM student_action WHERE lecture_id = ? AND name = ?";
                    $stm_select = $conn->prepare($sql_select);
                    $stm_select->bindValue(1,$lecture["id"]);
                    $stm_select->bindValue(2,$student_name);
                    $stm_select->execute();
                    $selects = $stm_select->fetchAll(PDO::FETCH_ASSOC);
                    $minuts = 0;
                    $time_start = 0;
                    $time_end = 0;
                    $left_count = 0;
                    foreach ($selects as $select){


                        if ($select["action"] == "Joined"){
                            $left_count = 0;
                            if((strtotime($select["timestamp"]) > strtotime($time_end)) && ($time_end != 0)) {
                                $time_end = 0;
                                $time_start = $select["timestamp"];
                            }else{
                                $time_start = $select["timestamp"];
                            }
                        }
                        if ($select["action"] == "Left"){
                            $left_count += 1;
                            $time_end = $select["timestamp"];
                        }

                        if (($time_start != 0) && ($time_end != 0)){
                            $minuts += time_diff_minutes($time_start,$time_end);
                            }



                    }

                    if (($time_start != 0) && ($time_end == 0)){
                        $time_end = $lecture["lecture_end"];
                        $minuts += time_diff_minutes($time_start,$time_end);
                    }
                    if ($minuts != 0){
                        $ucast += 1;
                    }
                    $total_time += $minuts;
                    $modal_name = $student_name;
                    $modal_name = str_replace(' ', '', $modal_name);
                    if (($left_count == 0) && ($minuts != 0)) {
                        echo '<td>' . '<button type="button" class="btn btn-danger" data-toggle="modal"  data-target=' . '#' . $modal_name  .  $lecture["id"] . '>'
                            . "{$minuts}" . '</button>'
                            . '</td>';

                    }else{
                        echo '<td>' . '<button type="button" class="btn btn-light" data-toggle="modal"  data-target=' . '#' . $modal_name  .  $lecture["id"] . '>'
                            . "{$minuts}" . '</button>'
                            . '</td>';

                    }
                    ?>

                    <div class="modal" id="<?php echo $modal_name  .  $lecture["id"];?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <?php
                                $name_lecture_modal = $lecture['timestamp_lecture'];
                                $name_lecture_modal = str_replace('00:00:00', '', $name_lecture_modal);
                                ?>
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title"><?php echo $student_name  . " " . $name_lecture_modal;?></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">
                                    <?php
                                    echo '<ul class="list-group">';
                                        foreach ($selects as $select) {
                                        if ($select["action"] == "Joined"){
                                            echo '<li class="list-group-item list-group-item-success">'. "Príchod " . $select["timestamp"]  .'</li>';
                                        }
                                        if ($select["action"] == "Left"){
                                            echo '<li class="list-group-item list-group-item-danger">'. "Odchod " . $select["timestamp"]  .'</li>';
                                        }
                                        }
                                    echo '</ul>';
                                    ?>
                                </div>

                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '<td>' . "{$ucast}"  . '</td>';
                echo '<td>' . "{$total_time}"  . '</td>';
                echo '</tr>';
            }

        ?>

            </tbody>
        </table>

        <h4 class="card-title mt-3 text-center">Graf dochádzky Webtech2</h4>
        <canvas id="myChart" width="900" height="400"></canvas>
        <script type="text/javascript">
            <?php
            $lectures_names = array();
            $students_count = array();
            foreach ($lectures as $lecture) {
                $name_lecture = $lecture['timestamp_lecture'];
                $name_lecture = str_replace('00:00:00', '', $name_lecture);
                array_push($lectures_names,$name_lecture);
                $sql_count = "SELECT COUNT(DISTINCT name) FROM student_action WHERE lecture_id = ?";
                $stm_count = $conn->prepare($sql_count);
                $stm_count->execute([$lecture["id"]]);
                $count = $stm_count->fetch(PDO::FETCH_ASSOC);
                array_push($students_count,$count["COUNT(DISTINCT name)"]);
            }
            ?>

            var js_lectures_names = [<?php echo '"'.implode('","', $lectures_names).'"' ?>];
            var js_students_count = [<?php echo '"'.implode('","', $students_count).'"' ?>];
            var ctx = document.getElementById("myChart");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {

                    labels: js_lectures_names,
                    datasets: [
                        { label: 'Pocet studentov',
                            data: js_students_count,
                            borderWidth : 1

                        }
                    ]
                },
                options: {

                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });

        </script>



    </article>
</div>



<script>
    $(document).ready( function () {

        $('#myTable2').DataTable({
            "order": [[ 0, "asc" ]]
        } );
    } );


</script>
</body>
</html>
