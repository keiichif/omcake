<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;


class SearchPtController extends AppController
{

    public $str_for_orca_conn = 'host=localhost dbname=orca user=orca';

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('MrCommon');
        
        if (!$this->MrCommon->is_allowed_ip()) $this->redirect ('/forbidden');
    
        $this->viewBuilder()->setLayout('mr-common');        
    }

    
// action ---------------------------------------------------------------

    
    public function index()
    {    
        $ptlist_header = ['患者番号', '氏名', 'カナ氏名', '性別', '生年月日', '電話番号'];
        $ptlist = [];        
        $this->set('ptlist_header', $ptlist_header);
        $this->set('ptlist_body', $ptlist);
    
        $data_for_search = $this->request->getData('data_for_search');

        if ($data_for_search) {
            //全角空白を半角に
            $data_for_search = mb_convert_kana($data_for_search,'s');
            $data_for_search = trim($data_for_search);
            //文字列中の半角空白を全角に戻す。
            $data_for_search = mb_convert_kana($data_for_search,'S');
            $ptlist = $this->get_ptlist($data_for_search);
            $this->set('ptlist_header', $ptlist_header);
            $this->set('ptlist_body', $ptlist);
        }
    }


    public function get_ptid($data_for_search)
    {
        if ($data_for_search === null) return [];

        //　英字を大文字に。
        $data_for_search = mb_convert_case($data_for_search, MB_CASE_UPPER);
        // まず、すべて全角化。
        $data_for_search = mb_convert_kana($data_for_search, 'NS');

        // 半角化してすべて数字ならば患者番号とみなす。
        if (is_numeric(mb_convert_kana($data_for_search, 'n'))) {
            $data_for_search = mb_convert_kana($data_for_search, 'n');
            // 8桁にゼロパディング。
            $data_for_search = str_pad($data_for_search, 8, 0, STR_PAD_LEFT);
            $data_type = 'number';
        // すべて「ひらがな」または「カタカナ」（空白を途中に含んでもよい）ならば
        } elseif (!preg_match('/[^ぁ-んア-ヶー　]/u', $data_for_search)){
            // カナにコンバート(C)。
            $data_for_search = mb_convert_kana($data_for_search,'C');
            $data_type = 'katakana';
        } else {
            $data_type = 'name_probable';
            $data_for_search = mb_convert_kana($data_for_search,'AS');
        }

        $pg_conn = pg_connect($this->str_for_orca_conn);                       
        switch ($data_type) {
        case 'number':
            $command = "SELECT ptid FROM tbl_ptnum WHERE ptnum = " 
                        . pg_escape_literal($data_for_search); 
            $result = pg_query_params($command, []);
            $ptid_list = pg_fetch_all($result, PGSQL_ASSOC);
            break;
        case 'katakana':
            $command = "SELECT ptid FROM tbl_ptinf WHERE kananame~"
                        . pg_escape_literal($data_for_search)
                        . " ORDER BY kananame";
            $result = pg_query_params($command, []);
            $ptid_list = pg_fetch_all($result, PGSQL_ASSOC);
            break;
        case 'name_probable':
            $command = "SELECT ptid FROM tbl_ptinf WHERE name~"
                        . pg_escape_literal($data_for_search)
                        . " ORDER BY kananame";
            $result = pg_query_params($command, []);
            $ptid_list = pg_fetch_all($result, PGSQL_ASSOC);
            break;
        default:
            $ptid_list = [];
            break;
        }        
        pg_close($pg_conn);

        return $ptid_list;
    }

    
    public function get_ptlist($data_for_search)
    {

        if(!$data_for_search) return [];
        
        $ptid_list = $this->get_ptid($data_for_search);
        if (!$ptid_list) return [];
        
        $pg_conn = pg_connect($this->str_for_orca_conn);                       
        $ptlist = [];
        foreach ($ptid_list as $obj) {
            
            $ptid = (string)$obj['ptid'];
            
            $command = "SELECT ptnum FROM tbl_ptnum WHERE ptid = " 
                                            . pg_escape_string($ptid);
            $result = pg_query_params($command, []);
            $arr = pg_fetch_assoc($result);
            $ptnumber = preg_replace('/\A0+/', '', $arr['ptnum']);
            
            $command = "SELECT kananame, name, sex, birthday, home_tel1 "
                        . "FROM tbl_ptinf WHERE ptid = " . pg_escape_string($ptid);
            $result = pg_query_params($command, []);
            $arr2 = pg_fetch_assoc($result);
            $birthday = preg_replace('/(\A.{4})(.{2})(.{2})/', '${1}-${2}-${3}', 
                                                              $arr2['birthday']);
            
            $birthday = '(' . $this->MrCommon->get_gengo_year($birthday) . ')' . $birthday;
            
            $ptlist[] = [ 
                    '<a href="/mr/headlines?ptnumber=' . $ptnumber . '"' 
                        . ' target="_blank" rel="noopener noreferrer"' . '>' 
                        . $ptnumber . '</a>', 
                    $arr2['name'], 
                    $arr2['kananame'],
                    $arr2['sex'] == 1 ? '男' : '女', 
                    $birthday, 
                    $arr2['home_tel1']
                ];
        }
        
        return $ptlist;
    }
    
    
}
