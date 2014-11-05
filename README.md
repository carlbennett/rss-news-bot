# RSS News Bot
## Preface
- Polls multiple RSS feeds.
- Aggregates all articles found, with duplicates excluded.
- Searches for multiple keywords.
- Upon finding new articles, dispatches multiple services.
- Each service can define its own way of handling a new article.

There is already the HipChat service class which allows new articles to be posted in HipChat rooms given an API token.

## Installation
1. Clone the repository locally.
2. Ensure the execute bit is enabled (i.e. unix permissions 0755) for ```/main.php``` and that ```/usr/bin/php``` exists.
3. Change various settings in the ```/Settings.json``` file.
4. Run the ```/main.php``` file as if it was a shell script (e.g. ```$ ./main.php```).
