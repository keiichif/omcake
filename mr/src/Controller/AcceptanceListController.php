<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;


class AcceptanceListController extends AppController
{

    
    public function initialize(): void
    {
        parent::initialize();
        
        $this->loadComponent('MrCommon');
        $this->loadComponent('OrcaApi');
    
        $this->viewBuilder()->setLayout('mr-common');        
    }

    
    public function beforeFilter(\Cake\Event\EventInterface $event) 
    {
        parent::beforeFilter($event);
            
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $userid = $this->request->getAttribute('identity')->userid;
            //var_dump($userid);
            $users = TableRegistry::getTableLocator()->get('Users');
            $query = $users->find()->select(['is_entering_newpw'])->where(['userid'=>$userid]);
            if ($query->first()->is_entering_newpw) {
                return $this->redirect('/users/login'); // パスワード変更中はログインに戻す。
            }
        }    
    }


// actions  --------------------------------------------------------------  

    
    public function index()
    {
    }

    
    public function past()
    {       
        $date = FrozenDate::yesterday()->toDateString();
        if ($this->request->is('post')) {
            $date = $this->request->getData('date', '');
        }

        $this->OrcaApi->get_aclist($date, $aclist_header, $aclist_body);

        $this->set('date', $date);
        $this->set('aclist_header',$aclist_header);      
        $this->set('aclist_body', $aclist_body);
    }

    
    public function printTicket()
    {                
        $ac_no = $this->request->getQuery('ac_no');
        $ptnumber = $this->request->getQuery('ptnumber');        
        $this->OrcaApi->get_pt_inf($ptnumber, $name, $kana_name, $birthdate, $age, $zipcode, $address, $phone_no);        
        $date = FrozenDate::today()->toDateString();
        
        $cmd = Text::insert('bash print_r-ticket_cakephp.sh :date :ac_no :name :ptnumber', 
                            ['date'=>$date, 'ac_no'=>$ac_no, 'name'=>$name,'ptnumber'=>$ptnumber]);
        $response = shell_exec($cmd);
        
        return $this->redirect('/acceptance-list/today');
    }    

    
    public function today()
    {
        $date = FrozenDate::today()->toDateString();

        $this->OrcaApi->get_aclist($date, $aclist_header, $aclist_body);
        
        $this->set('aclist_header',$aclist_header);      
        $this->set('aclist_body',  $aclist_body);        
    }

    
}
