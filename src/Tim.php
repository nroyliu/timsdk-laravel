<?php


namespace Nroyliu\Timsdk;


use Illuminate\Support\Facades\Http;

class Tim
{
    private static $appid = null;
    private static $key = null;
    private static $admin = null;
    private static $sig = null;

    public function __construct()
    {
        self::$appid = config("tim.appid");
        self::$key = config("tim.key");
        self::$admin = config("tim.admin");
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
        if (self::$appid != null && self::$key != null){
            $api = new \Tencent\TLSSigAPIv2(self::$appid, self::$key);
            $sig = $api->genSig($user);
            return $sig;
        }else{
            return "appid 和 key 不能为空";
        }
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

    public static function get_online_member_num($group_id){
        $data = [
            "GroupId" => $group_id
        ];
        return self::request('v4/group_open_http_svc/get_online_member_num', $data);
    }

    public static function get_push_url($streamName)
    {
        $domain = config('tim.push_domain');
        $key    = config('tim.key');
        $time   = date('Y-m-d H:i:s',strtotime('+1 day'));
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
        //txSecret = MD5( KEY + streamName + txTime )
        $txSecret = md5($key . $streamName . $txTime);
        $ext_str  = "?" . http_build_query(array(
                "txSecret" => $txSecret,
                "txTime"   => $txTime
            ));

        return "rtmp://" . $domain . "/live/" . $streamName . (isset($ext_str) ? $ext_str : "");
    }

    public static function get_player_url($streamName)
    {
        $domain = config('tim.player_domain');
        $key    = config('tim.key');
        $time   = date('Y-m-d H:i:s',strtotime('+1 day'));
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
        //txSecret = MD5( KEY + streamName + txTime )
        $txSecret = md5($key . $streamName . $txTime);
        $ext_str  = "?" . http_build_query(array(
                "txSecret" => $txSecret,
                "txTime"   => $txTime
            ));

        return "rtmp://" . $domain . "/live/" . $streamName . (isset($ext_str) ? $ext_str : "");
    }
}
