<?php
if ( ! function_exists( 'ccvf_mail_test_page_contents' ) ) {
    function ccvf_mail_test_page_contents() { ?>

        <h1>
            <?php esc_html_e('Test Mails', 'ccfv-plugin-textdomain'); ?>
        </h1>

        <form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
            <input type="email" name="test_mail" placeholder="Enter an email" class="regular-text"/>
            <input type="submit" value="Submit" class="button button-primary"/>
        </form>

        <style>
            form {
                border: 1px solid #dadadf;
                background: #fff;
                padding: 20px 30px;
            }
            input {
                width: 100%;
            }
            input[type="submit"] {
                display: block;
                width: 10rem;
                margin-top: 1rem;
            }
        </style>

        <?php
        $test_email = $_GET['test_mail'];
        $site_title = get_bloginfo( 'name' );

        if(!$test_email) {
            $message = 'Enter an email!!';
            
            // echo '<span style="color:red;">'.$message.'</span>';
        } else {
        
            $body = '<table class="mail-table">
                        <tr>
                            <h4 style="border-bottom: 2px solid #ccc; padding-bottom: 10px; width: 50%;">
                            Test Mail</h4>
                        </tr>                    
                        <tr>
                            This is a test mail send from '.$site_title.'.
                        </tr>
                        <tr>
                            <td>Send by: '. $test_email .'</td>
                        </tr>
                    </table>';
            $headers = array('Content-Type: text/html; charset=UTF-8', 'Reply-To: Viacon <' . $test_email. '>');
            $status = wp_mail( $test_email, 'Email Testing' , $body, $headers );
        
        }
        
    }
}