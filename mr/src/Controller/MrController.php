<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;


class MrController extends AppController
{
    protected $mr_conn;
    protected $userid;


    public function initialize(): void
    {
        parent::initialize();
        
        $this->loadComponent('MrCommon');
        $this->loadComponent('OrcaApi');
        
        $this->viewBuilder()->setLayout('mr-common');
        
        $this->mr_conn = ConnectionManager::get('mr');
    }

    
    public function beforeFilter(\Cake\Event\EventInterface $event) 
    {
        parent::beforeFilter($event);
            
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $this->userid = $this->request->getAttribute('identity')->userid;
            
            $users = TableRegistry::getTableLocator()->get('Users');
            $query = $users->find()->select(['is_entering_newpw'])->where(['userid'=>$this->userid]);
            if ($query->first()->is_entering_newpw) {
                return $this->redirect('/users/login'); // パスワード変更中はログインに戻す。
            }
        }    
    }
    
    
    
// actions --------------------------------------------------------------    

    
    public function index()
    {
    }
    
    
    public function editAdminNote()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $ac_no = $this->request->getQuery('ac_no');
           
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age,
                                $zipcode, $address, $phone_no);       
        $gengo_year = '(' . $this->MrCommon->get_gengo_year($birthdate) . ')';
        $arr_bd = explode('-', $birthdate);
        $birthdate = $gengo_year . $arr_bd[0] . '年' . $arr_bd[1] . '月' . $arr_bd[2] . '日';         
        $arr_ptinf = [
                       ['患者ID', $ptnumber],
                       ['氏名', $name],
                       ['カナ氏名', $kana_name],
                       ['生年月日', $birthdate],
                       ['年齢', $age . '歳'],
                       ['住所', $zipcode .' ' . $address],
                       ['電話', $phone_no]
                     ];
        $admin_note = $this->get_admin_note($ptnumber);
        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('name', $name);
        $this->set('arr_ptinf', $arr_ptinf);
        $this->set('admin_note', $admin_note);        
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
               
            $note1 = $data['note1'];        
            $note2 = $data['note2'];        
            $note3 = $data['note3'];        
            $note4 = $data['note4'];        
            $note5 = $data['note5'];        
            $note6 = $data['note6'];        
            $note7 = $data['note7'];        
            $note8 = $data['note8'];
            $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);    

            $query = $this->mr_conn->newQuery();
            $query->update('tbl_admin_note')
                  ->set(['note1'=>$note1, 'note2'=>$note2, 
                         'note3'=>$note3, 'note4'=>$note4,
                         'note5'=>$note5, 'note6'=>$note6, 
                         'note7'=>$note7, 'note8'=>$note8,
                        ])
                  ->where(['ptnum'=>$ptnum]);
            $result = $query->execute();

            return $this->redirect(['action'=>'view-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]);        
        }
    }


    public function editMr()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $ac_no = $this->request->getQuery('ac_no', '');
        $time = $this->request->getQuery('time');

        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                                   $zipcode, $address, $phone_no);
        $this->get_mr_data_by_time($ptnumber, $time, $mrid, $cc, $col_order);

        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);        
        $this->set('mrid', $mrid);
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);                
        $this->set('time', $time);
        $this->set('cc', $cc);
        $this->set('col_order', $col_order);
        $this->set('clin_note', $this->get_clin_note($ptnumber));
        
        $data = ['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no, 'time'=>$time];        
        if ($this->request->is('post')) {            
            $data += $this->request->getData();
            $is_newmr = false;
            $this->save_mr($data, $is_newmr);
            
            return $this->redirect(['action'=>'view-mr', 
                         '?'=>['ptnumber'=>$data['ptnumber'], 'ac_no'=>$data['ac_no'], 'time'=>$time]
                        ]);
        }
    }
 

    public function headlines()
    {
        $ptnumber = $this->request->getQuery('ptnumber');        
        $ac_no = $this->request->getQuery('ac_no', '');
        
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                                    $zipcode, $address, $phone_no);        
        $cc_list = $this->get_cc_list($ptnumber);
        
        // $this->MrCommon->make_patient_folder($ptnumber);
        // $this->MrCommon->open_patient_folder($ptnumber);

        if ($cc_list === []) {
            return $this->redirect (['action'=>'new_mr', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]);
        }
        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        
        $this->set('cc_list', $cc_list);        
    }

    
    public function newMr()
    {
        $ptnumber = $this->request->getQuery('ptnumber');        
        $ac_no = $this->request->getQuery('ac_no', '');
        $time = FrozenTime::now()->toDateTimeString();

        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                                    $zipcode, $address, $phone_no);
        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate',$birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);                
        $this->set('time', $time);
        $this->set('cc', '');
        $this->set('col_order', '');
        $this->set('clin_note', '');           

        $data = ['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no, 'time'=>$time];
        if ($this->request->is('post')) {
            $data += $this->request->getData();
            $is_newmr = true;
            $this->save_mr($data, $is_newmr);        

            $this->redirect(['action'=>'view-mr', 
                             '?'=>['ptnumber'=>$data['ptnumber'], 'ac_no'=>$data['ac_no'], 'time'=>$time]
                            ]);
        }
    }

    
    public function searchMr()
    {        
        $ptnumber= $this->request->getQuery('ptnumber');
        $ac_no = $this->request->getQuery ('ac_no', '');
        $str_for_search = $this->request->getData('str_for_search', '');
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                                   $zipcode, $address, $phone_no);

        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);        
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);               
        $this->set('cc_list', $this->get_cc_list_by_str_search($ptnumber, $str_for_search));
        $this->set('col_order_list', $this->get_order_list_by_str_search($ptnumber, $str_for_search));
    }

    
    public function viewAdminNote()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $ac_no = $this->request->getQuery('ac_no');
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age,
                                   $zipcode, $address, $phone_no);       
        $gengo_year = '(' . $this->MrCommon->get_gengo_year($birthdate) . ')';
        $arr_bd = explode('-', $birthdate);
        $birthdate = $gengo_year . $arr_bd[0] . '年' . $arr_bd[1] . '月' . $arr_bd[2] . '日';         
        $arr_ptinf = [
                       ['患者ID', $ptnumber],
                       ['氏名', $name],
                       ['カナ氏名', $kana_name],
                       ['生年月日', $birthdate],
                       ['年齢', $age . '歳'],
                       ['住所', $zipcode .' ' . $address],
                       ['電話', $phone_no]
                     ];
        $admin_note = $this->get_admin_note($ptnumber);
        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('name', $name);
        $this->set('arr_ptinf', $arr_ptinf);
        $this->set('admin_note', $admin_note);        
    }


    public function viewMr ()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $time = $this->request->getQuery('time');            
        $ac_no = $this->request->getQuery('ac_no', '');
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                                   $zipcode, $address, $phone_no);        
        $this->get_mr_data_by_time($ptnumber, $time, $mrid, $cc, $col_order);
        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('time', $time);
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        
        $this->set('mrid', $mrid);
        $this->set('time', $time);
        $this->set('cc', $cc);
        $this->set('col_order', $col_order);
        $this->set('clin_note', $this->get_clin_note($ptnumber));
    }
    

