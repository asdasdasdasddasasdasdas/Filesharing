<?php


namespace Filesharing\Helper;


class Helper
{


    private $types = [
        "audio" => "/img/audio.png",
        "application" => "/img/application.png",
        "video" => "/img/video.png",
        "text" => "/img/text.png"
    ];

    /**
     * @return array
     */
    public function getImagePath(string $type)
    {

        $type = $this->getType($type);
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        } elseif ($type == "image") {
            return false;
        } else {
            return "/img/undefined.png";
        }
    }

    private function getType($type)
    {
        return explode('/', $type)[0];
    }

    public function generateHtmlByFile($file)
    {
        $path = '/' . $file->getPublicPath();
        $type = $this->getType($file->getType());

        if ($type == "audio") {
            return "<div class='mb-5 text-center'><img class='file-show-img' src={$file->getImagePath()} >
            </div> 
            <div class='mb-5 text-center'>
            <audio controls>
            <source src={$path} type=\"audio/mp3\">     
                   <source src={$path} type='audio/mpeg'>
            <source src={$path} type='audio/ogg'>
</audio></div>";
        } elseif ($type == "video") {

            return " <div class='mb-5 text-center'>  <video class='video-file' width=\"300px\" height=\"200px\" src={$path} controls></video>  </div> ";
        } else {
            return "<div class='mb-5 text-center'><img class='file-show-img' src={$file->getImagePath()} >
            </div> ";
        }
    }
}