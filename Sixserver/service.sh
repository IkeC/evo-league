#!/bin/sh
### BEGIN INIT INFO
# Provides:          sixserver
# Required-Start:    $all
# Required-Stop:     $all
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start sixserver daemon at boot time
# Description:       Enable sixserver service
### END INIT INFO

export FSENV=/opt/sixserver/sixserver-env
export PYTHONPATH=/opt/sixserver/lib:$PYTHONPATH

RETVAL=0

PROG=sixserver
TAC=/opt/sixserver/etc/sixserver.tac
LOG=/var/log/sixserver/sixserver.log
PID=/opt/sixserver/sixserver.pid

case "$1" in
    run)
        echo "sixserver run..."
		FSENV}/bin/twistd -noy $TAC
        ;;
    start)
        echo "sixserver start..."
		${FSENV}/bin/twistd -ny $TAC --logfile $LOG --pidfile $PID &
        ;;
    stop)
		echo "sixserver stop..."
        cat $PID | xargs kill
        ;;
    restart)
		echo "sixserver restart..."
		$0 stop
        sleep 3
        $0 start
        ;;
status)
        if [ -f $PID ]; then
            pid=`cat $PID`
            ps $pid >/dev/null 2>&1
            if [ $? = 0 ]; then
                echo "$PROG ($pid) is running ..."
            else
                echo "$PROG is not running but pid-file exists"
                RETVAL=1
            fi
        else
            echo "$PROG is stopped"
        fi
        ;;
    *)
        echo "Usage $0 {run|start|stop|status}"
        RETVAL=3
esac

exit $RETVAL
