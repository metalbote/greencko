@echo off

set arg1=%1

if not{%1} == {} (
   drush make build-greenery.make.yml %1
) else (
   drush make build-greenery.make.yml dist
)
