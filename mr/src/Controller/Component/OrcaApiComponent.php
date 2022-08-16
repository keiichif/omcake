<?php
declare(strict_types=1);

/**
 * Description of OrcaApiComponent
 *
 * @author furu
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\I18n\FrozenDate;
use Cake\Utility\Xml;
use Cake\Utility\Text;


class OrcaApiComponent extends Component
{    
    public $components = ['MrCommon'];
    public $http_orca;
    

    public function initialize(array $config): void
    {        
        parent::initialize($config);
        
        $conf = Configure::read('mr');
        
        $this->http_orca = new Client(['host' => $conf['orca_host'], 'port' => $conf['orca_port'], 
            'auth' => ['username' => $conf['orca_username'], 'password' => $conf['orca_password']]]);        
    }
    
    
    public function get_aclist(string $date, &$aclist_header, &$aclist_body)
    {        
        $aclist_header = [ '受付番号','受付時刻','患者番号', '氏名', 'カナ氏名', '性別', '年齢', 'メモ' ];
        $aclist_body = [];
        $fd_date = new FrozenDate($date);
        
        $xml = $this->http_orca->post('/api01rv2/acceptlstv2?class=03', 
                            $this->make_postdata_for_aclist($date))->getXml();
        $array = Xml::toArray($xml);
        
        $array = $array['xmlio2']['acceptlstres'];
        if ($array['Api_Result']['@'] === '00') {
            $array = $array['Acceptlst_Information']['Acceptlst_Information_child'];
        } else {
            $array = [];
        }
        
        if ($array === []) return;
        
        //受付患者が1名の場合, 配列化する。        
        if (!array_key_exists('0', $array))  $array = [ $array ]; 

        foreach ($array as $obj) {                
            $birthdate = $obj['Patient_Information']['BirthDate']['@'];
            $ptnumber = (int)$obj['Patient_Information']['Patient_ID']['@'];
            $name = $obj['Patient_Information']['WholeName']['@'];
            $ac_no = (int)$obj['Acceptance_Id']['@'];

            if ($fd_date->isToday()) {
                $ac_no_link = Text::insert('<a href=:url?ptnumber=:ptnumber&ac_no=:ac_no target="_blank">:ac_no</a>',
                                        ['url'=>'/acceptance-list/print_ticket', 'ptnumber'=>$ptnumber,'ac_no'=>$ac_no]);
                $ptnumber_link = Text::insert('<a href=:url?ptnumber=:ptnumber&ac_no=:ac_no target="_blank">:ptnumber</a>',
                                        ['url'=>'/mr/headlines', 'ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]);
            } else {
                $ac_no_link = (string)$ac_no;
                $ptnumber_link = Text::insert('<a href=:url?ptnumber=:ptnumber target="_blank">:ptnumber</a>',
                                        ['url'=>'/mr/headlines', 'ptnumber'=>$ptnumber]);
            }
            
            $age = $this->MrCommon->calc_age($birthdate, $date);
            $memo = $this->get_memo((string)$ptnumber, $date);
            
            $aclist_body[] = [ $ac_no_link,
                               substr($obj['Acceptance_Time']['@'], 0, 5),
                               $ptnumber_link,
                               $name, 
                               $obj['Patient_Information']['WholeName_inKana']['@'],
                               $obj['Patient_Information']['Sex']['@'] == 1 ? '男': '女', 
                               (string)$age . '歳', 
                               $memo
                             ];
        }           
        
        return;
    }

    
    public function get_memo(string $ptnumber, string $date)
    {        
        $xml = $this->http_orca->post('/api01rv2/patientlst7v2', 
                        $this->make_postdata_for_memo($ptnumber, $date))->getXml();
        $array = Xml::toArray($xml);

        $array = $array['xmlio2']['patientlst7res'];
        if ($array['Api_Result']['@'] === '000') {    //メモ2あり
            $array = $array['Patient_Memo_Information']['Patient_Memo_Information_child'];
            if (isset($array['Patient_Memo']['@'])) { //メモ2が改行のみのとき、配列が作成されないのでチェック。
                return $array['Patient_Memo']['@'];
            }
        }
        return '';        
    }

    
    public function get_pt_inf(string $ptnumber, &$name, &$kana_name, &$birthdate, &$age,
                                &$zipcode, &$address, &$phone_no)
    {
        $xml = $this->http_orca->get("/api01rv2/patientgetv2?id=$ptnumber")->getXml();        
        $array = Xml::toArray($xml);        

        $array = $array['xmlio2']['patientinfores']['Patient_Information'];

        $name = $array['WholeName']['@'];
        $kana_name = $array['WholeName_inKana']['@'];
        $birthdate = $array['BirthDate']['@'];
        
        $zipcode  = '';
        $address1 = '';
        $address2 = '';
        $phone_no = '';
        
        if (array_key_exists('Home_Address_Information', $array)) {
            $array = $array['Home_Address_Information'];    //更に切り詰める。
            
            if (array_key_exists('Address_ZipCode', $array)) 
                        $zipcode = $array['Address_ZipCode']['@'];
            if (array_key_exists('WholeAddress1', $array)) 
                        $address1 = $array['WholeAddress1']['@'];
            if (array_key_exists('WholeAddress2', $array)) 
                        $address2 = $array['WholeAddress2']['@'];
            if (array_key_exists('PhoneNumber1', $array)) 
                        $phone_no = $array['PhoneNumber1']['@'];
        }
        
        $kana_name = preg_replace('/　/',' ',$kana_name); //全角空白を半角に       
        $age = $this->MrCommon->calc_age($birthdate, '');
        $zipcode = preg_replace('/(^.{3})(.{4})/','${1}-${2}', $zipcode);
        $address = $address1 . $address2;
    }

    
    public function get_worker_list()
    {
        $xml = $this->http_orca->post('/orca101/manageusersv2', 
                            $this->make_postdata_for_workerlist())->getXml();
        $array = Xml::toArray($xml);

        $array = $array['xmlio2']['manageusersres'];
        if ($array['Api_Result']['@'] === '0000') {
            $array = $array['User_Information']['User_Information_child'];
        } else {
            $array = [];
        }
       
        if ($array === []) return;
        
        //1名の場合、配列化する。        
        if (!array_key_exists('0', $array))  $array = [ $array ]; 
    
        foreach ($array as $obj) {
            if (array_key_exists('Start_Date', $obj)) {
                $start_date = str_replace('-', '', $obj['Start_Date']['@']);
            } else {
                $start_date =  '00000000';
            }
            
            if (array_key_exists('Expiry_Date', $obj)) {
                $expiry_date = str_replace('-', '', $obj['Expiry_Date']['@']);
            } else {
                $expiry_date = '99999999';
            }

            $workerlist[] = [
                                'user_id'     => $obj['User_Id']['@'], 
                                'group_no'    => $obj['Group_Number']['@'],
                                'user_no'     => $obj['User_Number']['@'],
                                'name'        => $obj['Full_Name']['@'],
                                'kana_name'   => $obj['Kana_Name']['@'],
                                'start_date'  => $start_date,
                                'expiry_date' => $expiry_date
                    ];
        }           
        
        return $workerlist;
    }

    
    public function make_postdata_for_aclist(string $date)
    {    
        $xml_declaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $sxe = Xml::build($xml_declaration . '<data></data>');

        $acceptlstreq = $sxe->addChild('acceptlstreq');
        $acceptlstreq->addAttribute('type', 'record');

        $date_ptr = $acceptlstreq->addChild('Acceptance_Date', $date);
        $date_ptr->addAttribute('type', 'string');
        return $sxe->asXML();
    }

    
    public function make_postdata_for_memo(string $ptnumber, string $date)
    {        
        $xml_declaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $sxe = Xml::build($xml_declaration . '<data></data>');
        
        $patientlst7req = $sxe->addChild('patientlst7req');
        $patientlst7req->addAttribute('type', 'record');
        
        $req_no = $patientlst7req->addChild('Request_Number', '01');
        $req_no->addAttribute('type', 'string');
        
        $pt_id = $patientlst7req->addChild('Patient_ID', $ptnumber);
        $pt_id->addAttribute('type', 'string');
        
        $base_date = $patientlst7req->addChild('Base_Date', $date);
        $base_date->addAttribute('type', 'string');
        
        return $sxe->asXML();
    }

    
    public function make_postdata_for_workerlist()
    {
        $xml_declaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $sxe = Xml::build($xml_declaration . '<data></data>');

        $manageusersreq = $sxe->addChild('manageusersreq');
        $manageusersreq->addAttribute('type', 'record');
        
        $req_no = $manageusersreq->addChild('Request_Number', '01');
        $req_no->addAttribute('type', 'string');

        return $sxe->asXML();
    }
    
    
}
