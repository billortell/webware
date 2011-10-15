#!/bin/sh
#

if [ -z "$HMQ_HOME" ];then
    export HMQ_HOME=/opt/hmqd
fi

cd $HMQ_HOME

su -s "/bin/sh" root -c "exec -a hmqd php -q hmqd.php 1>/dev/null 2>/dev/null &"

