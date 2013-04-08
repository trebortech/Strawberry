#! /usr/bin/env python
"""
Project: StrawberryPI
Author: Robert Booth

Description:
This is a simple script to control home sprinkler systems
via a RaspberryPI and some Relay controller.
"""
import sys
import os
import json
import time
from datetime import datetime

import RPi.GPIO as gpio

lockfile = 'imrunning'

mapZonePin = {}
boardPin = (3, 5, 7, 8, 10, 11, 12, 13)


def get_DayOfWeek(value):

    return {
        0: 'monday',
        1: 'tuesday',
        2: 'wednesday',
        3: 'thursday',
        4: 'friday',
        5: 'saturday',
        6: 'sunday',
        }.get(value, 'unknown')

def main():

    running = False

    #Check to see if the script is already running
    try:
        xfile = open(lockfile)
        running = True
    except:
        pass

    if running:
        print "I'm already running"
        sys.exit()
    else:
        xfile = open(lockfile, 'w')
        xfile.write('lockme')
        xfile.close()

    duration = checkSchedule()
    if duration != "none":
        initBoard()
        for zoneUp in duration:
            print "Zone {zone} for {seconds} seconds".format(zone=zoneUp,
                seconds=int(duration[zoneUp]) * 60)

            setZone(int(zoneUp), 'ON')
            time.sleep(int(duration[zoneUp]) * 60)

    os.remove(lockfile)


def checkSchedule():

    # Will try to move this to Google Calendar in the future
    try:
        scheduleFile = open('sprinklerSchedule.json')
    except:
        print "Can't find sprinklerSchedule file"
        os.remove(lockfile)
        sys.exit()

    try:
        schedule = json.loads(scheduleFile.read())
    except:
        print "Problem importing schedule file"
        sys.exit()

    now = time.localtime()
    weekday = get_DayOfWeek(now.tm_wday)
    timenow = "{hours}:{minutes}".format(hours=now.tm_hour,
                        minutes=now.tm_min)

    timeFMT = "%H:%M"

    starttime = schedule[weekday]['start']
    duration = schedule[weekday]['duration']

    timedelta = datetime.strptime(timenow, timeFMT) - datetime.strptime(starttime, timeFMT)

    if timedelta.total_seconds() > 0 and timedelta.total_seconds() < 300:
        return duration
    else:
        return "none"


def initBoard():

    # Set board mode
    gpio.setmode(gpio.BOARD)

    '''
    My watering zones

    Zone 1 - Backyard - Pin 13 - Pin 8 on relay board
    Zone 2 - Side House - Pin 12 - Pin 7 on relay board
    Zone 3 - Flower Bed - Pin 11 - Pin 6 on relay board
    Zone 4 - Front yard - Pin 10 - Pin 5 on relay board
    '''

    mapZonePin[1] = 13
    mapZonePin[2] = 12
    mapZonePin[3] = 11
    mapZonePin[4] = 10

    #Set All Pins to be output
    for pinid in boardPin:
        gpio.setup(pinid, gpio.OUT)


def setZone(intZone, status):
    if status == 'ON':
        gpio.output(mapZonePin[intZone], gpio.HIGH)
    else:
        gpio.output(mapZonePin[intZone], gpio.LOW)


def shutdownAll():
    for pinid in boardPin:
        setZone(pinid, 'OFF')


#Remove the lock file

if __name__ == "__main__":
    main()