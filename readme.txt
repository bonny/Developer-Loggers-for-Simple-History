=== Developer Loggers for Simple History ===
Contributors: eskapism
Donate link: http://eskapism.se/sida/donate/
Tags: simple history, developer, admins, administrators, email, debug, wp_mail,
Requires at least: 4.4
Tested up to: 4.4
Stable tag: 0.2

Useful loggers for SimpleHistory for developers during development of a site or to maintain a live site.


== Description ==

Bring more loggers to [WordPress user history plugin Simple History](http://simple-history.com).
That are useful for developers during development of a site or to maintain a live site.

= Included loggers and plugins =

**Slack poster**

All your events is posted to a [Slack](https://slack.com/) channel of your choice, using an [incoming webhook](https://api.slack.com/incoming-webhooks). Yes, with this plugin enabled
there is no need what so ever to ever leave Slack to see what's happening on your site or the site of your
clients or... well, on any site where you have Simple Histor and this plugin enabled.

**WP_Mail-logger**

See all mails sent with wp_mail(), no matter what the recipient address is.

**404 logger**

View page visits that load the 404 template.

**JavaScript error logger**

See what JavaScript errors users that visit your site is getting.

**SystemLog logger**

Log all messages from Simple History to the syslog on the server. With this logger enabled there is no need to use the beautiful GUI of Simple History ;).


== Screenshots ==

1. Enable the loggers you want in Settings » Simple History » Developer loggers.

2. Example output of the WordPress Cron debug logger.

3. Example output of the WPMail logger. You can see the subject, who the mail is sent to, and the contents of the body.


== Changelog ==

## Changelog

= 0.3 (February 2016) =

- Added: New plugin: Slack! Yes, now all your logged events can be posted to a Slack channel of your choice.
- Added: Actions `simple_history/developer_loggers/before_plugins_table` and `simple_history/developer_loggers/before_plugins_table`.


= 0.2 (December 2015) =

- Changes to Limit Login Attempts.

= 0.1 (December 2015) =

- First public version. Enjoy!
