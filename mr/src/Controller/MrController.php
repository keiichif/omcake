<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Utility\Xml;


class MrController extends AppController
{
    public $str_for_mr_conn = 'host=localhost dbname=mr user=mr';
    public $pt_documents_dir = '/mnt/sfc-shared/pt_documents';

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('MrCommon');
        $this->loadComponent('OrcaApi');
        
        if (!$this->MrCommon->is_allowed_ip()) $this->redirect ('/forbidden');
        
        $this->viewBuilder()->setLayout('mr-common');
    }

    
// action --------------------------------------------------------------    

    
    public function index()
    {
    }
    
    
    public function adminNote()
    {
        $this->_admin_note();
    }


    public function editAdminNote()
    {
        $this->_admin_note();
    }


    public function editMr()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->set('ptnumber', $ptnumber);
        
        $mrid = $this->request->getQuery('mrid');       
        $this->set('mrid', $mrid);
        
        $ac_no = $this->request->getQuery('ac_no');
        if ($ac_no === null) $ac_no = '';
        $this->set('ac_no', $ac_no);

        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                               $zipcode, $address, $phone_no);       
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        
        
        $this->get_mr_data_by_mrid($ptnumber, $mrid, $time, $cc, $col_order);
        $this->set('time', $time);
        $this->set('cc', $cc);
        $this->set('col_order', $col_order);
        $this->set('clin_note', $this->get_clin_note($ptnumber));        
    }
 

    public function headlines()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->set('ptnumber', $ptnumber);
        
        $ac_no = $this->request->getQuery('ac_no');
        if ($ac_no === null) $ac_no = '';
        $this->set('ac_no', $ac_no);
        
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age,$zipcode, $address, $phone_no);        
        $this->set('ptnumber', $ptnumber);
        $this->set('ac_no', $ac_no);
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        

        $cc_list = $this->get_cc_list($ptnumber);
        $this->set('cc_list', $cc_list);
        if ($cc_list === []) $this->redirect('/mr/new_mr?ptnumber=' . $ptnumber . '&ac_no=' . $ac_no);
    }

    
    public function newMr()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->set('ptnumber', $ptnumber);
        
        $ac_no = $this->request->getQuery('ac_no');
        if ($ac_no === null) $ac_no = '';
        $this->set('ac_no', $ac_no);

        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                               $zipcode, $address, $phone_no);                
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate',$birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        
        
        $this->set('time', Time::now()->toDateTimeString());
        $this->set('cc', '');
        $this->set('col_order', '');
        $this->set('clin_note', $this->get_clin_note($ptnumber));           
    }

    
    public function saveAdminNote()
    {
        $data = $this->request->getData();
        
        $ptnumber = $data['ptnumber'];
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);    
        $ac_no = $data['ac_no'];
        
        $note1 = $data['note1'];        
        $note2 = $data['note2'];        
        $note3 = $data['note3'];        
        $note4 = $data['note4'];        
        $note5 = $data['note5'];        
        $note6 = $data['note6'];        
        $note7 = $data['note7'];        
        $note8 = $data['note8'];

        $pg_conn = pg_connect($this->str_for_mr_conn);
        $command = "UPDATE tbl_admin_note SET"
                . "  note1 = " . pg_escape_literal($note1)
                . ", note2 = " . pg_escape_literal($note2) 
                . ", note3 = " . pg_escape_literal($note3) 
                . ", note4 = " . pg_escape_literal($note4) 
                . ", note5 = " . pg_escape_literal($note5) 
                . ", note6 = " . pg_escape_literal($note6) 
                . ", note7 = " . pg_escape_literal($note7)
                . ", note8 = " . pg_escape_literal($note8) 
                . " WHERE ptnum = " . pg_escape_literal($ptnum);
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
#        $this->redirect(['action'=>'admin_note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]);
        $this->redirect('/mr/admin_note?' . 'ptnumber=' . $ptnumber . '&ac_no=' . $ac_no);
    }


    public function saveMr()
    {
        $data = $this->request->getData();

        $ptnumber = $data['ptnumber'];
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
           
        $pg_conn = pg_connect($this->str_for_mr_conn);
        $command = "SELECT max(mrid) FROM tbl_cc_and_order";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        $max_mrid = $arr['max'];
        
        $mrid = (string)((int)$max_mrid + 1);
        
        $t_1st_save = $data['time'];
        $t_last_save = Time::now()->toDateTimeString();
        $cc = $data['cc'];
        $col_order = $data['col_order'];
        $clin_note = $data['clin_note'];        

        $pg_conn = pg_connect($this->str_for_mr_conn);       
        $command = "INSERT INTO tbl_cc_and_order " 
                . "(mrid, ptnum, t_1st_save, t_last_save, cc, col_order) "
                . "VALUES (" 
                    . pg_escape_string($mrid) . ", " 
                    . pg_escape_literal($ptnum) . ", "
                    . pg_escape_literal($t_1st_save) . "::timestamp, "
                    . pg_escape_literal($t_last_save) . "::timestamp, " 
                    . pg_escape_literal($cc) . ", "
                    . pg_escape_literal($col_order)
                . ")";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
    
        $pg_conn = pg_connect($this->str_for_mr_conn);       
        $command = "UPDATE tbl_clin_note SET clin_note = " 
                    . pg_escape_literal($clin_note)
                    . " WHERE ptnum = " . pg_escape_literal($ptnum) ;
        $result = pg_query_params($command, []);
        pg_close($pg_conn);

        $this->redirect('/mr/view_mr?ptnumber=' . $data['ptnumber'] . '&ac_no=' . $data['ac_no'] . '&time=' . $t_1st_save);                
    }

    
    public function searchMr()
    {
        $ptnumber= $this->request->getQuery('ptnumber');
        if ($ptnumber === null) $ptnumber = $this->request->getData('ptnumber');
        $this->set('ptnumber', $ptnumber);
                    
        $ac_no = $this->request->getQuery ('ac_no');
        if ($ac_no === null) $ac_no = $this->request->getData('ac_no');
        $this->set('ac_no', $ac_no);
        
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                               $zipcode, $address, $phone_no);        
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        
        
        $data_for_search = $this->request->getData('data_for_search');
        if ($data_for_search === null) $data_for_search = '';
       
        $this->set('cc_list', $this->get_cc_list_by_str_search($ptnumber, $data_for_search));
        $this->set('col_order_list', $this->get_col_order_list($ptnumber, $data_for_search));
    }

    
    public function viewMr ()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->set('ptnumber', $ptnumber);

        $time = $this->request->getQuery('time');
        $this->set('time', $time);
            
        $ac_no = $this->request->getQuery('ac_no');
        if ($ac_no === null) $ac_no = '';
        $this->set('ac_no', $ac_no);
        
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, 
                               $zipcode, $address, $phone_no);        
        $this->set('name', $name);
        $this->set('kana_name', $kana_name);
        $this->set('birthdate', $birthdate);
        $this->set('age', $age);
        $this->set('zipcode', $zipcode);
        $this->set('address', $address);
        $this->set('phone_no', $phone_no);        

        $this->get_mr_data_by_time($ptnumber, $time, $mrid, $cc, $col_order);
        $this->set('mrid', $mrid);
        $this->set('time', $time);
        $this->set('cc', $cc);
        $this->set('col_order', $col_order);
        $this->set('clin_note', $this->get_clin_note($ptnumber));
    }
    

