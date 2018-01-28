<!DOCTYPE html>
<html>
<head>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!-- libraries for xlsx parser -->
	<!-- https://github.com/SheetJS/js-xlsx/blob/master/jszip.js -->
	<script src="/path/to/jszip.js"></script>
	<!-- https://github.com/SheetJS/js-xlsx/blob/master/xlsx.js -->
	<script src="/path/to/xlsx.js"></script>

	<style type="text/css">

		/*horizontal & vertical center styling*/
		.center-wrapper {
		    display: table;
		    position: absolute;
		    height: 100%;
		    width: 100%;
		}

		.middle {
		    display: table-cell;
		    vertical-align: middle;
		}

		.inner {
		    margin-left: auto;
		    margin-right: auto; 
		    width: 300px;
		}

		/*drag and drop styling*/
		#drop{
			border:2px dashed #bbb;
			-moz-border-radius:5px;
			-webkit-border-radius:5px;
			border-radius:5px;
			padding:25px;
			text-align:center;
			font:20pt bold,"Vollkorn";color:#bbb
		}
		#b64data{
			width:100%;
		}
		a { text-decoration: none }
	</style>

	<?php 

		$user = 'root';
		$password = 'root';
		$db = 'tracy_alert_widget';
		$host = 'localhost';
		$port = 8889;

		$conn = mysqli_init();
		$success = mysqli_real_connect(
		   $conn, 
		   $host, 
		   $user, 
		   $password, 
		   $db,
		   $port
		);

		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 

		$sql = "SELECT username,password FROM USER_INFO where is_active > 0";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		        echo "Username: " . $row["username"]. " - Password: " . $row["password"]. "<br>";
		    }
		} else {
		    echo "0 results";
		}

	?>

	<title>Alert Widget</title>
</head>
<body>
	<div id="content">
		<div class="center-wrapper">
		  <div class="middle">
		    <div class="inner">
				<pre>
					<b><a href="http://sheetjs.com">SheetJS Data Preview Live Demo</a></b>
					(Base64 text works back to IE6; drag and drop works back to IE10)

					<a href="https://github.com/SheetJS/js-xlsx">Source Code Repo</a>
					<a href="https://github.com/SheetJS/js-xlsx/issues">Issues?  Something look weird?  Click here and report an issue</a>
					Output Format: <select name="format" onchange="setfmt()">
					<option value="csv" selected> CSV</option>
					<option value="json"> JSON</option>
					<option value="form"> FORMULAE</option>
					<option value="html"> HTML</option>
					</select><br />
					<div id="drop">Drop a spreadsheet file here to see sheet data</div>
					<input type="file" name="xlfile" id="xlf" /> ... or click here to select a file

					<textarea id="b64data">... or paste a base64-encoding here</textarea>
					<input type="button" id="dotext" value="Click here to process the base64 text" onclick="b64it();"/><br />
					<b>Advanced Demo Options:</b>
					Use Web Workers: (when available) <input type="checkbox" name="useworker" checked>
					Use readAsBinaryString: (when available) <input type="checkbox" name="userabs" checked>
					</pre>
				<pre id="out"></pre>
				<div id="htmlout"></div>
				<br />
		    </div>
		  </div>
		</div>
	</div>
</body>

<!-- uncomment the next line here and in xlsxworker.js for encoding support -->
<script src="dist/cpexcel.js"></script>
<script src="shim.js"></script>
<script src="jszip.js"></script>
<script src="xlsx.js"></script>

<script type="text/javascript">

function get_radio_value( radioName ) {
    var radios = document.getElementsByName( radioName );
    for( var i = 0; i < radios.length; i++ ) {
        if( radios[i].checked ) {
            return radios[i].value;
        }
    }
}
 
function to_json(workbook) {
    var result = {};
    workbook.SheetNames.forEach(function(sheetName) {
        var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
        if(roa.length > 0){
            result[sheetName] = roa;
        }
    });
    return result;
}
 
function to_csv(workbook) {
    var result = [];
    workbook.SheetNames.forEach(function(sheetName) {
        var csv = XLSX.utils.sheet_to_csv(workbook.Sheets[sheetName]);
        if(csv.length > 0){
            result.push("SHEET: " + sheetName);
            result.push("");
            result.push(csv);
        }
    });
    return result.join("\n");
}
 
function to_formulae(workbook) {
    var result = [];
    workbook.SheetNames.forEach(function(sheetName) {
        var formulae = XLSX.utils.get_formulae(workbook.Sheets[sheetName]);
        if(formulae.length > 0){
            result.push("SHEET: " + sheetName);
            result.push("");
            result.push(formulae.join("\n"));
        }
    });
    return result.join("\n");
}
 
var tarea = document.getElementById('b64data');
function b64it() {
    var wb = XLSX.read(tarea.value, {type: 'base64'});
    process_wb(wb);
}
 
function process_wb(wb) {
    var output = "";
    switch(get_radio_value("format")) {
        case "json":
        output = JSON.stringify(to_json(wb), 2, 2);
            break;
        case "form":
            output = to_formulae(wb);
            break; 
        default:
        output = to_csv(wb);
    }
    if(out.innerText === undefined) out.textContent = output;
    else out.innerText = output;
}
 
var drop = document.getElementById('drop');
function handleDrop(e) {
    e.stopPropagation();
    e.preventDefault();
    var files = e.dataTransfer.files;
    var i,f;
    for (i = 0, f = files[i]; i != files.length; ++i) {
        var reader = new FileReader();
        var name = f.name;
        reader.onload = function(e) {
            var data = e.target.result;
            //var wb = XLSX.read(data, {type: 'binary'});
            var arr = String.fromCharCode.apply(null, new Uint8Array(data));
            var wb = XLSX.read(btoa(arr), {type: 'base64'});
            process_wb(wb);
        };
        //reader.readAsBinaryString(f);
        reader.readAsArrayBuffer(f);
    }
}
 
function handleDragover(e) {
    e.stopPropagation();
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
}
 
if(drop.addEventListener) {
    drop.addEventListener('dragenter', handleDragover, false);
    drop.addEventListener('dragover', handleDragover, false);
    drop.addEventListener('drop', handleDrop, false);
}
</script>
</html>