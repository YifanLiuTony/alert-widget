<?php
    session_start();
    if ($_SESSION["logged_in"] != 'true') {
        header("Location: login.php?msg=pleaseLogin");
        exit();
    }else{
        include ('dbconnect.php');

        // get response from postpone action
        // if(!empty($_GET['type'])){
        //     $response_type = $_GET['type'];
        //     $ref_num = $_GET['refNum'];
        //     $new_date = $_GET['newDate'];
        // }
        $is_postpone = false;
        if(!empty($_SESSION['type']) && $_SESSION['type'] == 'postpone'){
            // $response_type = $_GET['type'];
            $is_postpone = true;
            $ref_num = $_SESSION['refNum'];
            $new_date = $_SESSION['newDate'];
        }

        date_default_timezone_set('America/Los_Angeles');
        $date_stripped = date('Ymd');
        $date = date('Y-m-d');
        $minDate = date('Y-m-d', strtotime($date. ' + 1 days'));

        // $result = $conn->query('SELECT id, vendor, amount_due, ref_num, memo, due_date FROM ALERT_DETAIL WHERE uid="'.$_SESSION['id'].'" AND due_date<='.$date_stripped.' AND is_done=0 ORDER BY vendor,due_date');
        $result = $conn->query('select t.id, t.threshold,t.vendor,a.sum_amount,a.min_date from threshold_info t join (select sum(amount_due_num) as sum_amount,min(due_date) as min_date, vendor from ALERT_DETAIL where uid="'.$_SESSION['id'].'" AND due_date<='.$date_stripped.' AND is_done=0 group by vendor) a on t.vendor = a.vendor where t.uid = "'.$_SESSION['id'].'" and a.sum_amount > t.threshold order by a.min_date');

        $rowCount = mysqli_num_rows($result);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>AP Alert System</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="icon" type="image/png" href="img/logo.png" />
        <link rel="stylesheet" type="text/css" href="css/sheetjs.css">
        <link rel="stylesheet" type="text/css" href="css/alert-system.css">
        <link rel="stylesheet" type="text/css" href="css/datatables.min.css">

        <style type="text/css">
            #alerts{
                margin-top: 10px;
            }
            #alerts .alert{
                margin-bottom: 0;
            }
            #task-table{
                margin-bottom: 20px;
            }
            #task-table tbody tr:hover{
                background-color: #65d6d8;
            }
            .clickable{
                cursor: pointer;
            }
            .modal-lg .modal-body{
                height: 440px;
            }
            .modal-lg .modal-body .vendor-ref-list{
                overflow-y: auto;
                height: 300px;
            }
            .modal-lg .modal-body .vendor-ref-list ul{
                list-style: none;
            }
            .modal-lg .modal-body .vendor-ref-list ul li:first-child{
                font-weight: bold;
            }
            .modal-body-content{
                margin-top: 20px;
            }

            .outer {
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
                width: 400px;
            }

            .center{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <script src="vendor/alertify.js"></script>
        <script src="vendor/jquery.min.js"></script>
        <script src="vendor/bootstrap.min.js"></script>
        <script src="vendor/jquery.handsontable.full.js"></script>
        <script src="vendor/spin.js"></script>
        <script src="js/datatables.min.js"></script>


        <link rel="stylesheet" type="text/css" href="vendor/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/jquery.handsontable.full.css">
        <link rel="stylesheet" type="text/css" href="vendor/samples.css">
        <link rel="stylesheet" type="text/css" href="vendor/alertify.css">

        <div class="container">
            <div class="row">

                <h1>Hi, <?php echo $_SESSION['name'] ?></h3>

                <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px;">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
                    <li role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">Upload Excel</a></li>
                    <li role="presentation" id="detail-tab" style="display: none;"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab"></a></li>
                    <li role="presentation" style="float: right;"><a data-toggle="modal" data-target="#logout-modal"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a></li>
                </ul>

                <!-- All Modals -->

                <!-- logout modal -->
                <div id="logout-modal" class="modal fade" role="dialog">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Log out</h4>
                      </div>
                      <div class="modal-body">
                        <p>Are you sure you want to log out?</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;width: 100px;">No</button>
                        <a type="button" class="btn btn-danger" style="width: 100px;" href="logout.php">Yes</a>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Confirm Complete modal -->
                <div id="complete-modal" class="modal fade modal-lg" role="dialog">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Mark Complete</h4>
                      </div>
                      <div class="modal-body">
                            <div class="modal-body-content">
                                <div class="col-xs-12 center">
                                    <p>Are you sure you want to mark the record(s) below as <b>Complete</b>?</p>
                                </div>

                                <hr class="col-xs-12" />

                                <!-- <div id="complete-ref-num-div" class="col-xs-12 vendor-ref-list">

                                </div> -->

                                <div class="vendor-ref-list col-xs-12">

                                    <div id="complete-vendor-div" class="col-xs-9">

                                    </div>

                                    <div id="complete-ref-num-div" class="col-xs-3">

                                    </div>

                                </div>
                            </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;width: 100px;">No</button>
                        <button type="button" class="btn btn-success" style="width: 100px;" onclick="confirm_complete();">Yes</button>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Postpone modal -->
                <div id="postpone-modal" class="modal fade modal-lg" role="dialog">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <form class=".form-inline" action="postpone_action.php" method="post">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Postpone Transaction</h4>
                          </div>
                          <div class="modal-body">
                            <div class="modal-body-content">
                                <div class="col-xs-12 center">
                                    <div class="form-group">
                                        <label for="dates">Postpone to </label>&nbsp;
                                        <input type="date" name="dates" id="dates" min="<?php echo $minDate?>" required="true" >
                                        <input type="hidden" name="ref_num" id="postpone-ref-hidden">
                                    </div>
                                </div>

                                <hr class="col-xs-12" />

                                <div class="vendor-ref-list col-xs-12">

                                    <div id="postpone-vendor-div" class="col-xs-9">

                                    </div>

                                    <div id="postpone-ref-num-div" class="col-xs-3">

                                    </div>

                                </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <a type="button" class="btn btn-default" data-dismiss="modal" style="float: left;width: 100px;">Cancel</a>
                            <button type="submit" class="btn btn-success" style="width: 100px;">Postpone</button>
                          </div>
                        </div>
                    </form>

                  </div>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="home">
                
                        <?php 
                            // if(!empty($_GET['type'])&&$response_type=='postpone'){
                            if($is_postpone){
                                echo '<div id="alerts">';
                                    if($new_date!='0') {
                                        echo    '<div class="alert alert-success alert-dismissable fade in">
                                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                    Record with Ref # - '.$ref_num.' is successfully postponed to '.$new_date.'
                                                </div>';
                                    }
                                    else{
                                        echo    '<div class="alert alert-danger alert-dismissable fade in">
                                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                    Something went wrong when updating record with Ref # - '.$ref_num.'. Please let Tony know about this.
                                                </div>';
                                    }
                                echo '</div>';

                                unset($_SESSION['type']);
                                unset($_SESSION['refNum']);
                                unset($_SESSION['newDate']);
                            }
                        ?>

                        <div class="col-xs-6">
                            <p style="margin: 10px 0;">Today - <b><?php echo $date ?></b> (YYYY-MM-DD)</p>
                            <!-- <p style="margin-bottom: 0;">Today's total due - <b><span id="total-due-span"></span></b></p> -->
                        </div>
                        <!-- <div class="col-xs-6" style="margin-top: 25px;text-align: right;">
                            <button class="btn btn-success complete-btn">Complete</button>&nbsp;<button class="btn btn-danger postpone-btn">Postpone</button>
                        </div> -->
                        
                        <hr class="col-xs-12" />
                        
                        <table id="task-table" class="stripe" cellspacing="0" width="100%" style="display: none;">
                            <thead>
                              <tr>
                                <th>Vendor</th>
                                <th>Subtotal</th>
                                <th>Earliest Date</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    while($row = $result->fetch_assoc()) {
                                        // array_push($tableRows,$row);
                                        echo 
                                            '<tr class="clickable" data-name="'.$row['vendor'].'">
                                                <td>'.$row['vendor'].'</td>
                                                <td>'.$row['sum_amount'].'</td>
                                                <td>'.$row['min_date'].'</td>
                                            </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>

                        <hr/>
                    </div>


                    <div role="tabpanel" class="tab-pane" id="upload">
                        <div id="left" class="col-xs-2">
                            <div id="logo" style="padding-left: 0">
                                <img src="img/logo.png" class="logo" alt="SheetJS Logo" width=128px height=128px />
                            </div>

                            <div id="drop">Drop a file here</div>
                            <h3> Choose a worksheet:</h3>
                            <div id="buttons"></div>
                            
                        </div>

                        <div id="right" class="col-xs-10">
                            <div id="header" style="height: auto;">
                                <!-- <pre id="out"></pre> -->
                                <h2>AP Alert System - Prototype</h2>
                                <h3>
                                    Drop a spreadsheet in the box to the left to see a preview.<br/>
                                </h3>
                            </div>
                            <div id="hot" class="handsontable"></div>
                            <div id="sql-buttons" class="row" style="display: none;margin-left: 0;">
                                <button style="margin-top: 10px;" onclick="uploadSheets()" class="btn btn-primary">Upload All Sheets</button>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="detail-tab">
                        <h1 id="detail-vendor-name"></h1>
                        <div id="detail-vendor-info">
                            <table id="detail-table" class="display">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Amount Due</th>
                                        <th>Ref#</th>
                                        <th>Memo</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    s
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Amount Due</th>
                                        <th>Ref#</th>
                                        <th>Memo</th>
                                        <th>Due Date</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/shim.js"></script>
        <script src="js/xlsx.full.min.js"></script>
        <script src="js/dropsheet.js"></script>
        <script src="js/main.js"></script>

        <script type="text/javascript">

            $(document).ready(function() {

                // calculateSubtotal();

                $('#task-table').DataTable({
                    "aaSorting": []
                });

                $('#task-table').show();
            } )

            // click handler for redirecting to detail page
            $('#task-table tbody tr').click(function () {
                var name = $(this).data('name');
                console.log(name);
                $.ajax({
                    type: "GET",
                    url: "get_detail.php",
                    data: {vendor_name: name},
                    success: function(detailList){
                        // var resultList = JSON.parse(detailList);
                        var resultList = detailList;
                        console.log('html: '+resultList);
                        console.log('name: '+name);
                        $('#detail-vendor-name').text(name);
                        $('#detail-tab').show();
                        $('#detail-tab a').html(name);

                        $('#detail-table').DataTable({
                            data: resultList,
                            columns: [
                                {title: "Vendor"},
                                {title: "Amount Due"},
                                {title: "Ref#"},
                                {title: "Memo"},
                                {title: "Due Date"}
                            ]
                        });

                        $('#detail-tab').click();
                        // window.location = window.location.pathname + window.location.hash;
                    }  
                });
            })

            function calculateSubtotal(){


                var vendor = '';
                var cur_vendor = '';
                var string_amount = '';

                var subtotal;
                var subtotal_final = 0;
                var total_due = 0;

                var cur_amount;

                var first_vendor;

                // calculate and display subtotal for each vendor & total due for today
                $('#task-table > tbody > tr').each(function () {
                    cur_vendor = $(this).find('td:first').text();
                    string_amount = $(this).find('td:nth-child(2)').text();
                    cur_amount = Number(string_amount.replace(/[^0-9\.-]+/g,""));
                    total_due += cur_amount;

                    if(vendor==''){
                        // base case
                        vendor = cur_vendor;
                        subtotal = cur_amount;
                        $(this).find('td:first').attr('id','first_vendor');
                    }else if(vendor == cur_vendor){
                        subtotal += cur_amount;
                    }else{
                        subtotal_final = subtotal.toFixed(2);
                        first_vendor = $('#first_vendor');
                        first_vendor.html(first_vendor.text()+' <b>('+subtotal_final+')</b>');
                        first_vendor.attr('id','');
                        subtotal = cur_amount;

                        $(this).find('td:first').attr('id','first_vendor');
                        vendor = cur_vendor;

                    }
                });

                total_due = '$ ' + total_due.toFixed(2);
                $('#total-due-span').text(total_due);

                var last = $('#task-table > tbody > tr:last');
                if(last.length){
                    subtotal_final = subtotal.toFixed(2);
                    var f_col = last.find('td:first');
                    f_col.html(f_col.text()+' <b>('+subtotal_final+')</b>');
                }
            }

            // stores workbook sheets after drag and drop
            var wbSheets = [];

            function uploadSheets() {
                // iterate through json
                // for (var s = 0; s < wbSheets.length; s++) {
                    if(wbSheets[0] && wbSheets[1]){
                        $.ajax({
                            type: "POST",
                            url: "insert_sheets.php",
                            data: {data: JSON.stringify(wbSheets)},
                            success: function(html){
                                alert(html);
                                // window.location = window.location.pathname + window.location.hash;
                            }  
                        });
                    }
                // }
            }

            var vendor_list = [];
            var ref_list = [];

            $("body").on("change",".row-checkbox",function(){
                checkBoxEventContent($(this));
            });

            $("body").on("click",".row-checkbox-parent",function(e){
                if (e.target !== this)
                    return;

                var checkbox = $(this).find('.row-checkbox');
                checkbox.click();
                // checkbox.prop('checked',(checkbox.prop('checked') ? false : true));
                // checkBoxEventContent(checkbox);
            });



            function checkBoxEventContent(checkbox) {

                var vendor = checkbox.data("vendor");
                var ref_num = checkbox.data("ref");

                if(checkbox.prop('checked')){

                    vendor_list.push(vendor);
                    ref_list.push(ref_num);

                }else{

                    vendor_list.splice(vendor_list.indexOf(vendor), 1 );
                    ref_list.splice(ref_list.indexOf(ref_num), 1 );

                }

                console.log(vendor_list);
                console.log(ref_list);
            }

            $("body").on("click",".complete-btn",function(){
                if(checkListEmpty()){
                    // $('#complete-ref-num-div').html(constructVendorList());
                    var lists = constructVendorList();
                    $('#complete-vendor-div').html(lists[0]);
                    $('#complete-ref-num-div').html(lists[1]);
                    $('#complete-modal').modal();
                }
            });

            $("body").on("click",".postpone-btn",function(){
                if(checkListEmpty()){
                    var lists = constructVendorList();
                    $('#postpone-vendor-div').html(lists[0]);
                    $('#postpone-ref-num-div').html(lists[1]);
                    $('#postpone-ref-hidden').val(ref_list.join(','));
                    $('#postpone-modal').modal();
                }
            });

            function checkListEmpty() {
                console.log(vendor_list.length);
                console.log(ref_list.length);
                if (vendor_list.length&&ref_list.length){
                    return true;
                }else{
                    alert('Please select at least one transaction before proceeding.');
                    return false;
                }
            }

            function constructVendorList() {

                var result_vendor = '<ul>';
                var result_ref = '<ul>';

                result_vendor += ('<li>Vendor</li>');
                result_ref += ('<li>Ref #</li>');

                for(var i=0;i<vendor_list.length;i++){
                    result_vendor += ('<li>'+vendor_list[i]+'</li>');
                    result_ref += ('<li>'+ref_list[i]+'</li>');
                }
                result_vendor += '</ul>';
                result_ref += '</ul>';

                var result = [];
                result.push(result_vendor);
                result.push(result_ref);

                return result;
            }

            function confirm_complete() {
                console.log(JSON.stringify(ref_list));
                $.ajax({
                    type: "POST",
                    url: "confirm_complete.php",
                    data: {ref_num: JSON.stringify(ref_list)},
                    success: function (html) {
                        alert(html);
                        window.location = window.location.pathname + window.location.hash;
                    }
                });
                $('#complete-modal').modal('toggle');
            }

            // function createRefListJSON(refs) {
            //     refs.forEach(function (item) { 
            //         item = "'"+item+"'";
            //     });
            //     return JSON.stringify(refs);
            // }

          // var _gaq = _gaq || [];
          // _gaq.push(['_setAccount', 'UA-36810333-1']);
          // _gaq.push(['_setDomainName', 'sheetjs.com']);
          // _gaq.push(['_setAllowLinker', true]);
          // _gaq.push(['_trackPageview']);

          // (function() {
          //   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          //   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          //   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          // })();
        </script>
    </body>
</html>
