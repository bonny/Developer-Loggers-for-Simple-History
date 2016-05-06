=== Developer Loggers for Simple History ===
Contributors: eskapism
Donate link: http://eskapism.se/sida/donate/
Tags: simple history, developer, admins, administrators, email, debug, wp_mail,
Requires at least: 4.4
Tested up to: 4.5
Stable tag: 0.3.2

Useful loggers for SimpleHistory for developers during development of a site or to maintain a live site.


== Description ==

Bring more loggers to [WordPress user history plugin Simple History](http://simple-history.com).
That are useful for developers during development of a site or to maintain a live site.

= Included loggers and plugins =

**Post to Slack**

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

**HTTP API logger**

Log all usage of HTTP calls from functions like wp_remote_post() and wp_remote_get().

You can the URL requested, the arguments posted and the full returned result, including server headers.

The time for the request to complete is also logged. Great for debugging!

== Screenshots ==

1. Enable the loggers you want in Settings » Simple History » Developer loggers.

2. Example output of the WordPress Cron debug logger.

3. Example output of the WPMail logger. You can see the subject, who the mail is sent to, and the contents of the body.


== Changelog ==

## Changelog

= 0.3.2 (May 2016) =

- Added: Logger HTTP API Logger that logs all GET and POST requests made using for example `wp_remote_get()` and `wp_remote_post()`.
- Fixed: The logger for available updates could throw a fatal error on the plugin install screen. Hopefully fixed now, and if
then it fixes https://github.com/bonny/Developer-Loggers-for-Simple-History/issues/1 (the very first issue for this plugin! 🎉).

= 0.3.1 (March 2016) =

- Fixed: Function `__return_empty_string` is not called `_return_empty_string`...
- Fixed: Undefined notice for `$initiator_text` in the post to Slack plugin

= 0.3 (February 2016) =

- Added: New plugin: Slack! Yes, now all your logged events can be posted to a Slack channel of your choice.
- Added: Actions `simple_history/developer_loggers/before_plugins_table` and `simple_history/developer_loggers/before_plugins_table`.

= 0.2 (December 2015) =

- Changes to Limit Login Attempts.

= 0.1 (December 2015) =

- First public version. Enjoy!
