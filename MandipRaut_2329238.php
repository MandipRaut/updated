<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="MandipRaut_2329238.css">
    <script src="https://kit.fontawesome.com/3412c74b1e.js" crossorigin="anonymous"></script>
    <title>Weather</title>
</head>
<body>
<?php
        
        // declaring variables to store servername, username, password 
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "weather";
        // $servername = "sql306.epizy.com";
        // $username = "epiz_34168790";
        // $password = "rml4UT2oXjIihb";
        // $dbname = "epiz_34168790_weather_app";
        $table='weather_details';
        // Creating a new mysqli object with above datas
        $conn1 = new mysqli($servername, $username, $password);
        // Check connection
        if ($conn1->connect_error) {
            die("Connection failed: " . $conn1->connect_error);
        }
        //SQL query to check if a database named 'weather' exist or not and to create if not exist
        $create_database="CREATE DATABASE IF NOT EXISTS $dbname";
        $conn1->query($create_database);

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //SQL query to check if table named weather exist or not and to create if not exist
        $sql101="CREATE TABLE IF NOT EXISTS weather_details (
            address Varchar(25),
            temperature float,
            wind float,
            temp_max float,
            temp_min float,
            date date,
            day varchar(10)
        );";
        $conn->query($sql101);
        //function to insert data into the table
        //function takes two parameter, one being name of the entered city and another mysqli object
        function data($city,$conn){
            // Geting the current date in the format of "YYYY-MM-DD"
            $today_date=date('Y-m-d');
            //executing SQL query to select all the records with entered address and current date
            $sql0="SELECT * FROM `weather_details` WHERE address='$city' AND date='$today_date';";
            $test = $conn->query($sql0);
            //if no record exist
            if (mysqli_num_rows($test)<1){
                try{
                    //api url with the entered city name
                    $api="https://api.openweathermap.org/data/2.5/weather?q=".$city."&units=metric&appid=62e01d85f3e6ccad9863af77b907de79";
                    //if intenet is down
                    if (!$sock = @fsockopen('api.openweathermap.org', 80, $errno, $errstr, 30)) {
                        throw new Exception("No internet connectivity");
                    }
                    $json_data = @file_get_contents($api);
                    //if api can not be fetched
                    if (!$json_data) {
                        throw new Exception("API not fetched!");
                    }
                    $response = json_decode($json_data,true);
                    $address=$response["name"];
                    $wind=$response["wind"]["speed"];
                    $tem_max=$response["main"]["temp_max"];
                    $tem_min=$response["main"]["temp_min"];
                    $temp=$response["main"]["temp"];
                    
                    $dt = strtotime($today_date);
                    $day = date("l", $dt);
                    //inserting data for new city or old city with new date
                    $sql1="INSERT INTO `weather_details`(`address`, `temperature`, `wind`, `temp_max`, `temp_min`, `date`, `day`) VALUES ('$address',$temp,$wind,$tem_max,$tem_min,'$today_date','$day');";
                    
                    $conn->query($sql1);
                    return $address;
                }catch (Exception $e) {
                    //error message displayed with red background color
                    echo "
                    <style>
                        body{
                            background-color: red;
                        }
                    </style>
                    Error: " . $e->getMessage();
                    return $city;
              }
              
            }else{
                return $city;
            }
            
           
            
            
        }
        //function to get the datas from the table 
        //function takes two parameters: mysqli object and the returned address from above function

        function get($conn,$address){
            
            $today_date=date('Y-m-d');
            $k=1;
            //creating empty arrays to store required data to show 
            $final=array();
            $arr_temp=array();
            $arr_wind=array();
            $arr_day=array();
            $arr_max=array();
            $arr_min=array();
            //alert user data is fetched from the database
            echo"<script>console.log('Data fetched from Database.')</script>";
            while ($k<=7){
                $start_date=date('Y-m-d',strtotime('-'.$k.' day',strtotime($today_date)));
                //SQL query to select each record of last 7 days
                $sql2="SELECT * FROM `weather_details` WHERE date='$start_date' AND address='$address';";
                $result = $conn->query($sql2);
                //if data exist adding data to respective arrays
                if($row = $result->fetch_assoc()){
                   //adding datas to array 
                   array_push($arr_day,$row["day"]);
                   array_push($arr_temp,$row["temperature"]);
                   array_push($arr_wind,$row["wind"]);
                   array_push($arr_max,$row["temp_max"]);
                   array_push($arr_min,$row["temp_min"]);
                }else{
                    //stores each days in arr_day and NAN for all other arrays if record do not exist
                    $dt = strtotime($start_date);
                    $day = date("l", $dt);
                    $add=("NaN");
                    array_push($arr_day,$day);
                    array_push($arr_temp,$add);
                    array_push($arr_wind,$add);
                    array_push($arr_max,$add);
                    array_push($arr_min,$add);
                }
                $k=$k+1;
            }
            //adding all arrays to a single array 
            array_push($final,$arr_day,$arr_temp,$arr_wind,$arr_max,$arr_min);
            
            return $final;
            
            
        
        }
        // $address=data("North Somerset",$conn);
        // $final=get($conn,$address);
        // $city="North Somerset";
        // $address=data($city,$conn);
        // $final=get($conn,$address);
        // if (isset($_POST['search'])){
        //     $city=$_REQUEST['search'];
        //     $address=data($city,$conn);
        //     $final=get($conn,$address);
        // }
        // else{
        //     $city="North Somerset";
        //     $address=data($city,$conn);
        //     $final=get($conn,$address);
        // }
       
        if (!isset($_POST['search']) || empty($_POST['search'])) {
            $city = "North Somerset";
        } else {
            $city = $_REQUEST['search'];
        }
    
        // Get the weather data for the city (either the default or the searched city)
        $address = data($city, $conn);
        $final = get($conn, $address);

       
        
        $conn->close();
    ?>
    <!-- input field to get city's name from user -->
    <form action="" method="post">
        <div class="button">
            <input type="text" id="search" placeholder="City Name" name="search" value="<?php echo $city;?>">
            <button type="submit" id="button1"><i class="fa fa-search"></i>  Search</button>
        </div>
    </form>
    <div class="first">
        <div class="left">
            <table>
                <tr>
                    <td ><i id="a1" class="fa-solid fa-droplet"></i></td>
                    <td id="humidity">10%</td>
                </tr>
                <tr>
                    <td><i id="a2" class="fa-solid fa-wind"></i></td>
                    <td id="wind">12m/s</td>
                </tr>
                <tr>
                    <td><i id="a4" class="fa-solid fa-temperature-arrow-up"></i></td>
                    <td id="max">39°C</td>
                </tr>
                <tr>
                    <td><i id="a5" class="fa-solid fa-temperature-arrow-down"></i></td>
                    <td id="min">19°C</td>
                </tr>
                <tr>
                    <td><i class="fa-solid fa-gauge"></i></td>
                    <td id="pressure"></td>
                </tr>
                <tr>
                    <td><i class="fa-solid fa-cloud-rain"></i></td>
                    <td id="rain"></td>
                </tr>
            </table>
        </div>
        <div class="middle">
            <p id="name">Kathmandu, Nepal</p>
            <img id="image" src="" alt="Cloud">
            <div class="deg">
                <p id="temperature">27°C</p>
            </div>
            
            <p id="cloud">Clouds</p>
            
        </div>
        <div class="right">
            <li id="feelsLike">29°C</li>
            <li id="date">2023/07/06</li>
            <li id="day">Sunday</li>
            <li id="time">20:23:40</li>
        </div>

    </div>
    
    
 
    <!--table to print above data from database with the help of th arrays  -->
    <table id="history">
        <tr>
            <td>
                <p class="day"><?php echo $final[0][0];?></p>
                <P>Average Temperature: <?php echo $final[1][0];?>°C</P>
                <p>Wind: <?php echo $final[2][0];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][0];?>°C</p>
                    <p>Min: <?php echo $final[4][0];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][1];?></p>
                <P>Average Temperature: <?php echo $final[1][1];?>°C</P>
                <p>Wind: <?php echo $final[2][1];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][1];?>°C</p>
                    <p>Min: <?php echo $final[4][1];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][2];?></p>
                <P>Average Temperature: <?php echo $final[1][2];?>°C</P>
                <p>Wind: <?php echo $final[2][2];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][2];?>°C</p>
                    <p>Min: <?php echo $final[4][2];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][3];?></p>
                <P>Average Temperature: <?php echo $final[1][3];?>°C</P>
                <p>Wind: <?php echo $final[2][3];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][3];?>°C</p>
                    <p>Min: <?php echo $final[4][3];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][4];?></p>
                <P>Average Temperature: <?php echo $final[1][4];?>°C</P>
                <p>Wind: <?php echo $final[2][4];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][4];?>°C</p>
                    <p>Min: <?php echo $final[4][4];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][5];?></p>
                <P>Average Temperature: <?php echo $final[1][5];?>°C</P>
                <p>Wind: <?php echo $final[2][5];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][5];?>°C</p>
                    <p>Min: <?php echo $final[4][5];?>°C</p>
                </div>
            </td>
            <td>
                <p class="day"><?php echo $final[0][6];?></p>
                <P>Average Temperature: <?php echo $final[1][6];?>°C</P>
                <p>Wind: <?php echo $final[2][6];?>m/s</p>
                <div class="mima">
                    <p>Max: <?php echo $final[3][6];?>°C</p>
                    <p>Min: <?php echo $final[4][6];?>°C</p>
                </div>
            </td>
            
        </tr>
    </table>
    <script src="MandipRaut_232923.js"></script>
</body>
</html>