#Rules Schedule setup

One of the main features of the Rules Module is its capacity to run the rules automatically when they are enabled and it is the time to do so.

The file *rules_run_schedule.php* is the one that runs the schedule (triggers the rules and check pending acks). It is intended to be a cron job (Linux) or a scheduled task (Windows) so that the schedule can be run periodically.

For Linux systems in order to add a cron job, type from the command line:

```
sudo sed -i '$a 1 * * * * root /usr/bin/php /var/www/emoncms/Modules/rules/rules_run_schedule.php' /etc/crontab
```

This assumes your emonCMS installation is in `/var/www/emoncms` and php is in `/usr/bin`. if you wnt to check where is the binary file for php run `whereis php` from the command line.

The cron job just added will try to run the schedule every minute, but in fact the shedule will only be launched the first time. One of the first thing the schedule does is to open a locked file so that the next time it is called the schedule is not run. The way we keep the schedule going is in a never ending while loop.