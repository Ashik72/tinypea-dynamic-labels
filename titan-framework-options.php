<?php

if (!defined('ABSPATH'))
  exit;


add_action( 'tf_create_options', 'tinypea_dynamic_labels_options', 150 );

function tinypea_dynamic_labels_options() {


	$titan = TitanFramework::getInstance( 'tinypea_dynamic_labels' );

  $section = $titan->createAdminPanel( array(
        'name' => __( 'Tinypea Dynamic Labels', 'tinypea_dynamic_labels' ),
        'icon'	=> 'dashicons-networking'
    ) );

  $section_mother = $section;

  $tab = $section->createTab( array(
          'name' => 'Download Files'
      ) );

      $tab->createOption(
        array(
        'type' => 'custom',
        'custom' => adminOptsTDL::orderDownloadsAll()
      ));


//   $section->createOption( array(
//       'type' => 'save',
// ) );


$WoolabelConfig = $titan->createMetaBox( array(
'name' => 'Configure Label Options',
'post_type' => 'product'
) );

$WoolabelConfig->createOption( array(
'name' => 'PDF Template',
'id' => 'tdl_pdf_template',
'type' => 'file',
'desc' => 'Upload the pdf template file'
) );



$WoolabelConfig->createOption( array(
'name' => 'How many blocks?',
'id' => 'tdl_blocks',
'type' => 'text',
'desc' => '<b>[This is very important]</b> Set how many (integer value) text blocks will be there and save the product. You will see further options appeared below. Configure them also.'
) );

$WoolabelConfig->createOption( array(
'name' => 'Page width',
'id' => 'tdl_page_width',
'type' => 'text',
'desc' => ''
) );


$WoolabelConfig->createOption( array(
'name' => 'Page height',
'id' => 'tdl_page_height',
'type' => 'text',
'desc' => ''
) );



adminOptsTDL::generate_dynamic_blocks($WoolabelConfig);
adminOptsTDL::orderOpts( "order_option" );

$WoolabelConfig = $titan->createMetaBox( array(
'name' => 'Test PDF Generator',
'post_type' => 'product',
'context' => 'side'
) );

$WoolabelConfig->createOption( array(
'name' => 'Text 1',
'id' => 'tdl_test_txt_1',
'type' => 'text',
'desc' => ''
) );

$WoolabelConfig->createOption( array(
'name' => 'Text 2',
'id' => 'tdl_test_txt_2',
'type' => 'text',
'desc' => ''
) );

$WoolabelConfig->createOption( array(
'name' => 'Order ID',
'id' => 'tdl_test_order_id',
'type' => 'text',
'desc' => ''
) );

$WoolabelConfig->createOption(
  array(
  'type' => 'custom',
  'custom' => "<button id='tdl_test_pdf'>Test PDF</button>"
  )
);


$WoolabelConfig = $titan->createMetaBox( array(
'name' => 'Download PDF',
'post_type' => 'shop_order',
'context' => 'side'
) );

$WoolabelConfig->createOption(
  array(
  'type' => 'custom',
  'custom' => adminOptsTDL::orderDownloads()
));


}


 ?>
