## Installation

```bash
composer install

php bin/console doc:database:create
php bin/console doc:mig:mig -n
```

pas besoin d'utiliser RabbitMQ.

## Exécution

```bash
symfony serve -d

php bin/console messenger:consume -vv
```

Note dès que l'utilisateur a annulé une tâche le processus messenger:consume quitte. il faut le relancer manuellement. Pas idéal je trouve. peut-être qu'il serait intéressant d'exécuter la tâche dans un sous processus ?

## Production

créer un service qui relance automatiquement messenger:consume dès qu'il y a un crash ou dès que l'utilisateur a annulé une action.

```ini
# ~/.config/systemd/user/messenger-worker.service
[Unit]
Description=Symfony messenger-consume %i

[Service]
ExecStart=php /path/to/your/app/bin/console messenger:consume async --time-limit=3600

# c'est cette ligne qui redémarrera automatiquement le worker à la suite d'un message comme
# ./bin/console messenger:stop-workers

# always => signifie dans tous les cas sauf si on appelle explicitement : `systemctl stop <service>`
# clean exit code or signal, unclean exit code, unclean signal, timeout, watchdog
Restart=always 

# temps d'attente avant de relancer un processus qui a été arrêté
RestartSec=30

[Install]
WantedBy=default.target
```