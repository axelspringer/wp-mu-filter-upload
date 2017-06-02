<?php

defined( 'ABSPATH' ) || exit;

class Asse_Upload {

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
        $ext = explode('.', $filename); // explode with extension
        $ext = end($ext); // pointer to last entry
        // replace all weird characters
        $sanitized = preg_replace('/[^a-zA-Z0-9-_.]/','', substr(remove_accents($string), 0, -(strlen($ext)+1)));
        // replace dots inside filename
        $sanitized = str_replace('.','-', $sanitized);
        
        return strtolower($sanitized.'.'.$ext);
    }

    public function filter_mime_types() {
        return [
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' =>  'image/gif',
            'png' =>  'image/png',
            // audio, video
            'mp4|m4v' => 'video/mp4',
            'mp3|m4a|m4b' => 'audio/mpeg',
            // zip
            'zip' => 'application/zip',
            // pdf
            'pdf' => 'application/pdf'
        ];
    }
}


