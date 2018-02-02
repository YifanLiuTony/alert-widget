<!DOCTYPE html>
<html>
<head>
	<title>Test page</title>

        <link rel="stylesheet" type="text/css" href="vendor/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/b-1.5.1/sl-1.2.5/datatables.min.css"/>
</head>
<body>
	<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>
					test
				</th>
				<th>
					test2
				</th>
				<th>
					test3
				</th>
				<th>
					test4
				</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
			<tr><td>data1</td><td>data2</td><td>data3</td><td>data4</td></tr>
		</tbody>
	</table>
</body>

        <script type="text/javascript" src="vendor/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="vendor/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/b-1.5.1/sl-1.2.5/datatables.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function () {
			$('#example').DataTable();
		})
	</script>
</html>