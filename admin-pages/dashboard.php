<?php
if ( ! function_exists( 'ccvf_dashboard' ) ) {
    function ccvf_dashboard() {  ?>


        <div class="wrapper">
            <h1><?php esc_html_e('Dashboard', 'ccfv-plugin-textdomain'); ?></h1>
            
            <p>This a custom contact form plugin with captcha.</p>
            
            <p>
                Add the shortcode [ccvf_contact_form] for a contact form. 
                Add <strong>echo do_shortcode('[ccvf_contact_form]');</strong> within a PHP tag on the template
            </p>
            
            <a class="button-primary admin-btn" href="admin.php?page=mail-settings-page#one">Mail settings</a>
            <a class="button-primary admin-btn" href="admin.php?page=mail-settings-page#two">Form settings</a>
            <a class="button-primary admin-btn" href="admin.php?page=mail-settings-page#three">Captcha settings</a>
            
        </div>

        <style>
            .wrapper {
                background: #fff;
                border: 1px solid #e5e5e5;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                padding: 1px 20px 20px 20px;
                margin: 2rem 0;
            }
            p { font-size: 16px; }
            strong { font-weight: 700; }
        </style>

    <?php
    }
}