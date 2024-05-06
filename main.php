<style>
    .tb_sty {
        border: 5px;
        background-color: skyblue;
        border-style: double;
        border-radius: 13px;
        gap: 5px;
        margin-left: auto;
        margin-right: auto;
    }

    .th_item {
        margin: 10px;
        padding: 10px;
        border: 3px;
        border-style: dashed;
        border-radius: 12px;
    }

    .tb_item {
        margin: 10px;
        padding: 10px;
        border: 3px;
        border-style: solid;
        border-radius: 12px;
    }

    .centerDiv {
        display: flex;
        justify-content: center;
    }

    .Button_sty {
        border-style: none;
        padding: 10px;
        color: black;
        background-color: palegreen;
        border-radius: 16px;
        margin-left: auto;
        margin-right: auto;
        font-weight: bold;

    }

    .Button_sty:hover {
        color: white;
        background-color: black;

    }

    .centerTable {
        margin-left: auto;
        margin-right: auto;
        border: 5px;
        background-color: burlywood;
        border-style: double;
        border-radius: 13px;
        gap: 5px;
        margin-top: 100px;
    }

    .tr_style {
        margin: 10px;
    }
</style>
<?php
include("database.php");
?>
<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
//use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
//use PhpOffice\PhpSpreadsheet\Worksheet\Row;

function xlsToXml($inputFilePath, $outputFileName)
{
    try {

        $spreadsheet = IOFactory::load($inputFilePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $xml = new SimpleXMLElement('<root/>');


        foreach ($worksheet->getRowIterator() as $row) {
            $xmlRow = $xml->addChild('row');

            foreach ($row->getCellIterator() as $cell) {
                $value = $cell->getValue();

                // Check if the value is a date/time value
                if (Date::isDateTime($cell)) {
                    // Convert the value to a PHP DateTime object
                    $dateTime = Date::excelToDateTimeObject($value);
                    // Format the DateTime object as needed
                    $formattedValue = $dateTime->format('H:i:s');
                } else {
                    // Otherwise, treat the value as a string
                    $formattedValue = (string) $value;
                }

                $xmlRow->addChild('cell', $formattedValue);
            }
        }

        $xml->asXML($outputFileName);

        //echo "Conversion completed successfully."; 
    } catch (\Exception $e) {
        echo 'Error: ' . $e->getMessage(); //This One Was Used A lot XD XD XD!!!!!!!!
    }
}

$inputFilePath = 'arrivals.xls';
$outputFileName = 'arrivals.xml';
xlsToXml($inputFilePath, $outputFileName);
?>

<?php
function xmlToHtmlTable($inputFilePath)
{
    try {

        $xml = simplexml_load_file($inputFilePath);


        $html = '<table class = "tb_sty">';
        $i_check = 0;

        foreach ($xml->row as $row) {
            if ($i_check == 1) {
            } else {
                if ($i_check == 0) {

                    $html .= '<tr class="tr_style">';

                    foreach ($row->cell as $cell) {
                        $html .= '<th class = "th_item">' . htmlspecialchars((string)$cell) . '</th>';
                    }

                    $html .= '</tr>';
                } else {
                    $html .= '<tr>';


                    foreach ($row->cell as $cell) {
                        $html .= '<td class = "tb_item">' . htmlspecialchars((string)$cell) . '</td>';
                    }

                    $html .= '</tr>';
                }
            }
            $i_check++;
        }


        $html .= '</table>';


        echo $html;
    } catch (\Exception $e) {

        echo 'Error: ' . $e->getMessage();
    }
}


$inputFilePath = 'arrivals.xml';
xmlToHtmlTable($inputFilePath);
?>

<?php
function xmlToJson($inputFilePath, $outputFilePath)
{
    try {

        $xml = simplexml_load_file($inputFilePath);


        $array = json_decode(json_encode((array)$xml), true);


        $json = json_encode($array, JSON_PRETTY_PRINT);


        file_put_contents($outputFilePath, $json);
    } catch (\Exception $e) {

        echo 'Error: ' . $e->getMessage();
    }
}

// Example usage
$inputFilePath = 'arrivals.xml';
$outputFilePath = 'arrivals.json';
xmlToJson($inputFilePath, $outputFilePath);
?>
<br><br>
<div class="centerDiv" id="dvTable"></div>
<table id="insideTb" class="centerTable">

</table>
<script>

    fetch('arrivals.json')
        .then(response => response.json())
        .then(data => {
            let row_length = data.row.length
            let column_length = data.row[0].length
            let Output = "<tr>"
            data.row[0].cell.forEach((cell) => {
                Output += "<th class = 'th_item'>" + cell + "</th>"
            })

            Output += "</tr>";
            let checker = 0;
            data.row.forEach((row) => {
                if (checker === 0) {
                    checker++;
                } else {
                    Output += "<tr>";
                    row.cell.forEach((cell) => {
                        Output += "<td class = 'tb_item'>" + cell + "</td>";
                    })
                    Output += "</tr>"

                }
            });

            document.getElementById("insideTb").innerHTML = Output
        })
        .catch(e => {
            console.log('There was a problem with your fetch operation: ' + e.message);
        });
</script>

<?php

$query = "CREATE TABLE arrivals(
    Airline varchar(65) not NULL,
    Flight_No varchar(25) primary key,
    Flight_Date varchar(35),
    Schedule_time time,
    Estimate_time time,
    Actual_time time,
    From_loc varchar(25) not NULL,
    Via varchar(5),
    Terminal int,
    Hall int,
    Flight_Status varchar(10)
) ";
try {
    mysqli_query($conn, $query);
} catch (Exception $e) {
}

