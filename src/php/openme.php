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

function openme_web_to_array($web, $prefix, $remove=true)
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

function openme_web_file_to_array($web)
{
  $r=array();
  foreach ($web as $key => $value)
  {
    if (array_key_exists('tmp_name', $value))
    {
       $r[$key.'_uploaded']=$value['tmp_name'];
    }
  }
  return $r; 
}

function openme_web_err($cfg, $tp, $err, $str)
{
  if ($tp=='json')
  {
    $a=array('return' => strval($err), 'error' => $str);
    $s=json_encode($a);
  }
  else if ($tp=='con')
  {
    $s=$str;
  }
  else
  {
    $tp='html'; 
    $s='<html><body><pre>'.$str.'</pre></html></body>';
  }

  return openme_web_out($cfg, $tp, $s, '');
}

function openme_web_out($cfg, $tp, $str, $filename)
{
  if ($tp=='' || $tp=='web')
     $tp='html';

  $tpx1=$cfg['content_types'];
  if (!array_key_exists($tp, $tpx1))
     $tp='unknown';

  $tpx=$cfg['content_types'][$tp];

  foreach ($tpx as $key => $value) 
  {
    $x=$key.': '.$value;
    $y=str_replace('$#filename#$',$filename, $x);
    header($y); 
  }

  print $str;

  return;
}

function openme_ck_access($i, $output=true)
{
 # Convert to json and call CK
 # FGG: in the future we may want to connect through socket

 # Get action
 if (!array_key_exists('action', $i))
    return array("return"=>1,"error"=>"action is not defined");

 $action=$i["action"];
 unset($i["cm_action"]);

 # Decode dict to json and save to temp file
 $str=json_encode($i);
 $ftmp=tempnam("", "ck-");
 $f=fopen($ftmp, "w");
  fwrite($f, $str);
 fclose($f);

 # Prepare call to CK
 $ckr=getenv("CK_ROOT"); 
 if ($ckr=="") $ckr=getcwd();
 $ck=$ckr . '/bin/ck';

 $cmd=$ck." ".$action." @".$ftmp;

 #Add cmd if Windows (FGG:TODO maybe can be done cleaner?)
 if (substr(strtoupper(PHP_OS),0,3)=="WIN")
    $cmd="cmd /c ".$cmd;

 #FGG: important note for Windows: stderr pipe should be "a" not "w"
 #     see http://www.php.net/manual/en/function.proc-open.php   Luceo 28-Mar-2010 07:39
 $tmpfname = tempnam("", "ck-err-");
 $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("file", $tmpfname, "w"));

 $process = proc_open($cmd, $descriptorspec, $pipes, NULL, NULL);

 $tout='';
 $terr='';
 $rv=0;
 if (is_resource($process)) 
 {
    fclose($pipes[0]);

    $tout=stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $rv = proc_close($process);
  }

  # Read and delete temporal file with error output
  $terr=file_get_contents($tmpfname);
  unlink($tmpfname);

  if ($output)
  {
    echo $tout;
    echo $terr;
  }

  # Delete temporal input file
  if (file_exists($ftmp)) 
     unlink($ftmp);

  $r=array("return"=>$rv, "stdout"=>$tout, "stderr"=>$terr, "std"=>$tout.$terr);

  if ($rv>0)
    $r["error"]=$tout.$terr;

  return $r;
}

function urlsafe_b64decode($s) 
{
# return str_replace(array('-','_','.'), array('+','/','='), $s);
 return base64_decode(str_replace(array('-','_'), array('+','/'), $s));
}

?>
