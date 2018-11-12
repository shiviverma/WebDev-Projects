<html>
 <head>
 <title>PHP File</title>
 <meta http-equiv="Content-Type" content="text/html;
 charset=ISO-8859-1"> 
<style>
    #desc{
        color: gray;
        font-size: 80%;
    }
    .anch{
        text-decoration: none;
    }
    #container{
        width: 80%;
        border: 1px solid gray;
        align-self: center;
        margin: auto;

    }
	.tab{
        border-collapse: collapse;
        width: 80%;
        }
    .tab td{
	border: 1px solid gray;
	}
    button.show_hide {
    background-color: white;
    cursor:pointer;
    padding: 15px;
    width: 100%;
    border: none;
    text-align: left;
    font-size: 15px;
    transition: 0.5s;
    text-align: center;
    }
    div.feed {
    padding: 0 18px;
    background-color: white;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-out;
    }
</style>
</head>
<body>

 <table align="center" bgcolor="#e6e6e6" >
	<th style="border-bottom: 1px solid gray"><i> Stock search </i></th>
	<tr>
		<form method="POST" action="hwknee.php" onsubmit="return formValid()">
				<td> Enter Stock Ticker Symbol:*<input  id="sea" type="text" name="symbol" value="<?php echo isset($_POST['symbol'])?$_POST['symbol']:''; ?>"> </td>
		</tr>
		<tr>
				<td align="right"><input type="submit" name="Search" value="Search">
				<input type="reset" value="Clear" onclick="clearFunc()"> </td>
		</tr>
		</form>
	<tr>
	<td>
	<p> *-Mandatory fields<br><br> </p>
	</td>
	</tr>
</table>
<script>
function formValid()
{
    if(document.getElementById('sea').value=='')
    {
        alert("Please enter a symbol");
        return false;
    }
}
function clearFunc()
     {
     document.getElementById('sea').setAttribute('value',"");
     document.getElementById('clr').innerHTML=" ";
     document.getElementById('desc').innerHTML=" ";
     document.getElementById('up_down').innerHTML=" ";
     document.getElementById('feed').innerHTML=" ";
    
     }

</script> 
<script>
    function createNews(jsonLinks){

        var tableData = '';
        for(var i = 0; i < jsonLinks.length; i++) {
                var obj = jsonLinks[i];
                tableData += '<tr ><td class="tab" style="border-bottom: 1px solid gray" bgcolor="f2f2f2"><a href="'+obj.link+'" target="_blank" style="text-decoration: none;">'+obj.title+'</a><span>&nbsp;&nbsp;&nbsp;Publicated Time: '+obj.date+'</span></td></tr>'

            }
        document.getElementById('tableNews').innerHTML = tableData;

    }
</script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<?php 
echo'<div id="clr">';

