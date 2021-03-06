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

        date_default_timezone_set('America/Los_Angeles');
        $date_stripped = date('Ymd');
        $date = date('Y-m-d');
        $minDate = date('Y-m-d', strtotime($date. ' + 1 days'));

        $query = 'select t.id, t.threshold,t.vendor,a.sum_amount,a.min_date from THRESHOLD_INFO t join (select sum(amount_due_num) as sum_amount,min(due_date) as min_date, vendor from ALERT_DETAIL where uid="'.$_SESSION['id'].'" AND due_date<='.$date_stripped.' AND is_done=0 group by vendor) a on t.vendor = a.vendor where t.uid = "'.$_SESSION['id'].'" and a.sum_amount > t.threshold order by a.min_date';

        $result = $conn->query($query);

        $rowCount = mysqli_num_rows($result);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>AP Alert System</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="img/logo.png" />


        <link rel="stylesheet" type="text/css" href="vendor/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/jquery.handsontable.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/samples.css">
        <!-- <link rel="stylesheet" type="text/css" href="vendor/alertify.css"> -->
        <link rel="stylesheet" type="text/css" href="vendor/sheetjs.css">
        <link rel="stylesheet" type="text/css" href="vendor/datatables.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/select.datatables.min.css">
        <link rel="stylesheet" type="text/css" href="alert-system.css">


        <style type="text/css">
            html body{
                background-color: white;
            }
            #alerts{
                margin-top: 10px;
            }
            #alerts .alert{
                margin-bottom: 0;
            }
            hr{
                width: 100%;
            }
            tbody tr{
                cursor: pointer;
            }
            #task-table tbody tr:hover,
            #detail-table tbody tr:hover{
                background-color: #65d6d8;
            }
            #detail-table tbody tr.selected td{
                background-color: #5bc0de;
            }
            .modal{
                width: 100%!important;
            }
            .modal-lg .modal-body{
                height: 440px;
            }
            .modal-lg .modal-body .vendor-ref-list{
                overflow-y: auto;
                height: 280px;
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
            .modal .modal-subtotal-container p{
                text-align: center;
                font-weight: bold;
                font-size: 16px;
            }
            .vendor-ref-list dl dd{
                text-align: center;
                line-height: 30px;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="row">

                <h1>Hi, <?php echo $_SESSION['name'] ?></h3>

                <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px;">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
                    <li role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">Upload Excel</a></li>
                    <li role="presentation" id="detail-tab-controll" style="display: none;"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab"></a></li>
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
                        <h4 class="modal-title">Mark Complete - <span class="vendor-name-span" style="font-weight: bold;"></span></h4>
                      </div>
                      <div class="modal-body">
                            <div class="modal-body-content">
                                <div class="col-xs-12 center">
                                    <p>Are you sure you want to mark the record(s) below as <b>Complete</b>?</p>
                                </div>

                                <hr />

                                <!-- <div id="complete-ref-num-div" class="col-xs-12 vendor-ref-list">

                                </div> -->

                                <div class="vendor-ref-list col-xs-12">

                                    <div class="modal-ref-num-div col-xs-6">

                                    </div>

                                    <div class="modal-amount-div col-xs-6">

                                    </div>

                                </div>
                                <hr/>
                                <div class="modal-subtotal-container col-xs-6 col-xs-offset-6">
                                    <p class="modal-subtotal"></p>
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
                    <!-- <form class=".form-inline" action="postpone_action.php" method="post"> -->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Postpone Transaction</h4>
                            <p>Vendor Name: <span class="vendor-name-span"></span></p>
                          </div>
                          <div class="modal-body">
                            <div class="modal-body-content">
                                <div class="col-xs-12 center">
                                    <div class="form-group">
                                        <label for="dates">Postpone to </label>&nbsp;
                                        <input type="date" name="dates" id="dates" min="<?php echo $minDate?>" required="true" >
                                    </div>
                                </div>

                                <hr />

                                <div class="vendor-ref-list col-xs-12">

                                    <div class="modal-ref-num-div col-xs-6">

                                    </div>

                                    <div class="modal-amount-div col-xs-6">

                                    </div>

                                </div>
                                <hr/>
                                <div class="modal-subtotal-container col-xs-6 col-xs-offset-6">
                                    <p class="modal-subtotal"></p>
                                </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;width: 100px;">Cancel</button>
                            <button type="button" class="btn btn-success" style="width: 100px;" onclick="confirm_postpone();">Confirm</button>
                          </div>
                        </div>
                    <!-- </form> -->

                  </div>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="home">
                
                        <?php 
                            // if($is_postpone){
                            //     echo '<div id="alerts">';
                            //         if($new_date!='0') {
                            //             echo    '<div class="alert alert-success alert-dismissable fade in">
                            //                         <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            //                         Record with Ref # - '.$ref_num.' is successfully postponed to '.$new_date.'
                            //                     </div>';
                            //         }
                            //         else{
                            //             echo    '<div class="alert alert-danger alert-dismissable fade in">
                            //                         <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            //                         Something went wrong when updating record with Ref # - '.$ref_num.'. Please let Tony know about this.
                            //                     </div>';
                            //         }
                            //     echo '</div>';
                            // }
                        ?>

                        <div class="col-xs-6">
                            <p style="margin: 10px 0;">Today - <b><?php echo $date ?></b> (YYYY-MM-DD)</p>
                            <!-- <p style="margin-bottom: 0;">Today's total due - <b><span id="total-due-span"></span></b></p> -->
                        </div>
                        
                        <hr class="col-xs-12" />
                        
                        <table id="task-table" class="table table-striped table-bordered" cellspacing="0" width="100%" style="display: none;">
                            <thead>
                              <tr>
                                <th>Vendor</th>
                                <th>Subtotal</th>
                                <th>Threshold</th>
                                <th>Earliest Date</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    while($row = $result->fetch_assoc()) {
                                        // array_push($tableRows,$row);
                                        echo 
                                            '<tr data-name="'.$row['vendor'].'">
                                                <td>'.$row['vendor'].'</td>
                                                <td class="currency">'.$row['sum_amount'].'</td>
                                                <td class="currency">'.$row['threshold'].'</td>
                                                <td>'.$row['min_date'].'</td>
                                            </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>

                        <hr/>
                    </div>


                    <div role="tabpanel" class="tab-pane" id="upload">
                        <div class="col-xs-2">
                            <div id="logo" style="padding-left: 0">
                                <img src="img/logo.png" class="logo" alt="SheetJS Logo" width=128px height=128px />
                            </div>

                            <div id="drop">Drop a file here</div>
                            <h3> Choose a worksheet:</h3>
                            <div id="buttons"></div>
                            
                        </div>

                        <div class="col-xs-10">
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

                    <div role="tabpanel" class="tab-pane" id="detail">

                        <div class="col-xs-6" style="margin-top: 25px;text-align: center;width: 100%;">
                            <button type="button" class="btn btn-success complete-btn" data-type="#complete" onclick="handleAction(this)">
                                Complete <span class="glyphicon glyphicon-flag"></span>
                            </button>&nbsp;
                            <button class="btn btn-danger postpone-btn" data-type="#postpone" onclick="handleAction(this)">
                                Postpone <span class="glyphicon glyphicon-time"></span>
                            </button>
                            <button type="button" class="btn btn-primary" style="float: right;" onclick="toggleAllRows(true)">
                              Check All&nbsp;<span class="glyphicon glyphicon-ok-circle"></span>
                            </button>
                            <button type="button" class="btn btn-info" style="float: right;" onclick="toggleAllRows(false)">
                              Uncheck All&nbsp;<span class="glyphicon glyphicon-remove-circle"></span>
                            </button>
                        </div>
                        
                        <hr class="col-xs-12" />

                        <div id="detail-vendor-info">
                            <table id="detail-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
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

    </body>

        <script src="vendor/jquery-3.3.1.min.js"></script>
        <script src="vendor/bootstrap.min.js"></script>
        <script src="vendor/jquery.handsontable.min.js"></script>
        <script src="vendor/spin.js"></script>
        <script src="vendor/datatables.min.js"></script>
        <script src="vendor/datatables.select.min.js"></script>
        
        <script src="js/shim.js"></script>
        <script src="js/xlsx.full.min.js"></script>
        <script src="js/dropsheet.js"></script>
        <script src="js/main.js"></script>

        <script type="text/javascript">


            // stores workbook sheets after drag and drop
            var wbSheets = [];

            // stores selected ref# - amount info
            // var selectedRefAmountMap = {};

            // detail-table pointer
            var detailTable;

            // stores current vendor
            var curVendorName;

            $(document).ready(function() {

                // calculateSubtotal();

                $('.currency').each(function () {
                    $(this).html(formatDollarAmount(parseFloat($(this).html())));
                })

                $('#task-table').DataTable({
                    "aaSorting": []
                });

                $('#task-table').show();
            } )

            // click handler for redirecting to detail page
            $('#task-table tbody tr').click(function () {
                var name = $(this).data('name');
                console.log(name);
                curVendorName = name;
                constructDetailTable(name);
            });

            function constructDetailTable(name) {
                // empty selectedRefAmountMap
                // selectedRefAmountMap = {};

                if($('#task-table tbody tr[data-name="'+name+'"]')){
                    $.ajax({
                        type: "GET",
                        url: "get_detail.php",
                        data: {vendor_name: name},
                        success: function(detailList){
                            var resultList = JSON.parse(detailList);
                            // var resultList = detailList;
                            console.log('html: ',resultList);
                            console.log('name: '+name);
                            $('#detail-vendor-name').text(name);
                            $('#detail-tab-controll').show();
                            $('#detail-tab-controll a').html(name);

                            // destroy table before reinitiating
                            if($.fn.DataTable.isDataTable('#detail-table')){
                                $('#detail-table').DataTable().destroy();
                            }

                            // construct and append datatable body row html
                            $('#detail-table tbody').html(
                                function () {
                                    var result = '';
                                    for(var i = 0;i<resultList.length;i++){
                                        var cur = resultList[i];
                                        result += ('<tr data-ref="'+cur['ref_num']+'" data-amount="'+cur['amount_due_num']+'">'+
                                                        '<td>'+cur['vendor']+'</td>'+
                                                        '<td>'+cur['amount_due']+'</td>'+
                                                        '<td>'+cur['ref_num']+'</td>'+
                                                        '<td>'+cur['memo']+'</td>'+
                                                        '<td>'+cur['due_date']+'</td>'+
                                                   '</tr>');
                                    }
                                    return result;
                                }
                            );
                            detailTable = $('#detail-table').DataTable({
                                aaSorting: [[4,"asc"]],
                                select:{
                                    style: 'multi',
                                    className:'selected'
                                }
                            });

                            // provide vendor name to modals
                            $('.vendor-name-span').text(name);

                            $('.nav-tabs a[href="#detail"]').tab('show');
                        }  
                    });
                }else{
                    window.location = window.location.pathname + window.location.hash;
                }
            }

            // add selected data to map on click
            // $(document).on('click','#detail-table tbody tr',function () {
            //     if(selectedRefAmountMap[$(this).data('ref')]){
            //         delete selectedRefAmountMap[$(this).data('ref')];
            //     }else{
            //         selectedRefAmountMap[$(this).data('ref')] = parseFloat($(this).data('amount'));
            //     }
            // })

            function toggleAllRows(setTrue) {
                if($.fn.DataTable.isDataTable(detailTable)){
                    if(setTrue){
                        detailTable.rows().select();
                    }else{
                        detailTable.rows().deselect();
                    }
                }
            }

            function getSelectedData(name) {
                return $.map(detailTable.rows('.selected').nodes(), function (item) {
                    return $(item).data(name);
                });
            }

            function constructSelectedMap() {
                var selectedRefAmountMap = {};
                var ref_list = getSelectedData("ref");
                var amount_list = getSelectedData("amount");
                if(ref_list.length != amount_list.length){
                    return undefined;
                }

                for(var i=0;i<ref_list.length;i++){
                    selectedRefAmountMap[ref_list[i]] = parseFloat(amount_list[i]);
                }

                console.log(selectedRefAmountMap);

                return selectedRefAmountMap;
            }

            // Open Modal
            function handleAction(button){
                // var map = constructRefAmountMap();
                var selectedRefAmountMap = constructSelectedMap();
                if(selectedRefAmountMap == undefined){
                    alert('Something went wrong, let tony know about this and how to replicate the issue.');
                    return false;
                }
                if(!jQuery.isEmptyObject(selectedRefAmountMap)){
                    var buttonId = $(button).data('type');
                    setModalLists(selectedRefAmountMap);
                    $(buttonId+'-modal').modal();
                }else{
                    alert('Please select at least one transaction before proceeding.');
                }
            }

            function setModalLists(selectedRefAmountMap) {
                // construct lists
                var result_ref = '<dl>';
                var result_amount = '<dl>';

                var total_amount = 0;
                var total_amount_text = '';

                result_ref += ('<dt>Ref #</dt>');
                result_amount += ('<dt>Amount $</dt>');

                $.each(selectedRefAmountMap,function(key,value){
                    result_ref += ('<dd>'+key+'</dd>');
                    result_amount += ('<dd>'+value+'</dd>');
                    total_amount += value;
                });

                result_ref += '</dl>';
                result_amount += '</dl>';

                // append subtotal
                total_amount_text = formatDollarAmount(total_amount);

                $('.modal-subtotal').text('Subtotal : '+total_amount_text)

                $('.modal-ref-num-div').html(result_ref);
                $('.modal-amount-div').html(result_amount);
            }

            // format money helper
            function formatDollarAmount(amount) {
                return '$'+amount.formatMoney(2, '.', ',');
            }

            // util method for adding ',' to number like currency
            Number.prototype.formatMoney = function(c, d, t){
                var n = this, 
                    c = isNaN(c = Math.abs(c)) ? 2 : c, 
                    d = d == undefined ? "." : d, 
                    t = t == undefined ? "," : t, 
                    s = n < 0 ? "-" : "", 
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
                    j = (j = i.length) > 3 ? j % 3 : 0;
                   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            function confirm_complete() {
                // console.log(JSON.stringify(Object.keys(selectedRefAmountMap)));
                var ref_list = getSelectedData("ref");
                $.ajax({
                    type: "POST",
                    url: "confirm_complete.php",
                    data: {
                        ref_num: JSON.stringify(ref_list)
                    },
                    success: function (html) {
                        alert(html);
                        constructDetailTable(curVendorName);
                        // window.location = window.location.pathname + window.location.hash;
                    }
                });
                $('#complete-modal').modal('hide');
            }

            function confirm_postpone() {
                var ref_list = getSelectedData("ref");
                var dates = $('#dates').val();
                console.log(dates);
                if(dates){
                    $.ajax({
                        type: "POST",
                        url: "confirm_postpone.php",
                        data: {
                            ref_num:    JSON.stringify(ref_list),
                            dates:      dates
                        },
                        success: function (html) {
                            alert(html);
                            constructDetailTable(curVendorName);
                            // window.location = window.location.pathname + window.location.hash;
                        }
                    });
                    $('#postpone-modal').modal('hide');
                }else{
                    alert('Please enter a valid date');
                }
            }

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
                                window.location = window.location.pathname + window.location.hash;
                            }  
                        });
                    }
                // }
            }

            // function constructRefAmountMap() {
            //     var result = {};
            //     $('.selected-row').each(function () {
            //         result[$(this).data('ref')] = parseFloat($(this).data('amount'));
            //         // result.push($(this).data('ref'));
            //     });
            //     console.log(result);
            //     return result;
            // }

            // function checkListEmpty() {
            //     console.log(vendor_list.length);
            //     console.log(ref_list.length);
            //     if (vendor_list.length&&ref_list.length){
            //         return true;
            //     }else{
            //         alert('Please select at least one transaction before proceeding.');
            //         return false;
            //     }
            // }


            // function calculateSubtotal(){


            //     var vendor = '';
            //     var cur_vendor = '';
            //     var string_amount = '';

            //     var subtotal;
            //     var subtotal_final = 0;
            //     var total_due = 0;

            //     var cur_amount;

            //     var first_vendor;

            //     // calculate and display subtotal for each vendor & total due for today
            //     $('#task-table > tbody > tr').each(function () {
            //         cur_vendor = $(this).find('td:first').text();
            //         string_amount = $(this).find('td:nth-child(2)').text();
            //         cur_amount = Number(string_amount.replace(/[^0-9\.-]+/g,""));
            //         total_due += cur_amount;

            //         if(vendor==''){
            //             // base case
            //             vendor = cur_vendor;
            //             subtotal = cur_amount;
            //             $(this).find('td:first').attr('id','first_vendor');
            //         }else if(vendor == cur_vendor){
            //             subtotal += cur_amount;
            //         }else{
            //             subtotal_final = subtotal.toFixed(2);
            //             first_vendor = $('#first_vendor');
            //             first_vendor.html(first_vendor.text()+' <b>('+subtotal_final+')</b>');
            //             first_vendor.attr('id','');
            //             subtotal = cur_amount;

            //             $(this).find('td:first').attr('id','first_vendor');
            //             vendor = cur_vendor;

            //         }
            //     });

            //     total_due = '$ ' + total_due.toFixed(2);
            //     $('#total-due-span').text(total_due);

            //     var last = $('#task-table > tbody > tr:last');
            //     if(last.length){
            //         subtotal_final = subtotal.toFixed(2);
            //         var f_col = last.find('td:first');
            //         f_col.html(f_col.text()+' <b>('+subtotal_final+')</b>');
            //     }
            // }

            // $("body").on("change",".row-checkbox",function(){
            //     checkBoxEventContent($(this));
            // });

            // $("body").on("click",".row-checkbox-parent",function(e){
            //     if (e.target !== this)
            //         return;

            //     var checkbox = $(this).find('.row-checkbox');
            //     checkbox.click();
            //     // checkbox.prop('checked',(checkbox.prop('checked') ? false : true));
            //     // checkBoxEventContent(checkbox);
            // });



            // function checkBoxEventContent(checkbox) {

            //     var vendor = checkbox.data("vendor");
            //     var ref_num = checkbox.data("ref");

            //     if(checkbox.prop('checked')){

            //         vendor_list.push(vendor);
            //         ref_list.push(ref_num);

            //     }else{

            //         vendor_list.splice(vendor_list.indexOf(vendor), 1 );
            //         ref_list.splice(ref_list.indexOf(ref_num), 1 );

            //     }

            //     console.log(vendor_list);
            //     console.log(ref_list);
            // }

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
</html>
