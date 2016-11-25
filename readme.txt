=== Developer Loggers for Simple History ===
Contributors: eskapism
Donate link: http://eskapism.se/sida/donate/
Tags: simple history, developer, admins, administrators, email, debug, wp_mail,
Requires at least: 4.4
Tested up to: 4.5
Stable tag: 0.5

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
changelog

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

4. Example output form the HTTP API logger. View method and target URL.

5. Details from the HTTP API logger. Even more details here, like all the headers in the response and also the full response.

6. Example output from the WordPress post to Slack Logger. Here you can see me failing to login, then succeeding to login, updating a plugin, and finally I log out.


== Changelog ==

## Changelog

= 0.5 (November 2016) =

- Add notice in admin that [Simple History](https://simple-history.com) must be installed and activated to use this plugin.
- Add some very basic Travis CI tests, so the plugin is less likely to cause any obvious errors.
- The Available Updates Logger has now moved to the main Simple History plugin, because it's useful for most users and not only admins.
- Added filter `simple_history/developer_loggers/enabled_loggers` to modify what plugins are enabled.
- Added filter `simple_history/developer_loggers/slackit/settings` to modify the settings of the Slackit logger.
- Added `examples.php` with some examples of filter usage.

= 0.4.1 (August 2016) =

- Check that the `sys_getloadavg()` exists before trying to use. Could cause error on Windows.

= 0.4 (August 2016) =

- Logger for Limit Logins Attempts has moved to Simple History, so more users can benefit from it.
- When a plugin update is found: use the english name for the plugin, instead of the translated name.

= 0.3.4 (June 2016) =

- Bump up requirement to 5.4 because I'm to tired to write code for 5.3

= 0.3.3 (June 2016) =

- Fixed notice warning in post to Slack
- Add error message if not running PHP 5.3 or higher
- Change unit in HTTP API message from milliseconds to seconds

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
