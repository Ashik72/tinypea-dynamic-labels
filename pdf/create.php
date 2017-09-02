<?php

set_time_limit (0);
ini_set('memory_limit', '-1');


if (file_exists( dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'wp-load.php' ))
  require ( dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'wp-load.php' );
else
  die("Failed to load WordPress!");


require_once "gfonts.php";
require_once "makefont".DIRECTORY_SEPARATOR."makefont.php";

class PDF extends FPDI {

    var $_tplIdx;
    public static $post_id = 0;


        public static function tdl_get_the_template() {
          $post_id = static::$post_id;
          $_GET['postID'] = &$post_id;
          $postID = $_GET['postID'];

          if (!isset($_GET['postID']) || empty($_GET['postID']))
            return;

          $text_1 = ( isset($_GET['text_1']) ? $_GET['text_1'] : "" );
          $text_2 = ( isset($_GET['text_2']) ? $_GET['text_2'] : "" );
          $postMeta = get_post_meta($postID);
          $tinypea_dynamic_labels_tdl_pdf_template = get_post_meta($postID, 'tinypea_dynamic_labels_tdl_pdf_template', true);
          if (empty($tinypea_dynamic_labels_tdl_pdf_template)) return;
          $tinypea_dynamic_labels_tdl_pdf_template = get_attached_file($tinypea_dynamic_labels_tdl_pdf_template);
          if (file_exists($tinypea_dynamic_labels_tdl_pdf_template))
            copy($tinypea_dynamic_labels_tdl_pdf_template, "tmp.pdf");

          if (!file_exists("tmp.pdf"))
            return;

            return "tmp.pdf";
        }

    function Header() {

        $tinypea_dynamic_labels_tdl_pdf_template = static::tdl_get_the_template();
        if (is_null($this->_tplIdx)) {

            // THIS IS WHERE YOU GET THE NUMBER OF PAGES
            $this->numPages = $this->setSourceFile($tinypea_dynamic_labels_tdl_pdf_template);
            $this->_tplIdx = $this->importPage(1);

        }
        $this->useTemplate($this->_tplIdx, 0, 0,200);

    }


    function Footer() {}

}


/**
 * TextWriterTDL
 */
class TextWriterTDL
{
  public static $pdf;
  public static $postID;
  public static $text_1;
  public static $text_2;

  function __construct($post_id = 0, $text_1 = "", $text_2 = "") {
    if (empty($post_id)) return;
    PDF::$post_id = $post_id;
    $tinypea_dynamic_labels_tdl_pdf_template = get_post_meta($post_id, 'tinypea_dynamic_labels_tdl_pdf_template', true);
    if (empty($tinypea_dynamic_labels_tdl_pdf_template)) return;
    static::$postID = $post_id;
    static::$text_1 = $text_1;
    static::$text_2 = $text_2;
    define('FPDF_FONTPATH', dirname(__FILE__).DIRECTORY_SEPARATOR."fonts".DIRECTORY_SEPARATOR);

    $tdl_page_width = get_post_meta($post_id, 'tinypea_dynamic_labels_tdl_page_width', true);
    $tdl_page_height = get_post_meta($post_id, 'tinypea_dynamic_labels_tdl_page_height', true);


    //static::$pdf = new PDF();
    static::$pdf = new PDF('P','mm',array($tdl_page_width,$tdl_page_height));

    static::$pdf->AddPage();

  }

  public static function hexToRgb($hex, $alpha = false) {
     $hex      = str_replace('#', '', $hex);
     $length   = strlen($hex);
     $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
     $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
     $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
     if ( $alpha ) {
        $rgb['a'] = $alpha;
     }
     return $rgb;
  }

