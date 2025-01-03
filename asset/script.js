jQuery("document").ready(function(){
    console.log(ajax_request);
    jQuery(".save-btn").on("click",function(e){
        e.preventDefault();
        var formdata = jQuery("#feedback-form").serialize();
        console.log(formdata);
        jQuery.ajax({
            url: ajax_request.admin_url, 
            method: 'POST',
            data: {
                action:"user_data",
                form:formdata
            },
            success: function(response) {
                alert(response);
                jQuery("#feedback-form").trigger('reset');
            },
            error: function(xhr, status, error) {
              console.error('Error submitting form:', error);
            }
          });
    })
})