#!/bin/sh
LOGFILE="/var/log/sixserver/maintenance.log"
date >> $LOGFILE

# En Docker, MySQL está en el host "db" (red evo_net)
mysql -h db -uroot -ppassword -e 'SELECT onlineUsers FROM six_stats' evo >> $LOGFILE

echo "Setting maintenance=1..." >> $LOGFILE
mysql -h db -uroot -ppassword -e 'UPDATE six_stats SET maintenance=1' evo

echo "Sleeping 20 Minutes..."  >> $LOGFILE
sleep 20m

date >> $LOGFILE

mysql -h db -uroot -ppassword -e 'SELECT onlineUsers FROM six_stats' evo >> $LOGFILE

echo "Restarting Sixserver..." >> $LOGFILE
# En Docker, el reinicio se hace enviando SIGHUP al proceso twistd dentro del contenedor sixserver
kill -HUP $(cat /opt/sixserver/sixserver.pid 2>/dev/null) 2>/dev/null || echo "Sixserver not running or pid not found" >> $LOGFILE

echo "Setting maintenance=0..."  >> $LOGFILE
mysql -h db -uroot -ppassword -e 'UPDATE six_stats SET maintenance=0' evo

echo "Done." >> $LOGFILE
