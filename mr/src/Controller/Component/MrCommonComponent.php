<?php

/**
 * Description of MrCommonComponent
 *
 * @author furu
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\Time;


class MrCommonComponent extends Component
{

    public function calc_age($birthdate, $date)
    {    
        $t_birthdate = new Time($birthdate);
        
        if ($date === '') {
            $t_date = Time::now();
        } else {
            $t_date = new Time($date);
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
        $meiji_begin  = new Time('1868-10-23');
        $taisho_begin = new Time('1912-07-30');
        $showa_begin  = new Time('1926-12-25');
        $heisei_begin = new Time('1989-01-08');
        $reiwa_begin  = new Time('2019-05-01');
        
        $date = new Time($str_date);
        
        if ($date < $meiji_begin)  return '';
        if ($date < $taisho_begin) return 'M' . ($date->year - $meiji_begin->year  + 1);
        if ($date < $showa_begin)  return 'T' . ($date->year - $taisho_begin->year + 1);
        if ($date < $heisei_begin) return 'S' . ($date->year - $showa_begin->year  + 1);
        if ($date < $reiwa_begin)  return 'H' . ($date->year - $heisei_begin->year + 1);
        // else
        return                            'R' . ($date->year - $reiwa_begin->year  + 1);
    }


    public function is_allowed_ip()
    {
        $filename = 'allowed_ip.list';
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if (!file_exists($filename)) return false;
        
        $arr_ip_allowed = file($filename, FILE_IGNORE_NEW_LINES);
        foreach ($arr_ip_allowed as $obj) {
            if (preg_match('/\A#/', $obj) === 1) continue; //コメント行を飛ばす
            if (ip2long($ip) === ip2long($obj)) return true;
        }
        return false;
    }
 
    
}
