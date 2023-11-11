#!/bin/bash
php artisan migrate --force
php artisan schedule:work
