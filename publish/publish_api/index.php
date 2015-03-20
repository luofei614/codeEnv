<?php
$client= new GearmanClient();
$client->addServer('127.0.0.1', 4730);
if(!extension_loaded('taint') || !ini_get('taint.enable'))
{
    $client->doBackground('no_taint', 'no_taint');
}
else
{
    $input = file_get_contents('php://input');
    if($input)
    {
        $client->doBackground('githook_coding',$input);
    }
}

echo "publish api";
