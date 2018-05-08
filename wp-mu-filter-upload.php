<?php

defined( 'ABSPATH' ) || exit;

class Axelspringer_Filter_Upload {

    public function __construct() {
        add_filter( 'upload_dir', array( $this, 'set_upload_dir' ) );
        add_filter( 'sanitize_file_name', array( $this, 'sanitize_filename_specialchars' ), 10);
        add_filter( 'upload_mimes', array( $this, 'filter_mime_types' ), 1);
    }

    public function set_upload_dir( $uploads ) {
        $time = current_time('mysql');
        $y = substr($time, 0, 4);
        $m = substr($time, 5, 2);
        $subDir = "/$y/$m";

        $uploads['path'] = DATA_DIR . '/' . UPLOADS_DIR_NAME . $subDir;
        $uploads['url'] = WP_HOME . '/' . DATA_DIR_NAME . '/' . UPLOADS_DIR_NAME . $subDir;
        $uploads['subdir'] = isset($uploads['subdir']) ? isset($uploads['subdir']) : '';
        $uploads['basedir'] = DATA_DIR . '/' . UPLOADS_DIR_NAME;
        $uploads['baseurl'] = WP_HOME . '/' . DATA_DIR_NAME . '/' . UPLOADS_DIR_NAME;

        return $uploads;
    }

    public function sanitize_filename_specialchars( $filename ) {
      $pathinfo = pathinfo( $filename );
      $pathinfo[ 'filename' ] = preg_replace('/[^a-zA-Z0-9-_.]/','', remove_accents($pathinfo[ 'filename' ]));
      return strtolower($pathinfo[ 'filename' ] . '.' . $pathinfo[ 'extension' ] );
    }

    public function filter_mime_types() {
      return [
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          =>  'image/gif',
        'png'          =>  'image/png',
        'mp4|m4v'      => 'video/mp4',
        'mp3|m4a|m4b'  => 'audio/mpeg',
        'zip'          => 'application/zip',
        'pdf'          => 'application/pdf'
      ];
    }
}

$asse_upload = new Asse_Axelspringer_Upload();