?>


<?php
function fillDB($jsonData)
{
    // Connect to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "flightsdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    for ($i = 2; $i < count($jsonData['row']); $i++) {
        $row = $jsonData['row'][$i]['cell'];

        // Check if the record already exists
        $checkQuery = "SELECT Flight_No FROM arrivals WHERE Flight_No = '" . $row[1] . "'";
        $result = $conn->query($checkQuery);

        // If the record already exists, skip insertion
        if ($result->num_rows > 0) {
            echo "";
            continue;
        }

        // Parse string dates to MySQL DATE type
        $flightDate = date('Y-m-d', strtotime(strval($row[2]))); 

        // Parse string times to MySQL TIME type
        $scheduleTime = date('H:i:s', strtotime(strval($row[3]))); 
        $estimateTime = date('H:i:s', strtotime(strval($row[4]))); 
        $actualTime = date('H:i:s', strtotime(strval($row[5])));

        // Validate Terminal and Hall values
        $terminal = intval($row[8]);
        $hall = intval($row[9]);

        // Check if Terminal and Hall are valid integers
        if (!is_int($terminal) || !is_int($hall)) {
            // Handle the case where Terminal or Hall is not an integer (e.g., skip insertion, log an error, etc.)
            echo "Error: Terminal or Hall value is not an integer for flight number " . $row[1] . "<br>";
            continue; // Skip insertion for this row
        }

        // Construct the INSERT query
        $query = "INSERT INTO arrivals (Airline, Flight_No, Flight_Date, Schedule_time, Estimate_time, Actual_time, From_loc, Via, Terminal, Hall, Flight_Status) 
              VALUES ('" . $row[0] . "', '" . $row[1] . "', '" . $flightDate . "', '" . $scheduleTime . "', '" . $estimateTime . "', '" . $actualTime . "', '" . $row[6] . "', '" . $row[7] . "', '" . $terminal . "', '" . $hall . "', '" . $row[10] . "')";

        // Execute the INSERT query
        if (mysqli_query($conn, $query)) {
            echo "Data inserted successfully for flight number: " . $row[1] . "<br>";
        } else {
            // Handle error if INSERT query fails
            echo "Error: " . mysqli_error($conn) . "<br>";
        }
    }
}


$jsonData = json_decode(file_get_contents('arrivals.json'), true);


fillDB($jsonData);
?>

<?php
function getSameCityFlights()
{
    // Connect to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "flightsdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = ' SELECT * from arrivals a1
                   Order by a1.From_loc
             
                   ';
    $result = mysqli_query($conn, $query);
    echo '<div id="filtered_table_1">';
    echo '<table>';
    $temp = "";
    while ($row = mysqli_fetch_array($result)) {
        if ($temp != $row['From_loc']) {
            echo "</table>";
            echo "<br><br><br>";
            echo "<h1>Flight From " . $row['From_loc'] . "</h1>";
            echo "<table class = 'centerTable' border=1 >";
            echo "<tr>
                    <th class='th_item'>Airline</th>
                    <th class='th_item'>Flight No.</th>
                    <th class='th_item'>Flight Date</th>
                    <th class='th_item'>Schedule time</th>
                    <th class='th_item'>Estimate time</th>
                    <th class='th_item'>Actual time</th>
                    <th class='th_item'>From</th>
                    <th class='th_item'>Via Terminal</th>
                    <th class='th_item'>Hall</th>
                    <th class='th_item'>Status</th>
                   </tr>";
        }
        $temp = $row['From_loc'];
        echo '<tr>
                    <td  class = "tb_item" >' . $row['Airline'] . '</td>
                    <td  class = "tb_item" >' . $row['Flight_No'] . '</td>
                    <td class =  "tb_item" >' . $row['Flight_Date'] . '</td>
                    <td class =  "tb_item" >' . $row['Schedule_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Estimate_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Actual_time'] . '</td>
                    <td class =  "tb_item" >' . $row['From_loc'] . '</td>
                    <td class =  "tb_item" >' . $row['Terminal'] . '</td>
                    <td class =  "tb_item" >' . $row['Hall'] .       '</td>
                    <td class =  "tb_item" >' . $row['Flight_Status'] . '</td>';
        echo "</tr>";
    }

    echo '</table>';
    echo '</div>';
    mysqli_free_result($result);
}
?>

