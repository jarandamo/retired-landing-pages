
<?php
/**
 * Inbound Marketing Button in editor
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Inbound_Template_Utils {

    public function __construct() {
        self::init();
    }

    public function init() {
        /* add extra menu items */
        add_action('admin_menu', array( __CLASS__ , 'add_screen' ) );

        //if (isset($_GET['inbound-template-gen'])) {
            //add_action( 'admin_head', array(__CLASS__, 'html'));
        //}

    }

    static function add_screen() {
        add_submenu_page(
            'edit.php?post_type=landing-page',
            __( 'Generate Template' , 'leads' ),
            __( 'Generate Template' , 'leads' ),
            'manage_options',
            'template_utils',
            array( __CLASS__ , 'html' )
        );
    }

    static function get_json() {

        if (!function_exists('acf_get_field_group')) {
            echo 'You need ACF activated to use this screen';
            exit;
        }
        $keys = (isset($_GET['generate-template-id'])) ? array($_GET['generate-template-id']) : array();
        //print_r($keys);
        //exit;
        //$keys = $_GET['acf_export_keys'];
        //$keys = array('group_55e23ad63ecc3');
        //$keys = array('group_55d38b033048e');
        //$keys = array('group_55d26a506a990');

        // validate
        if( empty($keys) ) {

            return false;

        }


        // vars
        $json = array();


        // construct JSON
        foreach( $keys as $key ) {

            // load field group
            $field_group = acf_get_field_group( $key );


            // validate field group
            if( empty($field_group) ) {

                continue;

            }


            // load fields
            $field_group['fields'] = acf_get_fields( $field_group );


            // prepare fields
            $field_group['fields'] = acf_prepare_fields_for_export( $field_group['fields'] );


            // extract field group ID
            $id = acf_extract_var( $field_group, 'ID' );


            // add to json array
            $json[] = $field_group;

        }


        // return
        return $json;

    }
    /*
     There are two places the marketing button renders:
     in normal WP editors and via JS for ACF normal
     */

    static function html($args) {

        if (!function_exists('acf_get_field_groups')) {
            echo 'You need ACF activated to use this screen';
            exit;
        }
        /* Todo intercept and update the special key here */
        //print_r($json); exit;
        ?>
        <div class="wrap acf-settings-wrap">



            <h2><?php _e('Import / Export', 'acf'); ?></h2>

            <div class="acf-box">
                <div class="title">
                    <h3><?php _e('Generate Your Template Output', 'inboundnow'); ?></h3>
                </div>

                <div class="inner">
                <script type="text/javascript">
                function replaceUrlParam(url, paramName, paramValue){
                    var pattern = new RegExp('('+paramName+'=).*?(&|$)')
                    var newUrl=url
                    if(url.search(pattern)>=0){
                        newUrl = url.replace(pattern,'$1' + paramValue + '$2');
                    }
                    else{
                        newUrl = newUrl + (newUrl.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue
                    }
                    return newUrl
                }
                jQuery(document).ready(function($) {
                   // put all your jQuery goodness in here.
                    jQuery("#generate_template").on('change', function () {
                        var val = jQuery(this).val();
                        var newUrl = replaceUrlParam(window.location.href, 'generate-template-id', val);
                       window.location.href = newUrl;
                    });
                 });

                </script>
                <div id="options-available">
                    <?php
                    $choices = array('none' => "Choose template");
                    $field_groups_ids = acf_get_field_groups();

                    // populate choices
                    if( !empty($field_groups_ids) ) {
                        foreach( $field_groups_ids as $field_group ) {
                            //print_r($field_group);
                            $choices[ $field_group['key'] ] = $field_group['title'];
                        }
                    }
                    echo "<label>Select the ACF options you wish to generate markup for</label>";
                    // render field
                    $acf_id = (isset($_GET['generate-template-id'])) ? $_GET['generate-template-id'] : false;
                    acf_render_field(array(
                        'type'      => 'select',
                        'name'      => 'generate_template',
                        'prefix'    => false,
                        'value'     => $acf_id,
                        'toggle'    => true,
                        'choices'   => $choices,
                    ));

    /* get the data */
    $json = self::get_json();
    //print_r($json);

    // validate
    if( $json === false || empty($json)) {

        acf_add_admin_notice( __("No field groups selected", 'acf') , 'error');
        exit;

    }

    // vars
    $field_groups = $json;
                    ?>
                </div>
                <p>This page is for helping developing templating super simple.</p>

                <p>This is generated output from your landing page options to copy/paste into your index.php</p>



<textarea style="width:100%; height:500px;">
<?php echo "<?php
/**
* Template Name: Template Name
* @package  WordPress Landing Pages
* @author   Inbound Template Generator
*/\r\n
/* Declare Template Key */
\$key = lp_get_parent_directory(dirname(__FILE__));
\$path = LANDINGPAGES_UPLOADS_URLPATH .\"\$key/\";
\$url = plugins_url();
/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
do_action('wp_head');
\$post_id = get_the_ID(); ";?>
?>

<?php

echo '<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>  <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>  <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <!--  Define page title -->
    <title><?php wp_title(); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- include your assets -->
    <!-- <link rel="stylesheet" href="<?php echo $path; ?>css/css_file_name.css"> -->
    <!-- <script src="<?php echo $path; ?>js/js_file_name.js"></script> -->

    <!-- Load Normal WordPress wp_head() function -->
    <?php wp_head(); ?>

    <!-- Load Landing Pages\'s custom pre-load hook for 3rd party plugin integration -->
    <?php do_action("lp_head"); ?>
</head>'. "\r\n\r\n".
'<body>'. "\r\n\r\n";
 //print_r($field_groups); exit;
if(isset($field_groups)) {
echo "<?php ". "\r\n\r\n";
foreach( $field_groups as $field_group ) {


    foreach( $field_group['fields'] as $field ) {

        if($field['type'] === "repeater") {
        echo "/* Start ".$field['name']." Repeater Output */" ."\r\n";
        echo '<?php if ( have_rows( "'.$field['name'].'" ) )  { ?>'. "\r\n\r\n";
        echo '<?php while ( have_rows( "'.$field['name'].'" ) ) : the_row();' . "\r\n";
            $count = count($field['sub_fields']);
            foreach ($field['sub_fields'] as $subfield) {
echo "\t$".$subfield['name']. " = " . "get_sub_field(\"".$subfield['name']."\");"."\r\n";
            }
            echo '?>'."\r\n\r\n";
            echo '<!-- your markup here -->'."\r\n\r\n";
            echo '<?php endwhile; ?>'."\r\n\r\n";
            echo '<?php } /* end if have_rows */ ?>';
            echo "/* End ".$field['name']." Repeater Output */" ."\r\n\r\n";
        } else if($field['type'] === "flexible_content") {
            echo "/* Start ".$field['name']." Flexible Content Area Output */" ."\r\n";
            echo "if(function_exists('have_rows')) :" ."\r\n";
            echo "\tif(have_rows('".$field['name']."')) :" ."\r\n";
            echo "\t\t while(have_rows('".$field['name']."')) : the_row();" ."\r\n";
            echo "\t\t\t switch(get_row_layout()) :" ."\r\n";
            foreach ($field['layouts'] as $layout) {
                $layout['name'];
                echo "\t\t\t case '".$layout['name']."' : " ."\r\n";
                foreach ($layout['sub_fields'] as $layout_subfield) {
                echo "\t\t\t\t$".$layout_subfield['name']. " = " . "get_sub_field(\"".$layout_subfield['name']."\");"."\r\n";

                }
                echo "\t\t\t?>"."\r\n\r\n";
                echo "\t\t\t<!-- your markup here -->"."\r\n\r\n";
                echo "\t\t\t <?php break;" ."\r\n";
            }
            echo "\t\t\tendswitch; /* end switch statement */ "."\r\n";
            echo "\t\tendwhile; /* end while statement */"."\r\n";
            echo "\t endif; /* end have_rows */"."\r\n";
            echo "endif;  /* end function_exists */"."\r\n";
            echo "/* End ".$field['name']." Flexible Content Area Output */" ."\r\n\r\n";

        } else {
            if($field['name']) {
                echo "\t$".$field['name']. " = " . "get_field(\"".$field['name']."\", \$post_id);"."\r\n";
            }

        }
    }


}
echo "?>"."\r\n\r\n";

/* break; endwhile; endif; */
echo "<?php "."\r\n";
echo "do_action('lp_footer');"."\r\n";
echo "do_action('wp_footer');"."\r\n";
echo "?>"."\r\n";
echo "</body>"."\r\n";
echo "</html>"."\r\n";
}
?>
</textarea>
<p>This is the config.php file</p>

                    <?php /* TODO: add config begging output */ ?>
                    <textarea class="pre" readonly="true"><?php

                    echo "if( function_exists('acf_add_local_field_group') ):" . "\r\n" . "\r\n";

                    foreach( $field_groups as $field_group ) {

                        // code
                        $code = var_export($field_group, true);

                        // change double spaces to tabs
                        $code = str_replace("  ", "\t", $code);

                        // correctly formats "=> array("
                        $code = preg_replace('/([\t\r\n]+?)array/', 'array', $code);

                        // Remove number keys from array
                        $code = preg_replace('/[0-9]+ => array/', 'array', $code);

                        // echo
                        echo "acf_add_local_field_group({$code});" . "\r\n" . "\r\n";

                    }

                    echo "endif;";

                    ?></textarea>

                </div>

            </div>

        </div>
        <div class="acf-hidden">
            <style type="text/css">
                textarea.pre {
                    width: 100%;
                    padding: 15px;
                    font-size: 14px;
                    line-height: 1.5em;
                    resize: none;
                }
            </style>
            <script type="text/javascript">
            (function($){

                var i = 0;

                $(document).on('click', 'textarea.pre', function(){

                    if( i == 0 )
                    {
                        i++;

                        $(this).focus().select();

                        return false;
                    }

                });

                $(document).on('keyup', 'textarea.pre', function(){

                    $(this).height( 0 );
                    $(this).height( this.scrollHeight );

                });

                $(document).ready(function(){

                    $('textarea.pre').trigger('keyup');

                });

            })(jQuery);
            </script>
        </div>

    <?php

    }
}
$Inbound_Template_Utils = new Inbound_Template_Utils();
