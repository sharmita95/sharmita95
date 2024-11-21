jQuery(document).ready(function ($) {
    
  console.log('TTP2');

    $(document).on('submit','#ccvf_contact_form', function (e) {
        e.preventDefault();
        var _data = $(this).serialize();
        $.post(Front.ajaxurl, _data, function (resp) {
            if(resp.flag == true) {
                $('.success-msg').show();
                $('.success-msg').append('<span class="smessage" style="color: green;">' + resp.msg + '</span>');
                $('#ccvf_contact_form').trigger("reset");
                setTimeout(function() {
                    $('span.smessage').remove();
                    $('.success-msg').hide();
                }, 6000);
            } else {
                $('.error-msg').show();
                $('.error-msg').append('<span class="emessage" style="color: red;">' + resp.msg + '</span>');
                setTimeout(function() {
                    $('span.emessage').remove();
                    $('.error-msg').hide();
                }, 6000);
            }
        }, 'json');

    }); 
  
  
  
  
  
    $(document).on('submit','#ccvf_contact_form_2', function (e) {
        e.preventDefault();
        
        // var data_2;
        var data_2 = $(this).serialize();
        
        // console.log(data_2);
        
        $.post(Front.ajaxurl, data_2, function (resp) {
            
            console.log(resp);
            
        }, 'json');
        
        /*
        $.ajax({
            type: "POST",
            url: "http://dreamandtravel.com/wp-content/plugins/custom-contact-form/form-temp.php",
            data: jQuery('#form2').serialize(),
            async:false,
            success: function(data) {
                console.log(data);
                if(data.nocaptcha==="true") {
                    data_2=1;
                } else if(data.spam==="true") {
                    data_2=1;
                } else {
                    data_2=0;
                }
            }
        });
        if(data_2!=0) {
            e.preventDefault();
            if(data_2==1) {
                alert("Check the captcha box");
            } else {
                alert("Please Don't spam");
            }
        } else {
            jQuery("#form2").submit
        }
        */
        
    });

});