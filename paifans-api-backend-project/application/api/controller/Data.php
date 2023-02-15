<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 数据爬虫
 */

class Data extends Api
{
        protected $noNeedLogin = '*';
        protected $noNeedRight = '*';

        public function getContent(){
           $html_source = file_get_contents("https://dashboard.pi-blockchain.net");
           $res = $this->getTagData($html_source,'table','class','table table-centered table-nowrap mb-0');
           // PI TESTNET DASHBOARD
            $arr['pi'] = [
                'pi_value'=>$this->getTagData($html_source,'span','style',"font-size: 18px;"),
                'pi_name'=>$this->getTagData($html_source,'p','class',"mb-0")
            ];
            // Transactions Per Ledger
            preg_match_all("/<script.*?>(.*?)<\/script>/is",$html_source,$ms);
            $arr['tr'] = [
                'series'=>explode(',',$this->get_between($ms[0][17],'series: [','],')),
                'labels'=>explode('","',$this->get_between($html_source,'["','"],')),
            ];
            // 圆型
            $arr['round'] = $this->getTable($res[0]);
            // Top 10 Richest PI Accounts
            $arr['top'] = $this->getTable($res[1]);
            $this->success("请求成功！",$arr);
        }

    /**
     * @param $input
     * @param $start
     * @param $end
     * @return false|string
     * 字符串截取
     */
        public function get_between($input, $start, $end) {
        $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
        return $substr;
        }

    /**
     * @param $table_data
     * Table处理
     * @return array
     */
        public function getTable($table_data){
            $table_array = explode('<tr>',$table_data);
            $data = array();
            for($i=2;$i<count($table_array);$i++){
                $data[$i] = explode('</td>',$table_array[$i]);
                for($j = 0;$j<count($data[$i]);$j++){
                    $data[$i][$j] = preg_replace('/\s(?=\s)/','',trim(strip_tags($data[$i][$j])));
                }
                array_pop($data[$i]);
            }
            return $data;
        }
    /**
     * @param $html
     * @param $tag
     * @param $attr
     * @param $value
     * 数据爬取封装
     * @return mixed
     */
        public function getTagData($html,$tag,$attr,$value){
            $regex = "/<$tag.*?$attr=\".*?$value.*?\".*?>(.*?)<\/$tag>/is";
            preg_match_all($regex,$html,$matches,PREG_PATTERN_ORDER);
            return $matches[1];
        }


        /**
         * @return void
         *
         * 金色财经
         */
        public function noahList($id = 0){
            $url = "https://api.jinse.cn/noah/v2/lives?limit=10&reading=false&source=web&flag=down&id=".$id."&category=0";
            $res = json_decode($this->getSslPage($url),true);
            $data = [];
            // 数据处理
            if(!empty($res['list'][0]['lives'])){
               foreach ($res['list'][0]['lives'] as $v){
                   $data[] = [
                       'id'=>$v['id'],
                       'content_prefix'=>$v['content_prefix'],
                       'content'=>$v['content'],
                       'link_name'=>$v['link_name'],
                       'link'=>$v['link'],
                       'created_at'=>date("Y-m-d",$v['created_at'])
                   ];
               }

            }
            $top_id = 0;
            if(!empty($res['top_id'])){
                $top_id = $res['top_id'];
            }
            $bottom_id = 0;
            if(!empty($res['bottom_id'])){
                $bottom_id = $res['bottom_id'];
            }
            $list = [
                'top_id'=>$top_id,
                'bottom_id'=>$bottom_id,
                'list'=>$data
            ];
            $this->success("请求成功！",$list);

        }

    /**
     * @param $url
     * @return bool|string
     * get请求
     */
        public function getSslPage($url,$header = []) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if(!empty($header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }


    /**
     * @return void
     * 巴比特
     */
        public function babbitt(){
            $html_source = file_get_contents("https://www.8btc.com/flash");
            $res = $this->getTagData($html_source,'div','class','flash-item');
            $res1 = $this->getTagData($html_source,'div','class','ne-render-content');
            $data = [];
             // 数据处理
            foreach ($res as $k=>$v){
                foreach ($res1 as $key=>$vv){
                    if($k == $key){
                        $data[] = [
                            'title'=> $this->get_between($v,'<!----> <p data-v-52f33b17>','</p>'),
                            'content'=> $this->get_between($vv,'<p>','</p>'),
                            'date_time'=>$this->get_between($this->getTagData($v,'p','class','flash-time')[0],'<span  data-v-6bbd6a80 data-v-52f33b17>','</span>')
                        ];
                    }
                }
            }
            $this->success("请求成功！",$data);
        }


}