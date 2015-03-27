#!/bin/sh
LOGFILE="/var/log/sixserver/maintenance.log"
date >> $LOGFILE

mysql -uroot -pmypassword -e 'SELECT onlineUsers FROM six_stats' evo >> $LOGFILE

echo "Setting maintenance=1..." >> $LOGFILE
mysql -uroot -pmypassword -e 'UPDATE six_stats SET maintenance=1' evo

echo "Sleeping 20 Minutes..."  >> $LOGFILE
sleep 20m

date >> $LOGFILE

mysql -uroot -pmypassword -e 'SELECT onlineUsers FROM six_stats' evo >> $LOGFILE

echo "Restarting Sixserver..." >> $LOGFILE
service sixserver restart

echo "Setting maintenance=0..."  >> $LOGFILE
mysql -uroot -pmypassword -e 'UPDATE six_stats SET maintenance=0' evo

echo "Done." >> $LOGFILE