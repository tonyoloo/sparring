jq2 = jQuery.noConflict();
console.log("i have loaded")
jq2(function ($) {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    /////////////////////////////////////////////////////LOANBLOCKED
    if ($.fn.DataTable.isDataTable('#datatableblocked')) {
        $('#datatableblocked').DataTable().clear().destroy();
    }

    //bulk

    if ($.fn.DataTable.isDataTable('#bulkdatatablepaymentalocation')) {
        $('#bulkdatatablepaymentalocation').DataTable().clear().destroy();
    }


    // $("#uploadForm").submit(function (e) {
    //     e.preventDefault();

    //     var submitButton = document.querySelector("#btn-save-toform");
    //     if (submitButton) {
    //         submitButton.disabled = true;
    //     }

    //     var imageupload = document.getElementById("image").value;
    //     var image = document.getElementById("image").files[0];

    //     // Allow only JPEG, PNG, and JPG
    //     var allowedExtensions = /(\.jpeg|\.png|\.jpg)$/i;

    //     if (!allowedExtensions.exec(imageupload)) {
    //         document.getElementById("payslipErr").innerText = "Please upload an image file (JPEG, PNG, JPG)";
    //         return;
    //     }

    //     var formData = new FormData(this);

    //     $.ajax({
    //         data: formData,
    //         url: "/uploadanddetectface",
    //         type: "POST",
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         success: function (data) {
    //             console.log("success:", data);
    //             $("#result").html(data.message);
    //             submitButton.disabled = false;
    //         },
    //         error: function (data) {
    //             console.log("Error:", data);
    //             $("#result").html("An error occurred while processing the image.");
    //             submitButton.disabled = false;
    //         }
    //     });
    // });






    // $.ajax({


    //     url: "/bulkallocationreport",

    //     type: "POST",
    //     dataType: "json",


    //     success: function (data) {
    //         let formattedData;
    //         if (Array.isArray(data.data)) {
    //             formattedData = data.data;
    //         } else if (data.data) {
    //             formattedData = [data.data];
    //         } else {
    //             formattedData = []; // Handle cases where data is not available or not formatted as expected
    //         }
    //         console.log("formatted", formattedData);


    //         $('#bulkdatatablepaymentalocation').DataTable({
    //             scrollX: true,
    //             pageLength: 5,

    //             data: formattedData, // Assuming data is an array of objects
    //             //  data: [data], // Wrapping data in an array if it's a single object
    //             // data: Array.isArray(data) ? data : [data], // Ensure data is an array

    //             columns: [
    //                 { data: 'allocatedQuota' },
    //                 { data: 'status' },
    //                 { data: 'batchno', defaultContent: 'empty' },

    //                 { data: 'beneficiaryIdentifier' },
    //                 { data: 'clawBackAmount' },
    //                 { data: 'createTime' },

    //                 { data: 'idno', defaultContent: 'empty' },
    //                 { data: 'institutioncode', defaultContent: 'empty' },
    //                 { data: 'loanserialno', defaultContent: 'empty' },
    //                 { data: 'expiredAmount' },

    //                 { data: 'expiredTime' },
    //                 { data: 'remainingAmount' },
    //                 { data: 'utilizedAmount' },




    //             ]
    //         }).columns.adjust();








    //     },
    //     error: function (jqXHR, textStatus, errorThrown) {
    //         console.log("searchpaymobi", jqXHR);


    //     }
    // });

    //bulk

    // var bulkdatatablepaymentalocation = $('#bulkdatatablepaymentalocation').DataTable({
    //     scrollX: true,
    //     responsive: true,
    //     processing: true,
    //     serverSide: true,
    //     pageLength: 5,

    //     ajax: {
    //         url: "/bulkallocationreport",
    //         type: "POST",
    //         dataSrc: function (json) {
    //             // You can process the data here if necessary before it's rendered to the table
    //             return json.data;
    //         },
    //         success: function(data) {
    //             console.log('Data successfully loaded:', data);
    //             // Perform actions after data has been successfully loaded
    //             alert('Data loaded successfully!');
    //         },
    //         error: function(xhr, error, thrown) {
    //             console.log('Error loading data:', error);
    //             alert('Failed to load data!');
    //         }
    //     },
    //     pageLength: 10, // Set default number of rows per page
    //     columns: [
    //         { data: 'allocatedQuota' },
    //         { data: 'status' },
    //         { data: 'batchno', defaultContent: 'empty' },
    //         { data: 'beneficiaryIdentifier' },
    //         { data: 'clawBackAmount' },
    //         { data: 'createTime' },
    //         { data: 'idno', defaultContent: 'empty' },
    //         { data: 'institutioncode', defaultContent: 'empty' },
    //         { data: 'loanserialno', defaultContent: 'empty' },
    //         { data: 'expiredAmount' },
    //         { data: 'expiredTime' },
    //         { data: 'remainingAmount' },
    //         { data: 'utilizedAmount' }
    //     ],
    //     order: [[0, "desc"]]
    // });


    // // Adjust columns and redraw the table after initialization
    // bulkdatatablepaymentalocation.columns.adjust().draw();

    // // Optionally, you can add an event listener to adjust columns on window resize
    // $(window).on('resize', function () {
    //     bulkdatatablepaymentalocation.columns.adjust();
    // });

    // Initialize the DataTable
    var datatablepaymentrealocation = $('#datatablepaymentrealocation').DataTable({
        scrollX: true,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 5,

        ajax: {
            url: "/datatablepaymentrealocation",
            type: "POST"
        },
        columns: [


            { data: "id" },
            { data: "clawbackid" },
            { data: "reallocated" },

            { data: "allocatedQuota" },
            { data: "remainingAmount" },

            { data: "phoneNumber" },
            { data: "currency" },

            { data: "expiredTime" },
            { data: "fundType" },
            { data: "groupCode" },

            { data: "providerId" },
            { data: "batchno" },
            { data: "idno" },

            { data: "institutioncode" },
            { data: "loanserialno" },
            { data: "date_created" },
            { data: "update_time" },
            { data: "action" },



        ],
        rowCallback: function (row, data, index) {
            console.log("this is " + data.paidby);
            if (data.reallocated === "1") {
                $(row)
                    .find("td:eq(2)")
                    .css("background-color", "green")
                    .css("font-weight", "bold")
                    .css("color", "white")
                    .html("<b>YES</b>");
            } else {
                $(row)
                    .find("td:eq(2)")
                    .css("background-color", "red")
                    .css("font-weight", "bold")
                    .css("color", "white")
                    .html("<b>NO</b>");


            }


        },
        order: [[0, "desc"]]
    });

    // Adjust columns and redraw the table after initialization
    datatablepaymentrealocation.columns.adjust().draw();

    // Optionally, you can add an event listener to adjust columns on window resize
    $(window).on('resize', function () {
        datatablepaymentrealocation.columns.adjust();
    });




var studentstable = $('#studentstable').DataTable({
    scrollX: true,
    responsive: true,
    processing: true,
    serverSide: true,
    pageLength: 5,
    ajax: {
        url: "/studentdata",
        type: "POST",
        dataSrc: function(json) {
            console.log("Full JSON response:", json); // Logs the entire API response
            console.log("Data array:", json.data);    // Logs just the data array
            return json.data; // Pass data to DataTables
        }
    },
    columns: [
        { data: 'first_name' },
        { data: 'last_name' },
        { data: 'email' },
        { data: 'phone' },
        { data: 'gender' },
        { data: 'id_number' },
        { data: 'institution_name' },
        { data: 'course' },
        { data: 'year_of_study' },
        { data: 'county' },
        { data: 'town' },
        { data: 'attachment_from' },
        { data: 'attachment_to' },
        { data: 'department' }
    ],
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'csv',
            text: 'CSV',
            className: 'd-none',
            title: 'Students'
        },
        {
            extend: 'excel',
            text: 'Excel',
            className: 'd-none',
            title: 'Students'
        },
        {
            extend: 'pdf',
            text: 'PDF',
            className: 'd-none',
            title: 'Students'
        }
    ]
});
$('#download-students').on('click', function() {
    // Show a menu or just trigger CSV for now
    studentstable.button('.buttons-csv').trigger();
});





    // Initialize the DataTable
    var tableblocked = $('#datatableblocked').DataTable({
        scrollX: true,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 5,

        ajax: {
            url: "/loanblocked",
            type: "POST"
        },
        pageLength: 10, // Set default number of rows per page
        columns: [
            { data: "id" },
            { data: "idno" },
            { data: "reason" },

            { data: "status" },
            { data: "action", name: "action", orderable: false },
            { data: "academicyear" },



        ],
        order: [[0, "desc"]]
    });

    // Adjust columns and redraw the table after initialization
    tableblocked.columns.adjust().draw();

    // Optionally, you can add an event listener to adjust columns on window resize
    $(window).on('resize', function () {
        tableblocked.columns.adjust();
    });


 // Refresh button click handler
 $("body").on("click", ".refresh-btn", function (e) {

    // Get all the row data from the button
    var rowData = $(this).data('row');
    var userId = $(this).data('id');
    var button = $(this);
    
    console.log('Full row data:', rowData); // Verify in browser console
    
    // Show loading state
    button.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
    button.prop('disabled', true);
    
    // Make AJAX call with all the active user's data
    $.ajax({
        url: '/refreshuser', // Your refresh endpoint
        method: 'POST',
        data: {
            user_data: rowData, // Send all user data
         //   _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Show success state
            button.html('<i class="fas fa-check"></i> Refreshed');
            setTimeout(function() {
                button.html('Refresh').prop('disabled', false);
                // Optionally reload the table
                table.ajax.reload(null, false); // false means don't reset paging
            }, 2000);
            var oTable = $("#datatableappliedstats").dataTable();
            oTable.fnDraw(false);
            var oTable = $("#idnumbersearchdatatableappliedstats").dataTable();
            oTable.fnDraw(false);

        },
        error: function(xhr) {
            // Show error state
            button.html('<i class="fas fa-times"></i> Error');
            console.error('Error:', xhr.responseText);
            setTimeout(function() {
                button.html('Refresh').prop('disabled', false);
            }, 2000);
        }
    });
});




    $("body").on("click", ".approveFormr", function (e) {
        console.log("here..");
        var loanserial = document.getElementById("rbankserial").value;
        var reversalreason = $('#reversalreason').val();
        var rbanknationalidno = $('#rbanknationalidno').val();

        $.ajax({
            data: {
                loanserial: loanserial,
                reversalreason: reversalreason,
                rbanknationalidno: rbanknationalidno,

            },
            // data: $('#approvalForm').serialize(),


            // url: '/approveFormr',
            url: "{{ route('approveFormr') }}",

            type: "POST",
            dataType: 'json',
            success: function (response) {


                console.log(response);
                $('#btn-approveFormr').html(response);

                var oTable = $("#datatablebankpays").dataTable();
                oTable.fnDraw(false);



            },
            error: function (response) {
                //console.log('Error:', response);
                $('#btn-approveFormr').html('not sent');
            }
        });

    });


    //////////////////////////////////////////////END LOAN STATUS

    /////////////////////////////////////////////////////LOANSTATUS
    if ($.fn.DataTable.isDataTable('#datatableloanstatus')) {
        $('#datatableloanstatus').DataTable().clear().destroy();
    }
    // Initialize the DataTable
    var table = $('#datatableloanstatus').DataTable({
        scrollX: true,
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/loanstatus",
            type: "GET"
        },
        pageLength: 20, // Set default number of rows per page
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "type" },
            { data: "academicyear" },
            { data: "count" },
            { data: "mobile" },
            { data: "ussd" },
            { data: "miniapp" },
            { data: "ios" },

            { data: "closedate" }
        ],
        order: [[0, "desc"]]
    });

    // Adjust columns and redraw the table after initialization
    table.columns.adjust().draw();

    // Optionally, you can add an event listener to adjust columns on window resize
    $(window).on('resize', function () {
        table.columns.adjust();
    });




    //////////////////////////////////////////////END LOAN STATUS

    //users
    if ($.fn.DataTable.isDataTable('#datatableusers')) {
        $('#datatableusers').DataTable().clear().destroy();
    }

    var tableusers = $('#datatableusers').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/users-list",
            type: "GET"
        },
        columns: [
            { data: "id", name: "pid", visible: false },
            { data: "DT_RowIndex", name: "DT_RowIndex" },
            { data: "name", name: "name" },
            { data: "email", name: "email" },
            { data: "menuroles", name: "menuroles" },
            {
                data: "created_at",
                name: "created_at",
                render: function (data, type, row) {
                    return moment(data).format("YYYY-MM-DD");
                }
            },
            {
                data: "updated_at",
                name: "updated_at",
                render: function (data, type, row) {
                    return moment(data).format("YYYY-MM-DD");
                }
            },
            { data: "action", name: "action", orderable: false }
        ],
        order: [[0, "desc"]]
    });

    // Adjust columns and redraw the table after initialization
    tableusers.columns.adjust().draw();

    // Optionally, you can add an event listener to adjust columns on window resize
    $(window).on('resize', function () {
        tableusers.columns.adjust();
    });


    //end users



    /////////////////////////////////////////////SUBMIT AXID
    $("#axidform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#axidform").serialize();
        console.log("axidform:", formData);
        console.log("form submitted:", formData);
        if ($.fn.DataTable.isDataTable('#datatableportalname')) {
            $('#datatableportalname').DataTable().clear().destroy();
        }

        $.ajax({
            data: $("#axidform").serialize(),
            // url: "batchedit",
            url: "/axidform",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: 'The operation was completed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#axidform").trigger("reset");
                // Clear existing table data

                // Populate the table with new data
                // Check if the data contains the IDNO field
                //  if (data.IDNO) {
                //     console.log("IDNO found in response:", data.IDNO);
                // } else {
                //     console.log("IDNO not found in response.");
                // }

                $('#datatableportalname').DataTable({
                    //scrollX: true,

                    // data: data, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'IDNO' },
                        { data: 'ACCOUNTNUM' },
                        { data: 'EMAIL' },
                        { data: 'NAME' },
                        { data: 'PHONE' },


                    ]
                }).columns.adjust();
            },
            error: function (data) {
                $('#overlay').hide();
                console.log("AXidFormError:", data);
                Swal.fire({
                    title: 'An error occured!',
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    });
    /////////////////////////////////////////////END SUBMIT AX ID

    /////////////////////////////////////////////SUBMIT PAYOPTED
    $("#mobipayform").submit(function (e) {

        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#mobipayform").serialize();
        console.log("mobipayform:", formData);
        //console.log("form submitted:", platform);

        if ($.fn.DataTable.isDataTable('#datatablepaymentnumber')) {
            $('#datatablepaymentnumber').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#datatableappliedstats')) {
            $('#datatableappliedstats').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#datatablepaymentalocation')) {
            $('#datatablepaymentalocation').DataTable().clear().destroy();
        }


        $.ajax({

            data: formData += '&type=searchpay',

            // url: "batchedit",
            url: "/searchpaymobi",

            type: "POST",
            dataType: "json",
            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#mobipayform").trigger("reset");
                // const formattedData = data.data || [data];

                $('#datatablepaymentnumber').DataTable({
                    scrollX: true,

                    data: formattedData, // Assuming data is an array of objects
                    //data: formattedData, // Wrapping data in an array if it's a single object
                    // data: Array.isArray(formattedData) ? data : [formattedData], // Ensure data is an array

                    columns: [

                        { data: 'LOANSERIALNO', name: 'LOANSERIALNO' },
                        { data: 'ADMISSIONNUMBER', name: 'ADMISSIONNUMBER' },
                        { data: 'INSTITUTIONCODE', name: 'INSTITUTIONCODE' },
                        { data: 'INSTITUTION', name: 'INSTITUTION' },
                        { data: 'EMAIL', name: 'EMAIL' },
                        { data: 'LOANPRODUCTCODE', name: 'LOANPRODUCTCODE' },
                        { data: 'IDNO', name: 'IDNO' },
                        { data: 'PRODUCTDESCRIPTION', name: 'PRODUCTDESCRIPTION' },
                        { data: 'LASTNAME', name: 'LASTNAME' },
                        { data: 'FIRSTNAME', name: 'FIRSTNAME' },
                        { data: 'MIDDLENAME', name: 'MIDDLENAME' },
                        { data: 'APPLICANTYPE', name: 'APPLICANTYPE' }
                    ]


                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("searchpaymobi", jqXHR);
                $('#overlay').hide();



            }
        });

        $.ajax({

            data: formData += '&type=searchstats',

            // url: "batchedit",
            url: "/searchpaymobi",

            type: "POST",
            dataType: "json",
            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // $("#mobipayform").trigger("reset");
                $('#datatableappliedstats').DataTable({
                    scrollX: true,
                    data: formattedData, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'fullName' },
                        { data: 'idNumber' },
                        { data: 'phoneNumber' },

                        { data: 'idType' },
                        { data: 'kycFlag' },

                        { data: 'status' },
                        { data: 'userStatus' },
                        { data: 'userType' },
                        { data: 'action' },



                    ]
                }).columns.adjust();









            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("searchpaymobi", jqXHR);


            }
        });

        $.ajax({

            data: formData += '&type=searchallocationstats',

            // url: "batchedit",
            url: "/searchpaymobi",

            type: "POST",
            dataType: "json",

            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // $("#mobipayform").trigger("reset");

                $('#datatablepaymentalocation').DataTable({
                    scrollX: true,

                    data: formattedData, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'allocatedQuota' },
                        { data: 'status' },
                        { data: 'dynamicFields.batchno', defaultContent: 'empty' },

                        { data: 'beneficiaryIdentifier' },
                        { data: 'clawBackAmount' },
                        { data: 'createTime' },

                        { data: 'dynamicFields.idno', defaultContent: 'empty' },
                        { data: 'dynamicFields.institutioncode', defaultContent: 'empty' },
                        { data: 'dynamicFields.loanserialno', defaultContent: 'empty' },
                        { data: 'expiredAmount' },

                        { data: 'expiredTime' },
                        { data: 'remainingAmount' },
                        { data: 'utilizedAmount' },




                    ]
                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("searchpaymobi", jqXHR);


            }
        });







    });
    /////////////////////////////////////////////END SUBMIT AX ID

    /////////////////////////////////////////////SUBMIT PAYOPTED
    $("#withdrawform").submit(function (e) {

        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#withdrawform").serialize();
        console.log("withdrawform:", formData);
        //console.log("form submitted:", platform);

        if ($.fn.DataTable.isDataTable('#datatablepaymentnumber')) {
            $('#datatablepaymentnumber').DataTable().clear().destroy();
        }




        $.ajax({

            data: formData,

            // url: "batchedit",
            url: "/withdrawstatement",

            type: "POST",
            dataType: "json",
            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#withdrawform").trigger("reset");
                // const formattedData = data.data || [data];

                $('#datatablewithdrawalstats').DataTable({
                    scrollX: true,

                    data: formattedData, // Assuming data is an array of objects
                    //data: formattedData, // Wrapping data in an array if it's a single object
                    // data: Array.isArray(formattedData) ? data : [formattedData], // Ensure data is an array

                    columns: [

                        { data: 'amount', defaultContent: 'empty' },
                        { data: 'finishTime', defaultContent: 'empty' },

                        { data: 'beneficiaryFirstName', defaultContent: 'empty' },
                        { data: 'errorInfo', defaultContent: 'empty' },
                        { data: 'mmTransactionId', defaultContent: 'empty' },
                        { data: 'status', defaultContent: 'empty' },

                    ]



                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("withdrawform", jqXHR);
                $('#overlay').hide();



            }
        });





    });
    /////////////////////////////////////////////END SUBMIT AX ID


    /////////////////////////////////////////////SUBMIT PAYOPTED
    $("#idnumbersearchmobipayform").submit(function (e) {

        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#idnumbersearchmobipayform").serialize();
        console.log("idnumbersearchmobipayform:", formData);
        //console.log("form submitted:", platform);

        if ($.fn.DataTable.isDataTable('#idnumbersearchdatatablepaymentnumber')) {
            $('#idnumbersearchdatatablepaymentnumber').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#idnumbersearchdatatableappliedstats')) {
            $('#idnumbersearchdatatableappliedstats').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#idnumbersearchdatatablepaymentalocation')) {
            $('#idnumbersearchdatatablepaymentalocation').DataTable().clear().destroy();
        }


        $.ajax({

            data: formData += '&type=idnumbersearchpay',

            // url: "batchedit",
            url: "/idnumbersearchpaymobi",

            type: "POST",
            dataType: "json",
            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#idnumbersearchmobipayform").trigger("reset");

                $('#idnumbersearchdatatablepaymentnumber').DataTable({
                    scrollX: true,

                    data: formattedData, // Assuming data is an array of objects
                    //data: formattedData, // Wrapping data in an array if it's a single object
                    // data: Array.isArray(formattedData) ? data : [formattedData], // Ensure data is an array

                    columns: [

                        { data: 'LOANSERIALNO', name: 'LOANSERIALNO' },
                        { data: 'ADMISSIONNUMBER', name: 'ADMISSIONNUMBER' },
                        { data: 'INSTITUTIONCODE', name: 'INSTITUTIONCODE' },
                        { data: 'INSTITUTION', name: 'INSTITUTION' },
                        { data: 'EMAIL', name: 'EMAIL' },
                        { data: 'LOANPRODUCTCODE', name: 'LOANPRODUCTCODE' },
                        { data: 'PRODUCTDESCRIPTION', name: 'PRODUCTDESCRIPTION' },
                        { data: 'LASTNAME', name: 'LASTNAME' },
                        { data: 'FIRSTNAME', name: 'FIRSTNAME' },
                        { data: 'MIDDLENAME', name: 'MIDDLENAME' },
                        { data: 'APPLICANTYPE', name: 'APPLICANTYPE' }
                    ]


                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("idnumbersearchpaymobi", jqXHR);
                $('#overlay').hide();



            }
        });

        $.ajax({

            data: formData += '&type=idnumbersearchstats',

            // url: "batchedit",
            url: "/idnumbersearchpaymobi",

            type: "POST",
            dataType: "json",
            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $('#idnumbersearchdatatableappliedstats').DataTable({
                    scrollX: true,
                    data: formattedData, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'fullName' },
                        { data: 'idNumber' },
                        { data: 'phoneNumber' },

                        { data: 'idType' },
                        { data: 'kycFlag' },

                        { data: 'status' },
                        { data: 'userStatus' },
                        { data: 'userType' },
                        { data: 'action' },







                    ]
                }).columns.adjust();









            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("searchpaymobi", jqXHR);


            }
        });

        $.ajax({

            data: formData += '&type=idnumbersearchallocationstats',

            // url: "batchedit",
            url: "/idnumbersearchpaymobi",

            type: "POST",
            dataType: "json",

            success: function (data) {
                let formattedData;
                if (Array.isArray(data.data)) {
                    formattedData = data.data;
                } else if (data.data) {
                    formattedData = [data.data];
                } else {
                    formattedData = []; // Handle cases where data is not available or not formatted as expected
                }
                console.log("formatted", formattedData);

                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $('#idnumbersearchdatatablepaymentalocation').DataTable({
                    scrollX: true,

                    data: formattedData, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'allocatedQuota' },
                        { data: 'status' },
                        { data: 'dynamicFields.batchno', defaultContent: 'empty' },

                        { data: 'beneficiaryIdentifier' },
                        { data: 'clawBackAmount' },
                        { data: 'createTime' },

                        { data: 'dynamicFields.idno', defaultContent: 'empty' },
                        { data: 'dynamicFields.institutioncode', defaultContent: 'empty' },
                        { data: 'dynamicFields.loanserialno', defaultContent: 'empty' },
                        { data: 'expiredAmount' },
                        { data: 'expiredTime' },
                        { data: 'remainingAmount' },
                        { data: 'utilizedAmount' },

                    ]
                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("idnumbersearchpaymobi", jqXHR);


            }
        });







    });
    /////////////////////////////////////////////END SUBMIT AX ID

            $('#importblockform').trigger("reset");
   $('#importblockform').submit(function(e) {

                e.preventDefault();
                var formData = new FormData(this);

                console.log(formData);
                $.ajax({
                    data: formData,
                    // data:'',
                    // url: '/import-bank',
                   // url: "{{ route('import-block') }}",
                    url: $('#importblockform').data('url'),

                    type: "POST",

                    success: function(response) {

                     
       
   try {
                            $vals = response[0]['errors'];

                            $one = $vals[0];
                            $idno = 'idno: ' + response[0]['values'][
                                'idno'
                            ];
                            $status = 'status: ' + response[0]['values']['status'];
                            $academicyear = 'academicyear: ' + response[0]['values'][
                                'academicyear'
                            ];
                            $updated_by = 'updated_by: ' + response[0]['values']['updated_by'];

                            $combined = $idno + ' ' + $status + ' ' + $academicyear +  ' ' + $updated_by;

                            $('#btn-importblockform').html($combined);
                        } catch (e) {
                            $('#btn-importblockform').html("SUCCESSFUL UPLOAD");

                        }

                        $('#importblockform').trigger("reset");
                       





                    },
                    xhr: function() {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            var progress = Math.round((e.loaded / e.total) * 100);
                            console.log(progress + '%');
                        });
                        return xhr;
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    error: function(response) {
                        $vr = JSON.stringify(response);
                        console.log('Error:', $vr);

                        $('#btn-importblockform').html(
                            "AN ERROR OCCURED: POSSIBLE  TRYING TO UPLOAD  NULL VALUES"
                        );

                    }
                });

            });











    /////////////////////////////////////////////SUBMIT AXID
    $("#iprsidform").submit(function (e) {
        e.preventDefault();

        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#iprsidform").serialize();
        console.log("iprsidform:", formData);
        console.log("form submitted:", formData);

        $.ajax({
            data: formData,
            // url: "batchedit",
            url: "/idnumberiprsmaisha",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Response received!',
                    text: data,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#iprsidform").trigger("reset");

            },
            error: function (data) {
                $('#overlay').hide();
                console.log("idnumberiprs:", data);
                Swal.fire({
                    title: 'An error occured!',
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    });
    /////////////////////////////////////////////END SUBMIT AX ID

    /////////////////////////////////////////////mpesa AXID
    $("#mpesaidform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#mpesaidform").serialize();
        console.log("mpesaidform:", formData);
        console.log("form mpesaidform:", formData);

        $.ajax({
            data: $("#mpesaidform").serialize(),
            // url: "batchedit",
            url: "/mpesaidpost",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("mpesaidforms:", data.message);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Response received!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#mpesaidform").trigger("reset");

            },
            error: function (data) {
                $('#overlay').hide();
                console.log("mpesaidforme:", data);
                Swal.fire({
                    title: 'An error occured!',
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    });
    /////////////////////////////////////////////END SUBMIT mpesa ID
    /////////////////////////////////////////////SCHOLARSHIP AXID
    $("#schidform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#schidform").serialize();
        console.log("schidform:", formData);

        $.ajax({
            data: $("#schidform").serialize(),
            // url: "batchedit",
            url: "/schidformpost",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("schidformpost:", data.message);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Response received!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#schidform").trigger("reset");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.errors;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: errorMessage,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    });
    /////////////////////////////////////////////END SUBMIT SCHOLARSHIP FORM 
    /////////////////////////////////////////////SCHOLARSHIP AXID
    $("#allocateform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#allocateform").serialize();
        console.log("allocateform:", formData);

        $.ajax({
            data: $("#allocateform").serialize(),
            // url: "batchedit",
            url: "/updatesallocation",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("allocateform:", data.message);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Response received!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#allocateform").trigger("reset");
                $('#datatablepaymentrealocation').DataTable().ajax.reload();




            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("addinstitutionformV:", jqXHR);


                // var errorMessage = jqXHR.responseText;

                // var responseObj = JSON.parse(errorMessage);
                // var message = responseObj.errors;
                $('#overlay').hide();
                //  console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: "errorMessage",
                    text: 'Kindly contact lending team.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    });
    /////////////////////////////////////////////END SUBMIT SCHOLARSHIP FORM 


    /////////////////////////////////////////////SUBMIT AXID
    $("#minorform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        var kcseInput = document.getElementById('kcseindexnumber');
        if (kcseInput.value.length < 11) {
            Swal.fire({
                title: 'An error occured!',
                text: 'KCSE INDEX has to be 11 digits.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;

        }
        $('#overlay').css('display', 'flex').show();


        var formData = $("#minorform").serialize();
        console.log("minorform:", formData);
        console.log("form submitted:", formData);



        $.ajax({
            data: $("#minorform").serialize(),
            // url: "batchedit",
            url: "/minorform",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Response received!',
                    text: data,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#minorform").trigger("reset");

            },
            // error: function(jqXHR, textStatus, errorThrown) {
            //     var errorMessage = jqXHR.responseText;

            //     var responseObj = JSON.parse(errorMessage);
            //     var message = responseObj.message;

            //     Swal.fire({
            //         title: "AN ERROR OCCURED",
            //         text: message,
            //         icon: "error",
            //         confirmButtonText: "OK"
            //     });
            // }
            error: function (jqXHR, textStatus, errorThrown) {

                $('#overlay').hide();
                var errorMessage = jqXHR.responseText;

                console.log("minorform:", errorMessage);
                Swal.fire({
                    title: 'Response received!',
                    text: errorMessage,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                $("#minorform").trigger("reset");

            }
        });

    });
    /////////////////////////////////////////////END SUBMIT AX ID

    /////////////////////////////////////////////SUBMIT  ifqualified
    $("#ifqualifiedform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#ifqualifiedform").serialize();
        console.log("ifqualifiedform:", formData);
        // console.log("form submitted:", formData);
        if ($.fn.DataTable.isDataTable('#datatableifqualified')) {
            $('#datatableifqualified').DataTable().clear().destroy();
        }

        $.ajax({
            data: $("#ifqualifiedform").serialize(),
            // url: "batchedit",
            url: "/ifqualifiedform",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: 'The operation was completed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#ifqualifiedform").trigger("reset");
                // Clear existing table data

                // Populate the table with new data
                // Check if the data contains the IDNO field
                //  const firstObjectNames = data[0];



                // var data = [
                //     {
                //         names: "Oloo Otieno Oloo",
                //         serial_number: "2320297345",
                //         name: "UNDERGRADUATE SECOND SUBSEQUENT LOAN"
                //     }
                // ];

                //   if (data.names) {
                //     console.log("IDNO found in response:", data.names);
                // } else {
                //     console.log("IDNO NOT found in response:", data);
                //     console.log("IDNO NOT found h in response:", datareal.data);

                // }
                $('#datatableifqualified').DataTable({
                    scrollX: true,

                    //  data: data, // Assuming data is an array of objects
                    data: datareal.data, // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'names' },
                        { data: 'productname' },
                        { data: 'serial_number' },
                        { data: 'idno' },

                        { data: 'submittedloan' },
                        { data: 'submittedscholarship' },
                        { data: 'source' },
                        { data: 'date_loan_submit' },
                        { data: 'date_sch_submit' },
                        { data: 'disbursementoption' },
                        { data: 'disbursementoptionvalue' },



                    ],
                    rowCallback: function (row, data, index) {
                        console.log("this is " + data.paidby);
                        if (data.submittedloan === "1") {
                            $(row)
                                .find("td:eq(4)")
                                .css("background-color", "green")
                                .css("font-weight", "bold")
                                .css("color", "white")
                                .html("<b>SUBMITTED</b>");
                        } else {
                            $(row)
                                .find("td:eq(4)")
                                .css("background-color", "red")
                                .css("font-weight", "bold")
                                .css("color", "white")
                                .html("<b>NOT SUBMITTED</b>");


                        }
                        if (data.submittedscholarship === "1") {
                            $(row)
                                .find("td:eq(5)")
                                .css("background-color", "green")
                                .css("font-weight", "bold")
                                .css("color", "white")
                                .html("<b>SUBMITTED</b>");
                        } else {
                            $(row)
                                .find("td:eq(5)")
                                .css("background-color", "red")
                                .css("font-weight", "bold")
                                .css("color", "white")
                                .html("<b>NOT SUBMITTED</b>");


                        }

                    }
                }).columns.adjust();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.error;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: message,
                    text: 'Kindly contact lending team.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////END ifqualified AX ID




    /////////////////////////////////////////////fetch inst
    $("#minorverifyform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#minorverifyform").serialize();
        console.log("minorverifyform:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#minorverifyform").serialize(),
            // url: "batchedit",
            url: "/minorverify",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data.message);
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#minorverifyform").trigger("reset");


            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.errors;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: errorMessage,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst

    /////////////////////////////////////////////fetch inst

    $("#addinstitutionform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#addinstitutionform").serialize();
        console.log("addinstitutionform:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#addinstitutionform").serialize(),
            // url: "batchedit",
            url: "/addinstitution",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#ifqualifiedform").trigger("reset");


            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.error;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: errorMessage,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst
    /////////////////////////////////////////////fetch inst
    $("#addinstitutionformextra").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#addinstitutionformextra").serialize();
        console.log("addinstitutionformextra:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#addinstitutionformextra").serialize(),
            // url: "batchedit",
            url: "/addinstitutionextra",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#ifqualifiedformextra").trigger("reset");


            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.error;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: errorMessage,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst
/////////////////////////////////////////////fetch inst
    /////////////////////////////////////////////fetch inst
    $("#addlateapplicant").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#addlateapplicant").serialize();
        console.log("addlateapplicant:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#addlateapplicant").serialize(),
            // url: "batchedit",
            url: "/addlateapplicant",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("here", data);
                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#addlateapplicant").trigger("reset");


            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = jqXHR.responseText;

                var responseObj = JSON.parse(errorMessage);
                var message = responseObj.error;
                $('#overlay').hide();
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: errorMessage,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst
    /////////////////////////////////////////////fetch platform
    var tabledatatableandroid;
    $("#platform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#platform").serialize();
        console.log("platform:", formData);
        console.log("form submitted:", platform);

        if ($.fn.DataTable.isDataTable('#datatableandroid')) {
            $('#datatableandroid').DataTable().clear().destroy();
        }

        if ($.fn.DataTable.isDataTable('#datatableminiapp')) {
            $('#datatableminiapp').DataTable().clear().destroy();
        }

        $.ajax({

            data: formData += '&type=miniapp',

            // url: "batchedit",
            url: "/addplatform",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("hhvvvvh", data.data);


                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#ifqualifiedform").trigger("reset");

                $('#datatableminiapp').DataTable({
                    scrollX: true,

                    data: data.data, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'cell_phone' },
                        { data: 'deviceinfo' },
                        { data: 'time_added' },


                    ]
                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

        $.ajax({

            data: formData += '&type=androidapp',

            // url: "batchedit",
            url: "/addplatform",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("hhvvvvh", data.data);


                var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#ifqualifiedform").trigger("reset");
                tabledatatableandroid = $('#datatableandroid').DataTable({
                    scrollX: true,

                    data: data.data, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: "DT_RowIndex", name: "DT_RowIndex" },

                        { data: 'cell_phone' },
                        { data: 'idno' },
                        { data: 'gsf' },




                        { data: 'deviceinfo' },
                        { data: 'appversion' },

                        { data: 'time_added' },
                        { data: "action", name: "action", orderable: false }


                    ]
                }).columns.adjust();








            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });







    });

    $('body').on('click', '.deleteandroid', function () {

        var gsf = $(this).data('gsf');

        console.log('kkkkkkkkkkkkkkk', gsf);

        Swal.fire({
            title: "Are you sure?DELETING  GSF: " + gsf,
            text: "You will not be able to undo this action!",
            icon: "warning",
            showCancelButton: true, // This shows the cancel button
            confirmButtonText: 'Yes, I am sure!', // Text for the confirm button
            cancelButtonText: 'No, cancel it!', // Text for the cancel button
            dangerMode: true,
        }).then((result) => {
            if (result.isConfirmed) { // Check if the user confirmed the action
                $.ajax({
                    type: "get",
                    url: '/deleteandroiduser/' + gsf,
                    success: function (data) {
                        Swal.fire({
                            title: 'DELETED!',
                            text: data.message,
                            icon: 'success'
                        }).then(function () {
                            var oTable = $('#datatableandroid').DataTable(); // Use DataTable() instead of dataTable()
                            oTable.draw(false); // Redraw the DataTable
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        var errorMessage = jqXHR.responseText;

                        var responseObj = JSON.parse(errorMessage);
                        var message = responseObj.errors;
                        $('#overlay').hide();
                        // console.log("addinstitutionformV:", message);
                        Swal.fire({
                            title: errorMessage,
                            text: 'Kindly contact lending team.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) { // Check if the user canceled the action
                Swal.fire("Cancelled", "Your record is safe :)", "error");
            }
        });

















    });

    $('body').on('click', '.unblockuser', function () {

        var record_id = $(this).data('idno');

        console.log('kkkkkkkkkkkkkkk', record_id);

        Swal.fire({
            title: "Are you sure?Unblocking ID: " + record_id,
            text: "You will not be able to undo this action!",
            icon: "warning",
            showCancelButton: true, // This shows the cancel button
            confirmButtonText: 'Yes, I am sure!', // Text for the confirm button
            cancelButtonText: 'No, cancel it!', // Text for the cancel button
            dangerMode: true,
        }).then((result) => {
            if (result.isConfirmed) { // Check if the user confirmed the action
                $.ajax({
                    type: "get",
                    url: '/unblockrecord/' + record_id,
                    success: function (data) {
                        Swal.fire({
                            title: 'UPDATED!',
                            text: data.message,
                            icon: 'success'
                        }).then(function () {
                            var oTable = $('#datatableblocked').DataTable(); // Use DataTable() instead of dataTable()
                            oTable.draw(false); // Redraw the DataTable
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        var errorMessage = jqXHR.responseText;

                        var responseObj = JSON.parse(errorMessage);
                        var message = responseObj.errors;
                        $('#overlay').hide();
                        // console.log("addinstitutionformV:", message);
                        Swal.fire({
                            title: errorMessage,
                            text: 'Kindly contact lending team.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) { // Check if the user canceled the action
                Swal.fire("Cancelled", "Your record is safe :)", "error");
            }
        });

















    });
    /////////////////////////////////////////////fetch platform
    /////////////////////////////////////////////fetch phonenumber
    $("#phonenumberverifyform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#phonenumberverifyform").serialize();
        console.log("phonenumberverifyform:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#phonenumberverifyform").serialize(),
            // url: "batchedit",
            url: "/phonenumberverify",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("sccess", data.message);
                // $("#phonenumberverifyform").trigger("reset");

                //var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });



            },
            error: function (data) {
                console.log("herefail", data.message);

                $('#overlay').hide();

                // var errorMessage = jqXHR.responseText;

                //var responseObj = JSON.parse(errorMessage);
                // var message = responseObj.error;
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: data,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst

    /////////////////////////////////////////////fetch ussdetails
    $("#ussdverifyform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();




        var formData = $("#ussdverifyform").serialize();
        console.log("ussdverifyform:", formData);
        // console.log("form submitted:", formData);


        if ($.fn.DataTable.isDataTable('#datatableussdetails')) {
            $('#datatableussdetails').DataTable().clear().destroy();
        }

        $.ajax({
            data: $("#ussdverifyform").serialize(),
            // url: "batchedit",
            url: "/ussdverify",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("sccess", data.data);
                // $("#phonenumberverifyform").trigger("reset");

                //var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });


                $("#ussdverifyform").trigger("reset");
                $('#datatableussdetails').DataTable({
                    scrollX: true,

                    data: data.data, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'user_id' },
                        { data: 'first_name' },
                        { data: 'id_no' },

                        { data: 'user_name' },
                        { data: 'date_of_birth' },
                        { data: 'login_flag' },



                    ]
                }).columns.adjust();












            },
            error: function (data) {
                console.log("herefail", data.message);

                $('#overlay').hide();

                // var errorMessage = jqXHR.responseText;

                //var responseObj = JSON.parse(errorMessage);
                // var message = responseObj.error;
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: data,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch ussdetails
    /////////////////////////////////////////////update unique userid
    $("#updateussdform").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#updateussdform").serialize();
        console.log("updateussdform:", formData);
        // console.log("form submitted:", formData);



        if ($.fn.DataTable.isDataTable('#datatableupdatedussdetails')) {
            $('#datatableupdatedussdetails').DataTable().clear().destroy();
        }
        $.ajax({
            data: $("#updateussdform").serialize(),
            // url: "batchedit",
            url: "/updateussd",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("sccess", data.message);
                $("#updateussdform").trigger("reset");

                //var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // $("#ussdverifyform").trigger("reset");
                $('#datatableupdatedussdetails').DataTable({
                    scrollX: true,

                    data: data.data, // Assuming data is an array of objects
                    //  data: [data], // Wrapping data in an array if it's a single object
                    // data: Array.isArray(data) ? data : [data], // Ensure data is an array

                    columns: [
                        { data: 'user_id' },
                        { data: 'first_name' },
                        { data: 'id_no' },

                        { data: 'user_name' },
                        { data: 'date_of_birth' },
                        { data: 'login_flag' },



                    ]
                }).columns.adjust();



            },
            error: function (data) {
                console.log("herefail", data.message);

                $('#overlay').hide();

                // var errorMessage = jqXHR.responseText;

                //var responseObj = JSON.parse(errorMessage);
                // var message = responseObj.error;
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: data,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////update unique userid
    /////////////////////////////////////////////fetch phonenumber
    $("#updateussdfm").submit(function (e) {
        e.preventDefault();
        // $('#overlay').show();
        $('#overlay').css('display', 'flex').show();


        var formData = $("#updateussdfm").serialize();
        console.log("updateussdfm:", formData);
        // console.log("form submitted:", formData);

        $.ajax({
            data: $("#updateussdfm").serialize(),
            // url: "batchedit",
            url: "/updateussdnumber",

            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log("sccess", data.message);
                // $("#phonenumberverifyform").trigger("reset");

                //var datareal = data;
                $('#overlay').hide();
                Swal.fire({
                    title: 'Operation Successful!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });



            },
            error: function (data) {
                console.log("herefail", data.message);

                $('#overlay').hide();

                // var errorMessage = jqXHR.responseText;

                //var responseObj = JSON.parse(errorMessage);
                // var message = responseObj.error;
                // console.log("addinstitutionformV:", message);
                Swal.fire({
                    title: data,
                    text: 'Kindly contact system admin.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    /////////////////////////////////////////////fetch inst

    // $("#ussdverifyform").trigger("reset");
    $('#datatableadmins').DataTable({
        scrollX: true,

        processing: true,
        serverSide: true,
        regex: true,

        ajax: {
            url: "/admins-list",
            type: "GET"

        },
        columns: [
            { data: "id" },
            { data: "DT_RowIndex" },
            { data: "usersname" },
            { data: "email" },
            { data: "rolesname" },
            { data: "permissionsname" },
            { data: "created_at" },
            { data: "updated_at" },



        ]
    }).columns.adjust();


    $("body").on("click", ".edituser", function () {
        var id = $(this).data("id");
        $("#ajax-editpriviledges-modal").modal("show");
        $("#editpriviledgesCrudModal").html("EDIT USER PRIVILEDGES");
        $("#useridentifier").val(id);
        table = $("#datatableuseroles").DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                url: "/users-list/" + id + "/useroles",

                type: "GET"
            },

            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },

                { data: "result", name: "user_id" }
            ],
            order: [[0, "desc"]]
        });
        table.destroy();
        table = $("#datatableuserpermissions").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/users-list/" + id + "/userpermissions",

                type: "GET"
                /*dataType: 'json',
         success: function (data) {
            console.log(data);

         },
         error: function (data) {
         console.log('Error:', data);

         }





         */
            },

            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },

                { data: "result", name: "user_id" }
            ],
            order: [[0, "desc"]]
        });
        table.destroy();
        table = $("#datatableallroles").DataTable({
            ajax: {
                url: "/users-list/" + id + "/allroles",

                type: "GET",
                responsive: true
            },

            columns: [{ data: "name" }, { data: "id" }, { data: "name" }],

            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row) {
                        //return '<input type="checkbox" name= "'+ $('<div/>').text(id).html() + '" value="'+ $('<div/>').text(data).html() + '">';}
                        return (
                            '<input type="checkbox" name="id[]" value="' +
                            $("<div/>")
                                .text(data + id)
                                .html() +
                            '">'
                        );
                    }
                }
            ],
            order: [[1, "asc"]]
        });
        table.destroy();
        table = $("#datatableallpermission").DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,

            ajax: {
                url: "/users-list/" + id + "/allpermission",

                type: "GET",
                responsive: true
            },

            columns: [{ data: "name" }, { data: "id" }, { data: "name" }],

            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row) {
                        //return '<input type="checkbox" name= "'+ $('<div/>').text(id).html() + '" value="'+ $('<div/>').text(data).html() + '">';}
                        return (
                            '<input type="checkbox" name="id[]" value="' +
                            $("<div/>")
                                .text(data + id)
                                .html() +
                            '">'
                        );
                    }
                }
            ],
            order: [[1, "asc"]]
        });
        table.destroy();
    });
    $("#updatepermissionform").on("submit", function (e) {
        e.preventDefault();
        var data = table.$('input[type="checkbox"]').serializeArray();
        //event.preventDefault();
        //console.log( $( this ).serialize() );
        //  form.submit();
        var form = this;
        var userident = document.getElementById("useridentifier").value;
        table.$('input[type="checkbox"]').each(function () {
            // If checkbox doesn't exist in DOM
            if (!$.contains(document, this)) {
                // If checkbox is checked
                if (this.checked) {
                    // Create a hidden element
                    $(form).append(
                        $("<input>")
                            .attr("type", "hidden")
                            .attr("name", this.name)
                            .val(this.value)
                    );
                }
            }
        });
        console.log("Form ", data);
        //console.log("Form  one", data.concat(userident));
        console.log("Form submission", $(form).serialize());

        // Prevent actual form submission
        e.preventDefault();

        $.ajax({
            data: $(form).serialize(),
            url: "/updatedpermission-store",
            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log(data);
                $("#updatepermissionform").trigger("reset");
                $("#btn-save-updatepermission").html(data);
                var oTable = $("#datatableuserpermission").dataTable();
                oTable.fnDraw(false);
            },
            error: function (data) {
                console.log("Error:", data);
                $("#btn-save-updatepermission").html("not saved");
            }
        });
    });
    $("#updateroleform").on("submit", function (e) {
        e.preventDefault();
        var data = table.$('input[type="checkbox"]').serializeArray();
        //event.preventDefault();
        //console.log( $( this ).serialize() );
        //  form.submit();
        var form = this;
        var userident = document.getElementById("useridentifier").value;
        table.$('input[type="checkbox"]').each(function () {
            // If checkbox doesn't exist in DOM
            if (!$.contains(document, this)) {
                // If checkbox is checked
                if (this.checked) {
                    // Create a hidden element
                    $(form).append(
                        $("<input>")
                            .attr("type", "hidden")
                            .attr("name", this.name)
                            .val(this.value)
                    );
                }
            }
        });
        console.log("Form ", data);
        //console.log("Form  one", data.concat(userident));
        console.log("Form submission", $(form).serialize());

        // Prevent actual form submission
        e.preventDefault();

        $.ajax({
            data: $(form).serialize(),
            url: "/updatedrole-store",
            type: "POST",
            dataType: "json",
            success: function (data) {
                console.log(data);
                $("#updateroleform").trigger("reset");
                $("#btn-save-updaterole").html(data);
                var oTable = $("#datatableuseroles").dataTable();
                oTable.fnDraw(false);
            },
            error: function (data) {
                console.log("Error:", data);
                $("#btn-save-updaterole").html("not saved");
            }
        });
    });























});
