<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;


class SearchPtController extends AppController
{
    protected $orca_conn;
    
    public function initialize(): void
    {
        parent::initialize();
        
        $this->loadComponent('MrCommon');
        
        $this->viewBuilder()->setLayout('mr-common');

        $this->orca_conn = ConnectionManager::get('orca');
    }

    
    public function beforeFilter(\Cake\Event\EventInterface $event) 
    {
        parent::beforeFilter($event);
            
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $userid = $this->request->getAttribute('identity')->userid;
            
            $users = TableRegistry::getTableLocator()->get('Users');
            $query = $users->find()->select(['is_entering_newpw'])->where(['userid'=>$userid]);
            if ($query->first()->is_entering_newpw) {
                return $this->redirect('/users/login'); // パスワード変更中はログインに戻す。
            }
        }    
    }
    
    
// action ---------------------------------------------------------------

    
    public function index()
    {    
        $ptlist_header = ['患者番号', '氏名', 'カナ氏名', '性別', '生年月日', '電話番号'];
        $ptlist = [];        
        $this->set('ptlist_header', $ptlist_header);
        $this->set('ptlist_body', $ptlist);
    
        $data_for_search = $this->request->getData('data_for_search', '');

        if ($data_for_search) {
            //空白トリムするために全角空白を半角に
            $data_for_search = mb_convert_kana($data_for_search,'s');
            $data_for_search = trim($data_for_search);
            //文字列中の半角空白を全角に戻す。
            $data_for_search = mb_convert_kana($data_for_search,'S');
            $ptlist = $this->get_ptlist($data_for_search);
            $this->set('ptlist_header', $ptlist_header);
            $this->set('ptlist_body', $ptlist);
        }
    }


// methods -----------------------------------------------------------


    public function get_ptid(string $data_for_search = '')
    {
        if ($data_for_search === '') return [];

        //　英字を大文字に。
        $data_for_search = mb_convert_case($data_for_search, MB_CASE_UPPER);
        // まず、すべて全角化。
        $data_for_search = mb_convert_kana($data_for_search, 'NS');

        // 半角化してすべて数字ならば患者番号とみなす。
        if (is_numeric(mb_convert_kana($data_for_search, 'n'))) {
            $data_for_search = mb_convert_kana($data_for_search, 'n');
            // 8桁にゼロパディング。
            $data_for_search = str_pad($data_for_search, 8, '0', STR_PAD_LEFT);
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

        $query = $this->orca_conn->newQuery();
        switch ($data_type) {
        case 'number':
            $query->select('ptid')->from('tbl_ptnum')->where(['ptnum'=>$data_for_search]);
            $ptid_list = $query->execute()->fetchAll('assoc');
            break;
        case 'katakana':
            $query->select('ptid')->from('tbl_ptinf')
                    ->where(['kananame LIKE'=>('%'. $data_for_search . '%')])
                    ->order('kananame');
            $ptid_list = $query->execute()->fetchAll('assoc');
            break;
        case 'name_probable':
            $query->select('ptid')->from('tbl_ptinf')
                    ->where(['name LIKE'=>'%'. $data_for_search . '%'])
                    ->order('kananame');
            $ptid_list = $query->execute()->fetchAll('assoc');
            break;
        default:
            $ptid_list = [];
            break;
        }        
        
        return $ptid_list;
    }

    
    public function get_ptlist($data_for_search = null)
    {
        if(!$data_for_search) return [];
        
        $ptid_list = $this->get_ptid($data_for_search);
        if (!$ptid_list) return [];
        
        $ptlist = [];
        foreach ($ptid_list as $obj) {            
            $ptid = (string)$obj['ptid'];
            
            $query = $this->orca_conn->newQuery();
            $query->select('ptnum')->from('tbl_ptnum')->where(['ptid'=>$ptid]);
            $arr = $query->execute()->fetch('assoc');
            $ptnumber = preg_replace('/^0+/', '', $arr['ptnum']);
            
            $query = $this->orca_conn->newQuery();
            $query->select(['kananame', 'name', 'sex', 'birthday', 'home_tel1'])
                  ->from('tbl_ptinf')->where(['ptid'=>$ptid]);
            $arr2 = $query->execute()->fetch('assoc');
            
            $birthday = preg_replace('/(^.{4})(.{2})(.{2})/', '${1}-${2}-${3}', $arr2['birthday']);
            $birthday = '(' . $this->MrCommon->get_gengo_year($birthday) . ')' . $birthday;
            
            $ptlist[] = 
                [ 
                    Text::insert('<a href=:url?ptnumber=:ptnumber target="_blank">:ptnumber</a>', 
                                 ['url'=>'/mr/headlines', 'ptnumber'=>$ptnumber]),
                    $arr2['name'], 
                    $arr2['kananame'],
                    $arr2['sex'] === '1' ? '男' : '女', 
                    $birthday, 
                    $arr2['home_tel1']
                ];
        }
        
        return $ptlist;
    }
    
    
}