  public function write_text($x = 0, $y = 0, $text_to_write = "", $line = 1 ,$block = 1) {
    global $postID, $gFontURL, $text_1;
    $postID = static::$postID;
    $text_1 = static::$text_1;

    $tinypea_dynamic_labels_tdl_font_style_1 = get_post_meta($postID, 'tinypea_dynamic_labels_tdl_font_style_'.$line."_".$block, true);
    $tinypea_dynamic_labels_tdl_font_style_1 = maybe_unserialize($tinypea_dynamic_labels_tdl_font_style_1);
    //d($gFontURL);
    if ($tinypea_dynamic_labels_tdl_font_style_1['font-type'] != "google")
      return;
    $font_color = ( isset($tinypea_dynamic_labels_tdl_font_style_1['color']) ? $tinypea_dynamic_labels_tdl_font_style_1['color'] : "" );
    $font_size = ( isset($tinypea_dynamic_labels_tdl_font_style_1['font-size']) ? $tinypea_dynamic_labels_tdl_font_style_1['font-size'] : "" );
    $font_size = (int) $font_size;

    if (empty($font_color)) return;
    $font_color = static::hexToRgb($font_color);
    //$font_color = implode(",", $font_color);
    $namespace = 'tinypea_dynamic_labels_';
    $tdl_letters_decrease = get_post_meta($postID, $namespace.'tdl_letters_decrease_'.$block, true);
    $tdl_letters_decrease = explode("|", $tdl_letters_decrease);
    if (!empty($tdl_letters_decrease) && ( count($tdl_letters_decrease) == 2 )) {

      if (strlen($text_to_write) > $tdl_letters_decrease[0]) {
        $font_size = (double) $font_size;
        $tdl_letters_decrease[1] = (double) $tdl_letters_decrease[1];

        $tdl_letters_decrease[1] = ( strlen($text_to_write) - ((int) $tdl_letters_decrease[0] ) ) * $tdl_letters_decrease[1];
        $font_size = $font_size - ( ($tdl_letters_decrease[1]/100.00) * $font_size );
        $font_size = (int) $font_size;
      }

    }

    $font_size = abs($font_size);

    $font_family = $tinypea_dynamic_labels_tdl_font_style_1['font-family'];
    $font_family_original = $font_family;
    $font_family = urlencode($font_family);
    $gFontURLtmp = $gFontURL.$font_family;
    global $uaFonts;
    $content = curlGoogleFont($gFontURLtmp, $uaFonts['ttf']);
    $content = parseCss($content);
    $contentallKeys = array_keys($content);
    $content = array_values($content);
    $content = ( isset($content[0]) ? $content[0] : "" );

    if (empty($content)) return;

    $fontfilename = basename($content);
    $fontfilename_original = $fontfilename;
    $dir = tinypea_dynamic_labels_PLUGIN_DIR."pdf".DS;
    //d(file_exists($dir."fonts".DIRECTORY_SEPARATOR.$fontfilename));
    $fontfilename_to_check = pathinfo($fontfilename);
    $fontfilename_to_check = $fontfilename_to_check['filename'].".php";

    if (!file_exists($dir."fonts".DIRECTORY_SEPARATOR.$fontfilename_to_check)) {
        $get_the_font = file_get_contents($content);
        file_put_contents($dir."fonts".DIRECTORY_SEPARATOR.$fontfilename, $get_the_font);
    }


    $fontfilenameData = pathinfo($fontfilename);
    $dir = tinypea_dynamic_labels_PLUGIN_DIR."pdf".DS;

    if ( isset($fontfilenameData['filename']) && !file_exists($dir."fonts".DIRECTORY_SEPARATOR.$fontfilenameData['filename'].".php") ) {

      $fontfilename = $dir."fonts".DIRECTORY_SEPARATOR.$fontfilename;
      $path = tinypea_dynamic_labels_PLUGIN_DIR."pdf".DS."fonts".DS;

      $make_font = MakeFont($fontfilename, $path);
      $fontfilename_original_data = pathinfo($fontfilename);
      $fontfilename_original_data = $fontfilename_original_data['filename'];
      $dir = tinypea_dynamic_labels_PLUGIN_DIR."pdf".DS;
      //rename($dir.$fontfilename_original_data.'.php', $dir."fonts".DIRECTORY_SEPARATOR.$fontfilename_original_data.'.php');
      //rename($dir.$fontfilename_original_data.'.z', $dir."fonts".DIRECTORY_SEPARATOR.$fontfilename_original_data.'.z');
    };
    $fontfilename_original_data = pathinfo($fontfilename_original);
    $fontfilename_original_data = $fontfilename_original_data['filename'];
    $pdf = &static::$pdf;
    $text_to_write = strtoupper($text_to_write);

    $pdf->AddFont($fontfilename_original_data,'', $fontfilename_original_data.'.php');
    $pdf->SetFont($fontfilename_original_data, "", $font_size);
    $pdf->SetTextColor($font_color['r'], $font_color['g'], $font_color['b']);
    //$pdf->Text($x, $y, $text_to_write);
    //$pdf->Text($x, $y, $text_to_write);
        $pdf->Text($x - ($pdf->GetStringWidth($text_to_write) / 2), $y, $text_to_write);

    //$pdf->Cell(5,10,$text_to_write,0,1,'C');
    //$pdf->Text(40, 60,"Sample Text over overlay");

    // THIS PUTS THE REMAINDER OF THE PAGES IN
    if($pdf->numPages>1) {
        for($i=2;$i<=$pdf->numPages;$i++) {
            //$pdf->endPage();
            $pdf->_tplIdx = $pdf->importPage($i);
            $pdf->AddPage();
        }
    }
  }

