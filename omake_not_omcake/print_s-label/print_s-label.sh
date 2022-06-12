#!/bin/bash

#localeが LANG=ja_JP.UTF-8 になっていること。

. /etc/jma-receipt/jma-receipt.env

orca_server_addr=localhost
printer=PT-P950 #Trim Tape -> On
label_form=/home/ANY_DIRECTORY/s-label.glabels
declare -i max_name_len=20

work_dir=/tmp
data_to_print=$work_dir/data_to_print.csv
output_pdf=$work_dir/output.pdf

ptid=$5		#10桁
ptnum=$6	#8桁(当院仕様)

ptnum=$(echo $ptnum | sed -e 's/^0\+//')	#頭の0を除去

sql_command="SELECT name from tbl_ptinf WHERE ptid=$ptid;"
name=$(echo $sql_command | psql -h $orca_server_addr -U orca -A -t)

declare -i name_len
declare -i excess_len
name_len=$(echo -n $name | wc --chars)
((excess_len = name_len - max_name_len ))

if (( excess_len >= 1 ))
then
  #超過文字列を削除
  name=$(echo -n $name | sed -e 's/.\{'$excess_len'\}$//')
fi

echo $ptnum,$name >$data_to_print

glabels-3-batch $label_form -i $data_to_print -o $output_pdf
lpr -P $printer $output_pdf

rm $data_to_print
rm $output_pdf

exit
