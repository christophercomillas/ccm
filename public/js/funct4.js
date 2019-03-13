var siteurl ="";
//$('#loaderdiv').html("<h4 class='loadh4'>Saving...Please Wait...</h4>")
//$('#processing-modal').modal('show');
$(document).ready(function() {  
    siteurl = $('input[name="baseurl"]').val();

    $('#checkdate').mask("00/00/0000");

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 

    $.extend( $.fn.dataTable.defaults, {
        language: {
            "processing": "Loading. Please wait..."
        },     
    });

    $('button#updatedbatp').click(function(){
        //console.log($(this).text());
        $(this).prop('disabled',true);
        $(this).html('<span class="fa fa-spinner fa-spin"></span> Updating..');

        stream('processdbupdatefromatp');        
    });

    var upfiles = $('#uploadfiles');
    $('input#upload').change(function(e) {        
        e.preventDefault(); 

        var x = document.getElementById("upload");
        var txt = "";
        if ('files' in x) 
        {
            if(x.files.length == 0)
            {
                alert('Select one or more files');
                return false;
            }
            // else 
            // {
            //     for (var i = 0; i < x.files.length; i++) {
            //         txt += "<br><strong>" + (i+1) + ". file</strong><br>";
            //         var file = x.files[i];
            //         if ('name' in file) {
            //             txt += "name: " + file.name + "<br>";
            //         }
            //         if ('size' in file) {
            //             txt += "size: " + file.size + " bytes <br>";
            //         }
            //     }
            // }
        }                
        var x = document.getElementById("upload");
        var txt = "";
        if ('files' in x) 
        {
            if(x.files.length == 0)
            {
                alert('Select one or more files');
                return false;
            }
            // else 
            // {
            //     for (var i = 0; i < x.files.length; i++) {
            //         txt += "<br><strong>" + (i+1) + ". file</strong><br>";
            //         var file = x.files[i];
            //         if ('name' in file) {
            //             txt += "name: " + file.name + "<br>";
            //         }
            //         if ('size' in file) {
            //             txt += "size: " + file.size + " bytes <br>";
            //         }
            //     }
            // }
        }        

        var formData = new FormData(upfiles[0]);
        $.ajax({
            url:siteurl+'/addtocartupload',
            type:'POST',
            data: formData,
            enctype: 'multipart/form-data',
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function (success) {
                //console.log(success);
    
                //var success = JSON.parse(success);
    
            },
            error: function (error) {
                //console.log(error);
            },
            complete: function (complete) {
                //console.log(complete);
            },
            beforeSend: function (jqXHR, settings) {
    
                // var self = this;
                var xhr = settings.xhr;
                
                settings.xhr = function () {
                    var output = xhr();
                    output.previous_text = '';
                    //dialogItself.close();
                    $('#processing-modal').modal('show');
                    //console.log(output);
                    output.onreadystatechange = function () {
                        try{
                            //console.log(output.readyState);
                            if (output.readyState == 3) {
                                
                                var new_response = output.responseText.substring(output.previous_text.length);
    
                                var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                               
                                
                                //var result2 = JSON.parse( new_response );
                                console.log(result);
    
                                if(result.status=='checking')
                                {
                                    $('h4.loading').html(result.message);
                                }
                                
                                if(result.status=='error')
                                {
                                    
                                    $('#processing-modal').modal('hide');
                                    swal({
                                        title: "EOD Failed",
                                        type: "warning",
                                        text: result.message,
                                        showCancelButton: false,
                                        confirmButtonColor: "#DD6B55",
                                        confirmButtonText: "OK",
                                        closeOnConfirm: true
                                    })
                                }     
    
                                if(result.status=='complete')
                                {
                                    $('#processing-modal').modal('hide');
                                    swal({
                                        title: "Complete",
                                        type: "success",
                                        text: result.message,
                                        showCancelButton: false,
                                        confirmButtonColor: "#DD6B55",
                                        confirmButtonText: "OK",
                                        closeOnConfirm: true
                                    },function(){
                                        location.reload();
                                    })
                                }
                                
                                if(result.status=='looping')
                                {
                                    $('h4.loading').html(result.progress);
                                }                               
    
                                output.previous_text = output.responseText;
    
                                //console.log(new_response);
                            }
                        }catch(e){
                            console.log("[XHR STATECHANGE] Exception: " + e);
                        }
                    };
                    return output;
                }
            }
        });


        // formData.append('file', $('#upload')[0].files[0]);

        // var value = $(this).val();
        // var allowedExtensions = ["txt"];
        // file = value.toLowerCase(),
        // extension = file.substring(file.lastIndexOf('.') + 1);
        // if ($.inArray(extension, allowedExtensions) == -1) 
        // {
        //     alert('xx');
        //     $('.response').html('<div class="alert alert-danger">Invalid File.</div>');
        //     //return false;
        // } 
    });

    $('form#formCheckSearch').submit(function(e){
        e.preventDefault();     
        var formData = $(this).serialize();
        var formURL = $(this).attr('action');

        $('._loadchecks a').css('color', '#dfecf6');

        $('._loadchecks').append('<img class="tableload" src="'+siteurl+'/img/ring-alt.svg" />');
        getArticles(formURL,formData);
        window.history.pushState("", "", formURL);
    });

    $('body').on('click', '.pagination a', function(e) {
        e.preventDefault();
        
        $('._loadchecks a').css('color', '#dfecf6');

        $('._loadchecks').append('<img class="tableload" src="'+siteurl+'/img/ring-alt.svg" />');

        var url = $(this).attr('href');  

        var data = $('form#formCheckSearch').serialize();
        getArticles(url,data);
        window.history.pushState("", "", url);
    });

    $('span.isearch').click(function(){
        $('#_checkssearch').slideToggle();
    });

    $('#check-info').dataTable( {
        "pagingType": "full_numbers",
        "ordering": false,
        "processing": true
    });

    $('#_salesman').DataTable({
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                    "url": siteurl+"/getsalesman",
                    "dataType": "json",
                    "type": "POST"
               },
        "columns": [
            { "data": "smancode" },
            { "data": "smanname" },
            { "data": "createdby" },
            { "data": "datecreated" },
            { "data": "action" }
        ]    
    }); 

    $('#_banks').DataTable({
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                    "url": siteurl+"/getbanks",
                    "dataType": "json",
                    "type": "POST"
               },
        "columns": [
            { "data": "bankcode" },
            { "data": "branchname" },
            { "data": "createdby" },
            { "data": "datecreated" },
            { "data": "action" }
        ]    
    });   

    $('#_customers').DataTable({
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                    "url": siteurl+"/getcustomers",
                    "dataType": "json",
                    "type": "POST"
               },
        "columns": [
            { "data": "custcode" },
            { "data": "customername" },
            { "data": "createdby" },
            { "data": "datecreated" },
            { "data": "action" }
        ]    
    });   

    $('#userlist').DataTable({
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                    "url": siteurl+"/allusers",
                    "dataType": "json",
                    "type": "POST"
               },
        "columns": [
            { "data": "id" },
            { "data": "fullname" },
            { "data": "username" },
            { "data": "company" },
            { "data": "department" },
            { "data": "businessunit" },
            { "data": "usertype" },
            { "data": "status" },
            { "data": "date" },
            { "data": "action" }
        ]    
    });

    $('#checkstatus').DataTable({
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                    "url": siteurl+"/checklist",
                    "dataType": "json",
                    "type": "POST"
               },
        "columns": [
            { "data": "custcode" },
            { "data": "acctno" },
            { "data": "acctname" },
            { "data": "checkno" },
            { "data": "checkdate" },
            { "data": "branchname" },
            { "data": "amt" },
            { "data": "status" },
            { "data": "action" }
        ]    
    });
   
    $('.panel-body').on('submit','form#_saveBanks',function(event){
        event.preventDefault()

        $('.response').html('');
        var formURL = $(this).attr('action');
        var formData = new FormData($(this)[0]);       

        if($('#file').val().trim()=='')
        {
            alert('Please select file.');
            return false;
        }     

        BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Are you sure you want to submit GC Request?',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
                $('#btnUpBank').prop("disabled",true);
            },
            onhidden:function(dialog){
                $('#btnUpBank').prop("disabled",false);
            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Yes',
                cssClass: 'btn-primary',
                hotkey: 13,
                action:function(dialogItself){
                    $buttons = this;
                    $buttons.disable();                 
                    dialogItself.close();
                        $.ajax({
                            url:formURL,
                            type:'POST',
                            data: formData,
                            dataType: 'text',
                            enctype: 'multipart/form-data',
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            beforeSend:function(){
                            },
                            success:function(data){
                                //console.log(data);
                                var data = JSON.parse(data);

                                if(data['st'])
                                {
                                    dialogItself.close();
                                    var dialog = new BootstrapDialog({
                                    message: function(dialogRef){
                                    var $message = $('<div>GC Request Saved.</div>');                   
                                        return $message;
                                    },
                                    closable: false
                                    });
                                    dialog.realize();
                                    dialog.getModalHeader().hide();
                                    dialog.getModalFooter().hide();
                                    dialog.getModalBody().css('background-color', '#0088cc');
                                    dialog.getModalBody().css('color', '#fff');
                                    dialog.open();
                                    setTimeout(function(){
                                        window.location.reload();
                                    }, 1700);                                           
                                }
                                else 
                                {
                                    $('.response').html('<div class="alert alert-danger" id="danger-x">'+data['msg']+'</div>');
                                    $buttons.enable();
                                }
                            }
                        });
                }
            }, {
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'No',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });

    $("body").on('change', "input#checkdate", function(){
        var cdate = $(this).val();
        // check validation
        if(!isValidDate(cdate))
        {
           alert('Date is invalid.');
           return false;
        }

        checkType(cdate);
    });

    $('#newuser').click(function(){
        BootstrapDialog.show({
            title: '<i class="fa fa-user-plus" aria-hidden="true"></i> Add New User',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'addnewuserdialog',
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Add',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var username = $('#username').val();
                    var idnumber = $('#idnumber').val();
                    var fullname = $('#fullname').val();
                    var company = $('#company').val();
                    var bunit = $('#bunit').val();
                    var department = $('#department').val();
                    var usertype = $('#usertype').val();
                    var password = $('#password').val();                    
                    var ipaddress = $('#ipaddress').val();

                    if(username.trim()=='' || idnumber.trim()=='' || fullname.trim()=='' || idnumber.trim()=='' || bunit.trim()=='' || department.trim()=='' || usertype.trim()=='' || password.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields.</div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Add New User?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>User Successfully Added.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });

    // Begin Salesman script

    $('#_addnewSalesman').click(function(){
        BootstrapDialog.show({
            title: '<i class="fa fa-user-plus" aria-hidden="true"></i> Add New Salesman',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'addnewsalesmandialog',
            },
            cssClass: 'changeaccountpass',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveSalesman').serialize(), formURL = $('form#_saveSalesman').attr("action");
                    var scode = $('form#_saveSalesman input#sman_code').val(), fullname = $('form#_saveSalesman input#fullname').val();

                    if(scode.trim()=='' || fullname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields.</div>');
                        $('input#fullname').select();
                        $button.enable();
                        return false;
                    }                   

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Add New Salesman?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Salesman Successfully Created.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            $('#smanid').val(data.salesmanid);

                                            $('#scodename').val(data.smanfullname);

                                            $('#ssearch').val('');

                                            setTimeout(function(){                                                
                                                dialog.close(); 
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_saveSalesman input#fullname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });

    $("body").on('click', "table#userlist tbody tr td a span#edituser", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Edit User',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'edituserdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var username = $('#username').val();
                    var idnumber = $('#idnumber').val();
                    var fullname = $('#fullname').val();
                    var company = $('#company').val();
                    var bunit = $('#bunit').val();
                    var department = $('#department').val();
                    var usertype = $('#usertype').val();                  
                    var ipaddress = $('#ipaddress').val();

                    if(username.trim()=='' || idnumber.trim()=='' || fullname.trim()=='' || bunit.trim()=='' || department.trim()=='' || usertype.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update User?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>User Successfully Updated.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });

    $("body").on('click', "table#userlist tbody tr td a span#viewuser", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View User',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewuserdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var username = $('#username').val();
                    var idnumber = $('#idnumber').val();
                    var fullname = $('#fullname').val();
                    var company = $('#company').val();
                    var bunit = $('#bunit').val();
                    var department = $('#department').val();
                    var usertype = $('#usertype').val();                  
                    var ipaddress = $('#ipaddress').val();

                    if(username.trim()=='' || idnumber.trim()=='' || fullname.trim()=='' || bunit.trim()=='' || department.trim()=='' || usertype.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    });    

    $("body").on('click', "table#userlist tbody tr td a span#changepassword", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
        BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Change User Password',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'changepassdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Change',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var password = $('#password').val();

                    if(password.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Change User Password?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>User Password Successfully Changed.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;
                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });  

    $("body").on('click', "li.salesmanlist", function(){
        // var name = $(this).text();
        var id = $(this).attr('data-id');
        var scode = $(this).attr('data-code');
        var name = $(this).attr('data-name');

        $('#smanid').val(id);

        var cname = scode+" - "+name;

        $('#ssearch').val(name);

        $('#scodename').val(cname);

        $('#save').focus();

        $('span.smanlist').hide();
    });  

    $( "#ssearch" ).bind({
        keyup: function() {
            var formURL = $('#ssearchroute').val();

            //alert(formURL);
            var sman = $(this).val().trim();
            if(sman.length > 2)
            {
                $('span.smanlist').show();
                $('span.smanlist').html("<img src='"+siteurl+"/img/ring-alt.svg' class='loadver'> Processing Please Wait..");
                $.ajax({
                    url:formURL,
                    type:'get',
                    data:{'search':sman},
                    beforeSend:function(){
                        
                    },
                    success:function(data){                        
                        $('span.smanlist').html(data.output); 
                    }
                });
            }
            else 
            {
                $('span.smanlist').hide();
            }
        },
        blur: function(){
            setTimeout(function(){
                $('span.smanlist').hide();
            },500)             
        }

    });

    $("body").on('click', "table#_salesman tbody tr td a span#viewsalesman", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Salesman',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewsalesmandialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var salesman_id = $('#salesman_id').val();
                    var sman_code = $('#sman_code').val();
                    var fullname = $('#fullname').val();
                    var createdby = $('#createdby').val();
                    var datecreated = $('#datecreated').val();

                    if(salesman_id.trim()=='' || sman_code.trim()=='' || fullname.trim()=='' || createdby.trim()=='' || datecreated.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    }); 

    $("body").on('click', "table#_salesman tbody tr td a span#editsalesman", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Edit Salesman',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'editsalesmandialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var sman_code = $('#sman_code').val();
                    var fullname = $('#fullname').val();


                    if(sman_code.trim()=='' || fullname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Salesman?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Salesman Successfully Updated.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });


    // End Salesman Script

    // Begin Customer Script

    $("body").on('click', "table#_customers tbody tr td a span#viewcustomer", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Customer',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcustomerdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var customer_id = $('#customer_id').val();
                    var cus_code = $('#cus_code').val();
                    var fullname = $('#fullname').val();
                    var createdby = $('#createdby').val();
                    var datecreated = $('#datecreated').val();

                    if(customer_id.trim()=='' || cus_code.trim()=='' || fullname.trim()=='' || createdby.trim()=='' || datecreated.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    }); 

    $("body").on('click', "table#_customers tbody tr td a span#editcustomer", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Edit Customer',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'editcustomerdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var cus_code = $('#cus_code').val();
                    var fullname = $('#fullname').val();


                    if(cus_code.trim()=='' || fullname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Customer?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Customer Successfully Updated.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    }); 

    $("body").on(
        'keyup', "input#ssearchcustomer", function(){
        var cusname = $(this).val().trim();
        if(cusname.length > 2)
        {
            $('span.custlist').show();
            $('span.custlist').html("<img src='"+siteurl+"/img/ring-alt.svg' class='loadver'> Processing Please Wait..");
            $.ajax({
                url:siteurl+'/searchcustomer',
                data:{cusname:cusname},
                type:'GET',
                beforeSend:function(){
                    
                },
                success:function(data){
                    console.log(data);
                    $('span.custlist').html(data.output);                       
                }
            });
        }
        else 
        {
            $('span.custlist').hide();
        }
    }).on('click', "li.customerlist", function(){

        var id = $(this).attr('data-id');
        var ccode = $(this).attr('data-code');
        var name = $(this).attr('data-name');

        $('#cid').val(id);

        var cname = ccode+" - "+name;

        $('#customerdetails').val(cname);

        $('#ssearchcustomer').val(name);

        $('#custcode').val(ccode);

        // $('#scodename').val(cname);

        // $('#save').focus();

        $('span.custlist').hide();
        

    }).on('click', "button#_addnewCustomer", function(){
        BootstrapDialog.show({
            title: '<i class="fa fa-user-plus" aria-hidden="true"></i> Add New Customer',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {                
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'addnewcustomerdialog',
            },
            cssClass: 'changeaccountpass',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveCustomer').serialize(), formURL = $('form#_saveCustomer').attr("action");

                    var cuscode = $('form#_saveCustomer input#cus_code').val(), cusname = $('form#_saveCustomer input#fullname').val();

                    if($('form#_saveCustomer input#cus_code').val()==undefined)
                    {
                        $button.enable();
                        return false;
                    }

                    if(cuscode.trim()=='' || cusname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields.</div>');
                        $('form#_saveCustomer input#fullname').focus();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Add New Customer?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        dialogItself.close();
                                        dialogItself1.close();
                                        console.log(data);
                                        //var data = JSON.parse(data);
                                        
                                        if(data.status)
                                        {
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Customer Added.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            $('form#_addnewcheck input#cid').val(data.customerid);

                                            $('form#_addnewcheck input#customerdetails').val(data.cusfullname);

                                            $('input#ssearchcustomer').val("");

                                            setTimeout(function(){                                                
                                                dialog.close();                                                 
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            $('form#_saveCustomer div.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">'+data.error+'</div>');
                                            $('form#_saveCustomer input#fullname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });
    $('body').on('blur', "input#ssearchcustomer, span.custlist", function(){
        setTimeout(function(){
            $('span.custlist').hide();
        },300) 

    });

    //End Customer Script

    //Begin Bank Script

    $("body").on('click', "table#_banks tbody tr td a span#viewbank", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Bank',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewbankdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var bankcode = $('#bankcode').val();
                    var bankbranchname = $('#bankbranchname').val();


                    if(bankcode.trim()=='' || bankbranchname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    }); 

    $("body").on('click', "table#_banks tbody tr td a span#editbank", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Edit Bank',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'editbankdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var bankcode = $('#bankcode').val();
                    var bankbranchname = $('#bankbranchname').val();


                    if(bankcode.trim()=='' || bankbranchname.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Bank?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {
                                        //console.log(data);
                                        if(data.status)
                                        {
                                            BootstrapDialog.closeAll();
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Bank Successfully Updated.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            setTimeout(function(){                                                
                                                window.location.reload();
                                            }, 1500);
                                        }
                                        else 
                                        {
                                            printErrorMsg(data.error);
                                            $('form#_addnewSalesman input#fname').focus();
                                            dialogItself.close();
                                            $button.enable();   
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    }); 

    $("body").on('click', "button#_addnewBank", function(){
        BootstrapDialog.show({
            title: '<i class="fa fa-user-plus" aria-hidden="true"></i> Add New Bank',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {                
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'addnewbankdialog',
            },
            cssClass: 'changeaccountpass',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();

            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){ 
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var hasError = false;
                    var postData = $('form#_saveBank').serialize(), formURL = $('form#_saveBank').attr("action");

                    var bankcode = $('form#_saveBank input#bankcode').val(), bankbranchname = $('form#_saveBank input#bankbranchname').val();

                    if($('form#_saveBank input#bankcode').val()==undefined)
                    {
                        $button.enable();
                        return false;
                    }

                    $('form#_saveBank .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();
                        }

                    });  

                    if(bankcode.trim()=="")
                    {
                        hasError = true;
                        $('input#bankcode').parent().addClass('has-error');                    
                        $( "input#bankcode" ).after( "<span class='help-block'><strong>Please enter bank code.</strong></span>" );
                    }

                    if(bankcode.trim()=="")
                    {
                        hasError = true;
                        $('input#bankbranchname').parent().addClass('has-error');                    
                        $( "input#bankbranchname" ).after( "<span class='help-block'><strong>Please enter branch name.</strong></span>" );
                    }


                    if(hasError)
                    {           
                        $('input#bankcode').focus();
                        $button.enable();             
                        return false;
                    }

                    $('form#_saveBank .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();
                        }

                    }); 

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Add New Bank?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                var $button1 = this;
                                $button1.disable();
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {                                                                         
                                        console.log(data);                                       
                                        
                                        if(data.status)
                                        {
                                            dialogItself.close();   
                                            dialogItself1.close();    
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Bank Added.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();

                                            $('form#_addnewcheck input#bid').val(data.bankid);

                                            $('form#_addnewcheck input#bankdetails').val(data.codebranchname);

                                            $('form#_addnewcheck input#bankcode').val(data.bankcode);

                                            $('form#_addnewcheck input#bankname').val(data.bankname);

                                            $('input#ssearchbank').val("");

                                            setTimeout(function(){                                                
                                                dialog.close();                                                 
                                            }, 1500);
                                        }
                                        else 
                                        {                                            
                                            for (var i = 0; i < data.error.length; i++) 
                                            {
                                                //alert(data.error[i]);
                                                if(data.error[i].indexOf("Bank Code") >0)
                                                {
                                                    $('input#bankcode').parent().addClass('has-error');
                                                    $( "input#bankcode" ).after( "<span class='help-block'><strong>"+data.error[i]+"</strong></span>" );
                                                }

                                                if(data.error[i].indexOf("Branch Name") >0)
                                                {
                                                    $('input#bankbranchname').parent().addClass('has-error');
                                                    $( "input#bankbranchname" ).after( "<span class='help-block'><strong>"+data.error[i]+"</strong></span>" );
                                                }

                                            }                   
                                            
                                            dialogItself1.close();
                                            $button.enable();   
                                            $('input#bankcode').focus();
                                            return false;
                                        }
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself){
                                dialogItself.close();
                                $button.enable();  
                            }
                        }]
                    }); 

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

    $("body").on('click', "table#_banks tbody tr td a span#viewbank", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Bank',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewbankdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var bankcode = $('#bankcode').val();
                    var bankbranchname = $('#bankbranchname').val();
                    var createdby = $('#createdby').val();
                    var datecreated = $('#datecreated').val();

                    if(bankcode.trim()=='' || bankbranchname.trim()=='' || createdby.trim()=='' || datecreated.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    });

    }).on(
        'keyup', "input#ssearchbank", function(){
        var bankbranch = $(this).val().trim();
        if(bankbranch.length > 2)
        {
            $('span.banklist').show();
            $('span.banklist').html("<img src='"+siteurl+"/img/ring-alt.svg' class='loadver'> Processing Please Wait..");
            $.ajax({
                url:siteurl+'/searchbankbranch',
                data:{bankbranch:bankbranch},
                type:'GET',
                beforeSend:function(){
                    
                },
                success:function(data){
                    console.log(data);
                    $('span.banklist').html(data.output);                       
                }
            });
        }
        else 
        {
            $('span.banklist').hide();
        }
    }).on('click', "li.bankbranchlist", function(){
        var id = $(this).attr('data-id');
        var bcode = $(this).attr('data-code');
        var name = $(this).attr('data-name');

        $('#bid').val(id);

        var bcodename = bcode+" - "+name;

        $('#bankdetails').val(bcodename);

        $('#ssearchbank').val(name);

        $('#bankcode').val(bcode);

        $('#bankname').val(name);

        // $('#scodename').val(cname);

        // $('#save').focus();

        $('span.banklist').hide();       
    });     
    //End Bank Script
    // $('#check-info tbody').on( 'click', 'tr td.action-check a', function () {
    //     var table = $('#check-info').DataTable();
    //     console.log( table.row( this ).data() );
    //     var newdata = ['x','xx','xcc','34839','34893498','43430834','43430'];
    //     table.row( this ).data( newdata ).draw();
    // } );
    // Check Script
    $("body").on('click',"div.action-check span#editcheck",function(event){
        event.preventDefault();               
        var key = $(this).closest('div.action-check').attr('data-id');
        console.log("key="+key);
        // var key = $(this).parent().text();
        // return false;
        if(key.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> Edit Check',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'editcheckdialog/'+key,
            },
            cssClass: 'addnewcheck',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-edit',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    //var key = 0;
                    var table = $('#check-info').DataTable();
                    console.log( "Saving..= "+table.row( key ).data() );

                    return false;
                    $('.response-dialog').html('');
                    // dialogItself.enableButtons(false);
                    // dialogItself.setClosable(false);
                    var hasError = false;
                    var dateError = false;
                    //$button.disable();

                    var postData = $('form#_editcheck').serialize(), formURL = $('form#_editcheck').attr("action");
                    var customerdetails = $('#customerdetails').val()
                    var cusid = $('#cid').val();
                    var checkno = $('#checkno').val();
                    var checkdate = $('#checkdate').val();
                    var accountno = $('#accountno').val();
                    var accountname = $('#accountname').val();
                    var bankdetails = $('#bankdetails').val();
                    var bankname = $('#bankname').val();
                    var checkamt = $('#checkamt').val();
                    var custcode = $('#custcode').val();
                    var checktype = $('#checktype').val();
                    var checkclass = $('#cclass').val();
                    var checkamt = $('#checkamt').val();                    

                    if($('form#_editcheck input#checkno').val()==undefined)
                    {
                        dialogItself.enableButtons(true);
                        dialogItself.setClosable(true);
                        return false;
                    }

                    $('form#_editcheck .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();                            
                        }
                    });  

                    if(customerdetails.trim()=="" || cusid.trim()=="")
                    {
                        hasError = true;
                        $('input#customerdetails').parent().addClass('has-error');                    
                        $( "input#customerdetails" ).after( "<span class='help-block'><strong>Please enter customer details.</strong></span>" );
                    }

                    if(checkno.trim()=="")
                    {
                        hasError = true;
                        $('input#checkno').parent().addClass('has-error');                    
                        $( "input#checkno" ).after( "<span class='help-block'><strong>Please enter Check #.</strong></span>" );
                    }
                    
                    if(checkdate.trim()=="")
                    {
                        hasError = true;
                        dateError = true
                        $('input#checkdate').parent().addClass('has-error');                    
                        $( "input#checkdate" ).after( "<span class='help-block'><strong>Please enter Check Date.</strong></span>" );
                    }

                    if(!isValidDate(checkdate) && !dateError)                    
                    {
                        hasError = true;
                        $('input#checkdate').parent().addClass('has-error');                    
                        $( "input#checkdate" ).after( "<span class='help-block'><strong>Check Date is invalid.</strong></span>" );                       
                    }

                    if(accountno.trim()=="")
                    {
                        hasError = true;                        
                        $('input#accountno').parent().addClass('has-error');                    
                        $( "input#accountno" ).after( "<span class='help-block'><strong>Please enter Account Number.</strong></span>" );
                    }

                    if(accountname.trim()=="")
                    {
                        hasError = true;                        
                        $('input#accountname').parent().addClass('has-error');                    
                        $( "input#accountname" ).after( "<span class='help-block'><strong>Please enter Account Name.</strong></span>" );
                    }

                    if(bankdetails.trim()=="")
                    {
                        hasError = true;                        
                        $('input#bankdetails').parent().addClass('has-error');                    
                        $( "input#bankdetails" ).after( "<span class='help-block'><strong>Please enter Bank Details.</strong></span>" );
                    }

                    if(checkamt.trim()=="" || checkamt.trim()=="0.00")
                    {
                        hasError = true;                        
                        $('input#checkamt').parent().addClass('has-error');                    
                        $( "input#checkamt" ).after( "<span class='help-block'><strong>Please enter Check Amount.</strong></span>" );
                    }

                    if(hasError)
                    {           
                        $('input#ssearchcustomer').focus();
                        dialogItself.enableButtons(true);
                        dialogItself.setClosable(true);          
                        return false;
                    }

                    $('form#_editcheck .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();
                        }
                    }); 

                    console.log( table.row( key ).data()+"2" );

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Check?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                // return false;
                                // dialogItself1.enableButtons(false);
                                // dialogItself1.setClosable(false);
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {    
                                        console.log(data);                                                               
                                        if(data.status)
                                        {                                            
                                            console.log( "success ="+table.row( key ).data() );
                                            $('.response-dialog').html("");
                                            $('._totAmt').text(data.totalamount);
                                            $('#savecheck').prop("disabled",false);
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                            var newdata = [
                                                custcode,
                                                checktype,
                                                checkclass,
                                                accountno,
                                                accountname.toUpperCase(),
                                                checkno,
                                                checkdate,
                                                bankname,   
                                                checkamt,   
                                                '<div class="action-check" data-id="'+key+'">&emsp;<a href="#" title="SHOW"><span class="glyphicon glyphicon-list" id="showcheck"></span></a>&emsp;<a href="#" title="EDIT" ><span class="glyphicon glyphicon-edit" id="editcheck"></span></a>&emsp;<a href="#" title="REMOVE" ><span class="glyphicon glyphicon-remove-sign" id="removecheck"></span></a></div>'];
                                            table.row( key ).data( newdata ).draw();
                                            var tabz = $('#check-info').DataTable();
                                            dialogItself1.close();        
                                            dialogItself.close();
                                            setTimeout(function(){
                                                alert('Check #'+checkno+' Details Updated.');     
                                            },100);
                                                                           
                                        }   
                                        else 
                                        {
                                            $('.response-dialog').html(data.error);
                                            dialogItself1.enableButtons(true);
                                            dialogItself1.setClosable(true);
                                        }                              
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself1){
                                dialogItself1.close();
                                dialogItself.enableButtons(true);
                                dialogItself.setClosable(true);    
                            }
                        }]
                    });                     

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

    }).on('click',"div.action-check span#removecheck",function(event){
        event.preventDefault();
        var checknum = $(this).closest('tr').find('td:eq(5)').text();
        var key = $(this).closest('div.action-check').attr('data-id');

        var $that = $(this);
 
        //td:nth-child(2)
        var r = confirm("Remove Check #"+checknum);
        if (r == true) 
        {
            $.ajax({
                url:'removecheck/'+key,
                success:function(data)
                {    
                    console.log(data);                                                               
                    if(data.status)
                    {
                        var table = $('#check-info').DataTable();
                        table
                        .row( $($that).parents('tr') )
                        .remove()
                        .draw(); 
                        console.log(data);
                        $('._totAmt').text(data.totalamount);
                        setTimeout(function(){
                            alert('Check #'+checknum+' successfully removed.');
                        },200);
                        
                    }   
                    else 
                    {
                        alert(data.error);
                    }                              
                } 
            });   

        }
    }).on('click',"div.action-check span#showcheck",function(event){
        event.preventDefault();               
        var key = $(this).closest('div.action-check').attr('data-id');
        console.log("key="+key);
        // var key = $(this).parent().text();
        // return false;
        if(key.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> Show Check Details',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'showcheckdialog/'+key,
            },
            cssClass: 'addnewcheck',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });

    $('#savecheck').click(function(event){
        event.preventDefault();
        $('#savecheck').prop("disabled",true);
        var postData = $('form#_saveCheck').serialize(), formURL = $('form#_saveCheck').attr("action");
        BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Save Check?',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden:function(dialog)
            {
                $('#savecheck').prop("disabled",false);
            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-primary',
                hotkey: 13,
                action:function(dialogItself){
                    // dialogItself.enableButtons(false);
                    // dialogItself.setClosable(false);
                    $.ajax({
                        url:formURL,
                        data:postData,
                        type:'POST',
                        beforeSend:function(){
                            $('#loaderdiv').html("<h4 class='loadh4'>Saving...Please Wait...</h4>")
                            $('#processing-modal').modal('show');
                            dialogItself.close();
                        },
                        success:function(data)
                        {                                                                         
                            if(data.status)
                            {
                                setTimeout(function(){
                                    $('#processing-modal').modal('hide');   
                                    swal({
                                        title: "Success.",
                                        type: "success",
                                        text: data.message,
                                        showCancelButton: false,
                                        confirmButtonColor: "#7cd1f9",
                                        confirmButtonText: "OK",
                                        closeOnConfirm: false
                                    },function(){
                                        location.reload();
                                    })                                     
                                },100);
                            }   
                            else 
                            {
                                setTimeout(function(){
                                    $('#processing-modal').modal('hide');   
                                    swal({
                                        title: "EOD Failed",
                                        type: "warning",
                                        text: data.error,
                                        showCancelButton: false,
                                        confirmButtonColor: "#DD6B55",
                                        confirmButtonText: "OK",
                                        closeOnConfirm: true
                                    })
                                },100)       
                            }                              
                        }
                    });                                                    
                }
            },{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close(); 
                }
            }]
        });     

    });

    $('#newcheck').click(function(){
        var t = $('#check-info').DataTable();
        var formURL = $('#createcheckroute').val();

        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-plus"></span> Add New Check',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': formURL,
            },
            cssClass: 'addnewcheck',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Add',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    dialogItself.enableButtons(false);
                    dialogItself.setClosable(false);
                    var hasError = false;
                    var dateError = false;
                    //$button.disable();

                    var postData = $('form#_addnewcheck').serialize(), formURL = $('form#_addnewcheck').attr("action");

                    var customerdetails = $('#customerdetails').val()
                    var cusid = $('#cid').val();
                    var checkno = $('#checkno').val();
                    var checkdate = $('#checkdate').val();
                    var accountno = $('#accountno').val();
                    var accountname = $('#accountname').val();
                    var bankdetails = $('#bankdetails').val();
                    var bankname = $('#bankname').val();
                    var checkamt = $('#checkamt').val();
                    var custcode = $('#custcode').val();
                    var checktype = $('#checktype').val();
                    var checkclass = $('#cclass').val();
                    var checkamt = $('#checkamt').val();

                    if($('form#_addnewcheck input#checkno').val()==undefined)
                    {
                        dialogItself.enableButtons(true);
                        dialogItself.setClosable(true);
                        return false;
                    }

                    $('form#_addnewcheck .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();                            
                        }
                    });  

                    if(customerdetails.trim()=="" || cusid.trim()=="")
                    {
                        hasError = true;
                        $('input#customerdetails').parent().addClass('has-error');                    
                        $( "input#customerdetails" ).after( "<span class='help-block'><strong>Please enter customer details.</strong></span>" );
                    }

                    if(checkno.trim()=="")
                    {
                        hasError = true;
                        $('input#checkno').parent().addClass('has-error');                    
                        $( "input#checkno" ).after( "<span class='help-block'><strong>Please enter Check #.</strong></span>" );
                    }
                    
                    if(checkdate.trim()=="")
                    {
                        hasError = true;
                        dateError = true
                        $('input#checkdate').parent().addClass('has-error');                    
                        $( "input#checkdate" ).after( "<span class='help-block'><strong>Please enter Check Date.</strong></span>" );
                    }

                    if(!isValidDate(checkdate) && !dateError)                    
                    {
                        hasError = true;
                        $('input#checkdate').parent().addClass('has-error');                    
                        $( "input#checkdate" ).after( "<span class='help-block'><strong>Check Date is invalid.</strong></span>" );                       
                    }

                    if(accountno.trim()=="")
                    {
                        hasError = true;                        
                        $('input#accountno').parent().addClass('has-error');                    
                        $( "input#accountno" ).after( "<span class='help-block'><strong>Please enter Account Number.</strong></span>" );
                    }

                    if(accountname.trim()=="")
                    {
                        hasError = true;                        
                        $('input#accountname').parent().addClass('has-error');                    
                        $( "input#accountname" ).after( "<span class='help-block'><strong>Please enter Account Name.</strong></span>" );
                    }

                    if(bankdetails.trim()=="")
                    {
                        hasError = true;                        
                        $('input#bankdetails').parent().addClass('has-error');                    
                        $( "input#bankdetails" ).after( "<span class='help-block'><strong>Please enter Bank Details.</strong></span>" );
                    }

                    if(checkamt.trim()=="" || checkamt.trim()=="0.00")
                    {
                        hasError = true;                        
                        $('input#checkamt').parent().addClass('has-error');                    
                        $( "input#checkamt" ).after( "<span class='help-block'><strong>Please enter Check Amount.</strong></span>" );
                    }

                    if(hasError)
                    {           
                        $('input#ssearchcustomer').focus();
                        dialogItself.enableButtons(true);
                        dialogItself.setClosable(true);          
                        return false;
                    }

                    $('form#_saveBank .form-group').each(function() 
                    {            
                        var str = $(this).attr('class');
                        if(str.indexOf("has-error") >0)
                        {
                            $(this).removeClass("has-error");
                            $(this).find('span.help-block').hide();
                        }
                    }); 
                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Add New Check?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                dialogItself1.enableButtons(false);
                                dialogItself1.setClosable(false);
                                $.ajax({
                                    url:formURL,
                                    data:postData,
                                    type:'POST',
                                    success:function(data)
                                    {           
                                        if(data.status)
                                        {
                                            $('form#_addnewcheck').trigger("reset");
                                            $('.response-dialog').html("");
                                            $('._totAmt').text(data.totalamount);
                                            $('#savecheck').prop("disabled",false);
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                            t.row.add( [                    
                                                custcode,
                                                checktype,
                                                checkclass,
                                                accountno,
                                                accountname.toUpperCase(),
                                                checkno,
                                                checkdate,
                                                bankname,   
                                                checkamt,                                               
                                                '<div class="action-check" data-id="'+data.checkkey+'">&emsp;<a href="#" title="SHOW"><span class="glyphicon glyphicon-list" id="showcheck"></span></a>&emsp;<a href="#" title="EDIT" ><span class="glyphicon glyphicon-edit" id="editcheck"></span></a>&emsp;<a href="#" title="REMOVE" ><span class="glyphicon glyphicon-remove-sign" id="removecheck"></span></a></div>'
                                            ] ).draw( false );
                                            dialogItself1.close();
                                            // t.row.add( [                 
                                            //     data['denomination'],
                                            //     data['barcode'],
                                            //     '<input type="hidden" value="'+data['key']+'" class="denoms"><i class="fa fa-times remove-employee" aria-hidden="true"></i>'
                                            // ] ).draw( false );
                                            
                                        }   
                                        else 
                                        {
                                            dialogItself1.enableButtons(true);
                                            dialogItself1.setClosable(true);
                                        }                              
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself1){
                                dialogItself1.close();
                                dialogItself.enableButtons(true);
                                dialogItself.setClosable(true);    
                            }
                        }]
                    });                     

                    return false;

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    });

    $("body").on('keyup',"input#checkamt",function(){
        $('#checkamtwords').val($(this).AmountInWords());   
    }).on('blur', "input#ssearchbank, span.banklist", function(){
        setTimeout(function(){
            $('span.banklist').hide();
        },300) ;
    });

    $('#duepdcexport').click(function(){
        location.href="duepdcexport";
    });

    $('#checksforDepositExport').click(function(){
        location.href="checksforDepositExport";
    });

    $('#checksforClearingExport').click(function(){
        location.href="checksforClearingExport";
    });

    $('#bouncedChecksExport').click(function(){
        location.href="bouncedChecksExport";
    });

    $("#cboxcleared").click(function () {
        if($(this).is(':checked'))
        {            
            $('#viewcheck2,#tagcheckas').css('color','#337ab7');
            $('table.paleBlueRows tbody tr').css('color','#333');
            $('input.checkboxItems').prop('checked', true);
            //alert($('input[id^="tagbouncecid-"]').value());
            $('input[id^="tagbouncecid-"]').val(false);
        }
        else
            $('input.checkboxItems').prop('checked', false);        
    });

    $('.checkboxItems').click(function(){
        var id = $(this).closest('div.action-duecheck').attr('data-id');
        if($(this).not(':checked'))
        {            
            $('#tagbouncecid-'+id).val(false);
            $('#tagbouncecid-'+id).parent().closest('tr').css('color','#333');
            $('#tagbouncecid-'+id).closest('td').find('#viewcheck2').css('color','#337ab7');
            $('#tagbouncecid-'+id).closest('td').find('#tagcheckas').css('color','#337ab7');
            // $('#viewcheck,#tagcheckas').css('color','#337ab7');
            // $('table.paleBlueRows tbody tr').css('color','#000');
            // $('input.checkboxItems').prop('checked', true);
            // $('^tagbouncecid-').value(false);
        }

    });


    $('span#viewcheck').click(function(event){            
        var id = $(this).closest('div.action-check').attr('data-id');
        console.log("key="+id);
        // var key = $(this).parent().text();
        // return false;
        if(id.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> View Check Details',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckdialog/'+id,
            },
            cssClass: 'addnewcheck',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });

    $('span#viewcheck2').click(function(event){            
        var id = $(this).closest('div.action-duecheck').attr('data-id');
        console.log("key="+id);
        // var key = $(this).parent().text();
        // return false;
        if(id.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> View Check Details',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckdialog2/'+id,
            },
            cssClass: 'addnewcheck',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        return false;
    });


    $("body").on('click', "table#checklist tbody tr td a span#viewcheck", function(){
        var id = $(this).closest('div.action-user').attr('data-id');
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Check Details',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckdialog/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();
                    var postData = $('form#_saveUser').serialize(), formURL = $('form#_saveUser').attr("action");

                    var checkamt = $('#checkamt').val();
                    var checkamtwords = $('#checkamtwords').val();


                    if(checkamt.trim()=='' || checkamtwords.trim()=='')
                    {
                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields. </div>');
                        $('input#fnamedialog').select();
                        $button.enable();
                        return false;
                    }

                    dialogItself.close();

                }
            }]
        });
        return false;
    }); 

    $('span#tagcheckas').click(function(){
        var id = $(this).closest('div.action-duecheck').attr('data-id');
        var state = $('#tagbouncecid-'+id).val();
        
        if(id.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-warning-sign"></span> Confirmation',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },300);
              return $message;
            },
            data: {
                'pageToLoad': 'tagasdialog/'+id+'/'+state,
            },
            cssClass: 'tagasdialog',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    if(state=='false')
                    {
                        $('#tagbouncecid-'+id).val(true);
                        $('#tagbouncecid-'+id).parent().closest('tr').css('color','#cc0000');
                        $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                        $('#tagbouncecid-'+id).closest('td').find('#viewcheck2').css('color','#cc0000');
                        $('#tagbouncecid-'+id).closest('td').find('#tagcheckas').css('color','#cc0000');
                    }
                    else 
                    {
                        $('#tagbouncecid-'+id).val(false);
                        $('#tagbouncecid-'+id).parent().closest('tr').css('color','#333');
                        $('#tagbouncecid-'+id).closest('td').find('#viewcheck2').css('color','#337ab7');
                        $('#tagbouncecid-'+id).closest('td').find('#tagcheckas').css('color','#337ab7');
                    }
                    dialogItself.close();
                    return false;
                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        // $(this).parent().closest('tr').css('color','#cc0000');
        // $(this).closest('td').find('[type=checkbox]').prop('checked', false);
        return false;
    });

    $('span#updatebounced').click(function(){
        var $this = $(this);
        var id = $(this).closest('div.action-check').attr('data-id');
        var state = $('#tagbouncecid-'+id).val();
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> Update Bounced Check',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'updatebounced/'+id,
            },
            cssClass: 'updatebounced',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');

                    var $button = this;    
                    
                    var formURL = $('#_updatebouncedchecktemp').attr('action');
                    var formDATA = $('#_updatebouncedchecktemp').serialize();

                    var checkno = $('#ocheckno').val();
                    var checkid = $('#checkid').val();

                    var hasUpdate           = false;
                    var updatetype          = null;

                    //redeposit
                    var redepcheckamount    = $('#redepcheckamount').val();

                    //replacement
                    var repcheckamt         = $('#repcheckamt').val();
                    var repaccountname      = $('#repaccountname').val();
                    var repaccountno        = $('#repaccountno').val();
                    //var repcheckamt         = $('#repcheckamt').val();
                    var checkdate           = $('#checkdate').val();
                    
                    //cash
                    var cashamt             = $('#cashamt').val();

                    if($('#checknum').val()==undefined)
                    {
                        return false;
                    }
                    
                    $('.updatetype').each(function(){
                        if($(this).is(':checked')) 
                        {  
                            updatetype = $(this).val();
                            hasUpdate = true;
                        }
                    });

                    if(!hasUpdate)
                    {
                        $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please select update type.</div>");
                        return false;
                    }

                    //update validation
                    if(updatetype=='redeposit')
                    {
                        if(redepcheckamount.trim()=='' || redepcheckamount=='0.00' || redepcheckamount=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Check Amount is not valid.</div>");
                            return false;
                        }
                    }
                    
                    if(updatetype=='replacement')
                    {
                        if(repcheckamt.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Check No.</div>");
                            return false;
                        }

                        if(repaccountname.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Account Name.</div>");
                            return false;
                        }


                        if(repaccountno.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Check No.</div>");
                            return false;
                        }

                        if(repcheckamt.trim()=='' || repcheckamt=='0.00' || repcheckamt=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Check Amount is not valid.</div>");
                            return false;
                        }

                        if(checkdate.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input valid Check Date.</div>");
                            return false;
                        }
                    }     

                    if(updatetype=='cash')
                    {
                        if(cashamt.trim()=='' || cashamt=='0.00' || cashamt=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Amount is not valid.</div>");
                            return false;
                        }
                    }      
                    
                    dialogItself.enableButtons(false);
                    dialogItself.setClosable(false);    

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Bounced Check?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                $('.response-dialog').html('');
                                if(state=='false' && updatetype =='redeposit')
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','#808000');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#808000');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced').css('color','#808000');
                                }
                                else if(state=='false' && updatetype =='replacement')
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','#0000CD');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#0000CD');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced').css('color','#0000CD');
                                }
                                else
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','green');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','green');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced').css('color','green');
                                }

                                $.ajax({
                                    url:formURL,
                                    data:formDATA,
                                    type:'POST',
                                    success:function(data)
                                    {    
                                        console.log(data);                                                               
                                        if(data.status)
                                        {                    
                                            // tagged check                        
                                            //alert($this);   
                                            dialogItself.close();
                                            dialogItself1.close();
                                            //alert("xx");
                                            //$(this).closest('div.action-check').html("xxx");
                                            $this.closest('span#updatebounced').next('span').remove();
                                            $('.taggedcnt').text(data.tagcount);
                                            $this.closest('span#updatebounced').after("<span class='tagg glyphicon glyphicon glyphicon-"+data['icon']+" gly-icon"+data['updatetype']+"' id='showtaggedcheckinfo' title='"+data['updatetype']+"' onclick='showTaggedCheck("+checkid+",\""+data['updatetype']+"\")'></span>");     
                                            //BootstrapDialog.closeAll();      
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Check # '+checkno+' successfully Tagged as '+data['updatetype']+'.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();   
                                            setTimeout(function(){
                                                dialog.close();
                                            },1800)                                                                   
                                        }   
                                        else 
                                        {
                                            $('.response-dialog').html('<div class="alert alert-danger">'+data.error+'</div>');
                                            dialogItself1.close();
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                        }                              
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself1){
                                dialogItself1.close();
                                dialogItself.enableButtons(true);
                                dialogItself.setClosable(true);    
                            }
                        }]
                    });                     

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

        return false;
    }); 
    
    $('span#updatebounced2').click(function(){
        var $this = $(this);
        var id = $(this).closest('div.action-check').attr('data-id');
        var state = $('#tagbouncecid-'+id).val();
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-edit"></span> Update Bounced Check Test',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'updatebounced2/'+id,
            },
            cssClass: 'updatebounced',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Update',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');

                    var $button = this;    
                    
                    var formURL = $('#_updatebouncedchecktemp2').attr('action');
                    var formDATA = $('#_updatebouncedchecktemp2').serialize();

                    var checkno = $('#ocheckno').val();
                    var checkid = $('#checkid').val();

                    var hasUpdate           = false;
                    var updatetype          = null;

                    //redeposit
                    var redepcheckamount    = $('#redepcheckamount').val();

                    //replacement
                    var repcheckno        = $('#repcheckno').val();
                    var repcheckamt         = $('#repcheckamt').val();
                    var repaccountname      = $('#repaccountname').val();
                    var repaccountno        = $('#repaccountno').val();
                    //var repcheckamt         = $('#repcheckamt').val();
                    var repcheckdate           = $('#checkdate').val();
                    
                    //cash
                    var cashamt             = $('#cashamt').val();

                    if($('#checknum').val()==undefined)
                    {
                        return false;
                    }
                    
                    $('.updatetype').each(function(){
                        if($(this).is(':checked')) 
                        {  
                            updatetype = $(this).val();
                            hasUpdate = true;
                        }
                    });

                    if(!hasUpdate)
                    {
                        $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please select update type.</div>");
                        return false;
                    }

                    //update validation
                    if(updatetype=='redeposit')
                    {
                        if(redepcheckamount.trim()=='' || redepcheckamount=='0.00' || redepcheckamount=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Check Amount is not valid.</div>");
                            return false;
                        }
                    }
                    
                    if(updatetype=='replacement')
                    {
                        if(repcheckno.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Check No.</div>");
                            return false;
                        }

                        if(repaccountname.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Account Name.</div>");
                            return false;
                        }


                        if(repaccountno.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input Check No.</div>");
                            return false;
                        }

                        if(repcheckamt.trim()=='' || repcheckamt=='0.00' || repcheckamt=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Check Amount is not valid.</div>");
                            return false;
                        }

                        if(repcheckdate.trim()== '')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Please input valid Check Date.</div>");
                            return false;
                        }
                    }     

                    if(updatetype=='cash')
                    {
                        if(cashamt.trim()=='' || cashamt=='0.00' || cashamt=='0')
                        {
                            $('.response-dialog').html("<div class='alert alert-danger mb-0'>Amount is not valid.</div>");
                            return false;
                        }
                    }      
                    
                    dialogItself.enableButtons(false);
                    dialogItself.setClosable(false);    

                    BootstrapDialog.show({
                        title: 'Confirmation',
                        message: 'Update Bounced Check?',
                        closable: true,
                        closeByBackdrop: false,
                        closeByKeyboard: true,
                        onshow: function(dialog) {
                            // dialog.getButton('button-c').disable();
                        },
                        buttons: [{
                            icon: 'glyphicon glyphicon-ok-sign',
                            label: 'Ok',
                            cssClass: 'btn-primary',
                            hotkey: 13,
                            action:function(dialogItself1){
                                $('.response-dialog').html('');
                                if(state=='false' && updatetype =='redeposit')
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','#808000');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#808000');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced2').css('color','#808000');
                                }
                                else if(state=='false' && updatetype =='replacement')
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','#0000CD');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#0000CD');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced2').css('color','#0000CD');
                                }
                                else
                                {
                                    $('#tagbouncecid-'+id).val(true);
                                    $('#tagbouncecid-'+id).parent().closest('tr').css('color','green');
                                    $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                                    $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','green');
                                    $('#tagbouncecid-'+id).closest('td').find('#updatebounced2').css('color','green');
                                }

                                $.ajax({
                                    url:formURL,
                                    data:formDATA,
                                    type:'POST',
                                    success:function(data)
                                    {    
                                        console.log(data);                                                               
                                        if(data.status)
                                        {                    
                                            // tagged check                        
                                            //alert($this);   
                                            dialogItself.close();
                                            dialogItself1.close();
                                            //alert("xx");
                                            //$(this).closest('div.action-check').html("xxx");
                                            //BootstrapDialog.closeAll();      
                                            var dialog = new BootstrapDialog({
                                            message: function(dialogRef){
                                            var $message = $('<div>Check # '+checkno+' successfully updated as '+ updatetype +'.</div>');                 
                                                return $message;
                                            },
                                            closable: false
                                            });
                                            dialog.realize();
                                            dialog.getModalHeader().hide();
                                            dialog.getModalFooter().hide();
                                            dialog.getModalBody().css('background-color', '#0088cc');
                                            dialog.getModalBody().css('color', '#fff');
                                            dialog.open();   
                                            setTimeout(function(){
                                                dialog.close();
                                            },1800)                                                                   
                                        }   
                                        else 
                                        {
                                            $('.response-dialog').html('<div class="alert alert-danger">'+data.error+'</div>');
                                            dialogItself1.close();
                                            dialogItself.enableButtons(true);
                                            dialogItself.setClosable(true);
                                        }                              
                                    }
                                });                                                    
                            }
                        },{
                            icon: 'glyphicon glyphicon-remove-sign',
                            label: 'Cancel',
                            action: function(dialogItself1){
                                dialogItself1.close();
                                dialogItself.enableButtons(true);
                                dialogItself.setClosable(true);    
                            }
                        }]
                    });                     

                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

        return false;
    }); 

    // $('#ssearch').blur(function(){
    //     setTimeout(function(){
    //         $('span.smanlist').hide();
    //     },100)        
    // });
    $('input#depsearch').keyup(function(){
        var value = $(this).val().toLowerCase();
        if(value.trim()!="")
        {
            filterdeposit(value)
            $('span#filterclear').removeClass('glyphicon-filter').addClass('glyphicon-remove');
        }
        else 
        {
            $('span#filterclear').removeClass('glyphicon-remove').addClass('glyphicon-filter');
        }
    });

    $('span#filterclear').click(function(){
        var value = "";
        $('input#depsearch').val("");
        filterdeposit(value);
        $('span#filterclear').removeClass('glyphicon-remove').addClass('glyphicon-filter');
    });

    $("#cboxdeposit").click(function () {
        
        if($(this).is(':checked'))
        {            
            $('#viewcheck,#tagasbounce').css('color','#337ab7');
            $('table.paleBlueRows tbody tr').css('color','#333');
            $('input.checkboxItemsDep').prop('checked', true);
            $('input[id^="tagbouncecid-"]').val(false);
        }
        else 
        {
            $('.checkboxItemsDep').each(function(){
                $(this).attr('checked', false);                
            });
        }
        calcAmountsBounceCleared();
            
    });    

    $('.checkboxItemsDep').click(function(){

        var checked = ($(this).is(':checked'));
        if(checked)
        {
            $(this).attr('checked', true);
        }
        else
        {
            $(this).attr('checked', false);
        }        

        var id = $(this).closest('div.action-check').attr('data-id');
        if($(this).not(':checked'))
        {            
            $('#tagbouncecid-'+id).val(false);
            $('#tagbouncecid-'+id).parent().closest('tr').css('color','#333');
            $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#337ab7');
            $('#tagbouncecid-'+id).closest('td').find('#tagasbounce').css('color','#337ab7');
            // $('#viewcheck,#tagcheckas').css('color','#337ab7');
            // $('table.paleBlueRows tbody tr').css('color','#000');
            // $('input.checkboxItems').prop('checked', true);
            // $('^tagbouncecid-').value(false);
        }

        calcAmountsBounceCleared();

    });

    $('span#tagasbounce').click(function(){
        var id = $(this).closest('div.action-check').attr('data-id');
        var state = $('#tagbouncecid-'+id).val();
        
        if(id.trim()=='')
        {
            return false;
        }
        BootstrapDialog.show({
            title: '<span class="glyphicon glyphicon-warning-sign"></span> Confirmation',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },300);
              return $message;
            },
            data: {
                'pageToLoad': 'tagasdialog/'+id+'/'+state,
            },
            cssClass: 'tagasdialog',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-ok-sign',
                label: 'Ok',
                cssClass: 'btn-success',
                hotkey: 13,
                action:function(dialogItself){   
                    $('.response-dialog').html('');
                    if(state=='false')
                    {
                        $('#tagbouncecid-'+id).val(true);
                        $('#tagbouncecid-'+id).parent().closest('tr').css('color','#cc0000');
                        $('#tagbouncecid-'+id).closest('td').find('[type=checkbox]').prop('checked', false);
                        $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#cc0000');
                        $('#tagbouncecid-'+id).closest('td').find('#tagasbounce').css('color','#cc0000');
                    }
                    else 
                    {
                        $('#tagbouncecid-'+id).val(false);
                        $('#tagbouncecid-'+id).parent().closest('tr').css('color','#333');
                        $('#tagbouncecid-'+id).closest('td').find('#viewcheck').css('color','#337ab7');
                        $('#tagbouncecid-'+id).closest('td').find('#tagasbounce').css('color','#337ab7');

                    }

                    calcAmountsBounceCleared();
                    dialogItself.close();
                    return false;
                }
            }, {
              icon: 'glyphicon glyphicon-remove-sign',
                label: 'Cancel',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        // $(this).parent().closest('tr').css('color','#cc0000');
        // $(this).closest('td').find('[type=checkbox]').prop('checked', false);
        return false;
    });

    $('form#_pdctagging').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        var formURL = $(this).attr('');

        var idsArr = [];  
        var bArr = [];

        $(".checkboxItems:checked").each(function() {  
            idsArr.push($(this).val());
        });         

        $("input.taggeditems").each(function(){
            var $this = $(this);
            if($this.val()=='true')
            {
                var bid = $this.attr('id');
                res = bid.split("-");
                bArr.push(res[1]);
            }
        });
       
        if(idsArr.length > 0 || bArr > 0)
        {
            pdctagging('pdctagging',idsArr,bArr);     
            return false;
        }

        swal({
            title: "Error",
            type: "warning",
            text: "Please tag bounce or at least check one checkbox.",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        })
        //pdctagging('pdctagging',idsArr,bArr);     
        
    });

    $('#clearcheck').click(function(e){
        e.preventDefault();

        $('#clearcheck').prop("disabled",true);
        var idsArr = [];  
        var bArr = [];

        $(".checkboxItemsDep:checked").each(function() {  
            idsArr.push($(this).val());
        });         

        $("input.taggeditems").each(function(){
            var $this = $(this);
            if($this.val()=='true')
            {
                var bid = $this.attr('id');
                res = bid.split("-");
                bArr.push(res[1]);
            }
        });

        if(idsArr.length > 0 || bArr.length > 0)
        {
            swal({
                title: "Are you sure?",
                text: "Save Check Tagging?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Please",
                closeOnConfirm: true,
                html: false
            }, function(response){
                if(response)
                {
                    checkClearing('checktagging',idsArr,bArr);    
                }
                else 
                {
                    $('#clearcheck').prop("disabled",false);
                }
            });
            // BootstrapDialog.show({
            //     title: 'Confirmation',
            //     message: 'Save Check Tagging?',
            //     closable: true,
            //     closeByBackdrop: false,
            //     closeByKeyboard: true,
            //     onshow: function(dialog) {
            //         // dialog.getButton('button-c').disable();
            //     },
            //     onhidden:function(dialog)
            //     {
            //         $('#savecheck').prop("disabled",false);
            //     },
            //     buttons: [{
            //         icon: 'glyphicon glyphicon-ok-sign',
            //         label: 'Ok',
            //         cssClass: 'btn-primary',
            //         hotkey: 13,
            //         action:function(dialogItself){
            //             // dialogItself.enableButtons(false);
            //             // dialogItself.setClosable(false);
            //             dialogItself.close();
            //             checkClearing('checktagging',idsArr,bArr);    
                                                                        
            //         }
            //     },{
            //         icon: 'glyphicon glyphicon-remove-sign',
            //         label: 'Cancel',
            //         action: function(dialogItself){
            //             dialogItself.close(); 
                        
            //         }
            //     }]
            // });  

            return false;
        }

        swal({
            title: "Error",
            type: "warning",
            text: "Please tag bounced or at least check one checkbox.",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        })
        $('#clearcheck').prop("disabled",false);
        return false;
    });    
    
    //Begin Due Check Clearing

    $('#clearcheck2').click(function(e){
        e.preventDefault();

        $('#clearcheck2').prop("disabled",true);
        var idsArr = [];  
        var bArr = [];

        $(".checkboxItems:checked").each(function() {  
            idsArr.push($(this).val());
        });         

        $("input.taggeditems").each(function(){
            var $this = $(this);
            if($this.val()=='true')
            {
                var bid = $this.attr('id');
                res = bid.split("-");
                bArr.push(res[1]);
            }
        });

        if(idsArr.length > 0 || bArr.length > 0)
        {
            swal({
                title: "Are you sure?",
                text: "Save Check Tagging?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Please",
                closeOnConfirm: true,
                html: false
            }, function(response){
                if(response)
                {
                    checkClearing2('checktagging2',idsArr,bArr);    
                }
                else 
                {
                    $('#clearcheck2').prop("disabled",false);
                }
            });
            // BootstrapDialog.show({
            //     title: 'Confirmation',
            //     message: 'Save Check Tagging?',
            //     closable: true,
            //     closeByBackdrop: false,
            //     closeByKeyboard: true,
            //     onshow: function(dialog) {
            //         // dialog.getButton('button-c').disable();
            //     },
            //     onhidden:function(dialog)
            //     {
            //         $('#savecheck').prop("disabled",false);
            //     },
            //     buttons: [{
            //         icon: 'glyphicon glyphicon-ok-sign',
            //         label: 'Ok',
            //         cssClass: 'btn-primary',
            //         hotkey: 13,
            //         action:function(dialogItself){
            //             // dialogItself.enableButtons(false);
            //             // dialogItself.setClosable(false);
            //             dialogItself.close();
            //             checkClearing2('checktagging2',idsArr,bArr);    
                                                                        
            //         }
            //     },{
            //         icon: 'glyphicon glyphicon-remove-sign',
            //         label: 'Cancel',
            //         action: function(dialogItself){
            //             dialogItself.close(); 
                        
            //         }
            //     }]
            // });  

            return false;
        }

        swal({
            title: "Error",
            type: "warning",
            text: "Please tag bounced or at least check one checkbox.",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        })
        $('#clearcheck2').prop("disabled",false);
        return false;
    });  

    // End Due Check Clearing

    $('#clearcheck3').click(function(e){
        e.preventDefault();

        $('#clearcheck3').prop("disabled",true);  
        var bArr = [];        

        $("input.taggeditems").each(function(){
            var $this = $(this);
            if($this.val()=='true')
            {
                var bid = $this.attr('id');
                res = bid.split("-");
                bArr.push(res[1]);
            }
        });

        if(bArr.length > 0)
        {
            swal({
                title: "Are you sure?",
                text: "Update tagged checks?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Please",
                closeOnConfirm: true,
                html: false
            }, function(response){
                if(response)
                {
                    saveBouncedCheck('checktagging3',bArr);    
                }
                else 
                {
                    $('#clearcheck3').prop("disabled",false);
                }
            });
            return false;
        }

        swal({
            title: "Error",
            type: "warning",
            text: "Please tag at least one check update.",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        })
        $('#clearcheck3').prop("disabled",false);
        return false;
    });

    $("body").on('click', "input.upbox", function(){
        var type = $(this).val();
        $('.redepositdiv, .replacementdiv, .cashdiv').addClass('divhide');

        if(type=='redeposit')
        {
            $('.redepositdiv').removeClass('divhide');
        }
        else if(type=='replacement')
        {
            $('.replacementdiv').removeClass('divhide');
        }
        else if(type=='cash')
        {
            $('.cashdiv').removeClass('divhide');
        }
    });

    //last

});

