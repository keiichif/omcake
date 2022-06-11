<?php

/**
 * Description of OrcaApiComponent
 *
 * @author furu
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;
use Cake\Utility\Xml;


class OrcaApiComponent extends Component
{
    
    public $components = ['MrCommon'];
    public $http_orca;
    

    public function initialize(array $config): void
    {        
        $this->http_orca = new Client(['host' => 'localhost', 'port' => '8000', 
            'auth' => ['username' => 'USERNAME', 'password' => 'PASSWORD']]);        
    }
    
    
    public function get_memo($ptnumber, $date)
    {        
        $xml = $this->http_orca->post('/api01rv2/patientlst7v2', 
                        $this->make_postdata_for_memo($ptnumber, $date))->getXml();
        $array = Xml::toArray($xml);

        $array = $array['xmlio2']['patientlst7res'];
        if ($array['Api_Result']['@'] === '000') {    //メモ2あり
            $array = $array['Patient_Memo_Information']['Patient_Memo_Information_child'];
            if (isset($array['Patient_Memo']['@'])) { //メモ2が改行のみのとき、配列が作成されない。
                return $array['Patient_Memo']['@'];
            } else {
                return '';
            }
        } else {
            return '';
        }        
    }

    
    public function get_aclist($date, $today_or_not, &$aclist_header, &$aclist_body)
    {        
        $aclist_header = [ '受付番号','受付時刻','患者番号', '氏名', 'カナ氏名', '性別', '年齢', 'メモ' ];
        $aclist_body = [];

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

            if ($today_or_not === 'today') {
                $ac_no_link = '<a href="print_ticket?ac_no=' . $ac_no 
                            . '&ptnumber=' . $ptnumber . '">' . $ac_no . '</a>';
                $ptnumber_link = '<a href="/mr/headlines?ptnumber=' . $ptnumber 
                        . '&ac_no=' . $ac_no . '"'
                        . ' target="_blank" rel="noopener noreferrer"' . '>'. $ptnumber .'</a>';
            } else {
                $ac_no_link = (string)$ac_no;
                $ptnumber_link = '<a href="/mr/headlines?ptnumber=' . $ptnumber . '"' 
                        . ' target="_blank" rel="noopener noreferrer"' . '>'. $ptnumber .'</a>';
            }
            
            $age = $this->MrCommon->calc_age($birthdate, $date);
            $memo = $this->get_memo($ptnumber, $date);
            
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

    
    public function get_pt_inf($ptnumber, &$name, &$kana_name, &$birthdate, &$age,
                                &$zipcode, &$address, &$phone_no)
    {
        $xml = $this->http_orca->get("/api01rv2/patientgetv2?id=$ptnumber")->getXml();        
        $array = Xml::toArray($xml);        

        $array = $array['xmlio2']['patientinfores']['Patient_Information'];

        $name = $array['WholeName']['@'];
        $kana_name = $array['WholeName_inKana']['@'];
        $birthdate = $array['BirthDate']['@'];
        
        $zipcode = '';
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

    
    public function make_postdata_for_aclist($date)
    {    
        $xml_declaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $sxe = Xml::build($xml_declaration . '<data></data>');

        $acceptlstreq = $sxe->addChild('acceptlstreq');
        $acceptlstreq->addAttribute('type', 'record');

        $date_ptr = $acceptlstreq->addChild('Acceptance_Date', $date);
        $date_ptr->addAttribute('type', 'string');
        return $sxe->asXML();
    }

    
    public function make_postdata_for_memo($ptnumber, $date)
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

    
}
