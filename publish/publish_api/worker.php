<?php
$worker= new GearmanWorker();
$worker->addServer('127.0.0.1', 4730);
$worker->addFunction('no_taint', 'no_taint');
$worker->addFunction('githook_coding', 'githook_coding');
while ($worker->work());
function no_taint($job)
{
       echo 'no_taint';
}
function githook_coding($job)
{
       shell_exec('cd /app; git pull');
       $workload=$job->workload();
       $json = json_decode($workload,true);
       echo 'start'.PHP_EOL;
       //获得最后一条注释
       $message = $json['commits'][0]['short_message'];
       $to_mail = $json['commits'][0]['committer']['email'];
       $owner_mail = $json['repository']['owner']['email'];
       $owner_name = $json['repository']['owner']['name'];
       $project_name = $json['repository']['description'].'('.$json['repository']['name'].')';

       if(false!==strpos($message,'@branch'))
       {
            echo 'branch'.PHP_EOL;
            //切换分支
             preg_match('/@branch (.*)/',$message,$match);
             $switch_branch=trim($match[1]);
             $ret=shell_exec('cd /app;git checkout '.$switch_branch.' 2>&1');
             if(false!==strpos($ret,'did not match any file'))
             {
                  $ret=shell_exec('cd /app;git checkout -b '.$switch_branch.' origin/'.$switch_branch.' 2>&1');
             }
             if(false===strpos($ret,'Switched') && false===strpos($ret,'Already on'))
             {
                   send_message($to_mail,'切换分支失败','切换到分支['.$switch_branch.']时失败,'.$ret);
                   return ;
             }
             else
             {
                   send_message($to_mail,'切换分支成功','分支已经切换到['.$switch_branch.']');
             }
        }

        $publish=false!==strpos($message,'@publish')?true:false;
        if($publish)
        {
              //切换到master
              $status_ret=shell_exec('cd /app;git status');
              preg_match('/On branch (.*)/',$status_ret,$match);
              $branch=$match[1];
              if('master'!=$branch)
              {
                  shell_exec('cd /app;git checkout master;');
                  echo 'git checkout master ok';
              }
         }

        echo 'git pull'.PHP_EOL;
        shell_exec('cd /app;git pull');

        if($publish)
        {

            echo 'publish'.PHP_EOL;
            if(file_exists('/test.sh'))
            {

                echo 'test'.PHP_EOL;
                 $ret=shell_exec('/test.sh'); 
                 if(!$ret_json=json_decode($ret,true))
                 {
                    return ;
                 }
                 if('success' == $ret_json['status'])
                 {
                    //发布代码
                    shell_exec('cp -r /app/* /publish_codedir'); 
                  //send_message($owner_mail,'代码上线,项目：'.$project_name,'项目'.$project_name.'代码申请上线');
                    send_message($to_mail,'代码上线审核','项目：'.$project_name.'申请上线，请联系'.$owner_name.'('.$owner_mail.')审核代码'); 
                 }
                 else
                 {
                    send_message($to_mail,'代码上线时单元测试错误,项目:'.$project_name,$ret_json['result']);
                 }
            }
            if('master'!=$branch)
            {
                 shell_exec('cd /app;git checkout '.$branch);
            }
        }
        echo 'finish'.PHP_EOL;
}


function send_message($to,$title,$content)
{
    //TODO 信息发到slack
    echo $title.'>>'.$content.PHP_EOL;
}