// method ---------------------------------------------------------------


    public function _admin_note()
    {
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->set('ptnumber', $ptnumber);

        $ac_no = $this->request->getQuery('ac_no');
        $this->set('ac_no', $ac_no);
           
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age,
                                $zipcode, $address, $phone_no);       
        $this->set('name', $name);

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
        $this->set('arr_ptinf', $arr_ptinf);

        $admin_note = $this->get_admin_note($ptnumber);
        $this->set('admin_note', $admin_note);        
    }
    

    public function create_admin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);
        $command = "SELECT max(id) FROM tbl_admin_note";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        $max_id = $arr['max'];
        
        $id = (string)((int)$max_id + 1);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);               
        $command = "INSERT INTO tbl_admin_note (id, ptnum, " 
                    . "note1, note2, note3, note4, note5, note6, note7, note8) " 
                    . "VALUES ("
                            . pg_escape_string($id) . ", "
                            . pg_escape_literal($ptnum) . ", "
                            . "'' , '', '', '', '', '', '', '')";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
    }
    
    
    public function create_clin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);
        $command = "SELECT max(id) FROM tbl_clin_note";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        $max_id = $arr['max'];
        
        $id = (string)((int)$max_id + 1);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "INSERT INTO tbl_clin_note (id, ptnum, clin_note) " 
                    . "VALUES (" 
                        . pg_escape_string($id) . ", "
                        . pg_escape_literal($ptnum) 
                        . ", '')";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
    }

    
    public function get_admin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT note1, note2, note3, note4, note5, note6, note7, note8"
                    . " FROM tbl_admin_note"
                    . " WHERE ptnum = " . pg_escape_literal($ptnum);       
        $result = pg_query_params($command, []);
        pg_close($pg_conn);

        $arr = pg_fetch_assoc($result);

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
                      ['メモ８', $arr['note8'] ? $arr['note8'] : '']    
                    ];
                 
        }
        
        return $note;
    }

    
    public function get_cc_list($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid, t_1st_save, cc FROM tbl_cc_and_order AS a"
                . " WHERE ptnum = " . pg_escape_literal($ptnum) 
                    . " AND NOT EXISTS (SELECT 1 FROM tbl_cc_and_order AS b"
                    . " WHERE a.t_1st_save = b.t_1st_save AND a.t_last_save < b.t_last_save)"
                . " ORDER BY a.t_1st_save DESC";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_all($result, PGSQL_ASSOC);
        if ($arr === false) $arr = [];

        return $arr;
    }

    
    public function get_cc_list_by_str_search($ptnumber, $data_for_search)
    {
        if ($data_for_search === '') return [];
        
        $data_for_search = mb_strtoupper($data_for_search);        
        $data_for_search = mb_convert_kana($data_for_search, "KVAS"); //半角を全角に

        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);   
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid, t_1st_save FROM tbl_cc_and_order AS a"
                . " WHERE ptnum = " . pg_escape_literal($ptnum) 
                    . " AND NOT EXISTS (SELECT 1 FROM tbl_cc_and_order AS b "
                    . " WHERE a.t_1st_save = b.t_1st_save AND a.t_last_save < b.t_last_save)"
                    . " AND to_zenkaku(upper(cc)) LIKE "
                    . pg_escape_literal('%' . $data_for_search . '%')
                . " ORDER BY a.t_1st_save DESC";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_all($result, PGSQL_ASSOC);
        if ($arr === false) $arr = [];

        return $arr;
    }

    
    public function get_clin_note($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT clin_note FROM tbl_clin_note" 
                    . " WHERE ptnum = " . pg_escape_literal($ptnum);
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);        

        if ($arr === false) { 
            $this->create_clin_note($ptnumber);
            return '';
        } else {
            return $arr['clin_note'];
        }
    }

    
    public function get_col_order_list($ptnumber, $data_for_search)
    {
        if ($data_for_search === '') return [];
        
        $data_for_search = mb_strtoupper($data_for_search);        
        $data_for_search = mb_convert_kana($data_for_search, "KVAS");//半角を全角に
        
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);        
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid, t_1st_save FROM tbl_cc_and_order AS a"
                . " WHERE ptnum = " . pg_escape_literal($ptnum) 
                    . " AND NOT EXISTS (SELECT 1 FROM tbl_cc_and_order AS b"
                    . " WHERE a.t_1st_save = b.t_1st_save AND a.t_last_save < b.t_last_save)"
                    . " AND to_zenkaku(upper(col_order)) LIKE "
                    . pg_escape_literal('%' . $data_for_search . '%')
                . " ORDER BY a.t_1st_save DESC";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_all($result, PGSQL_ASSOC);
        if ($arr === false) $arr = [];

        return $arr;
    }

    
    public function get_max_mrid($ptnumber)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid FROM tbl_cc_and_order" 
                        . " WHERE ptnum = " . pg_escape_literal($ptnum) 
                        . " ORDER BY mrid DESC LIMIT 1";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);       

        return $arr['mrid'];        
    }

    
    public function get_mr_data_by_mrid($ptnumber, $mrid, &$time, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        $mrid = (string)$mrid;
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
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

    
    public function get_mr_data_by_time($ptnumber, $time, &$mrid, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid, t_1st_save, cc, col_order FROM tbl_cc_and_order" 
                . " WHERE ptnum = " . pg_escape_literal($ptnum)
                . " AND t_1st_save = " . pg_escape_literal($time) . "::timestamp"
                . " ORDER BY t_1st_save desc, t_last_save desc LIMIT 1";  
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        
        $mrid = $arr['mrid'];
        $time = $arr['t_1st_save'];
        $cc = $arr['cc'];
        $col_order = $arr['col_order'];        
    }

    
    public function get_mr_latest_data($ptnumber, &$mrid, &$time, &$cc, &$col_order)
    {
        $ptnum = str_pad($ptnumber, 8, '0', STR_PAD_LEFT);
        
        $pg_conn = pg_connect($this->str_for_mr_conn);                       
        $command = "SELECT mrid, t_1st_save, cc, col_order FROM tbl_cc_and_order"
                    . " WHERE ptnum = " . pg_escape_literal($ptnum) 
                    . " ORDER BY t_1st_save desc, t_last_save desc LIMIT 1";
        $result = pg_query_params($command, []);
        pg_close($pg_conn);
        
        $arr = pg_fetch_assoc($result);
        
        $mrid = $arr['mrid'];
        $time = $arr['t_1st_save'];
        $cc = $arr['cc'];
        $col_order = $arr['col_order'];
    }


}

