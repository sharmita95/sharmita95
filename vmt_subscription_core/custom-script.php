<?php function custom_script_menu_action(){ ?>
    <script>
        jQuery(document).ready(function($) {
            //=================================Delete Ajax=================================
            $(".subscriber-delete").click(function(){
                selected = $(this);
                var vmt_id = selected.data('id');
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_delete', vmt_id: vmt_id },
                    beforeSend: function(){
                    },
                    success: function(data) {
                        $('#'+vmt_id).fadeOut();
                        setTimeout(function () {
                            $('#'+vmt_id).remove();
                        }, 400);
                    }
                });
            });
            //=================================Edit Ajax=================================
            $(".subscriber-edit").click(function(){
                selected = $(this);
                if(selected.parent().parent().siblings('input').prop('disabled') == false){
                    var vmt_email = selected.parent().parent().siblings('input').val();
                    var vmt_id = selected.data('id');
                    jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: { action: 'vmt_edit', vmt_id: vmt_id, vmt_email: vmt_email },
                        beforeSend: function(){
                            selected.parent().parent().siblings('input').attr('disabled', 'disabled');
                            selected.fadeOut().fadeIn();
                            setTimeout(function () {
                                selected.text("Editing...");
                            }, 400);
                        },
                        success: function(data){
                            selected.parent().parent().siblings('input').fadeOut();
                            setTimeout(function () {
                                selected.parent().parent().siblings('span').fadeIn().text(vmt_email);
                            }, 400);
                            selected.fadeOut().fadeIn();
                            setTimeout(function () {
                                selected.text("Done").delay(2000).fadeOut().fadeIn();
                            }, 400);
                            setTimeout(function () {
                                selected.text("Edit");
                            }, 3200);
                        }
                    });
                }else{
                    selected.fadeOut().fadeIn();
                    setTimeout(function () {
                        selected.text("Save");
                    }, 400);
                    selected.parent().parent().siblings('span').fadeOut();
                    setTimeout(function () {
                        selected.parent().parent().siblings('input').removeAttr('disabled').fadeIn();
                    }, 400);
                }
            });
            //=================================Change Ajax=================================
            $("a.subscriber-change").click(function(){
                if($(this).parent().parent().siblings('select').prop('disabled') == true){
                    $(this).parent().parent().siblings('span').fadeOut();
                    $(this).parent().parent().siblings('select').removeAttr('disabled').delay(400).fadeIn();
                }
            });
            $('select.subscriber-change').on('change', function() {
                selected = $(this);
                var vmt_status = selected.val();
                var vmt_id = selected.data('id');
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_change', vmt_id: vmt_id, vmt_status: vmt_status },
                    beforeSend: function(){
                        setTimeout(function () {
                            selected.attr('disabled', 'disabled');
                        }, 200);
                    },
                    success: function(data) {
                        selected.fadeOut();
                        selected.siblings('span').text(selected.val()).delay(400).fadeIn();
                    }
                });
            });
            //=================================Resend Ajax=================================
            $(".resend-confirmation").click(function(){
                selected = $(this);
                var vmt_email = selected.parent().parent().siblings('input').val();
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_resend', vmt_email: vmt_email },
                    beforeSend: function(){
                        selected.fadeOut().fadeIn();
                        setTimeout(function () {
                            selected.text("Sending...").addClass('resend-confirmation').parent().removeClass('delete');
                        }, 400);
                    },
                    success: function(data) {
                        selected.fadeOut().fadeIn();
                        setTimeout(function () {
                            if(data == 0){
                                selected.text("Error(Try Again)").parent().addClass('delete');
                            }else{
                                selected.text("Confirmation Sent").removeClass('resend-confirmation');
                            }
                        }, 400);
                    }
                });
            });
            //=================================Export Ajax=================================
            $("#export").click(function(){
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_export' },
                    beforeSend: function(){
                    },
                    success: function(data) {
                    }
                });
            });
            //=============================Import Old Data Ajax============================
            $("#import_old").click(function(){
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_import_old' },
                    beforeSend: function(){
                    },
                    success: function(data) {
                        location.reload(true);
                    }
                });
            });
            //=============================Bulk Edit Ajax============================
            $("button.action").click(function(){
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: { action: 'vmt_import_old' },
                    beforeSend: function(){
                    },
                    success: function(data) {
                        location.reload(true);
                    }
                });
            });
        });					
    </script>
<?php } ?>