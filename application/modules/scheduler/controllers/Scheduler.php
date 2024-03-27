<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Scheduler extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // logged_in();
        $this->load->model('scheduler_model');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
    }


    public function download_ship_images()
    {

        $config_param = get_config_param('url');
        $ship_image = $this->scheduler_model->get_list_ship_image();

        foreach ($ship_image as $key => $value) {

            //base url from setting param (dinamis)
            $url = $config_param['url_reporting'];

            $path     = $value->path; // Directory Logs

            $filename = $value->image; // 

            if (substr($path, 0, 2) == './') {
                $combine_path = substr($path, 1);
            } else {
                $combine_path = $path;
            }

            if (substr($filename, 0, 2) == './') {
                $combine_filename = substr($filename, 1);
            } else {
                $combine_filename = $filename;
            }

            $imageUrl = $url . $combine_path . $combine_filename;

            $extention = array("JPG", "JPEG", "PNG", "webp", "WEBP");

            $file_extention = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

            // print_r(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            if (!in_array(strtoupper($file_extention), $extention)) {
                echo "invalid file format<br>";
            } else {
                if (file_exists($path)) {
                    // Jika file sudah ada maka data di update 
                    if (file_exists($path . $filename)) {
                        // jika file sudah ada di skip
                        echo "file sudah ada<br>";
                    } else {
                        $rawImage = file_get_contents($imageUrl);
                        if ($rawImage) {
                            file_put_contents($path . $filename, $rawImage);
                            echo 'Image Saved<br>';
                        } else {
                            echo 'File tidak ditemukan<br>';
                        }
                    }
                } else {
                    // Create folder logs
                    if (!mkdir($path, 0777, true)) {
                        echo "Gagal membuat folder<br>";
                    } else {
                        $rawImage = file_get_contents($imageUrl);
                        if ($rawImage) {
                            file_put_contents($path . $filename, $rawImage);
                            echo 'Image Saved<br>';
                        } else {
                            echo 'File tidak ditemukan<br>';
                        }
                    }
                }
            }
        }
    }



    public function download_assets()
    {
        $fullIlpLocal = $this->config->item('full_ip_local');
        $ipLocal = $this->config->item('ip_local');

        $splitFullIP = explode('/', $fullIlpLocal);
        $splitIP = explode('.', $splitFullIP[2] ?? '');
        $codeIP = end($splitIP);

        if ($codeIP == '6') {
            $sourceUrl = str_replace('200.6', '200.5', $fullIlpLocal);
        } else if ($codeIP == '5') {
            $sourceUrl = str_replace('200.5', '200.6', $fullIlpLocal);
        } else {
            echo "IP Tidak Valid";
            return;
        }

        $fileAssets = $this->scheduler_model->getFileAssets($ipLocal);

        foreach ($fileAssets as $row) {
            $fullPath     = $row->path; // Directory Full File

            if (substr($fullPath, 0, 2) == './') {
                $combine_path = substr($fullPath, 1);
            } else {
                $combine_path = $fullPath;
            }

            $splitPath = explode('/', $combine_path);
            array_pop($splitPath);
            $path = '.' . implode('/', $splitPath);

            $assetUrl = $sourceUrl . $combine_path;



            if (file_exists($path)) {
                // Jika file sudah ada maka data di update 
                if (file_exists($fullPath)) {
                    $this->scheduler_model->updateStatusSyncFileAssets($row->id);
                    // jika file sudah ada di skip
                    echo "file sudah ada<br>";
                } else {
                    $rawImage = file_get_contents($assetUrl);
                    if ($rawImage) {
                        if (file_put_contents($fullPath, $rawImage)) {
                            $this->scheduler_model->updateStatusSyncFileAssets($row->id);
                            echo 'File Saved<br>';
                        }
                    } else {
                        echo 'File tidak ditemukan<br>';
                    }
                }
            } else {
                // Create folder logs
                if (!mkdir($path, 0777, true)) {
                    echo "Gagal membuat folder<br>";
                } else {
                    $rawImage = file_get_contents($assetUrl);
                    if ($rawImage) {
                        if (file_put_contents($fullPath, $rawImage)) {
                            $this->scheduler_model->updateStatusSyncFileAssets($row->id);
                            echo 'File Saved<br>';
                        }
                    } else {
                        echo 'File tidak ditemukan<br>';
                    }
                }
            }
        }
    }
}
