#!/bin/bash
while [ 1 == 1 ]
do
    date
    php improvePlanets.php
    SLEEP_TIME=$(($RANDOM / 50))
    echo sleeping $SLEEP_TIME seconds
    sleep $SLEEP_TIME
done
