<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <?php

    //bootstrap
    session_start();
    $dbh = new mysqli("localhost", "root", "", "giugno", 3305);
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }    

    //database
    function countNumbersFromSet($dbh, $set){
        $sql = "SELECT COUNT(*) AS count FROM insiemi WHERE insieme = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bind_param('i', $set);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp = $result->fetch_all(MYSQLI_ASSOC);
        return $temp[0]["count"];
    }
    function getNumbersFromSet($dbh, $set){
        $sql = "SELECT valore FROM insiemi WHERE insieme = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bind_param('i', $set);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp = $result->fetch_all(MYSQLI_ASSOC);
        return $temp;
    }
    function getMaxSet($dbh){
        $sql = "SELECT MAX(insieme) AS max FROM insiemi";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $temp = $result->fetch_all(MYSQLI_ASSOC);
        return $temp[0]["max"];
    }
    function insertSetFromArray($dbh, $set, $arr){
        $sql = "INSERT INTO insiemi (insieme, valore) VALUES (?, ?)";
        $stmt = $dbh->prepare($sql);
        foreach($arr as $value){
            $stmt->bind_param('ii', $set, $value["valore"]);
            $stmt->execute();
        }
    }

    //functions
    function isValidInput($dbh, $a){
        return is_numeric($a) && $a > 0 && countNumbersFromSet($dbh, $a) > 0;
    }

    function getIntersection($arr1, $arr2){
        $result = array();
        foreach($arr1 as $value){
            if(in_array($value, $arr2)){
                $result[] = $value;
            }
        }
        return $result;
    }
    function getUnion($arr1, $arr2){
        $result = array();
        foreach($arr1 as $value){
            $result[] = $value;
        }
        foreach($arr2 as $value){
            if(!in_array($value, $arr1)){
                $result[] = $value;
            }
        }
        return $result;
    }

    //exercise
    if(!isset($_GET['A']) || !isset($_GET['B'])){
        echo("Inserire i parametri A e B");
        die();
    }
    if(!isValidInput($dbh, $_GET['A']) || !isValidInput($dbh, $_GET['B'])){
        echo("Le variabili A e B devono essere numeri maggiori di 0 e ci deve essere almeno un elemento appartenente agli insiemi in questione");
        die();
    }
    
    if(!isset($_GET['O']) || ($_GET['O'] != 'i' && $_GET['O'] != 'u')){
        echo("La variabilie O deve essere settata a u o ad i");
        die();
    }

    $firstArr = getNumbersFromSet($dbh, $_GET['A']);
    $secondArr = getNumbersFromSet($dbh, $_GET['B']);
    $result = $_GET['O'] == 'u' ? getIntersection($firstArr, $secondArr) : getUnion($firstArr, $secondArr);
    $maxSet = getMaxSet($dbh);
    insertSetFromArray($dbh, $maxSet+1, $result);
   
    echo("E' stato creato il nuovo insieme " . ($maxSet+1) . " con i seguenti valori: <br/>");
    foreach($result as $value){
        echo($value["valore"] . "<br/>");
    }
    ?>
</body>
</html>