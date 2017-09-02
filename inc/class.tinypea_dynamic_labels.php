<?php

/**
 * TinypeaDynamicLabels
 */
class TinypeaDynamicLabels {

    private static $instance;

    public static function get_instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new self();
      }

      return self::$instance;
    }

  function __construct() {

    add_action( 'wp_enqueue_scripts', array($this, 'load_custom_wp_frontend_style') );
    add_action( 'admin_enqueue_scripts', array($this, 'load_custom_admin_wp_frontend_style') );
    add_action('template_redirect', [$this, 'inspect_func']);
    add_action('tf_pre_save_options_tinypea_dynamic_labels', [$this, 'save_custom_options'], 10, 1);
    //add_filter( 'bulk_actions-edit-post',  );
    add_action('current_screen', [$this, 'add_bulk_action']);

    add_action( 'wp_ajax_doBulkPDF', array($this, 'doBulkPDF_func') );
    add_action( 'wp_ajax_nopriv_doBulkPDF', array($this, 'doBulkPDF_func') );



  }
  public function doBulkPDF_func() {

    if (!isset($_POST['values']) || empty($_POST['values']) || !is_array($_POST['values']))
      wp_die();

    $values = $_POST['values'];

    array_map('unlink', glob(tinypea_dynamic_labels_PLUGIN_DIR.DS."pdf".DS."pdf".DS."*"));

    $pdf_result = [];

      foreach ($values as $key => $result)
        $pdf_result[] = [ 'id' => $result, 'link' => adminOptsTDL::orderDownloads($result) ];

    echo json_encode($pdf_result);
    wp_die();
  }

  public function add_bulk_action() {

    if (strcmp("edit-shop_order", get_current_screen()->id) !== 0)
      return;

    add_filter( 'bulk_actions-'.get_current_screen()->id, [$this, 'add_bulk_action_custom'] );
    add_filter( 'handle_bulk_actions-'.get_current_screen()->id, [$this, 'add_bulk_action_custom_handler'], 10, 3 );
    add_action( 'admin_notices', [$this, 'add_bulk_action_custom_handler'] );

  }

  public function add_bulk_action_custom ($bulk_actions) {

  $bulk_actions['download_pdf'] = __( 'Download PDF', 'download_pdf');
  return $bulk_actions;

  }


