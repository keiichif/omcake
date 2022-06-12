#!/bin/bash 

orca_server_addr=192.168.xxx.yyy
dir=$(pwd)

declare str_num=$(cat $dir/last_free_num)
declare -i num=$(echo $str_num | bc)

declare not_used=0

while true
do
  echo "空き番号検索中... "$num

  str_num=$(printf "%08d" $num)
  sql_command="SELECT count(*) FROM tbl_ptnum WHERE ptnum='$str_num';"
  status=$(echo $sql_command | psql -h $orca_server_addr -U orca -A -t)

  if [ "$status" == "" ]
  then
    echo
    echo -n "エラーが発生しました。"
    read
    exit
  fi

  if [ $status == $not_used ]
  then
    break
  fi

  (( num++ ))
#  echo $num
done

echo $num > $dir/last_free_num

echo
echo -n "空き番号： "$num
read
