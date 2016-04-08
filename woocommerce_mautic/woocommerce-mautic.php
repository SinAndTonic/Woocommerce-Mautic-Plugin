<?php
/*
Plugin Name: Woocommerce Mautic
Version: 1.1.0
Plugin URI: https://github.com/dontbetriangle/Woocommerce-Mautic-Plugin/
Description: Sends Woocommerce Customers details to a Mautic form
Author: Richard Legg
Author URI: https://github.com/dontbetriangle/Woocommerce-Mautic-Plugin/
*/

//admin menus and stuff like that

add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
add_options_page('WC Mautic', 'WC Mautic', 'manage_options', 'wc-mautic', 'plugin_options_page');
}

function plugin_options_page() {
?>
<div>
<h2>WC Mautic Settings</h2>

<form action="options.php" method="post">
<?php settings_fields('plugin_options'); ?>
<?php do_settings_sections('plugin'); ?>
 
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>
 
<?php
}

add_action('admin_init', 'plugin_admin_init');


function plugin_admin_init(){
register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate');
add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
add_settings_field('plugin_text_string', 'Mautic URL', 'plugin_setting_string', 'plugin', 'plugin_main');
add_settings_field('plugin_mautic_id', 'Mautic ID', 'plugin_setting_string2', 'plugin', 'plugin_main');

}

function plugin_section_text() {
echo '<p>All of the settings to allow WC to connect to Mautic forms.</p>';
}

function plugin_setting_string() {
$options = get_option('plugin_options');
echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='". ( isset( $options['text_string'] ) ? esc_attr( $options['text_string'] ) : '' ) . "' />";
}

function plugin_setting_string2() {
$options = get_option('plugin_options');
echo "<input id='plugin_text_string' name='plugin_options[mautic_id]' size='40' type='text' value='". ( isset( $options['mautic_id'] ) ? esc_attr( $options['mautic_id'] ) : '' ) . "' />";
}

function plugin_options_validate($input) {
$newinput['text_string'] = trim($input['text_string']);

$newinput['mautic_id'] = trim($input['mautic_id']);


if(!preg_match('/^(https?:\/\/)/', $newinput['text_string'])) {
$newinput['text_string'] = 'http://' . $newinput['text_string'];

}

$newinput['text_string'] = rtrim($newinput['text_string'], '/');
return $newinput;
}






// hook into woocommerce update order because name etc is not available during checkout
add_action( 'woocommerce_checkout_update_order_meta','order_status_changed', 10, 3 );

//add_action( 'woocommerce_checkout_update_order_meta', 'order_status_changed' 1000, 1 );

function order_status_changed( $id, $status = 'new', $new_status = 'pending' ) {
        
            // // Get WC order
            $order = wc_get_order( $id );
            // $order = WC_Order( $id );

            // // subscribe
            subscribe( $order->id, $order->billing_first_name, $order->billing_last_name, $order->billing_email);
            }
        
    




function subscribe( $order_id, $first_name, $last_name, $email) {

    $stuff = array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $email);

    $currentOptions = get_option( 'plugin_options', false );

    pushMauticForm($stuff);

}

function pushMauticForm($data, $ip = null)
{
    // Get IP from $_SERVER
    if (!$ip) {
        $ipHolders = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        foreach ($ipHolders as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    // Multiple IPs are present so use the last IP which should be the most reliable IP that last connected to the proxy
                    $ips = explode(',', $ip);
                    array_walk($ips, create_function('&$val', '$val = trim($val);'));
                    $ip = end($ips);
                }
                $ip = trim($ip);
                break;
            }
        }
    }

    $currentOptions = get_option ( 'plugin_options', 'not working');
    $formId = $currentOptions['mautic_id'];
    $theUrl = $currentOptions['text_string'];

    $data['formId'] = $formId;
    // return has to be part of the form data array
    if (!isset($data['return'])) {
        $data['return'] = 'http://www.somewhere.com';
    }
    $data = array('mauticform' => $data);
    // $url = get_option( 'mautic_url', 'not working1' );
    
    // $theFormId = get_option( 'mautic_id', 'not working2');
    

    // Change [path-to-mautic] to URL where your Mautic is

    
    
    $formUrl = $theUrl . '/form/submit?formId=' . $formId;
   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $formUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Forwarded-For: $ip"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);


    return $response;     
}

?>