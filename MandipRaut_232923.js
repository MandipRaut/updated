// cityname(document.getElementById("search").value)
// document.getElementById("button1").addEventListener("click",myFunction);
myFunction()
function myFunction(){
    //stores the user input into variable input
    input=document.getElementById("search").value
    // localStorage.removeItem(input.toUpperCase());
    cityname(input)
}

function cityname(namec){
    //checks if the city exist in local storage
    const name = localStorage.getItem(namec.toUpperCase());
    if(name){
        
        const finalVal=JSON.parse(name);
        //if exist checking if the city was fetched today to get accurate weather data
        var today = new Date();
        var dd = today.getDate();

        var mm = today.getMonth()+1; 
        var yyyy = today.getFullYear(); 

        if(dd<10) 
        {
            dd='0'+dd;
        } 

        if(mm<10) 
        {
            mm='0'+mm;
        } 
        today = yyyy+'-'+mm+'-'+dd;
        // console.log(today);
        // console.log(finalVal.date)
        1
2       //stores current hour
        const now = new Date();
        const hours = now.getHours();
        const hh=hours-2;

        //checks if local storage contains today's data along with data within 3 hours
        if (finalVal.date==today && finalVal.hours>=hh){
            icon=finalVal.icon
            if(icon=="10d" || icon=="10n" || icon=="09d" || icon=="09n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('rain.jpg')";
            }else if(icon=="01d" || icon=="01n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('clear.jpg')";
            }else if(icon=="02d" || icon=="02n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('clouds.jpg')";
            }else if(icon=="03d" || icon=="03n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('scatter.jpg')";
            }else if(icon=="04d" || icon=="04n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('broken.jpg')";
            }else if(icon=="11d" || icon=="11n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('thunder.jpg')";
            }else if(icon=="13d" || icon=="13n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('snow.jpg')";
            }else if(icon=="50d" || icon=="50n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('mist.jpg')";
            }
            //informing user data was fetched from local storage
            console.log("Data fetched from localstorage")
            document.getElementById("time").innerHTML=finalVal.time;
            document.getElementById("name").innerHTML=finalVal.name+", "+finalVal.name1;
            document.getElementById("pressure").innerHTML=finalVal.pressure+"mb";
            document.getElementById("min").innerHTML=finalVal.min+"°C";
            document.getElementById("feelsLike").innerHTML="<i>Feels Like:</i> "+"<b>"+finalVal.feel+"°C</b>"
            document.getElementById("max").innerHTML=finalVal.max+"°C";
            document.getElementById("image").alt=finalVal.icon;
            document.getElementById("cloud").innerHTML=finalVal.cloud;
            document.getElementById("wind").innerHTML=finalVal.wind+"m/s";
            document.getElementById("humidity").innerHTML=finalVal.humidity+"%";
            document.getElementById("temperature").innerHTML=finalVal.temperature+"°C";
            document.getElementById("date").innerHTML='<i class="fa-solid fa-calendar-days"></i> '+finalVal.date;
            document.getElementById("day").innerHTML='<i class="fa-solid fa-sun"></i> '+finalVal.day;
            if(finalVal.rain=="N/A"){
                document.getElementById("rain").innerHTML=finalVal.rain
            }else{
                document.getElementById("rain").innerHTML=finalVal.rain+"mm";
            }
            document.getElementById("cloud").innerHTML=finalVal.dis;
            document.getElementById("image").src="https://openweathermap.org/img/wn/"+icon+".png"
            
    
        }
        else{
            //if data is older than a day or older than 3 hours
            console.log("False date")
            //calling function to fetch api 
            apiFetch(namec)
               
        }
    }else{
        //if user enters new city 
        console.log('Name not found');
        apiFetch(namec)
    }
    function apiFetch(namec){
        //creating empty array everytime new city is called to store it in local storage
        arr={}
        const now = new Date();
        const hours = now.getHours();
        //storing data
        arr["hours"]=hours;
        //informing user data was fetched from the api
        console.log("Data fetched from api")
        //fetching the api with user input city name
        fetch('https://api.openweathermap.org/data/2.5/weather?q='+namec+'&units=metric&appid=62e01d85f3e6ccad9863af77b907de79')
         //converting response into json file
        .then(response => response.json())
        .then(data => program(data))
        //incase of invalid city name search bar clears out and a invalid name message is plased in the placeholder
        .catch(error => {
            document.getElementById("search").value=""
            document.getElementById("search").placeholder="Invalid name. Please enter again."
        })

        function program(d){
            //storing latitude and longitude of the city 
            let latitude=d["coord"]["lat"]
            let longitude=d["coord"]["lon"]
             //fetching data to get rain information
            fetch('https://api.openweathermap.org/data/2.5/weather?lat='+latitude+'&lon='+longitude+'&appid=62e01d85f3e6ccad9863af77b907de79')
            .then(res=>res.json())
            .then(dat=>{
                
                
                let rain=dat["rain"]["1h"];
                document.getElementById("rain").innerHTML=rain+"mm";
                arr["rain"]=rain;
            })
            .catch(error=>{
                //N/A message if no rain 
                document.getElementById("rain").innerHTML="N/A";
                arr["rain"]="N/A";
            })
            //api to get the time of the country, day of the week and time along with full name of the city
            fetch('https://api.timezonedb.com/v2.1/get-time-zone?key=GTVY9MLSTZYO&format=json&by=position&lat='+latitude+'&lng='+longitude)
            .then(response => response.json())
            .then(data1 => {
                // console.log("final",arr["name"].toUpperCase())
                // console.log(data1)
                
                let datetime=data1["formatted"]
                time=datetime.split(" ")
                const today = new Date();
                const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                const day = days[today.getDay()];
                let country=data1["countryName"]
                document.getElementById("date").innerHTML='<i class="fa-solid fa-calendar-days"></i> '+time[0]
                arr["date"]=time[0];
                document.getElementById("time").innerHTML='<i class="fa-regular fa-clock"></i> '+time[1];
                arr["time"]=time[1]
                document.getElementById("name").innerHTML+=", "+country
                arr["name1"]=country;
                document.getElementById("day").innerHTML='<i class="fa-solid fa-sun"></i> '+day;
                arr["day"]=day;
                let insert=JSON.stringify(arr);
                localStorage.setItem(arr["name"].toUpperCase(),insert);
            })
            .catch(error => {
                console.log("Error");
                // //added
                // cityname(namec)
                
            })
            //other datas from the first fetched api
            let cloud=d["weather"][0]["description"];
            arr["dis"]=cloud;
            let icon=d["weather"][0]["icon"]
            let temperature=d["main"]["temp"]
            let humidity=d["main"]["humidity"]
            let name=d["name"]
            let wind=d["wind"]["speed"]
            let max=d["main"]["temp_max"]
            let min=d["main"]["temp_min"]
            let feel=d["main"]["feels_like"]
            let pre=d["main"]["pressure"]
             //check the icon to set the background image according to the icon value
            if(icon=="10d" || icon=="10n" || icon=="09d" || icon=="09n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('rain.jpg')";
            }else if(icon=="01d" || icon=="01n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('clear.jpg')";
            }else if(icon=="02d" || icon=="02n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('clouds.jpg')";
            }else if(icon=="03d" || icon=="03n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('scatter.jpg')";
            }else if(icon=="04d" || icon=="04n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('broken.jpg')";
            }else if(icon=="11d" || icon=="11n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('thunder.jpg')";
            }else if(icon=="13d" || icon=="13n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('snow.jpg')";
            }else if(icon=="50d" || icon=="50n"){
                document.body.style.backgroundImage= "linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('mist.jpg')";
            }
            document.getElementById("name").innerHTML=name
            arr["name"]=name;
            document.getElementById("temperature").innerHTML=temperature+"°C" 
            arr["temperature"]=temperature;
            document.getElementById("humidity").innerHTML=humidity+"%"
            arr["humidity"]=humidity;
            document.getElementById("wind").innerHTML=wind+"m/s" 
            arr["wind"]=wind
            document.getElementById("cloud").innerHTML=cloud
            document.getElementById("image").src="https://openweathermap.org/img/wn/"+icon+".png"
            arr["icon"]=icon
            document.getElementById("search").placeholder="City Name"
            document.getElementById("max").innerHTML=max+"°C"
            arr["max"]=max
            document.getElementById("min").innerHTML=min+"°C"
            arr["min"]=min
            document.getElementById("feelsLike").innerHTML="<i>Feels Like:</i> "+"<b>"+feel+"°C</b>"
            arr["feel"]=feel
            document.getElementById("pressure").innerHTML=pre+"mb"
            arr["pressure"]=pre
        
    }
}

}

