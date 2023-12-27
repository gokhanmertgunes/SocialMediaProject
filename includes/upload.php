<?php

require_once 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryUpload {
    public $filename = null ;
    public $error = null ;
    const MAX_FILESIZE = 1024 * 1024 ;

    public function __construct($filebox, $uploadFolder)
    {
        if (!empty($_FILES[$filebox]["name"])) {
           // a file uploaded
           extract($_FILES[$filebox]) ;

           $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
           $whitelist = ["png", "jpg", "jpeg"];
           if (!in_array($ext, $whitelist)) {
             $this->error = "Not an image file";
           } else if ($size > self::MAX_FILESIZE) {
              $this->error = "Too big for this app.";
           } else {
              // file is valid to be used in the app
              $tempFilePath = $tmp_name;
              $cloudinaryConfig = Configuration::instance();
              $uploadApi = new UploadApi();
              $uploadResult = $uploadApi->upload($tempFilePath);

              if (isset($uploadResult['secure_url'])) {
                  // The file was uploaded successfully
                  $this->filename = $uploadResult['secure_url'];
              } else {
                  // Failed to upload the file
                  $this->error = "Failed to upload file.";
              }
            }
        } else {
            // upload failed or no file selected
            $this->error = "No file uploaded";
        }
    }
}
