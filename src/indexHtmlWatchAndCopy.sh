#!/bin/bash

file_to_watch="/app/clientApp/dist/index.html"
destination_folder="/app/backendApp/templates/index.html.twig"
last_modified=$(stat -c %y "$file_to_watch")

if [ -f "$file_to_watch" ]; then
  while true
  do
    sleep 1
    if [ -f "$file_to_watch" ]; then
      current_modified=$(stat -c %y "$file_to_watch" 2>/dev/null)

      if [ "$current_modified" != "$last_modified" ]; then
        cp "$file_to_watch" "$destination_folder"
        echo "$(date) : Copied updated file to $destination_folder"
        last_modified=$current_modified
      fi
    fi
  done
fi