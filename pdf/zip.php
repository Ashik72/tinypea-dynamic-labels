<?php

if (file_exists( dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'wp-load.php' ))
  require ( dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'wp-load.php' );
else
  die("Failed to load WordPress!");

  if( !current_user_can('administrator') )
    die("Cheating?");

    global $wpdb;
    $prefix = $wpdb->prefix;
    $table = $prefix.'posts';
    $results = $GLOBALS['wpdb']->get_results( "SELECT ID FROM $table WHERE `post_type` = 'shop_order'", OBJECT );
    array_map('unlink', glob(tinypea_dynamic_labels_PLUGIN_DIR.DS."pdf".DS."pdf".DS."*"));

    if (!empty($_GET) && is_array($_GET))
      foreach ($_GET as $key => $result)
        adminOptsTDL::orderDownloads($result);
        else
      foreach ($results as $key => $result)
        adminOptsTDL::orderDownloads($result->ID);

    // Get real path for our folder
    $rootPath = realpath(dirname(__FILE__).DS."pdf");
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open('download.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();


    // set example variables
    $filename = "download.zip";
    $filepath = dirname(__FILE__).DS;

    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$filename."\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filepath.$filename));
    @readfile($filepath.$filename);



 ?>
