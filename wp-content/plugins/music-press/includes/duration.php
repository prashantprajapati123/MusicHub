<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TZ_Music_Mp3Data
{
    protected $mp3data;
    protected $fileDirectory;
    protected $bitRate;
    protected $blockMax;

    public function __construct($filename, $bitrate = null)
    {
        $this->mp3data = array();
        $this->mp3data['filesize'] = $this->tz_get_size_of_file($filename);
        $this->fileDirectory = fopen($filename, "r");
        $this->blockMax = 1024;

        if($bitrate)
            $this->bitRate = $bitrate;
        else
            $this->bitRate= 128;

        $this->tz_set_data();
    }

    public function tz_get_mp3_duration() {
        return $this->mp3data['duration'];
    }


    public function tz_get_mp3_filesize() {
        return $this->mp3data['filesize'];
    }

    protected function tz_get_size_of_file($url) {
        if (substr($url,0,4)=='http') {

            $x = array_change_key_case(get_headers($url, 1),CASE_LOWER);

            if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') != 0 ) {
                $x = $x['content-length'][1];
            }
            else {
                $x = $x['content-length'];
            }
        }
        else {
            $x = @filesize($url);
        }

        return $x;
    }


    protected function tz_set_data() {
        $this->mp3data['length'] = $this->tz_get_duration($this->mp3data, $this->tz_tell(), $this->bitRate);
        $this->mp3data['duration'] = $this->tz_get_formatted_time($this->mp3data['length']);
    }
    protected function tz_tell()
    {
        return ftell($this->fileDirectory)-$this->blockMax - 1;
    }

    protected function tz_get_duration(&$mp3,$startat, $bitrate)
    {
        if ($bitrate > 0)
        {
            $KBps = ($bitrate * 1000)/8;
            $datasize = ($mp3['filesize'] - ($startat/8));
            $length = $datasize / $KBps;
            return sprintf("%d", $length);
        }
        return "";
    }

    protected function tz_get_formatted_time($duration)
    {
        return sprintf("%d:%02d", ($duration /60), $duration %60 );
    }
}