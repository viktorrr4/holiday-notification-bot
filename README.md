***Project Deployment***
```
php artisan migrate
```

Configurate your .env file with your database credentials

Add bot to database
```
php artisan telegraph:new-bot
```

Add webhook to bot
```
php artisan telegraph:set-webhook
```

Bot is ready to use!
Bot has yet only one command: /today

It shows today's holidays

I use 
```
defstudio/telegraph
```
[link to documentation](https://defstudio.github.io/telegraph/)

