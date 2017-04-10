<?php

// @codingStandardsIgnoreFile

/**
 * Set upload dir as subfolder of DATA_DIR
 *
 * @param array $uploads
 * @return array
 */
function setUploadDir($uploads)
{
    // Generate the yearly and monthly dirs
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
add_filter('upload_dir', 'setUploadDir');

/**
 * Remove all accent shit from filenames and lowercase it
 *
 * @param mixed $string
 * @return string
 */
function sanitizeFilenameSpecialChars($string)
{
    $ext = explode('.',$string); // explode with extension
    $ext = end($ext); // pointer to last entry
    // replace all weird characters
    $sanitized = preg_replace('/[^a-zA-Z0-9-_.]/','', substr(remove_accents($string), 0, -(strlen($ext)+1)));
    // replace dots inside filename
    $sanitized = str_replace('.','-', $sanitized);
    return strtolower($sanitized.'.'.$ext);
}
add_filter('sanitize_file_name', 'sanitizeFilenameSpecialChars', 10);

/**
 * Avoid uploading unwanted file types
 *
 * @return mixed
 */
function preventUnwantedMimeTypes()
{
/* 
 * List of Wordpress Default supported mime-types
 * 
   jpg|jpeg|jpe' => 'image/jpeg'
  'gif' => 'image/gif'
  'png' => 'image/png'
  'bmp' => 'image/bmp'
  'tiff|tif' =>  'image/tiff'
  'ico' =>  'image/x-icon'
  'asf|asx' =>  'video/x-ms-asf'
  'wmv' =>  'video/x-ms-wmv'
  'wmx' =>  'video/x-ms-wmx'
  'wm' =>  'video/x-ms-wm'
  'avi' =>  'video/avi'
  'divx' =>  'video/divx'
  'flv' =>  'video/x-flv'
  'mov|qt' =>  'video/quicktime'
  'mpeg|mpg|mpe' =>  'video/mpeg'
  'mp4|m4v' =>  'video/mp4'
  'ogv' =>  'video/ogg'
  'webm' =>  'video/webm'
  'mkv' =>  'video/x-matroska'
  '3gp|3gpp' =>  'video/3gpp'
  '3g2|3gp2' =>  'video/3gpp2'
  'txt|asc|c|cc|h|srt' =>  'text/plain'
  'csv' =>  'text/csv'
  'tsv' =>  'text/tab-separated-values'
  'ics' =>  'text/calendar'
  'rtx' =>  'text/richtext'
  'css' =>  'text/css'
  'htm|html' =>  'text/html'
  'vtt' =>  'text/vtt'
  'dfxp' =>  'application/ttaf+xml'
  'mp3|m4a|m4b' =>  'audio/mpeg'
  'ra|ram' =>  'audio/x-realaudio'
  'wav' =>  'audio/wav'
  'ogg|oga' =>  'audio/ogg'
  'mid|midi' =>  'audio/midi'
  'wma' =>  'audio/x-ms-wma'
  'wax' =>  'audio/x-ms-wax'
  'mka' =>  'audio/x-matroska'
  'rtf' =>  'application/rtf'
  'js' =>  'application/javascript'
  'pdf' =>  'application/pdf'
  'class' =>  'application/java'
  'tar' =>  'application/x-tar'
  'zip' =>  'application/zip'
  'gz|gzip' =>  'application/x-gzip'
  'rar' =>  'application/rar'
  '7z' =>  'application/x-7z-compressed'
  'psd' =>  'application/octet-stream'
  'xcf' =>  'application/octet-stream'
  'doc' =>  'application/msword'
  'pot|pps|ppt' =>  'application/vnd.ms-powerpoint'
  'wri' =>  'application/vnd.ms-write'
  'xla|xls|xlt|xlw' =>  'application/vnd.ms-excel'
  'mdb' =>  'application/vnd.ms-access'
  'mpp' =>  'application/vnd.ms-project'
  'docx' =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
  'docm' =>  'application/vnd.ms-word.document.macroEnabled.12'
  'dotx' =>  'application/vnd.openxmlformats-officedocument.wordprocessingml.template'
  'dotm' =>  'application/vnd.ms-word.template.macroEnabled.12'
  'xlsx' =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
  'xlsm' =>  'application/vnd.ms-excel.sheet.macroEnabled.12'
  'xlsb' =>  'application/vnd.ms-excel.sheet.binary.macroEnabled.12'
  'xltx' =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
  'xltm' =>  'application/vnd.ms-excel.template.macroEnabled.12'
  'xlam' =>  'application/vnd.ms-excel.addin.macroEnabled.12'
  'pptx' =>  'application/vnd.openxmlformats-officedocument.presentationml.presentation'
  'pptm' =>  'application/vnd.ms-powerpoint.presentation.macroEnabled.12'
  'ppsx' =>  'application/vnd.openxmlformats-officedocument.presentationml.slideshow'
  'ppsm' =>  'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
  'potx' =>  'application/vnd.openxmlformats-officedocument.presentationml.template'
  'potm' =>  'application/vnd.ms-powerpoint.template.macroEnabled.12'
  'ppam' =>  'application/vnd.ms-powerpoint.addin.macroEnabled.12'
  'sldx' =>  'application/vnd.openxmlformats-officedocument.presentationml.slide'
  'sldm' =>  'application/vnd.ms-powerpoint.slide.macroEnabled.12'
  'onetoc|onetoc2|onetmp|onepkg' =>  'application/onenote'
  'oxps' =>  'application/oxps'
  'xps' =>  'application/vnd.ms-xpsdocument'
  'odt' =>  'application/vnd.oasis.opendocument.text'
  'odp' =>  'application/vnd.oasis.opendocument.presentation'
  'ods' =>  'application/vnd.oasis.opendocument.spreadsheet'
  'odg' =>  'application/vnd.oasis.opendocument.graphics'
  'odc' =>  'application/vnd.oasis.opendocument.chart'
  'odb' =>  'application/vnd.oasis.opendocument.database'
  'odf' =>  'application/vnd.oasis.opendocument.formula'
  'wp|wpd' =>  'application/wordperfect'
  'key' =>  'application/vnd.apple.keynote'
  'numbers' =>  'application/vnd.apple.numbers'
  'pages' =>  'application/vnd.apple.pages'

 */
    // allow only these
    $mime_types = array(
        // images
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
    );
    return $mime_types;
}

add_filter('upload_mimes', 'preventUnwantedMimeTypes', 1);

//deactivate useless statement, which causes lots of DB traffic
//UPDATE `wp_options` SET `option_value` = '' WHERE `option_name` = 'uploads_use_yearmonth_folders'
add_filter('option_uploads_use_yearmonth_folders', '__return_false', 100);

