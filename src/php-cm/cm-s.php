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

 $form6='#form6'; # form for file upload - needed for server

 session_start();

 # initalize path to CM (either already defined in CM_ROOT or expected to share the same directory)
 $cm_root=getenv("CM_ROOT"); if ($cm_root=="") $cm_root=getcwd();

 # initalize path to CM TMP
 $cm_tmp=getenv("CM_TMP"); if ($cm_tmp=="") $cm_tmp=$cm_root."/tmp";

 # read config (to check auth, etc)
 $cm_default_cfg=getenv("CM_DEFAULT_CFG");
   if ($cm_default_cfg=="") $cm_default_cfg=$cm_root."/.cmr/kernel/default/.cm/data.json";
 $f=file_get_contents($cm_default_cfg);
 $cfg=json_decode($f,true);

 require_once 'cm.php';

 # get web environment variables
 $get=cm_web_to_array($_GET, "");
 $post=cm_web_to_array($_POST, "");
 $session=$_SESSION;
 $cookie=$_COOKIE;
 
 # Process cm_json
 if (array_key_exists("cm_json", $post))
 {
   $jd=check_cm_json($post["cm_json"]);
   unset($post["cm_json"]);
   $post=array_merge($post, $jd);
 }

 if (array_key_exists("cm_json", $get))
 {
   $jd=check_cm_json($get["cm_json"]);
   unset($get["cm_json"]);
   $get=array_merge($get, $jd);
 }

 # Process file upload
 if ( (array_key_exists('cm_file_upload',$post) && strlen($post['cm_file_upload'])>0) ||
      (array_key_exists('cm_file_upload_base64',$post) && strlen($post['cm_file_upload_base64'])>0) ||
      array_key_exists($form6.'##cm_file_upload',$_FILES))
 {
   # Generate tmp file
   $ii=array();
   $ii['cm_run_module_uoa']='core';
   $ii['cm_action']='gen_cm_tmp_file';
   $ii['cm_console']='json';
   $rr=trim(cm_access($ii, false));
   $r=json_decode($rr,true);
   if ($r==NULL) 
   {
     echo '<br><b>cM front-end error:</B> can\'t parse json output during preprocessing!</b><br><br>';
     echo '<pre><b>Module output:</b><br><br>';
     echo $rr;
     echo '</pre><br>';
     return 1;
    }
    else if ($r['cm_return']>0)
    {
      echo '<BR><B>cM error:</B> '.$r['cm_error'].'!<BR>';
      return 1;
    }
       
    $tf=$r['cm_path'];
    $uid=$r['cm_uid'];

    $y='';
    if (array_key_exists($form6.'##cm_file_upload_name',$get)) $y=$get[$form6.'##cm_file_upload_name'];
    if (array_key_exists($form6.'##cm_file_upload_name',$post)) $y=$post[$form6.'##cm_file_upload_name'];

    if (array_key_exists($form6.'##cm_file_upload',$_FILES))
    {
       $xn=$_FILES[$form6.'##cm_file_upload'];
       $yn=$form6.'##cm_file_upload_tmp_uid';
       if ($y=='') $post[$form6.'##cm_file_upload_name']=$xn['name'];
       if (!move_uploaded_file($xn['tmp_name'], $tf))
         echo "Internal problem moving tmp file ... Please, try again or report problem to developers!";
    }
    else
    {
      if (array_key_exists('cm_file_upload',$post) && strlen($post['cm_file_upload'])>0)
      {
         $xn=$post['cm_file_upload'];
         unset($post['cm_file_upload']);
         $yn='cm_file_upload_tmp_uid';
      }
      else
      {
         $xn=urlsafe_b64decode($post['cm_file_upload_base64']);
         unset($post['cm_file_upload_base64']);
         $yn='cm_file_upload_tmp_uid';
      }

      $handle = fopen($tf, "wb");
      if ($handle!=NULL)
        fwrite($handle, $xn);
      fclose($handle);
    }

    $post[$yn]=$uid;
 }

 # Force delete of cm_user_password2 which is used only in session or cookies
 if (array_key_exists("cm_user_password2", $post)) unset($post["cm_user_password2"]);
 if (array_key_exists("cm_user_password2", $get)) unset($get["cm_user_password2"]);

 # Force delete of cm_admin - it can be only done internally
 if (array_key_exists("cm_admin", $post)) unset($post["cm_admin"]);
 if (array_key_exists("cm_admin", $get)) unset($get["cm_admin"]);

 # Preprocessing web request (we move most of the logic from php to cM python modules).
 # Later, it will be easier to use this functionality in cM standalone python web server.
 $i=array();
 $i['cm_run_module_uoa']='web';
 $i['cm_action']='preprocess';
 $i['cm_console']='json';
 $i['cm_web_session']=$session;
 $i['cm_web_cookies']=$cookie;
 $i['cm_web_get']=$get;
 $i['cm_web_post']=$post;
 $i['cm_web']='yes'; # web environment - may be used to force authentication!

 # Pack username & password if in COOKIE OR SESSION OR POST
 if (array_key_exists("cm_user_uoa", $cookie))        $i['cm_user_uoa']=$cookie['cm_user_uoa'];
 if (array_key_exists("cm_user_uoa", $session))       $i['cm_user_uoa']=$session['cm_user_uoa'];
 if (array_key_exists("cm_user_uoa", $post))          $i['cm_user_uoa']=$post['cm_user_uoa'];
 if (array_key_exists("cm_user_password", $post))     $i['cm_user_password']=$post['cm_user_password'];
 if (array_key_exists("cm_user_password1", $post))    $i['cm_user_password1']=$post['cm_user_password1'];
 if (array_key_exists("cm_user_password1", $get))     $i['cm_user_password1']=$get['cm_user_password1'];
 if (array_key_exists("cm_user_password2", $cookie))  $i['cm_user_password2']=$cookie['cm_user_password2'];
 if (array_key_exists("cm_user_password2", $session)) $i['cm_user_password2']=$session['cm_user_password2'];

 # Check if detach console (only in the postprocessing)
 $cdc='';
 if (array_key_exists("cm_detach_console", $post)) {$cdc=$post["cm_detach_console"]; unset($post["cm_detach_console"]);} 
 if (array_key_exists("cm_detach_console", $get)) {$cdc=$get["cm_detach_console"]; unset($get["cm_detach_console"]);} 
                
 $rr=trim(cm_access($i, false));
 $r=json_decode($rr,true);

 if ($r==NULL) 
 {
   echo '<br><b>cM front-end error:</B> can\'t parse json output during preprocessing!</b><br><br>';
   echo '<pre><b>Module output:</b><br><br>';
   echo $rr;
   echo '</pre><br>';
   return 1;
 }
 else
 {
    #Check console for output if errors!
    $cm_console='web';
    if (array_key_exists('cm_console', $get)) $cm_console=$get['cm_console'];
    else if (array_key_exists('cm_console', $post)) $cm_console=$post['cm_console'];
        
    if (array_key_exists('cm_return', $r) && $r['cm_return']>0)
    {
       header("Content-type: text/html");
       if ($cm_console=='json')
       {
          echo json_encode($r);
       }
       else
       {
         echo '<html><body><BR><B>cM error:</B> '.$r['cm_error'].'!<BR></body></html>';
       }
       return 1;
    }

    if (array_key_exists('cm_stderr', $r) && $r['cm_stderr']!='')
    {
       header("Content-type: text/html");
       if ($cm_console=='json')
       {
          $et='cM failure - please report to developers';
          if (array_key_exists('report_to_developers',$cfg)) $et=$et.' '.(string)$cfg['report_to_developers'];
          $et=$et.':\n\n';
          if (array_key_exists('cm_stdout', $r) && $r['cm_stdout']!='') $et=$et.'STDOUT:\n'.$r['cm_stdout'].'\n\n';
          $et=$et.'STDERR:\n'.$r['cm_stderr'];
          $rx=array();
          $rx['cm_return']=33;
          $rx['cm_error']=$et;
          echo json_encode($rx);
       }
       else
       {
          $et='<html><body><BR><B>cM failure - please report to developers';
          if (array_key_exists('report_to_developers',$cfg)) $et=$et.' '.(string)$cfg['report_to_developers'];
          $et=$et.':\n\n';
          if (array_key_exists('cm_stdout', $r) && $r['cm_stdout']!='') $et=$et.'<B>STDOUT:</B><BR><pre>'.$r['cm_stdout'].'</pre><BR><BR>';
          $et=$et.'<B>STDERR:</B><BR><pre>'.$r['cm_stderr'].'</pre></body></html>';
          echo $et;
       }
       return 1;
    }
 }                                                                                                                                                                                                               

 # Check if need to destroy cookies
 if (get_var($r, 'cm_web_destroy_cookies')=='yes')
 {
   $vars=get_var($r, 'cm_web_cookies');
   if ($vars!=NULL)
     foreach ($vars as $value)
       setcookie($value, '', time()-3600);
 }

 # Check if need to destroy session
 if (get_var($r, 'cm_web_destroy_session')=='yes')
 {
   $_SESSION = array();
   session_destroy();
 }

 # Check if set cookies
 if (get_var($r, 'cm_web_set_cookies')=='yes')
 {
   $vars=get_var($r, 'cm_web_cookies');
   $time=get_var($r, 'cm_web_cookies_expire_time');
   if ($time==NULL) $time=604800; # 1 week by default

   if ($vars!=NULL)
     foreach ($vars as $value)
       setcookie($value[0], $value[1], time()+intval($time));
 }

 # Check if set session
 if (get_var($r, 'cm_web_set_session')=='yes')
 {
   $vars=get_var($r, 'cm_web_vars');

   if ($vars!=NULL)
     foreach ($vars as $value)
       $_SESSION[$value[0]]=$value[1];
 }

 # Check if set header
 if (get_var($r, 'cm_web_set_headers')=='yes')
 {
   $vars=get_var($r, 'cm_web_headers');
   if ($vars!=NULL)
     foreach ($vars as $value)
       header($value);
 }

 # Check if download
 if (get_var($r, 'cm_web_download')=='yes')
 {
   #read raw file
   if (array_key_exists('cm_full_filename', $r)==true)
   {
     if (file_exists($r['cm_full_filename'])==true)
     {
       $handle = fopen($r['cm_full_filename'], "rb");
       if ($handle!=NULL)
       {
         while (!feof($handle))
         {
           $s=fread($handle, 8192);
           print $s;
         }
         fclose($handle);
       }
     }
   }
 }

 # Check if need to delete file
 if (get_var($r, 'cm_delete_file_after_view')=='yes')
   if (get_var($r, 'cm_full_filename')!='')
     unlink(get_var($r, 'cm_full_filename'));

 # Check if postprocess
 if (get_var($r, 'cm_web_postprocess')=='yes')
 {
   # Postprocessing web request (we move most of the logic from php to cM python modules).
   # Later, it will be easier to use this functionality in cM standalone python web server.
   $i1=array();
   $i1['cm_run_module_uoa']='web';
   $i1['cm_action']='postprocess';
   $i1['cm_web_preprocess_return']=$r;
   $i1['cm_web_session']=$_SESSION;
   $i1['cm_web_get']=$get;
   $i1['cm_web_post']=$post;
   $i1['cm_web']='yes'; # web environment - may be used to force authentication!

   # Pack username & password if in COOKIE OR SESSION OR POST
   if (array_key_exists("cm_user_uoa", $cookie))        $i1['cm_user_uoa']=$cookie['cm_user_uoa'];
   if (array_key_exists("cm_user_uoa", $session))       $i1['cm_user_uoa']=$session['cm_user_uoa'];
   if (array_key_exists("cm_user_uoa", $post))          $i1['cm_user_uoa']=$post['cm_user_uoa'];
   if (array_key_exists("cm_user_password", $post))     $i1['cm_user_password']=$post['cm_user_password'];
   if (array_key_exists("cm_user_password1", $post))    $i1['cm_user_password1']=$post['cm_user_password1'];
   if (array_key_exists("cm_user_password1", $get))     $i1['cm_user_password1']=$get['cm_user_password1'];
   if (array_key_exists("cm_user_password2", $cookie))  $i1['cm_user_password2']=$cookie['cm_user_password2'];
   if (array_key_exists("cm_user_password2", $session)) $i1['cm_user_password2']=$session['cm_user_password2'];

   #Set console here!
   if (array_key_exists('cm_console', $get)==true) $i1['cm_console']=$get['cm_console'];
   else if (array_key_exists('cm_console', $post)==true) $i1['cm_console']=$post['cm_console'];
   else $i1['cm_console']='web';

   # Check console detaching
   if ($cdc!='') $i1['cm_detach_console']=$cdc;
   
   cm_access($i1, true);
 }

?>
