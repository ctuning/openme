<?php

/*

 OpenME - Event-driven, plugin-based interactive interface to "open up" 
          any software and connect it to cM or CK

 Developer: Grigori Fursin
 http://cTuning.org/lab/people/gfursin

 (C)opyright 2014 cTuning foundation

 This library is free software; you can redistribute it and/or
 modify it under the terms of the GNU Lesser General Public
 License as published by the Free Software Foundation; either
 version 2.1 of the License, or (at your option) any later version.

 This library is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 Lesser General Public License for more details.

 You should have received a copy of the GNU Lesser General Public
 License along with this library; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

*/

function cm_convert_all_web_vars_to_array($prefix, $remove=true)
{
  return array_merge(cm_web_to_array($_GET, $prefix, $remove), cm_web_to_array($_POST, $prefix, $remove));
}

function cm_web_to_array($web, $prefix, $remove=true)
{
 if ($prefix=="")
   $r=$web;
 else
   $r=array();
   foreach ($web as $key => $value)
     if ($prefix==substr($key, 0, strlen($prefix)))
     {
       if ($remove) $key=substr($key,strlen($prefix));
       $r[$key]=$value;
     }
 return $r; 
}

function cm_access($array, $output=true)
{
 # Convert to cM format and call cm front-end
 # FGG: in the future we may want to connect through socket

 $module=$array["cm_run_module_uoa"]; unset($array["cm_run_module_uoa"]);
 $action=$array["cm_action"];   unset($array["cm_action"]);

 $str=json_encode($array);
 $n=tempnam("", "cm-");
 $f=fopen($n, "w"); fwrite($f, $str); fclose($f);

 $cm_root=getenv("CM_ROOT"); if ($cm_root=="") $cm_root=getcwd();
 $cm_dir_bin="bin";

 # Prepare call to CM plugin (hardwired)
 $cmd=$cm_root.DIRECTORY_SEPARATOR.$cm_dir_bin.DIRECTORY_SEPARATOR.$cmd="cm ".$module." ".$action." @".$n;

 #Add cmd if Windows (FGG:TODO maybe can be done cleaner?)
 if (substr(strtoupper(PHP_OS),0,3)=="WIN")
    $cmd="cmd /c ".$cmd;

 #FGG: important note for Windows: stderr pipe should be "a" not "w"
 #     see http://www.php.net/manual/en/function.proc-open.php   Luceo 28-Mar-2010 07:39
 $tmpfname = tempnam("", "cmerr-");
 $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("file", $tmpfname, "w"));

 $process = proc_open($cmd, $descriptorspec, $pipes, NULL, NULL);

 $text1='';
 $text2='';
 if (is_resource($process)) 
 {
    fclose($pipes[0]);

    $text1=stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $return_value = proc_close($process);
  }

  $text2=file_get_contents($tmpfname);
  unlink($tmpfname);

  if ($output)
  {
    echo $text1;
    echo $text2;
  }

  if (file_exists($n)) unlink($n);

  return $text1.$text2;
}

function prepare_json_from_array($a, $prefix)
{
  $b=array();
  foreach ($a as $key => $value) 
  {
    if ($prefix!="") 
    {
      $x=strpos($key, $prefix);
      if (($x!==false) && ($x==0))
        $key=substr($key,strlen($prefix));
    }
    $b[$key]=$value;
  }

  return $b;
}

function prepare_str_from_array($a, $prefix)
{
  $str="";
  foreach ($a as $key => $value) 
  {
    if ($prefix!="") 
    {
      $x=strpos($key, $prefix);
      if (($x!==false) && ($x==0))
        $key=substr($key,strlen($prefix));
    }
    $str.=" "+$key."=".$value;
  }

  return $b;
}

function get_var($a, $p)
{
  if (!array_key_exists($p, $a)) return NULL;
  return $a[$p];
}  

function check_cm_json($s)
{
  $s=str_replace('^22^', '"', $s);
  $s=str_replace('%22', '"', $s);
  $s=str_replace('%26quot;', '"', $s);
  $s=str_replace('&quot;', '"', $s);

  $r=json_decode($s,TRUE);

  return $r;                                                                                                                                                                               
}

function urlsafe_b64decode($s) 
{
# return str_replace(array('-','_','.'), array('+','/','='), $s);
 return base64_decode(str_replace(array('-','_'), array('+','/'), $s));
}

?>