<?php
function getAlreadylanded()
{
    // Connect to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "flightsdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = " SELECT * from arrivals a1
               WHERE a1.Flight_status = 'Landed' 
             
                   ";
    $result = mysqli_query($conn, $query);
    echo '<div id="filtered_table_2" >';
    echo "<h1>Landed Flights </h1>";
    echo "<table class = 'centerTable' border=1 >";
    echo "<tr>
            <th class='th_item'>Airline</th>
            <th class='th_item'>Flight No.</th>
            <th class='th_item'>Flight Date</th>
            <th class='th_item'>Schedule time</th>
            <th class='th_item'>Estimate time</th>
            <th class='th_item'>Actual time</th>
            <th class='th_item'>From</th>
            <th class='th_item'>Via Terminal</th>
            <th class='th_item'>Hall</th>
            <th class='th_item'>Status</th>
           </tr>";
    $temp = "";
    while ($row = mysqli_fetch_array($result)) {

        echo '<tr>
                    <td  class = "tb_item" >' . $row['Airline'] . '</td>
                    <td  class = "tb_item" >' . $row['Flight_No'] . '</td>
                    <td class =  "tb_item" >' . $row['Flight_Date'] . '</td>
                    <td class =  "tb_item" >' . $row['Schedule_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Estimate_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Actual_time'] . '</td>
                    <td class =  "tb_item" >' . $row['From_loc'] . '</td>
                    <td class =  "tb_item" >' . $row['Terminal'] . '</td>
                    <td class =  "tb_item" >' . $row['Hall'] .       '</td>
                    <td class =  "tb_item" >' . $row['Flight_Status'] . '</td>';
        echo "</tr>";
    }

    echo '</table>';
    echo '</div>';
    mysqli_free_result($result);
}
?>

<br><br>
<div class='centerDiv'>
    <form id="SameCityF" method="post" action="main.php">
        <input type='submit' name="getSameCity" id="SameCityB" value="Filter By City" class="Button_sty">
        <input type='submit' name="getLanded" id="Landed" value="Landed Flights" class="Button_sty">
        <br><br>
        <label style="font-size: larger; font-weight:bolder;">Select schedule time</label>
        <input type='time' name="getTime" id="Landed"  class="Button_sty">
        <input type='submit'name='getTime_B' style="border-style:none; background-color:crimson; color:white; padding:10px; border-radius:12px;">
    </form>
</div>

<?php
function getSetTime($time)
{
    // Connect to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "flightsdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $query = " SELECT * from arrivals";
    $result = mysqli_query($conn, $query);
    echo '<div id="filtered_table_2" >';
    echo "<h1>All Flights After {$time} </h1>";
    echo "<table class = 'centerTable' border=1 >";
    echo "<tr>
            <th class='th_item'>Airline</th>
            <th class='th_item'>Flight No.</th>
            <th class='th_item'>Flight Date</th>
            <th class='th_item'>Schedule time</th>
            <th class='th_item'>Estimate time</th>
            <th class='th_item'>Actual time</th>
            <th class='th_item'>From</th>
            <th class='th_item'>Via Terminal</th>
            <th class='th_item'>Hall</th>
            <th class='th_item'>Status</th>
           </tr>";
    $temp = "";
    while ($row = mysqli_fetch_array($result)) {
        if($row['Schedule_time'] > $time){
        echo '<tr>
                    <td  class = "tb_item" >' . $row['Airline'] . '</td>
                    <td  class = "tb_item" >' . $row['Flight_No'] . '</td>
                    <td class =  "tb_item" >' . $row['Flight_Date'] . '</td>
                    <td class =  "tb_item" >' . $row['Schedule_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Estimate_time'] . '</td>
                    <td class =  "tb_item" >' . $row['Actual_time'] . '</td>
                    <td class =  "tb_item" >' . $row['From_loc'] . '</td>
                    <td class =  "tb_item" >' . $row['Terminal'] . '</td>
                    <td class =  "tb_item" >' . $row['Hall'] .       '</td>
                    <td class =  "tb_item" >' . $row['Flight_Status'] . '</td>';
        echo "</tr>";
    }
}

    echo '</table>';
    echo '</div>';
    mysqli_free_result($result);
}
?>
<?php

if (!empty($_POST["getSameCity"])) {
    getSameCityFlights();
} 
if(!empty($_POST["getLanded"])){
    getAlreadylanded();
}
if(!empty($_POST["getTime_B"])){
    if(isset($_POST["getTime"])){
        $date=date_create($_POST["getTime"]);
        $timeStr = date_format($date,'H:i:s');
        getSetTime($timeStr);
        
    }
    else{
        echo "you have not set time";
    }
}

?>

