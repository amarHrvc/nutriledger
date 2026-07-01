#!/bin/sh

cd backend

mkdir -p database
touch database/database.sqlite

php artisan migrate --force

php artisan db:seed

php artisan queue:work --sleep=3 --tries=3 --timeout=90 &
QUEUE_PID=$!

php artisan serve --host=0.0.0.0 --port=${PORT:-8000} &
SERVER_PID=$!

trap "kill $QUEUE_PID $SERVER_PID" TERM INT

wait -n $QUEUE_PID $SERVER_PID
