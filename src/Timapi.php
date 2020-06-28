<?php


namespace Nroyliu\Timapi;


use Illuminate\Support\Facades\Http;

class Timapi
{
    private static $appid = null;
    private static $key = null;
    private static $admin = null;
    private static $sig = null;

    public function __construct()
    {
        self::$appid = config("timapi.appid");
        self::$key = config("timapi.key");
        self::$admin = config("timapi.admin");
        self::$sig = self::getSig('admin');
    }

    /**
     * getSig
     * @param $user
     *
     * @return string
     */
    public static function getSig($user)
    {
        $api = new \Tencent\TLSSigAPIv2(self::$appid, self::$key);
        $sig = $api->genSig($user);
        return $sig;
    }

    public static function request($method, $data)
    {
        $sig = self::$sig;
        $appid = self::$appid;
        $admin = self::$admin;
        $url = "https://console.tim.qq.com/$method?sdkappid=$appid&identifier=$admin&usersig=$sig&random=955559&contenttype=json";
        return Http::post($url,$data);
    }

    public static function addUser($username,$name,$faceurl){
        $data = [
            "Identifier" => $username,
            "Nick"=> $name,
            "FaceUrl"=> $faceurl
        ];
        return self::request('v4/im_open_login_svc/account_import', $data);
    }

    public static function addRoom($user, $name){
        $data = [
            "Owner_Account" => $user,
            "Type"=> "AVChatRoom",
            "Name"=> $name
        ];
        return self::request('v4/group_open_http_svc/create_group', $data);
    }
}