// methods ---------------------------------------------------------------
   

    public function create_admin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);

        $query = $this->mr_conn->newQuery();
        $query->select('max(id)')->from('tbl_admin_note');
        $arr = $query->execute()->fetch('assoc');
        
        $max_id = $arr['max'];        
        $id = (string)((int)$max_id + 1);

        $query = $this->mr_conn->newQuery();
        $query->insert(['id', 'ptnum', 'note1', 'note2', 'note3', 'note4', 
                                       'note5', 'note6', 'note7', 'note8',])
              ->into('tbl_admin_note')
              ->values(['id'=>$id, 'ptnum'=>$ptnum, 
                        'note1'=>'', 'note2'=>'', 'note3'=>'', 'note4'=>'', 
                        'note5'=>'', 'note6'=>'', 'note7'=>'', 'note8'=>'', 
                       ]);
        
        $result = $query->execute();
    }
    
    
    public function create_clin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $query = $this->mr_conn->newQuery();
        $query->select('max(id)')->from('tbl_clin_note');
        $arr = $query->execute()->fetch('assoc');
        $max_id = $arr['max'];        
        $id = (string)((int)$max_id + 1);

        $query = $this->mr_conn->newQuery();
        $query->insert(['id', 'ptnum', 'clin_note'])->into('tbl_clin_note')
              ->values(['id'=>$id, 'ptnum'=>$ptnum, 'clin_note'=>'']);
        $result = $query->execute();
    }

    
    public function get_admin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $query = $this->mr_conn->newQuery();
        $query->select(['note1', 'note2', 'note3', 'note4', 'note5', 'note6', 'note7', 'note8'])
                     ->from('tbl_admin_note')
                     ->where(['ptnum'=>$ptnum]);
        $arr = $query->execute()->fetch('assoc');        

        if ($arr === false) { 
            $this->create_admin_note($ptnumber);
            $note = [ ['メモ１', ''],
                      ['メモ２', ''],
                      ['メモ３', ''],
                      ['メモ４', ''],
                      ['メモ５', ''],
                      ['メモ６', ''],
                      ['メモ７', ''],
                      ['メモ８', ''],
                    ];
        } else {
            $note = [ ['メモ１', $arr['note1'] ? $arr['note1'] : ''], 
                      ['メモ２', $arr['note2'] ? $arr['note2'] : ''], 
                      ['メモ３', $arr['note3'] ? $arr['note3'] : ''], 
                      ['メモ４', $arr['note4'] ? $arr['note4'] : ''], 
                      ['メモ５', $arr['note5'] ? $arr['note5'] : ''], 
                      ['メモ６', $arr['note6'] ? $arr['note6'] : ''], 
                      ['メモ７', $arr['note7'] ? $arr['note7'] : ''], 
                      ['メモ８', $arr['note8'] ? $arr['note8'] : ''],  
                    ];
        }
        
        return $note;
    }

    
    public function get_cc_list($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);

        $subquery = $this->mr_conn->newQuery();
        $subquery->select('1')->from('tbl_cc_and_order as b')
                 ->where(function (QueryExpression $exp){
                     return $exp->add('a.t_1st_save = b.t_1st_save');
                   })
                 ->andWhere(function (QueryExpression $exp){
                     return $exp->add('a.t_last_save < b.t_last_save');
                   });
                   
        $query = $this->mr_conn->newQuery();
        $query->select(['mrid', 't_1st_save', 'cc'])->from('tbl_cc_and_order as a')
                    ->where(['ptnum'=>$ptnum])
                    ->andWhere(['NOT EXISTS'=>$subquery])
                    ->order(['a.t_1st_save'=>'DESC']);
        $arr = $query->execute()->fetchAll('assoc');

        if ($arr === false) $arr = [];

        return $arr;
    }

    
    public function get_cc_list_by_str_search($ptnumber, $str_for_search = '')
    {
        if ($str_for_search === '') return [];
        
        $str_for_search = mb_strtoupper($str_for_search);        
        $str_for_search = mb_convert_kana($str_for_search, "KVAS"); //半角を全角に

        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);   

        $subquery = $this->mr_conn->newQuery();
        $subquery->select('1')->from('tbl_cc_and_order as b')
                 ->where(function (QueryExpression $exp){
                     return $exp->add('a.t_1st_save = b.t_1st_save');
                   })
                 ->andWhere(function (QueryExpression $exp){
                     return $exp->add('a.t_last_save < b.t_last_save');
                   });        
        $query = $this->mr_conn->newQuery();
        $query->select(['mrid', 't_1st_save'])->from('tbl_cc_and_order as a')
              ->where(['ptnum'=>$ptnum])
              ->andWhere(['NOT EXISTS'=>$subquery])
              ->andWhere(['to_zenkaku(upper(cc)) LIKE'=>'%' . $str_for_search . '%'])
              ->order(['a.t_1st_save'=>'DESC']);
        $arr = $query->execute()->fetchAll('assoc');
                
        if ($arr === false) $arr = [];

        return $arr;
    }

    
    public function get_clin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);

        $query = $this->mr_conn->newQuery();
        $query->select('clin_note')->from('tbl_clin_note')->where(['ptnum'=>$ptnum]);
        $arr = $query->execute()->fetch('assoc');
        
        if ($arr === false) { 
            $this->create_clin_note($ptnumber);
            return '';
        } else {
            return $arr['clin_note'];
        }
    }

    
    public function get_order_list_by_str_search($ptnumber, $str_for_search = '')
    {
        if ($str_for_search === '') return [];
        
        $str_for_search = mb_strtoupper($str_for_search);        
        $str_for_search = mb_convert_kana($str_for_search, "KVAS");//半角を全角に
        
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);        

        $subquery = $this->mr_conn->newQuery();
        $subquery->select('1')->from('tbl_cc_and_order as b')
                 ->where(function (QueryExpression $exp){
                     return $exp->add('a.t_1st_save = b.t_1st_save');
                   })
                 ->andWhere(function (QueryExpression $exp){
                     return $exp->add('a.t_last_save < b.t_last_save');
                   });
        $query = $this->mr_conn->newQuery();
        $query->select(['mrid', 't_1st_save'])->from('tbl_cc_and_order as a')
              ->where(['ptnum'=>$ptnum])
              ->andWhere(['NOT EXISTS'=>$subquery])
              ->andWhere(['to_zenkaku(upper(col_order)) LIKE'=>'%' . $str_for_search . '%'])
              ->order(['a.t_1st_save'=>'DESC']);
        $arr = $query->execute()->fetchAll('assoc');

        if ($arr === false) $arr = [];

        return $arr;
    }   
    
    
    /*
    public function get_mr_data_by_mrid($ptnumber, $mrid, &$time, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        $mrid = (string)$mrid;
        
        $pg_conn = pg_connect($this->MrCommon->str_for_mr_conn);                       
        $command = "SELECT t_1st_save, cc, col_order FROM tbl_cc_and_order" 
                . " WHERE ptnum = " . pg_escape_literal($ptnum)
                . " AND mrid = " . pg_escape_string($mrid);  
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        
        $time = $arr['t_1st_save'];
        $cc = $arr['cc'];
        $col_order = $arr['col_order'];        
    }
    */
    
    public function get_mr_data_by_time($ptnumber, $time, &$mrid, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);

        $query = $this->mr_conn->newQuery();
        $query->select(['mrid', 't_1st_save', 'cc', 'col_order'])->from('tbl_cc_and_order')
              ->where(['ptnum'=>$ptnum])
              ->andWhere(['t_1st_save'=>$time])
              ->order(['t_1st_save'=>'DESC', 't_last_save'=>'DESC']);
        $arr = $query->execute()->fetch('assoc');
        
        $mrid = $arr['mrid'];
        $time = $arr['t_1st_save'];
        $cc = $arr['cc'];
        $col_order = $arr['col_order'];        
    }

    
    public function get_mr_latest_data($ptnumber, &$mrid, &$time, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);

        $query = $this->mr_conn->newQuery();
        $query->select(['mrid', 't_1st_save', 'cc', 'col_order'])->from('tbl_cc_and_order')
              ->where(['ptnum'=>$ptnum])
              ->order(['t_1st_save'=>'DESC', 't_last_save'=>'DESC']);
        $arr = $query->execute()->fetch('assoc');
        
        $mrid = $arr['mrid'];
        $time = $arr['t_1st_save'];
        $cc = $arr['cc'];
        $col_order = $arr['col_order'];
    }
    

    public function save_admin_note()
    {
        $data = $this->request->getData();
        
        $ptnumber = $data['ptnumber'];
        $ac_no = $data['ac_no'];
        
        $note1 = $data['note1'];        
        $note2 = $data['note2'];        
        $note3 = $data['note3'];        
        $note4 = $data['note4'];        
        $note5 = $data['note5'];        
        $note6 = $data['note6'];        
        $note7 = $data['note7'];        
        $note8 = $data['note8'];
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);    

        $query = $this->mr_conn->newQuery();
        $query->update('tbl_admin_note')
              ->set(['note1'=>$note1, 'note2'=>$note2, 
                     'note3'=>$note3, 'note4'=>$note4,
                     'note5'=>$note5, 'note6'=>$note6, 
                     'note7'=>$note7, 'note8'=>$note8
                    ])
              ->where(['ptnum'=>$ptnum]);
        $result = $query->execute();

        $this->redirect(['action'=>'admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]);
    }


    public function save_mr($data, bool $is_newmr)
    {        
        $workerlist = $this->OrcaApi->get_worker_list();
        foreach ($workerlist as $obj) {
            if ($obj['user_id'] === $this->userid) {
                $username = $obj['name'];
                break;
            }
        }
        
        $ptnumber = $data['ptnumber'];
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        $query = $this->mr_conn->newQuery();
        $query->select('max(mrid)')->from('tbl_cc_and_order');
        $arr = $query->execute()->fetch('assoc');
        $max_mrid = $arr['max'];
        
        $mrid = (string)((int)$max_mrid + 1);
        
        $t_1st_save = $data['time'];
        $t_last_save = FrozenTime::now()->toDateTimeString();
        $cc = $data['cc'];
        $col_order = $data['col_order'];
        $clin_note = $data['clin_note'];

        $arr_cc = explode("\n", $cc);
        $size = count($arr_cc);
        
        // cc末尾に署名する。
        if ($is_newmr) {
            $arr_cc[$size] = ' ';
            $arr_cc[$size + 1] = "//-----";
            $arr_cc[$size + 2] = '//' . $t_last_save . ' ' . $username . ' 作成。';
        } elseif ($size >= 2 && preg_match("/^\\/\\/-----/", $arr_cc[$size - 2])) {            
            $arr_cc[$size - 1] = ('//' . $t_last_save . ' ' . $username . ' 修正。');
        } else {
                $arr_cc[$size] = ' ';
                $arr_cc[$size + 1] = "//-----";
                $arr_cc[$size + 2] = '//' . $t_last_save .  ' ' . $username . ' 修正。';
        }
        $cc = implode("\n" , $arr_cc);

        $query = $this->mr_conn->newQuery();
        $query->insert(['mrid', 'ptnum', 't_1st_save', 't_last_save', 'cc', 'col_order', 'userid'])
              ->into('tbl_cc_and_order')
              ->values(['mrid'=>$mrid, 'ptnum'=>$ptnum, 
                        't_1st_save'=>$t_1st_save, 't_last_save'=>$t_last_save, 
                        'cc'=>$cc, 'col_order'=>$col_order, 'userid'=>$this->userid]
                      );
        $result = $query->execute();
        
        $query = $this->mr_conn->newQuery();
        $query->update('tbl_clin_note')->set(['clin_note'=>$clin_note])->where(['ptnum'=>$ptnum]);
        $result = $query->execute();
    }


}

