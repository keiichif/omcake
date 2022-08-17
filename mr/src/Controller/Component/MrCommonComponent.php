<?php
declare(strict_types=1);

/**
 * Description of MrCommonComponent
 *
 * @author furu
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\FrozenDate;
use Cake\Core\Configure;


class MrCommonComponent extends Component
{


    public function calc_age($birthdate, $date)
    {    
        $t_birthdate = new FrozenDate($birthdate);
        
        if ($date === '') {
            $t_date = FrozenDate::today();
        } else {
            $t_date = new FrozenDate($date);
        }
        
        $age = $t_date->year - $t_birthdate->year;
        
        if (  $t_date->month < $t_birthdate->month) {
            $age--;
        } elseif ($t_date->month === $t_birthdate->month && $t_date->day < $t_birthdate->day) {
            $age--;
        }
        
        return $age;
    }


    public function get_gengo_year($str_date)
    {
        $meiji_begin  = new FrozenDate('1868-10-23');
        $taisho_begin = new FrozenDate('1912-07-30');
        $showa_begin  = new FrozenDate('1926-12-25');
        $heisei_begin = new FrozenDate('1989-01-08');
        $reiwa_begin  = new FrozenDate('2019-05-01');
        
        $date = new FrozenDate($str_date);
        
        if ($date < $meiji_begin)  return '';
        if ($date < $taisho_begin) return 'M' . ($date->year - $meiji_begin->year  + 1);
        if ($date < $showa_begin)  return 'T' . ($date->year - $taisho_begin->year + 1);
        if ($date < $heisei_begin) return 'S' . ($date->year - $showa_begin->year  + 1);
        if ($date < $reiwa_begin)  return 'H' . ($date->year - $heisei_begin->year + 1);
        // else
        return                            'R' . ($date->year - $reiwa_begin->year  + 1);
    }


    public function is_allowed_ip() : bool
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if (!Configure::configured('mr')) return false;
        
        $arr = Configure::read('mr');
        $arr = $arr['allowed_ip'];
        foreach ($arr as $obj) {
            if (ip2long($ip) === ip2long($obj)) return true;
        }
        
        return false;
    }
    

    public function make_patient_folder(string $ptnumber)
    {
        $ptnum = str_pad($ptnumber, 5, '0', STR_PAD_LEFT);
        $highest_no = substr($ptnum, 0, 1);
        
        $conf = Configure::read('mr');
        $dir = $conf['pt_data_dir'] . '/' . $highest_no . '/' . $ptnum;

        if (!file_exists($dir)) mkdir($dir, 0777, true);        
    }



    public function open_patient_folder(string $ptnumber)
    {
        $this->make_patient_folder($ptnumber);
        shell_exec('gio open ' . $dir);
    }
    
}