if(isset($_POST['symbol'])){

            $sym= $_POST["symbol"];
        	echo' <script> var stock_sym=',json_encode($sym),'</script>';
            // If no symbol entered
           
        	
            	$request = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&outputsize=full&symbol='.$sym.'&apikey=FUYI6C5PYHSZ8L9H';
            	$response = @file_get_contents($request);
                if($response==true){
                $jsonobj = json_decode($response, true);
                if(!isset($jsonobj["Error Message"]))
                {
            	$tsjsonobj = $jsonobj["Time Series (Daily)"];
            	date_default_timezone_set("EST");
            	$allkeys=array_keys($tsjsonobj);
            	$allvalues=array_values($tsjsonobj);
                //print_r($allvalues);
            	$dates = [];
                	for ($i= 0; $i<132; $i++){
                		$dates[] = strtotime($allkeys[$i])*1000; 
            	    }
        	
            	$curr=$allvalues[1]["4. close"];
            	$change= $allvalues[0]["4. close"]-$allvalues[1]["4. close"];
            	$perc=(($change/$curr)*100);
                if($change>0){
                    $image="http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png";
                }
                else{
                    $image="http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png";
                }
                if($perc>0){
                    $imgsrc="http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png";
                }
                else
                {
                    $imgsrc="http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png";
                }
            	$close=array();
            	$volume=array();
            	$ref=$jsonobj["Meta Data"];
            	$lastref=$ref["3. Last Refreshed"];

            	$dateSplit = [];
            	$dateSplit=  explode('-', $lastref);
            	$max=0;
            	$k=0;

            	$maxVal = 0;
                foreach($allvalues as $value)
                {   if($k>132)
                    break;
                    $k++;

                    $currentVol = (float)$value["5. volume"];
                    if($maxVal < $currentVol){
                        $maxVal = $currentVol;
                    }
                    array_push($close, (float)$value["4. close"]);
                    array_push($volume, $currentVol);   
                   
                }
                $allvalues[0]["5. volume"]=number_format($allvalues[0]["5. volume"]);
        	
            echo' 

            <table align="center" style="border: 1px gray" width="80%" bgcolor="f2f2f2" class="tab">
            <tr>
                <td><b>Stock Ticker Symbol</b></td>
                <td align="center">'.$sym.'</td>
            </tr>
            <tr>
                <td><b>Close</b></td>
                <td align="center">',$allvalues[0]["4. close"], '</td>
            </tr>
            <tr> 
                <td><b>Open</b></td>
                <td align="center">', $allvalues[0]["1. open"], '</td>
            </tr>
            <tr>    
                <td><b>Previous Close</b></td>
                <td align="center">', $allvalues[1]["4. close"],'</td>
            </tr>
            <tr>
                <td><b>Change</b></td>

                <td align="center">',round($change,2),'<img src="',$image,'" width="20px" height="20px"></td>
            </tr>
            <tr>
                <td><b>Change Percentage</b></td>
                <td align="center">',round($perc,2),'%<img src="',$imgsrc,'" width="20px" height="20px"></td>
            </tr>
            <tr>
                <td><b>Day&#39;s Range</b></td>
                <td align="center">',$allvalues[0]["3. low"],'-',$allvalues[0]["2. high"],'</td>
            </tr>
            <tr>
                <td><b>Volume</b></td>
                <td align="center">', $allvalues[0]["5. volume"], '</td>
            </tr>
            <tr>
                <td><b>Timestamp</b></td>
                <td align="center">',$allkeys[0],'</td>
            </tr>
            <tr>
                <td><b>Indicators</b></td>
                <td align="center">
                    <a href="javascript:void(0)" class="anch" onclick="showCharts()">Price&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'SMA\');">SMA&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'EMA\')">EMA&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0))" class="anch" onclick="getJSOn(\'STOCH\')">STOCH&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'RSI\')">RSI&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'ADX\')">ADX&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'CCI\')">CCI&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'BBANDS\')">BBANDS&nbsp;&nbsp;&nbsp;</a>
                    <a href="javascript:void(0)" class="anch" onclick="getJSOn(\'MACD\')">MACD&nbsp;&nbsp;&nbsp;</a>
                </td> 
            </table><br>';

            echo '<div id="container" style="width:80%; height:400px; margin: auto;"></div>';
        	$alphaurl="https://www.alphavantage.co/";
            $target="_blank";
            $aid="anch";
        	echo "<script>
            
            function showCharts(){
            	Highcharts.chart('container', {
                    chart: {
                        zoomType: 'xy'
                    },
                   
                    title: {
                        text: 'Stock Price(",$dateSplit[1],'/',$dateSplit[2],'/',$dateSplit[0],")'
                    },
                    subtitle: {
                        text: '<a href=",$alphaurl," target=".$target." class=".$aid."> Source: Alpha Vantage</a>',
                        useHTML: true
                    },
                    
                xAxis: [{
                    categories: ",json_encode($dates),",
                    crosshair: true,
                    type: 'datetime',
                    labels:
                    {
                    	format:'{value:%m/%d}'
                    },
                    tickInterval:5,
                    reversed :true
                }],
                    yAxis:[{ // Primary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.red
                        }
                    },
                    title: {
                        text: 'Stock Price',
                        style: {
                            color: Highcharts.setOptions().colors[10]
                        }
                    }
                }, { // Secondary yAxis
                	
                    title: {
                        text: 'Volume',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                       
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true,
                    max: 3.5*".$maxVal."
                }],
                	
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign : 'middle'
                    },
                    plotOptions: {
                        area: {
                            
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            },
                            threshold: null
                        }
                       
                    },
                    tooltip: {
                        shadow: false
                    },

                    series: [{
                        type: 'area',
                        name: '",$sym,"',
                        data: ",json_encode($close),",
                        color: '#ff3333',
                        tooltip:{
                        xDateFormat:'%m/%d',
                            }
                    	 

                    },
                    {
                    	type: 'column',
                        name: '",$sym," Volume',
            	    	data : ",json_encode($volume),",
            	    	yAxis: 1,
            	    	color: '#FFFFFF',
                        tooltip:{
                        xDateFormat:'%m/%d',
                            }
                        

                    }]
                });
            }
            
            </script>";

            echo "<script>showCharts();</script>";

        	echo '<button class="show_hide" onclick="hide_show()" ><span id="desc">Click to show Stock News</span><br><img id="up_down" src="http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png" width="20px" height="20px"></button>
            <div class="feed" id="feed">
            ';
               
                $url="https://seekingalpha.com/api/sa/combined/".$sym.".xml";
                $xml= simplexml_load_file($url);
                $linkArticle=array();
                $i=0;
                foreach($xml->channel->item as $currVal)
                    {

                    $link=$currVal->link;
                    if(strpos($link,'article')!=false)
                        {
                            $newArticle=array();

                            $newArticle['link'] = strval($currVal->link);
                            $newArticle['title'] = strval($currVal->title);
                            $newArticle['date'] = strval($currVal->pubDate);
                            $newArticle['date'] = explode('-', $newArticle['date'])[0];
                            //pushing each article data in linkArticle array
                            array_push($linkArticle, $newArticle);
                            $i++;
                        }
                    if($i==5)
                        break;
                    
                    }
                  echo'   <table align="center" id="tableNews" style="border: 1px solid gray" width="80%"></table>';
                echo '<script>createNews(',json_encode($linkArticle),');</script>';

        echo'</div>';
        }
                else
                {
                    echo '<table align="center" style="border: 1px solid gray" width="80%" bgcolor="f2f2f2" class="tab">
                    <tr>
                        <td><b>Error </b></td>
                        <td align="center">Error: NO record has been found, please enter a valid symbol</td> 
                    </tr>
                    </table>';
                }
            }
            else
            {
                echo '<table align="center" style="border: 1px solid gray" width="80%" bgcolor="f2f2f2" class="tab">
                    <tr>
                        <td><b>Error </b></td>
                        <td align="center">Server Error</td> 
                    </tr>
                    </table>';
            }
            
           
        }
            
    
        echo '</div>';?>
	
