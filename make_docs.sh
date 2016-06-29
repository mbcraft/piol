#!/bin/sh

rm -rf docs/
phpdoc -d src/ --encoding UTF-8 --title Piol --validate -t docs
