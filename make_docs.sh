#!/bin/sh

rm -rf docs/
php phpDocumentor.phar -d src/ --encoding UTF-8 --title Piol --validate -t docs
