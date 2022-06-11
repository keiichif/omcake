#!/bin/bash

#for CakePHP
#ファイルはCakePHPのプロジェクトのルートに置く。

printer=TM-T70  #Paper Reduction->Top & Bottom margins
work_dir=.

label_form=$work_dir/r-ticket.glabels
data_to_print=$work_dir/data_to_print.csv
pdf=$work_dir/output.pdf


uke_date=$1
uke_no=$2
name=$3
id=$4	

echo $uke_date,$uke_no,$name,$id > $data_to_print

glabels-3-batch $label_form -i $data_to_print -o $pdf
lpr -P $printer $pdf

exit
