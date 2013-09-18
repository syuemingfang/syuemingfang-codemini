<?php
/*!

# Code Mini
  Easy Optimizing Code for HTML, CSS and Javascript. 
  
  [Getting Started](http://codemini.cxm.tw) [GitHub project](https://github.com/syuemingfang/syuemingfang-codemini) [Documentation](http://comment.cxm.tw/?url=https://raw.github.com/syuemingfang/syuemingfang-codemini/master/comment.json)

****************************************************************************************************/

/*!

+ Version: 0.1.0.3
+ Copyright © 2013 [Syue](mailto:syuemingfang@gmail.com). All rights reserved.
+ Date: *Thu Aug 29 2013 11:16:29 GMT+0800 (Central Standard Time)*
+ Includes:
  + PclZip

****************************************************************************************************/

//! 
//!## Class
class codemini{
  //!### codemini
  public $filename_zip;
  public $temp_dir; 
  public function __construct(){
  }
  public function readFile($filename){
    //!+ **readFile**
    $str=null;
    if(file_exists($filename)){
      $file=fopen($filename, 'r');
      if($file != null){
        while(!feof($file)){
          $str.=fgets($file)."\n";
        }
        fclose($file);
      }
    }
    return $str;
  } 
  public function writeFile($filename, $str){
    //!+ **writeFile**
    $file=fopen($filename, 'w');
    fwrite($file, $str);
    fclose($file);
  } 
  public function getContent($url){
    //!+ **getContent**
    $ch=curl_init();
    $options=array(CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "Google Bot", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true);
    curl_setopt_array($ch, $options);
    $content=curl_exec($ch)."\n";
    curl_close($ch);
    return $content;
  }
  public function clear($type, $str){
    //!+ **clear**
    $str=trim($str);
    $str=preg_replace('/\s(?=\s)/', '', $str);
    if($type == 'js'){
      $str=preg_replace('/(.[a-z)\}\'\"0-9])\t/', '$1;', $str);
      $str=preg_replace('/\}([^\,|\;])/s', '};$1', $str);
      $str=preg_replace('/\s*([\"|\'|:|\,|\{|\}|\)|\()])\s*/s', '$1', $str);
    }
    $str=preg_replace('/[\n\r\t]/', ' ', $str);
    return $str;
  }
  public function clearComment($type, $str){
    //!+ **clearComment**
    if($type == 'css'){
      $str=preg_replace('/\/\*.*?\*\//s', '', $str);
    } else if($type == 'js'){
      $str=preg_replace('/\/\*.*?\*\//s', '', $str);
      $str=preg_replace('/\/\/.*/', ' ', $str);
    } else if(($type == 'html') || ($type == 'htm')){
      $str=preg_replace('/<!--(.|\s)*?-->/', '', $str);
    }
    return $str;
  }
  public function upload(){
    //!+ **upload**
      $arr=array();
      $arr2=array();
      $match=array();      
      foreach($_FILES['ff']['error'] as $key => $error){
          if($error == UPLOAD_ERR_OK){
              $tmp_name=$_FILES['ff']['tmp_name'][$key];
              $name=$_FILES['ff']['name'][$key];
              move_uploaded_file($tmp_name, $this->temp_dir.'/'.$name);
              array_push($arr, $name);
          }
      }
      for($i=0; $i < count($arr); $i++){
        $file=fopen($this->temp_dir.'/'.$arr[$i], 'r');
        if($file != null){
          while(!feof($file)){
            $str.=fgets($file);
          }
          fclose($file);
          preg_match_all('/(.*)\.(.*)/is', $arr[$i], $match);
          $str=$this->clearComment($match[2][0], $str);
          $str=$this->clear($match[2][0], $str);
          $this->writeFile($this->temp_dir.'/'.$match[1][0].'.min.'.$match[2][0], $str);
          array_push($arr2, $this->temp_dir.'/'.$match[1][0].'.min.'.$match[2][0]);
          unlink($this->temp_dir.'/'.$arr[$i]);
        }
        $this->createZIP($this->filename_zip, $arr2);
      }
      exit;
  }
  public function createZIP($filename, $files){
    //!+ **createZIP**
    require_once('pclzip.lib.php');
    unlink($filename);
    $archive=new PclZip($filename);
    for($i=0; $i < count($files); $i++){
      $archive->add($files[$i], PCLZIP_OPT_REMOVE_PATH, $this->temp_dir);
    }
  }
  public function url($url){
    //!+ **url**
    $str=$this->getContent($url);
    preg_match_all('/(.*)\.(.*)/is', $url, $match);
    $str=$this->clearComment($match[2][0], $str);
    $str=$this->clear($match[2][0], $str);
    return $str;
  }
}

$main=new codemini();
if(!isset($_REQUEST['zone'])){
  header('Content-type: text/html');
  require('main.html');
} else{
  if($_REQUEST['zone'] == 'upload'){
    $main->temp_dir='temp'; 
    $main->filename_zip='mini.zip';  
    $main->upload();    
  } else if($_REQUEST['zone'] == 'url'){
    $main->temp_dir='temp'; 
    $str=$main->url($_REQUEST['url']);  
    echo $str;
  }
}
?>