<!-- Script to fetch charts DATA -->
    <script>
    function getJSOn(param){
        var xtpRequest;
        xtpRequest = new XMLHttpRequest();
        
        if (!xtpRequest) {
          return false;
        }
        xtpRequest.onreadystatechange = alertContents;
        xtpRequest.open('GET', 'http://www.alphavantage.co/query?function='+param+'&symbol='+stock_sym+'&interval=daily&time_period=10&series_type=close&apikey=FUYI6C5PYHSZ8L9H', true);
        xtpRequest.send();
        var data;
        var SMAArray = [];
        var BB1Array = [];
        var BB2Array = [];

        function alertContents() {

                if (xtpRequest.readyState === XMLHttpRequest.DONE) {
                  
                   if (xtpRequest.status === 200) {

                       var js= JSON.parse(xtpRequest.responseText);
                      
                       name=js["Meta Data"]["2: Indicator"];
                       data = js['Technical Analysis: '+param];
                       var k=0;
                       var a=0;
                       var dateArray = [];
                       if(param=="BBANDS"){
                        
                        for (var key in data){
                            if(k>132)
                                    break;
                                else{
                                    k++;
                                    dateArray.push(Date.parse(key));
                                    SMAArray.push(parseFloat(data[key]["Real Lower Band"]));
                                    BB1Array.push(parseFloat(data[key]["Real Middle Band"]));
                                    BB2Array.push(parseFloat(data[key]["Real Upper Band"]));

                                 }
                             }
                             var y1 = stock_sym+" Real Lower Band";
                             var y2 = stock_sym+" Real Middle Band";
                             var y3 = stock_sym+" Real Upper Band";

                         create(dateArray, SMAArray, name, BB1Array, BB2Array, param, y1, y2, y3, stock_sym);
                     }
                        else if(param=="STOCH"){
                        for (var key in data){
                            if(k>132)
                                    break;
                                else{
                                    k++;
                                    dateArray.push(Date.parse(key));
                                    SMAArray.push(parseFloat(data[key]["SlowK"]));
                                    BB1Array.push(parseFloat(data[key]["SlowD"]));
                                 }
                             }
                             var y1 = stock_sym+" SlowK";
                             var y2 = stock_sym+" SlowD";
                             create_two(dateArray, SMAArray, name, BB1Array, param, y1, y2, stock_sym);
                        }
                        else if(param=="MACD"){
                        for (var key in data){

                            if(k>132)
                                    break;
                                else{
                                    k++;
                                    dateArray.push(Date.parse(key));
                                    SMAArray.push(parseFloat(data[key]["MACD_Hist"]));
                                    BB1Array.push(parseFloat(data[key]["MACD_Signal"]));
                                    BB2Array.push(parseFloat(data[key]["MACD"]));
                                 }
                             }
                             var y1 = stock_sym+" MACD_Hist";
                             var y2 = stock_sym+" MACD_Signal";
                             var y3 = stock_sym+" MACD";
                         create(dateArray, SMAArray, name, BB1Array, BB2Array, param, y1, y2, y3, stock_sym);
                        }
                        else{
                        for (var key in data) {
    
                                if(k>132)
                                    break;
                                else{
                                    k++;
                                    dateArray.push(Date.parse(key));
                                    SMAArray.push(parseFloat(data[key][param]));   
                                }

                                
                                
                            }
                            y1=stock_sym;
                            create_one(dateArray, SMAArray, name, param, y1, stock_sym);
                        }
                            
                    }


                } 
            }

      }

    // Create charts function
    function create(dateArray, SMAArray, name, BB1Array, BB2Array, param, y1, y2, y3, stock_sym){
        
        Highcharts.chart('container', {
            chart: {zoomType: 'xy'},
            title: {text: name},
            subtitle: {text: '<a href="https://www.alphavantage.co/" target="_blank" class="anch">Source: Alpha Vantage</a>',
                        useHTML: true
                    },

            xAxis: [{
                categories: dateArray,
                type: 'datetime',
                crosshair: true,
                labels:
                {
                    format:"{value:%m/%d}"
                },
                tickInterval:5,
                reversed: true
            }],
            
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: param,
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }
            ],
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign : 'middle'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true,
                        radius: 2
                    }
                }
            },
            series: [ {
                name: y1,
                type: 'spline',
                data: SMAArray,
                tooltip:{
                xDateFormat:'%m/%d',
                    },
                },
                {
                name: y2,

                type: 'spline',
                data: BB1Array,
                tooltip:{
                xDateFormat:'%m/%d',
                    }
                },
                {
                name: y3,
                type: 'spline',
                data: BB2Array,
                tooltip:{
                xDateFormat:'%m/%d',
                     }
                }]
        });
}       
        function create_two(dateArray, SMAArray, name, BB1Array, param, y1, y2, stock_sym){
        
        Highcharts.chart('container', {
            chart: {zoomType: 'xy'},
            title: {text: name},
            subtitle: {text: '<a href="https://www.alphavantage.co/" target="_blank" class="anch">Source: Alpha Vantage</a>',
                        useHTML: true},
            xAxis: [{
                categories: dateArray,
                type: 'datetime',
                crosshair: true,
                labels:
                {
                    format:"{value:%m/%d}"
                },
                tickInterval:5,
                reversed: true
            }],
            
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: param,
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }
            ],
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign : 'middle'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true,
                        radius: 2
                    }
                }
            },
            series: [ {
                name: y1,
                type: 'spline',
                data: SMAArray,
                tooltip:{
                xDateFormat:'%m/%d',
                    },
                },
                {
                name: y2,

                type: 'spline',
                data: BB1Array,
                tooltip:{
                xDateFormat:'%m/%d',
                    }
                }
                ]
        });
}
        function create_one(dateArray, SMAArray, name, param, y1, stock_sym){
        
        Highcharts.chart('container', {
            chart: {zoomType: 'xy'},
            title: {text: name},
            subtitle: {text: '<a href="https://www.alphavantage.co/" target="_blank" class="anch">Source: Alpha Vantage</a>',
                        useHTML: true
                    },
            xAxis: [{
                categories: dateArray,
                type: 'datetime',
                crosshair: true,
                labels:
                {
                    format:"{value:%m/%d}"
                },
                tickInterval:5,
                reversed: true
            }],
            
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: param,
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }
            ],
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign : 'middle'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true,
                        radius: 2
                    }
                }
            },
            series: [ {
                name: y1,
                type: 'spline',
                data: SMAArray,
                tooltip:{
                xDateFormat:'%m/%d',
                    
                }
                }]
        });
}

    </script>

<!-- Script end -->





<script>
var acc = document.getElementsByClassName("show_hide");
var i;


  hide_show = function() {
    if( document.getElementById("desc").innerHTML == 'Click to show Stock News')
    {
      document.getElementById("desc").innerHTML = "Click to hide Stock news";
      document.getElementById("up_down").src = "http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Up.png";
    }
        else{
           document.getElementById("desc").innerHTML = 'Click to show Stock News';
           document.getElementById("up_down").src = "http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png";
        }
    var feed = document.getElementById("feed");
    if (feed.style.maxHeight){
      feed.style.maxHeight = null;
    } else {
      feed.style.maxHeight = feed.scrollHeight + "px";
    } 
  }

</script>  


</body>
</html>
