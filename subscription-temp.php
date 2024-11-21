<?php /* Template Name: Subscription Message */ ?>

<?php while(have_posts()) : the_post();

    if(isset($_GET['subscriber']) && !empty($_GET['subscriber'])){
        global $wpdb;
        $encrypted_email = strrev($_GET['subscriber']);
        $cipher = 'AES-128-CBC';
        $key = 'vmt_viacon_subscription';
        $email = openssl_decrypt(base64_decode($encrypted_email), $cipher, $key, 0, substr(hash('sha256', $key), 0, openssl_cipher_iv_length($cipher)));
        $table_name = $wpdb->prefix . 'vmt_subscription';
        $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE email_id='$email'" );
        if(is_array($results) && count($results)>0){
            if(isset($_GET['status']) && !empty($_GET['status'])){
                $status = $_GET['status'];
                $wpdb->query("UPDATE $table_name SET `status`='$status' WHERE email_id='$email'");
                
                $span_class = get_option('vmt_subscribe_unsubscribe_class');
                $span_msg = get_option('vmt_subscribe_unsubscribe');
                echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
                    echo (!empty($span_msg))?$span_msg:'Sorry To See You Go';
                echo '</span>';
            }else{
                $wpdb->query("UPDATE $table_name SET `status`='subscribed' WHERE email_id='$email'");
                
                $span_class = get_option('vmt_subscribe_successful_class');
                $span_msg = get_option('vmt_subscribe_successful');
                echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
                    echo (!empty($span_msg))?$span_msg:'Successfully Confirmed.';
                echo '</span>';
            }
        }else{
            $span_class = get_option('vmt_subscribe_error_class');
            $span_msg = get_option('vmt_subscribe_error');
            echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
                echo (!empty($span_msg))?$span_msg:'It looks like some error has occurred.';
            echo '</span>';
        }

    }else{
        $encrypted_email = '';
        header("Location:".site_url());
    }

endwhile; ?>