function add_bulk_action_custom_handler() {
  if ( strcmp($_GET['action'], "download_pdf") !== 0 ) return;
  if ( !isset($_GET['post']) || empty($_GET['post']) ) return;

  $order_url = get_admin_url('', 'edit.php?post_type=shop_order');
  $_GET['post'] = http_build_query($_GET['post']);
  ob_start();
  $pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/zip.php?".$_GET['post'];
  echo("<a href='{$pdf_link}' target='_blank' class='tdl_download__all_pdf'>Download</button></a>");
  echo("<br><a href='{$order_url}' class='back_to_order_list'>Back</button></a>");

  $output = ob_get_clean();

  echo $output;

}

  public function save_custom_options($tf = "") {

    if (!isset($_POST['post_type']) || empty($_POST['post_type']))
      return;

    if (strcmp($_POST['post_type'], "product") !== 0)
      return;

    $post_ID = $_POST['post_ID'];
    $post_ID = (int) $post_ID;


    $blocks = (int) $_POST['tinypea_dynamic_labels_tdl_blocks'];
    $namespace = "tinypea_dynamic_labels_";
    $custom_keys = [
    'tdl_initial_x_y_',
    'tdl_horizontal_space_',
    'tdl_vertical_space_',
    'tdl_vertical_repeat_',
    'tdl_horizontal_repeat_',
    'tdl_inbetween_space_',
    'tdl_font_style_1_',
    'tdl_font_style_2_',
    'tdl_use_single_',
    'tdl_letters_decrease_',
    'tdl_horizontal_first_space_',
    'tdl_x_point_for_horizontal_',
    'tdl_y_point_for_vertical_',
    'tdl_decrease_space_between_two_text_char_perc_'
   ];
   $blocks_actual = 0;
   $key_tmp = "";
   for ($i=0; $i < $blocks; $i++) {
     $blocks_actual = ($i+1);

     foreach ($custom_keys as $key) {

       $key_tmp = $namespace.$key.$blocks_actual;

       if (strcmp($key, 'tdl_x_point_for_horizontal_') === 0) {

         $how_many_x = (int) get_post_meta($post_ID, $namespace."tdl_horizontal_repeat_".$blocks_actual, true);

         if (!empty($how_many_x)) {

           for ($i_horizontal_x = 0; $i_horizontal_x  < $how_many_x; $i_horizontal_x ++) {

             $key_tmp_hx = $namespace.$key.$blocks_actual."_".$i_horizontal_x;
             if (isset($_POST[$key_tmp_hx]))
               update_post_meta($post_ID, $key_tmp_hx, $_POST[$key_tmp_hx]);

           }

         }

       }



       if (strcmp($key, 'tdl_y_point_for_vertical_') === 0) {

         $how_many_y = (int) get_post_meta($post_ID, $namespace."tdl_vertical_repeat_".$blocks_actual, true);
         if (!empty($how_many_y)) {
           for ($i_horizontal_y = 0; $i_horizontal_y  < $how_many_y; $i_horizontal_y ++) {
             $key_tmp_hy = $namespace.$key.$blocks_actual."_".$i_horizontal_y;

             if (isset($_POST[$key_tmp_hy]))
               update_post_meta($post_ID, $key_tmp_hy, $_POST[$key_tmp_hy]);
           }

         }
       }


         //file_put_contents('post-titan-plug.txt', serialize([ isset($_POST[$key_tmp]), $_POST[$key_tmp], $key_tmp  ])."\n\n", FILE_APPEND);
         if (isset($_POST[$key_tmp])) {

           if (!is_array($_POST[$key_tmp]))
            update_post_meta($post_ID, $key_tmp, $_POST[$key_tmp]);
          else
            update_post_meta($post_ID, $key_tmp, maybe_serialize($_POST[$key_tmp]));

         }

     }


   }


   foreach ($_POST as $key => $value) {
     //file_put_contents('post-titan-plug.txt', serialize([ $key, $value ])."\n\n", FILE_APPEND);

   }


   }


  public function load_custom_wp_frontend_style() {


  wp_register_script( 'tinypea_dynamic_labels-script-custom', tinypea_dynamic_labels_PLUGIN_URL.'js/custom.js', array( 'jquery' ), '', true );
  wp_localize_script( 'tinypea_dynamic_labels-script-custom', 'tdl_data', array( 'ajax_url' => admin_url('admin-ajax.php') ));
  wp_enqueue_script( 'tinypea_dynamic_labels-script-custom' );
  wp_enqueue_style( 'tinypea_dynamic_labels-style-css', tinypea_dynamic_labels_PLUGIN_URL.'css/custom.css' );


  }


  public function load_custom_admin_wp_frontend_style() {

      $post_id = ( isset($_GET['post']) ? $_GET['post'] : "" );
      $create_pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/create.php";

      wp_register_script( 'tinypea_dynamic_labels-admin-script-custom', tinypea_dynamic_labels_PLUGIN_URL.'js/admin.js', array( 'jquery' ), '', true );
      wp_localize_script( 'tinypea_dynamic_labels-admin-script-custom', 'tdl_admin_data', array( 'ajax_url' => admin_url('admin-ajax.php'), 'tinypea_dynamic_labels_PLUGIN_URL' => tinypea_dynamic_labels_PLUGIN_URL,'post_id' => $post_id, 'create_pdf_link' => $create_pdf_link ));
      wp_enqueue_script( 'tinypea_dynamic_labels-admin-script-custom' );
      wp_enqueue_style( 'tinypea_dynamic_labels-admin-style-css', tinypea_dynamic_labels_PLUGIN_URL.'css/admin.css' );


  }

  public static function getTitan() {

    if (!class_exists('TitanFramework'))
      return;

    $titan = TitanFramework::getInstance( 'tinypea_dynamic_labels_options' );
    return $titan;
  }


  public function inspect_func() {

    if( !current_user_can('administrator') )
      return;
    $post_id = ( isset($_GET['inspect_id']) ? $_GET['inspect_id'] : 0 );

    if (empty($post_id))
      return;


    //$titan = static::getTitan();
    //$tdl_blocks = $titan->getOption('tdl_vertical_space', 1782);
    d(get_post_meta($post_id));
    //d($titan);
    wp_die();

    $order = new WC_Order();
    $order->set_id($post_id);

    $items = $order->get_items();
    $text_values = [];
    $count_loop_1 = 0;
    foreach ($items as $key => $item) {
      $count_loop_1++;
      $_tmcartepo_data = wc_get_order_item_meta($item->get_id(), '', FALSE );
      if (!isset($_tmcartepo_data['_tmcartepo_data'])) continue;
      $_tmcartepo_data = $_tmcartepo_data['_tmcartepo_data'];
      if (!isset($_tmcartepo_data[0])) continue;
      $_tmcartepo_data = $_tmcartepo_data[0];
      $_tmcartepo_data = maybe_unserialize($_tmcartepo_data);
      $text_val_tmp = [];
      $text_val_tmp['postID'] = $item->get_product()->get_id();
      $text_val_tmp['order_id'] = $post_id;
      foreach ($_tmcartepo_data as $key_po_data => $_tmcartepo_data_single) {
        if ($key_po_data == 0)
          $text_val_tmp['text_1'] = $_tmcartepo_data_single['value'];
        elseif ($key_po_data == 1)
          $text_val_tmp['text_2'] = $_tmcartepo_data_single['value'];


      }

      $text_val_tmp['file_name'] = $post_id."_".$count_loop_1;

      $text_values[] = $text_val_tmp;


    }

    if (empty($text_values)) return;
    $create_pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/create.php";

    foreach ($text_values as $key => $text_value) {
      $create_pdf_link_tmp = $create_pdf_link."/?".http_build_query($text_value);
      if (!file_exists(tinypea_dynamic_labels_PLUGIN_DIR.DS."pdf".DS."pdf".DS.$text_value['file_name'].".pdf")) {

        $writer = new TextWriterTDL($text_value['postID'], $text_value['text_1'], $text_value['text_2']);
        $instance = &$writer;
        $instance->writer_loop($instance);
        $order_id = ( isset($text_value['order_id']) ? $text_value['order_id'] : 0 );
        $writer->write_user_order_id($order_id, $writer);
        $fileName = ( isset($text_value['file_name']) ? $text_value['file_name'] : "" );
        $instance->import_pdf($fileName);

        //  @file_get_contents($create_pdf_link_tmp);
      }

      if (file_exists(tinypea_dynamic_labels_PLUGIN_DIR.DS."pdf".DS."pdf".DS.$text_value['file_name'].".pdf"))
        $pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/pdf/".$text_value['file_name'].".pdf";

        d($pdf_link);
    }

    //d(class_exists('TextWriterTDL'));


    //d($order);
    die();
  }

}


 ?>
