<?php

/**
 * adminOptsTDL
 */
class adminOptsTDL
{

  public static $meta_box = "";

  function __construct()
  {
    # code...
  }

  public static function getTitan() {

    if (!class_exists('TitanFramework'))
      return;

    $titan = TitanFramework::getInstance( 'tinypea_dynamic_labels_options' );
    return $titan;
  }

  public static function generate_dynamic_blocks($meta_box = "") {

  if (empty($meta_box)) return;
  static::$meta_box = $meta_box;
  $titan = static::getTitan();
  if (empty($_GET['post'])) return;

  $post_id = (int) $_GET['post'];
  $tdl_blocks = (int) get_post_meta($post_id, 'tinypea_dynamic_labels_tdl_blocks', 1);
  if (empty($tdl_blocks)) return;
  //d($tdl_blocks);

  for ($i=0; $i < $tdl_blocks; $i++)
    static::dynamic_structure($post_id, ($i+1) );

  }


  public static function dynamic_structure($post_id = 0, $block = 0) {

    if (empty(static::$meta_box))
      return;

    $WoolabelConfig = static::$meta_box;

    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Font Style Line 1',
    'id' => 'tdl_font_style_1_'.$block,
    'type' => 'font',
    'desc' => 'Select a style',
    'show_font_weight' => false,
    'show_font_style' => false,
    'show_line_height' => false,
    'show_letter_spacing' => false,
    'show_text_transform' => false,
    'show_font_variant' => false,
    'show_text_shadow' => false,
    'show_websafe_fonts' => false
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Font Style Line 2',
    'id' => 'tdl_font_style_2_'.$block,
    'type' => 'font',
    'desc' => 'Select a style',
    'show_font_weight' => false,
    'show_font_style' => false,
    'show_line_height' => false,
    'show_letter_spacing' => false,
    'show_text_transform' => false,
    'show_font_variant' => false,
    'show_text_shadow' => false,
    'show_websafe_fonts' => false
    ) );

    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'After \'X\' letters decrease font size by \'Y\' percent',
    'id' => 'tdl_letters_decrease_'.$block,
    'type' => 'text',
    'desc' => 'First integer indicate letters|then percentage in double/int - <br> i.e. 5|2.5'
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Use single line for both text',
    'id' => 'tdl_use_single_'.$block,
    'type' => 'checkbox',
    'desc' => '',
    'default' => false,
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'How many times to repeat horizontally?',
    'id' => 'tdl_horizontal_repeat_'.$block,
    'type' => 'text',
    'desc' => 'An integer<br> ie: 5'
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Initial (x,y) coordinate',
    'id' => 'tdl_initial_x_y_'.$block,
    'type' => 'text',
    'desc' => 'Set initial (x, y) point, integers - <br> ie: 0, 10'
    ) );

  /*  $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Horizontal space after 1st Element (in px)',
    'id' => 'tdl_horizontal_first_space_'.$block,
    'type' => 'text',
    'desc' => 'Set space (in px) - <br> ie: 20'
    ) ); */

    $titan = static::getTitan();

    $namespace = "tinypea_dynamic_labels_";
    $horizontal_xs = (int) get_post_meta($post_id, $namespace.'tdl_horizontal_repeat_'.$block, 1);
    if (!empty($horizontal_xs)) {
      for ($i_loop_h=0; $i_loop_h < $horizontal_xs; $i_loop_h++) {
        $WoolabelConfig->createOption( array(
        'name' => '<b>Block '.$block.': </b>'.'X point for horizontal element '.($i_loop_h+1),
        'id' => 'tdl_x_point_for_horizontal_'.$block.'_'.$i_loop_h,
        'type' => 'text',
        'desc' => 'Set point x for horizontal '.($i_loop_h+1).' on block '.$block
        ) );
      }
    }

    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Horizontal space (in px) (will be used if no point X provided)',
    'id' => 'tdl_horizontal_space_'.$block,
    'type' => 'text',
    'desc' => 'Set Horizontal space (in px) - <br> ie: 20'
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Vertical space (in px)',
    'id' => 'tdl_vertical_space_'.$block,
    'type' => 'text',
    'desc' => 'Set Vertical space (in px) - <br> ie: 10'
    ) );

    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Space between two texts',
    'id' => 'tdl_inbetween_space_'.$block,
    'type' => 'text',
    'desc' => 'Set space between two texts (in px) - <br> ie: 10'
    ) );

    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Decrease Space Between Two Text After X Characters (Percentage)',
    'id' => 'tdl_decrease_space_between_two_text_char_perc_'.$block,
    'type' => 'text',
    'desc' => 'Value set here will decrease internal space between two text by percentage for each character after X character. So if its set to 2|5 and there are 8 characters, after 5 characters it will start the calculation and negate (3*2)% or 6% of actual space. - <br> ie: 2|5'
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'How many times to repeat vertically?',
    'id' => 'tdl_vertical_repeat_'.$block,
    'type' => 'text',
    'desc' => 'An integer<br> ie: 10'
    ) );

    $vertical_xs = (int) get_post_meta($post_id, $namespace.'tdl_vertical_repeat_'.$block, 1);

    if (!empty($vertical_xs)) {
      for ($i_loop_v = 0; $i_loop_v < $vertical_xs; $i_loop_v++) {
        $WoolabelConfig->createOption( array(
        'name' => '<b>Block '.$block.': </b>'.'Y point for vertical element '.($i_loop_v+1),
        'id' => 'tdl_y_point_for_vertical_'.$block.'_'.$i_loop_v,
        'type' => 'text',
        'desc' => 'Set point y for vertical '.($i_loop_v+1).' on block '.$block
        ) );
      }
    }



  }


  public static function orderOpts($order_namespace = "") {

    $WoolabelConfig = static::$meta_box;
    $block = &$order_namespace;
    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Font Style Parent Name',
    'id' => 'tdl_font_style_1_'.$block,
    'type' => 'font',
    'desc' => 'Select a style',
    'show_font_weight' => false,
    'show_font_style' => false,
    'show_line_height' => false,
    'show_letter_spacing' => false,
    'show_text_transform' => false,
    'show_font_variant' => false,
    'show_text_shadow' => false,
    'show_websafe_fonts' => false
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Font Style Order No',
    'id' => 'tdl_font_style_2_'.$block,
    'type' => 'font',
    'desc' => 'Select a style',
    'show_font_weight' => false,
    'show_font_style' => false,
    'show_line_height' => false,
    'show_letter_spacing' => false,
    'show_text_transform' => false,
    'show_font_variant' => false,
    'show_text_shadow' => false,
    'show_websafe_fonts' => false
    ) );


    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Initial (x,y) coordinate',
    'id' => 'tdl_initial_x_y_'.$block,
    'type' => 'text',
    'desc' => 'Set initial (x, y) point, integers - <br> ie: 0, 10'
    ) );
    $WoolabelConfig->createOption( array(
    'name' => '<b>Block '.$block.': </b>'.'Space between two texts',
    'id' => 'tdl_inbetween_space_'.$block,
    'type' => 'text',
    'desc' => 'Set space between two texts (in px) - <br> ie: 10'
    ) );

  }

  public static function orderDownloads($order_id = 0) {
  ob_start();
  if (empty($order_id))
    $order_id = ( isset($_GET['post']) ? $_GET['post'] : 0 );

  if (empty($order_id)) return;
  $order_id = (int) $order_id;
  //$post_id = &$order_id;

      $order = new WC_Order();
      $order->set_id($order_id);
      $items = $order->get_items();

      $text_values = [];
      $count_loop_1 = 0;
      $post_id = $order_id;
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
      //d($text_values);
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

        if (file_exists(tinypea_dynamic_labels_PLUGIN_DIR.DS."pdf".DS."pdf".DS.$text_value['file_name'].".pdf")) {
          $pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/pdf/".$text_value['file_name'].".pdf";
          echo("<a href='{$pdf_link}' target='_blank' class='tdl_download_pdf'>Download</button></a>");
        }
    }

  $output = ob_get_clean();
  //_e($output);
  return $output;
  }

  public static function orderDownloadsAll() {

    ob_start();
    $pdf_link = tinypea_dynamic_labels_PLUGIN_URL."pdf/zip.php";
    echo("<a href='{$pdf_link}' target='_blank' class='tdl_download__all_pdf'>Download</button></a>");

    $output = ob_get_clean();
    return $output;

  }

}


 ?>
