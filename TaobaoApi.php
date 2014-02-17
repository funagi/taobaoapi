<?php
/**
 * 淘宝API处理类
 * User: Hugo
 * Date: 14-2-8
 * Time: 下午5:25
 */
define('TAOBAO_KEY', '1021732423');
define('TAOBAO_SECRET', 'sandbox13aec376ec7002404409ef595');
class TaobaoApi
{
    static $snoopy;
    //淘宝接口URL
    static $api_url = 'http://gw.api.tbsandbox.com/router/rest';
    static $version = '2.0';
    static $format = 'xml';
    static $signMethod = 'md5';

    //初始化HTTP请求
    static function instanceSnoopy()
    {
        if(! self::$snoopy instanceof Snoopy)
        {
            self::$snoopy = new Snoopy();
        }
    }

    /**
     * 提交请求
     * @param $params
     *
     * @return mixed
     */
    static function submit($params)
    {
        if(empty($params['method'])) return fasle;
        self::instanceSnoopy();
        //组装系统参数
        $sysParams["app_key"] = TAOBAO_KEY;
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams['v'] = self::$version;
        $sysParams['format'] = self::$format;
        $sysParams['sign_method'] = self::$signMethod;
        $params = array_merge($sysParams, $params);
        $params['sign'] = self::createSign($params);
        self::$snoopy->submit(self::$api_url, $params);
        $result = self::$snoopy->results;
        switch(self::$format)
        {
            case 'json':
                $result = json_decode($result);
                break;
            case 'xml':
                $result = @simplexml_load_string($result);
                break;
        }
        //返回结果
        return $result;
    }

    /**
     * 签名
     * @param $paramArr
     *
     * @return string
     */
    static function createSign ($paramArr)
    {
        $sign = TAOBAO_SECRET;
        ksort($paramArr);
        foreach ($paramArr as $key => $val)
        {
            if ($key != '' && $val != '')
            {
                $sign .= $key.$val;
            }
        }
        $sign .= TAOBAO_SECRET;
        $sign = strtoupper(md5($sign));
        return $sign;

    }

    /**
     * 组参函数
     * @param $paramArr
     *
     * @return string
     */
    static function createStrParam ($paramArr)
    {
        $strParam = '';
        foreach ($paramArr as $key => $val)
        {
            if ($key != '' && $val != '')
            {
                $strParam .= $key.'='.urlencode($val).'&';
            }
        }
        return $strParam;
    }
}