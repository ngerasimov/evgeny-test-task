#!/usr/bin/env bash
php bin/console ca:cl
php bin/console app:messenger:setup --no-interaction
php bin/console messenger:consume "$@" -v --time-limit=600