function showTaggedCheck(checkid,type)
{
        var id = checkid;
        var type = type;

        if(type == 'redeposit'){
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Check Update Details via Redeposit',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckupdateRedeposit/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();

                    dialogItself.close();

                }
            }]
        })
        }else if(type == 'replacement'){
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Check Update Details via Replacement',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckupdateReplacement/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();

                    dialogItself.close();

                }
            }]
        })
        }
        else
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> View Check Update Details via Cash',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'viewcheckupdateCash/'+id,
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();

                    dialogItself.close();

                }
            }]
        });
        return false;
}

function saveBouncedCheck(bArr)
{
            BootstrapDialog.show({
            title: '<i class="fa fa-user" aria-hidden="true"></i> Saving Bounced Checks',
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: true,
            message: function(dialog) {
                var $message = $("<div><img src='"+siteurl+"/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
                var pageToLoad = dialog.getData('pageToLoad');
                setTimeout(function(){
                    $message.load(pageToLoad); 
                },1000);
              return $message;
            },
            data: {
                'pageToLoad': 'savebouncedcheck',
            },
            cssClass: 'addnewuser',
            onshow: function(dialog) {
                // dialog.getButton('button-c').disable();
            },
            onhidden: function(dialogRef){

            },
            buttons: [{
                icon: 'glyphicon glyphicon-remove-sign',
                label: 'Close',
                action: function(dialogItself){   
                    $('.response-dialog').html('');
                    var $button = this;
                    $button.disable();

                    dialogItself.close();

                }
            }]
        })
        return false;
}

function checkClearing(url,ids,bids)
{
    var remarks = $('form#_pdctagclearing input#remarks').val();

    formData = 'ids='+ids+'&bids='+bids+'&remarks='+remarks;

    $.ajax({
        url: url,
        data: formData,
        type: 'POST',
        dataType: 'text',        
        cache: false,
        //contentType: false,
        processData: false,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                //dialogItself.close();
                //console.log(output);
                $('#loaderdiv').html("");
                $('#processing-modal').modal('show');
                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                               
                            
                            //var result2 = JSON.parse( new_response );
                            //console.log(output.responseText);

                            output.previous_text = output.responseText;

                            //$( ".panel-body" ).append( "<p>"+result.message+"</p>" );
                            if(result.status=='Clearing')
                            {
                                $('#loaderdiv').html("<div class='loading-con'>"+result.message+"</div>");
                            }

                            if(result.status=='complete')
                            {
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "Success",
                                    text: "Tagging successfully saved.",
                                    type: "success",
                                    //confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false,
                                    html: false
                                  }, function(){
                                    window.location.reload();
                                  });
                            }

                            console.log(result);


                        }
                    }catch(e){
                        //console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}

// Begin Due Check Clearing

function checkClearing2(url,ids,bids)
{

    formData = 'ids='+ids+'&bids='+bids;

    $.ajax({
        url: url,
        data: formData,
        type: 'POST',
        dataType: 'text',        
        cache: false,
        //contentType: false,
        processData: false,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                //dialogItself.close();
                //console.log(output);
                $('#loaderdiv').html("");
                $('#processing-modal').modal('show');
                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                               
                            
                            //var result2 = JSON.parse( new_response );
                            //console.log(output.responseText);

                            output.previous_text = output.responseText;

                            //$( ".panel-body" ).append( "<p>"+result.message+"</p>" );
                            if(result.status=='Clearing')
                            {
                                $('#loaderdiv').html("<div class='loading-con'>"+result.message+"</div>");
                            }

                            if(result.status=='complete')
                            {
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "Success",
                                    text: "Tagging successfully saved.",
                                    type: "success",
                                    //confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false,
                                    html: false
                                  }, function(){
                                    window.location.reload();
                                  });
                            }

                            console.log(result);


                        }
                    }catch(e){
                        //console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}

// End Due Check Clearing
function checkClearing3(url,bids)
{
    var remarks = $('form#_pdctagclearing input#remarks').val();

    formData = 'bids='+bids+'&remarks='+remarks;

    $.ajax({
        url: url,
        data: formData,
        type: 'POST',
        dataType: 'text',        
        cache: false,
        //contentType: false,
        processData: false,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                //dialogItself.close();
                //console.log(output);
                $('#loaderdiv').html("");
                $('#processing-modal').modal('show');
                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                               
                            
                            //var result2 = JSON.parse( new_response );
                            //console.log(output.responseText);

                            output.previous_text = output.responseText;

                            //$( ".panel-body" ).append( "<p>"+result.message+"</p>" );
                            if(result.status=='Clearing')
                            {
                                $('#loaderdiv').html("<div class='loading-con'>"+result.message+"</div>");
                            }

                            if(result.status=='complete')
                            {
                                $('#processing-modal').modal('hide');
                                swal({
                                    title: "Success",
                                    text: "Tagged bounced checks successfully cleared.",
                                    type: "success",
                                    //confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false,
                                    html: false
                                  }, function(){
                                    window.location.reload();
                                  });
                            }

                            console.log(result);


                        }
                    }catch(e){
                        //console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}


function calcAmountsBounceCleared()
{
    var totalCleared = 0;
    $('.checkboxItemsDep').each(function(){
        var $this = $(this);
        
        var checked = ($this.is(':checked'));
        if(checked)
        {
            var amt = $this.closest('div.action-check').attr('data-amount');
            
            totalCleared = parseFloat(totalCleared) + parseFloat(amt);
        }    
    });    
    $('span.totspan').text(addCommas(totalCleared.toFixed(2)));   
    
    var totalBounced = 0;

    $('input.taggeditems').each(function(){
        var $this = $(this);

        if($this.val()=="true")
        {
            var amtb = $this.closest('div.action-check').attr('data-amount');
            console.log(amtb);
            totalBounced = parseFloat(totalBounced) + parseFloat(amtb);
        }
        
    });
    $('span.totspanbounce').text(addCommas(totalBounced.toFixed(2)));   
}

function filterdeposit(value)
{
    $("#deptbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
}

function stream(url)
{
    $.ajax({
        url: url,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        async:true,
        success: function (success) {
            //console.log(success);

            //var success = JSON.parse(success);

        },
        error: function (error) {
            //console.log(error);
        },
        complete: function (complete) {
            //console.log(complete);
        },
        beforeSend: function (jqXHR, settings) {

            // var self = this;
            var xhr = settings.xhr;
            
            settings.xhr = function () {
                var output = xhr();
                output.previous_text = '';
                //dialogItself.close();
                $('.panel-body').text("Connecting to server...");
                //console.log(output);
                output.onreadystatechange = function () {
                    try{
                        //console.log(output.readyState);
                        if (output.readyState == 3) {
                            
                            var new_response = output.responseText.substring(output.previous_text.length);

                            var result = JSON.parse( output.responseText.match(/{[^}]+}$/gi) );                               
                            
                            //var result2 = JSON.parse( new_response );
                            console.log(output.responseText);

                            if(result.status=='checking')
                            {                                
                                //$('h4.loading').html(result.message);
                                $( ".panel-body" ).append( "<p>"+result.message+"</p>" );
                            }
                            
                            if(result.status=='counting')
                            {                                
                                //$('h4.loading').html(result.message);
                                $( ".panel-body" ).append( "<p>No. of check to import: "+result.message+
                                "</p><p class='atpimport'></p><p class='updatestatus'></p>" );
                            }

                            if(result.status=='saving')
                            {                                
                                //$('h4.loading').html(result.message);
                                $( "p.atpimport" ).text( result.message );
                            }

                            if(result.status=='noupdate')
                            {
                                $('.panel-body').text(result.message);
                                // $('#processing-modal').modal('hide');
                                // swal({
                                //     title: "EOD Failed",
                                //     type: "warning",
                                //     text: result.message,
                                //     showCancelButton: false,
                                //     confirmButtonColor: "#DD6B55",
                                //     confirmButtonText: "OK",
                                //     closeOnConfirm: true
                                // })
                                $( "p.updatestatus" ).text( result.message );
                                setTimeout(function(){
                                    $('button#updatedbatp').html('<span class="fa fa-refresh"></span> Update');
                                },800)
                            }     

                            if(result.status=='error')
                            {
                                $('.panel-body').text(result.message);
                                // $('#processing-modal').modal('hide');
                                // swal({
                                //     title: "EOD Failed",
                                //     type: "warning",
                                //     text: result.message,
                                //     showCancelButton: false,
                                //     confirmButtonColor: "#DD6B55",
                                //     confirmButtonText: "OK",
                                //     closeOnConfirm: true
                                // })
                            }  


                            if(result.status=='complete')
                            {
                                // $('#processing-modal').modal('hide');
                                // swal({
                                //     title: "Complete",
                                //     type: "success",
                                //     text: result.message,
                                //     showCancelButton: false,
                                //     confirmButtonColor: "#DD6B55",
                                //     confirmButtonText: "OK",
                                //     closeOnConfirm: true
                                // },function(){
                                //     location.reload();
                                // })
                                $( "p.updatestatus" ).text( result.message );
                                setTimeout(function(){
                                    $('button#updatedbatp').html('Data Imported');
                                },800)
                            }
                            
                            if(result.status=='looping')    
                            {
                                $('h4.loading').html(result.progress);
                            }                            

                            output.previous_text = output.responseText;

                            //console.log(new_response);
                        }
                    }catch(e){
                        console.log("[XHR STATECHANGE] Exception: " + e);
                    }
                };
                return output;
            }
        }
    });
}

function getArticles(url,data) {
    $.ajax({
        url : url,
        data 
    }).done(function (data) {
        $('._loadchecks').html(data);  
    }).fail(function () {
        alert('Articles could not be loaded.');
    });
}

function printErrorMsg(msg) {
    $(".response-dialog").html("<div class='alert alert-danger alert-danger-dialog'><ul></ul></div>");
    $(".response-dialog").css('display','block');
    $.each( msg, function( key, value ) {
        $(".response-dialog").find("ul").append('<li>'+value+'</li>');
    });
}

function addNewSalesman()
{
    BootstrapDialog.show({
        title: 'Add New Customer',
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: true,
        message: function(dialog) {
            var $message = $("<div><img src='"+siteurl+"assets/img/ajax.gif'><small class='text-danger'>please wait...</small></div>");
            var pageToLoad = dialog.getData('pageToLoad');
            setTimeout(function(){
                $message.load(pageToLoad); 
            },1000);
          return $message;
        },
        data: {
            'pageToLoad': siteurl+'customer/addNewCustomerDialog',
        },
        cssClass: 'changeaccountpass',
        onshow: function(dialog) {
            // dialog.getButton('button-c').disable();
        },
        onhidden: function(dialogRef){
            $('#numOnly').focus();
            flag=0;
        },
        buttons: [{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Add',
            cssClass: 'btn-success',
            hotkey: 13,
            action:function(dialogItself){   
                $('.response-dialog').html('');
                var $button = this;
                $button.disable();
                var postData = $('#_addcustomer').serialize(), formURL = $('#_addcustomer').attr("action");
                var fname = $('form#_addcustomer input#fnamedialog').val(), lname = $('form#_addcustomer input#lnamedialog').val();

                if(fname.trim()=='' || lname.trim()=='')
                {
                    $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">Please input required fields.</div>');
                    $button.enable();
                    return false;
                }

                BootstrapDialog.show({
                    title: 'Confirmation',
                    message: 'Add New Customer?',
                    closable: true,
                    closeByBackdrop: false,
                    closeByKeyboard: true,
                    onshow: function(dialog) {
                        // dialog.getButton('button-c').disable();
                    },
                    buttons: [{
                        icon: 'glyphicon glyphicon-ok-sign',
                        label: 'Ok',
                        cssClass: 'btn-primary',
                        hotkey: 13,
                        action:function(dialogItself){
                            var $button1 = this;
                            $button1.disable();
                            $.ajax({
                                url:formURL,
                                data:postData,
                                type:'POST',
                                success:function(data)
                                {
                                    console.log(data);
                                    var data = JSON.parse(data);
                                    if(data['st'])
                                    {
                                        BootstrapDialog.closeAll();
                                        var dialog = new BootstrapDialog({
                                        message: function(dialogRef){
                                        var $message = $('<div>Customer Successfully Added.</div>');                 
                                            return $message;
                                        },
                                        closable: false
                                        });
                                        dialog.realize();
                                        dialog.getModalHeader().hide();
                                        dialog.getModalFooter().hide();
                                        dialog.getModalBody().css('background-color', '#0088cc');
                                        dialog.getModalBody().css('color', '#fff');
                                        dialog.open();

                                        $('#cid').val(data['cid']);

                                        $('#fname').val(data['fname']);
                                        $('#lname').val(data['lname']);
                                        $('#mname').val(data['mname']);
                                        $('#next').val(data['next']);

                                        $('#_vercussearch').val(data['fullname']);

                                        setTimeout(function(){
                                            $('#gcbarcodever').focus();
                                            dialog.close(); 
                                        }, 1500);
                                    }
                                    else 
                                    {
                                        $('.response-dialog').html('<div class="alert alert-danger alert-danger-dialog">'+data['msg']+'</div>');
                                        $('form#_addcustomer input#fname').focus();
                                        dialogItself.close();
                                        $button.enable();   
                                        return false;
                                    }
                                }
                            });                                                    
                        }
                    },{
                        icon: 'glyphicon glyphicon-remove-sign',
                        label: 'Cancel',
                        action: function(dialogItself){
                            dialogItself.close();
                            $button.enable();  
                        }
                    }]
                }); 

                return false;

            }
        }, {
          icon: 'glyphicon glyphicon-remove-sign',
            label: 'Cancel',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    });
}

function isValidDate(date) {
    var temp = date.split('/');
    var d = new Date(temp[2] + '/' + temp[0] + '/' + temp[1]);
    return (d && (d.getMonth() + 1) == temp[0] && d.getDate() == Number(temp[1]) && d.getFullYear() == Number(temp[2]));
}

function checkType(date)
{
    var todaysDate = new Date();
    var inputDate = new Date(date);

    if(inputDate.setHours(0,0,0,0) <= todaysDate.setHours(0,0,0,0))
    {
        $('input#checktype').val("DATED CHECK");
    }
    else 
    {
        $('input#checktype').val("POST DATED");
    }
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

//function

