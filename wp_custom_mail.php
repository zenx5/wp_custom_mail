<?php
/**
 * Plugin Name: Custom Mails
 * Plugin URI: https://kavavdigital.com
 * Description: 
 * Version: 6.7.0
 * Author: Octavio Martinez
 * Author URI: https://github.com/zenx5 
 * 
 */

 require 'vendor/autoload.php';
 // use PHPMailer\PHPMailer\PHPMailer;
 // use PHPMailer\PHPMailer\SMTP;
 // use PHPMailer\PHPMailer\Exception;

register_activation_hook(__FILE__, array('WP_custom_mail', 'wpcm__activation'));
register_deactivation_hook(__FILE__, array('WP_custom_mail', 'wpcm__deactivation'));
add_action('init', array('WP_custom_mail', 'init'));

class WP_custom_mail{
    public static function init( ) {
        add_action('admin_menu', array('WP_custom_mail', 'wpcm_admin_menu'));
        add_action('wp_ajax_wpcm_send_mail', array('WP_custom_mail', 'wpcm_send_mail'));
        add_action('wp_ajax_wpcm_update_settings_data', array('WP_custom_mail', 'wpcm_update_settings_data'));
    }

    public static function wpcm__activation(){

    }

    public static function wpcm__deactivation(){
        
    }

    public static function wpcm_admin_menu(){

        add_menu_page(
            "Mail",
            "Mail",
            "manage_options",
            "menu-mails",
            function(){
                $user = get_option('wpcm_user');
                $name = get_option('wpcm_name');
                $password = get_option('wpcm_password') ? 'nopermitidoverelpassword' : '';
                ?>
                    <div style="margin: 40px;">
                        <form id="wpcm_settings_form">
                            <div>
                                <label>Dirección de correo electrónico del remitente</label>
                                <input type="text" id="user" name="user" value="<?=$user;?>">
                            </div>
                            <div>
                                <label>Nombre del remitente</label>
                                <input type="text" id="name" name="name" value="<?=$name;?>">
                            </div>
                            <div>
                                <label>Contraseña</label>
                                <input type="password" id="password" name="password" value="<?=$password;?>">
                            </div>
                            <input type="button" name="btn" id="btn" value="enviar">
                        </form>
                        <style type="text/css">
                            #wpcm_settings_form input[type='text'],
                            #wpcm_settings_form input[type='password'],
                            #wpcm_settings_form input[type='email'],
                            #wpcm_settings_form input[type='text']
                            {
                                width: 350px;
                            }
                            #wpcm_settings_form div {
                                padding: 10px;
                                margin: 10px;
                            }
                            #wpcm_settings_form label {
                                width: 500px;
                            }
                        </style>
                        <script>
                            jQuery("#btn").click("click", function(){
                                jQuery.ajax({
                                    url: ajaxurl,
                                    method:'post',
                                    data:{
                                        action: 'wpcm_update_settings_data',
                                        user: jQuery('#user').val( ),
                                        name: jQuery('#name').val( ),
                                        password: jQuery('#password').val( )
                                    },
                                    success: function(response){
                                        console.log(response);
                                    }
                                });
                            })
                        </script>
                    </div>

                <?php
            },
            "",
            6
        );
    }

    public static function wpcm_update_settings_data( ) {
        $user = $_POST['user'];
        $name = $_POST['name'];
        $password = $_POST['password'];

        update_option('wpcm_user', $user );
        update_option('wpcm_name', $name );
        update_option('wpcm_password', $password );
    }

    public static function wpcm_send_mail( ){
        $user = get_option('wpcm_user');
        $name = get_option('wpcm_name');
        $password = get_option('wpcm_password') ? 'nopermitidoverelpassword' : '';

        $mail = new wp_custom_mail( );
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $user;                  //SMTP username
            $mail->Password   = $password;   //'fhhynumuxgzfksmj';       //vsgweaowshtdgxdh        //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom( $user, $name );
            $mail->addAddress('elmoises.reyderey@gmail.com', 'Moises Rodriguez');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            ob_start( );
            include WP_PLUGIN_DIR.'/wp_custom_mail/template/index.html';
            $body = ob_get_contents( );
            ob_end_clean( );
            echo $body;

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject 2';
            $mail->Body    = $body;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();

            echo 'Message has been sent correctamente';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        
        wp_die();
    }
}