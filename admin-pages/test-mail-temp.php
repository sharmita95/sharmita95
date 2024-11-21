<?php
if ( ! function_exists( 'ccvf_mail_test_page_contents' ) ) {
    function ccvf_mail_test_page_contents() { ?>
    
    <div class="wrapper">

        <h1 class="wp-heading-inline"><?php esc_html_e('Send a Test', 'ccfv-plugin-textdomain'); ?></h1>

        <form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
            <input type="hidden" name="page" value="mail-test-page">
            <input type="email" name="test_mail" placeholder="Enter an email" class="regular-text"/>
            <input type="submit" value="Send Test Mail" class="button button-primary"/>
        </form>

        <style>
            input { width: 100%; display: block; margin: 20px 0; }
            input[type="submit"] {
                display: block;
                width: 10rem;
                margin-top: 1rem;
            }
        </style>

        <?php
        if (isset($_GET['test_mail'])) {

            $test_email = $_GET['test_mail'];
            $site_title = get_bloginfo( 'name' );

            if(!$test_email) {
                
                $message = '<span style="color:red;">Enter an email!!</span>';
                
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
                $status = wp_mail( $test_email, 'Testing Email of '.$site_title , $body, $headers );

                if($status == 1) {
                    $message = '<span style="color:green;">Mail has been sent. Check your inbox.</span>';
                } else {
                    $message = '<span style="color:red;">Try after sometime.</span>';
                }
            
            }

            if($message) {
                echo '<h3>'.$message.'</h3>';
                $mainURL = admin_url( 'admin.php?page=mail-test-page' );
                
                header('refresh:3; url="'.$mainURL.'"');
            }

        }
        
    ?>
    
    </div>
    
    <?php
    }
}