  public function import_pdf($file_name = "", $show_file = 0) {
    $pdf = &static::$pdf;

    if (empty($pdf)) return;

    if (empty($file_name))
      $pdf->Output();
    else {

      if (!empty($show_file))
        $pdf->Output($file_name.".pdf", 'D');

      if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR.$file_name.".pdf"))
        return;

        $pdf->Output(dirname(__FILE__).DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR.$file_name.".pdf", 'F');
    }


  }

  public function writer_loop($instance = "") {

    if (empty($instance)) return;

    if (!($instance instanceof self)) return;

    $postID = static::$postID;
    $blocks = (int) get_post_meta($postID, 'tinypea_dynamic_labels_tdl_blocks', true);
    if (empty($blocks)) return;
    $settings = [];
    $namespace = 'tinypea_dynamic_labels_';

    $text_1 = static::$text_1;
    $text_2 = static::$text_2;


    for ($i=1; $i <= $blocks; $i++) {

      $settings['tdl_initial_x_y'] = get_post_meta($postID, $namespace.'tdl_initial_x_y_'.$i, true);
      $settings['tdl_initial_x_y'] = preg_replace('/\s+/', '', $settings['tdl_initial_x_y']);
      $settings['tdl_initial_x_y'] = explode(",", $settings['tdl_initial_x_y']);
      $settings['tdl_initial_x_y'] = array_map('intval',  $settings['tdl_initial_x_y']);
      if ( count($settings['tdl_initial_x_y']) !== 2 )
        continue;

      $settings['tdl_horizontal_space'] = (int) get_post_meta($postID, $namespace.'tdl_horizontal_space_'.$i, true);
      $settings['tdl_vertical_space'] = (int) get_post_meta($postID, $namespace.'tdl_vertical_space_'.$i, true);
      $settings['tdl_vertical_repeat'] = (int) get_post_meta($postID, $namespace.'tdl_vertical_repeat_'.$i, true);
      $settings['tdl_horizontal_repeat'] = (int) get_post_meta($postID, $namespace.'tdl_horizontal_repeat_'.$i, true);
      $settings['tdl_inbetween_space'] = (int) get_post_meta($postID, $namespace.'tdl_inbetween_space_'.$i, true);
      $settings['tdl_use_single'] = (int) get_post_meta($postID, $namespace.'tdl_use_single_'.$i, true);

      $tdl_decrease_perc_space = get_post_meta($postID, $namespace.'tdl_decrease_space_between_two_text_char_perc_'.$i, true);

      if (!empty($tdl_decrease_perc_space)) {

        $tdl_decrease_perc_space = explode("|", $tdl_decrease_perc_space);

        if ( is_array($tdl_decrease_perc_space) && (count($tdl_decrease_perc_space) === 2)) {
          $tdl_decrease_perc_space[0] = floatval($tdl_decrease_perc_space[0]);
          $tdl_decrease_perc_space[1] = (int) $tdl_decrease_perc_space[1];

          if (strlen($text_1) > $tdl_decrease_perc_space[1]) {
            $tdl_decrease_perc_space[1] = strlen($text_1) - $tdl_decrease_perc_space[1];
            $percentage_to_decrease = (double) ($tdl_decrease_perc_space[1] * $tdl_decrease_perc_space[0]);
            $percentage_to_decrease = ($percentage_to_decrease/100.0) * $settings['tdl_inbetween_space'];
            $settings['tdl_inbetween_space'] -= $percentage_to_decrease;
          }
        }
      }

      $i_horizontal_x = $settings['tdl_initial_x_y'][0];
      $i_horizontal_y = $settings['tdl_initial_x_y'][1];
      $first_horizontal_space = 0;
      for ($i_horizontal = 0; $i_horizontal < $settings['tdl_horizontal_repeat']; $i_horizontal++) {

        $i_horizontal_x_fixed = (double) get_post_meta($postID, $namespace.'tdl_x_point_for_horizontal_'.$i.'_'.$i_horizontal, true);
        $i_horizontal_x = (empty($i_horizontal_x_fixed) ? $i_horizontal_x : $i_horizontal_x_fixed);

        $i_horizontal_y_loop2 = $i_horizontal_y;
        $i_horizontal_x_loop2 = $i_horizontal_x;
        for ($i_vertical = 0; $i_vertical < $settings['tdl_vertical_repeat'] ; $i_vertical++) {

          $i_horizontal_y_loop2_tmp = $i_horizontal_y_loop2;

          $i_vertical_y_fixed = (double) get_post_meta($postID, $namespace.'tdl_y_point_for_vertical_'.$i.'_'.$i_vertical, true);
          $i_vertical_y = (empty($i_vertical_y_fixed) ? $i_horizontal_y_loop2 : $i_vertical_y_fixed);
          $i_horizontal_y_loop2 = $i_vertical_y;



          if (!empty($settings['tdl_use_single'])) {

            $instance->write_text($i_horizontal_x, $i_horizontal_y_loop2, $text_1." ".$text_2, 1, $i);

            if (empty($i_vertical_y_fixed))
              $i_horizontal_y_loop2 = $i_horizontal_y_loop2_tmp;

            $i_horizontal_y_loop2 += $settings['tdl_vertical_space'];
            continue;
          }
          $i_horizontal_x_tmp = $i_horizontal_x;


            // d(static::$pdf->GetStringWidth("dddd"));
            // die();
          if (!empty($text_1))
            $instance->write_text($i_horizontal_x, $i_horizontal_y_loop2, $text_1, 1, $i);

          if (!empty($text_2))
            $instance->write_text($i_horizontal_x, ($i_horizontal_y_loop2+$settings['tdl_inbetween_space']), $text_2, 2, $i);

          if (empty($i_vertical_y_fixed))
            $i_horizontal_y_loop2 = $i_horizontal_y_loop2_tmp;

          $i_horizontal_y_loop2 += $settings['tdl_vertical_space'];
          $i_horizontal_x = $i_horizontal_x_tmp;


        }


        $i_horizontal_x += $settings['tdl_horizontal_space'];

      }


    }


  }

  public function write_user_order_id($id = 0) {

    if (empty($id)) return;

    if (empty(get_post_meta($id))) return;

    $_order_number = get_post_meta($id, '_order_number', true);
    $_billing_first_name = get_post_meta($id, '_billing_first_name', true);
    $_billing_last_name = get_post_meta($id, '_billing_last_name', true);

    $namespace = 'tinypea_dynamic_labels_';
    $block = "order_option";
    global $postID;

    $postID = static::$postID;
    $settings['tdl_initial_x_y'] = get_post_meta($postID, $namespace.'tdl_initial_x_y_'.'order_option', true);
    $settings['tdl_initial_x_y'] = preg_replace('/\s+/', '', $settings['tdl_initial_x_y']);
    $settings['tdl_initial_x_y'] = explode(",", $settings['tdl_initial_x_y']);
    $settings['tdl_initial_x_y'] = array_map('intval',  $settings['tdl_initial_x_y']);

    if ( count($settings['tdl_initial_x_y']) !== 2 )
      return;

    $settings['tdl_inbetween_space'] = (int) get_post_meta($postID, $namespace.'tdl_inbetween_space_'.'order_option', true);

    $this->write_text($settings['tdl_initial_x_y'][0] , $settings['tdl_initial_x_y'][1] , "{$_billing_first_name} {$_billing_last_name}", 1, $block);
    $this->write_text($settings['tdl_initial_x_y'][0] , $settings['tdl_initial_x_y'][1]+$settings['tdl_inbetween_space'] , "Order No: {$_order_number}", 2, $block);

}

public static function exec_get() {

  if (!isset($_GET))
    return;

  if (empty($_GET['postID']))
    return;

    $postID = &$post_id;
    $text_1 = ( isset($_GET['text_1']) ? $_GET['text_1'] : "" );
    $text_2 = ( isset($_GET['text_2']) ? $_GET['text_2'] : "" );

    //$tinypea_dynamic_labels_tdl_pdf_template = get_the_template($_GET['postID']);
    $writer = new self($_GET['postID'], $text_1, $text_2);
    $instance = &$writer;
    //$writer->write_text(30, 40, "test text", 1);
    $instance->writer_loop($instance);
    $order_id = ( isset($_GET['order_id']) ? $_GET['order_id'] : 0 );
    $writer->write_user_order_id($order_id, $writer);

    $fileName = ( isset($_GET['file_name']) ? $_GET['file_name'] : "" );

    if (isset($_GET['view']) && ($_GET['view'] == 1))
      $instance->import_pdf();
    else
      $instance->import_pdf($fileName);

}

}

TextWriterTDL::exec_get();

 ?>
