<?php


class ImgRedux{

  public $dir;
  public $limitSize;
  public $dirOut;

  public function redImg($nomeImg, $img, $outImg, $porc){
    $type = mime_content_type($img);
    if(!($type == "image/jpeg" || $type == "image/png")){
      return false;
    }
    $porcAt = $porc;
    $tam = getimagesize($img);
    $tamOrig = filesize($img);
    $imgAtual;

    if($tamOrig < $this->limitSize){
        copy($img, $outImg);
        echo "\r--------> $nomeImg - OK - [" . round(($tamOrig/1000)) . " kb]\n";
        return true;
    }

    $wAtual = ($tam[0] - (($porc*$tam[0])/100));
    $hAtual = ($tam[1] - (($porc*$tam[1])/100));

    $defaultImg = imagecreatetruecolor($wAtual, $hAtual);

    if($type == "image/jpeg"){
      $imgAtual = imagecreatefromjpeg($img);
      imagecopyresampled($defaultImg, $imgAtual, 0,0,0,0, $wAtual, $hAtual, $tam[0], $tam[1]);
      imagejpeg($defaultImg, $outImg);
    }
    if($type == "image/png"){
      $imgAtual = imagecreatefrompng($img);
      imagecopyresampled($defaultImg, $imgAtual, 0,0,0,0, $wAtual, $hAtual, $tam[0], $tam[1]);
        imagepng($defaultImg, $outImg);
    }

    imagedestroy($defaultImg);
    imagedestroy($imgAtual);

    $tamAt = filesize($outImg);

    if($tamAt >= $this->limitSize){
      echo "\r$nomeImg - " . round(($tamAt/1000)) . " kb - reduzindo...";
      unlink($outImg);
      return $this->redImg($nomeImg, $img, $outImg, ($porcAt+5));
    }
    else{
      echo "\r--------> $nomeImg - OK - [" . round(($tamOrig/1000)) . " kb --> " . round(($tamAt/1000)) . " kb]\n";
      return true;
    }
  }

  public function lst(){
  if ($handle = opendir($this->dir)) {
    if(!file_exists($this->dirOut)){
      mkdir($this->dirOut);
    }
      while ($file = readdir($handle)) {
          if($file == "." or $file == ".." or $file == "reduzidas"){
          //  break;
          }
          else{
            $this->redImg($file, $this->dir."/$file", $this->dirOut."/$file", 5);
          }
      }

      closedir($handle);
  }
}
}

$in = new ImgRedux();
$in->dir = "dirPathImages";
$in->dirOut = "dirPathImages/out";
$in->limitSize = 500000;
$in->lst();

?>
