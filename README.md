# RSS News Bot
## Preface
- Polls multiple RSS feeds.
- Aggregates all articles found, with duplicates excluded.
- Searches for multiple keywords.
- Upon finding new articles, dispatches multiple services.
- Each service can define its own way of handling a new article.

There is already the HipChat service class which allows new articles to be
posted in HipChat rooms given an API token.

## Installation
1. Clone the repository locally.
2. Ensure the execute bit is enabled (i.e. unix permissions 0755)
   for ```/main.php``` and that ```/usr/bin/php``` exists.
3. Copy the ```/Settings.sample.json``` to `/Settings.json``` and update it to
   your taste.
4. Run the ```/main.php``` file as if it was a shell script (e.g.
   ```$ ./main.php```).

## Systemd Service
Instead of running the bot by running the ```/main.php``` file as a shell
script, you could run it by using the Systemd service unit file.

1. Copy the ```/rss-news-bot.service```
   to ```/etc/systemd/system/rss-news-bot.service```.
2. Copy the bot to ```/opt/rss-news-bot/``` or modify the Systemd unit file.
3. Run ```sudo systemctl daemon-reload``` to make it aware of the new unit.
4. Run ```sudo systemctl start rss-news-bot.service``` to run the bot.
5. Run ```sudo systemctl enable rss-news-bot.service``` to have it run at boot.
