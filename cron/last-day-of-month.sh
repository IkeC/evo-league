#!/bin/bash

TODAY=`/bin/date +%d`
TOMORROW=`/bin/date +%d -d "1 day"`

# See if tomorrow's day is less than today's
if [ $TOMORROW -lt $TODAY ]; then
        exit 0
fi

exit 1
