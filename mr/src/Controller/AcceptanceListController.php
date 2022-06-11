<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;
use Cake\I18n\Time;
use Cake\Utility\Xml;


class AcceptanceListController extends AppController
{


    public function initialize(): void
    {
        parent::initialize();    
        $this->loadComponent('MrCommon');
        $this->loadComponent('OrcaApi');
    
        if (!$this->MrCommon->is_allowed_ip()) $this->redirect ('/forbidden');
        
        $this->viewBuilder()->setLayout('mr-common');        
    }

    
// action  --------------------------------------------------------------  

    
    public function index()
    {
    }

    
    public function past()
    {       
        $date = $this->request->getData('date');
        // 全角英数字を半角に
        $date = mb_convert_kana($date, 'a');
        
        if (strtotime($date)) {  // $dateのフォーマットチェック
            $t_date = new Time($date);
            $date = $t_date->toDateString();
        } else {
            $date = Time::yesterday()->toDateString();
        }
                    
        $this->OrcaApi->get_aclist($date, 'past', $aclist_header, $aclist_body);

        $this->set('aclist_header',$aclist_header);      
        $this->set('aclist_body', $aclist_body);
        $this->set('date', $date);
        $this->render('past');        
    }

    
    public function printTicket() {                
        $ac_no = $this->request->getQuery('ac_no');
        $ptnumber = $this->request->getQuery('ptnumber');
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, $zipcode, $address, $phone_no);        
        $now = Time::now()->toDateString();
        $response = shell_exec("bash print_r-ticket_cakephp.sh " . $now . ' ' . 
                $ac_no . ' ' . $name . ' ' . $ptnumber);
        $this->redirect('/acceptance-list/today');
    }    

    
    public function today()
    {
        $date = Time::now()->toDateString();

        $this->OrcaApi->get_aclist($date, 'today', $aclist_header, $aclist_body);
        
        $this->set('aclist_header',$aclist_header);      
        $this->set('aclist_body', $aclist_body);        
    }

    
}
