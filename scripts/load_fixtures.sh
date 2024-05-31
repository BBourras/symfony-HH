#!/usr/bin/env bash

bash script reset_db.sh
symfony console doctrine:fixtures:load --no-interaction