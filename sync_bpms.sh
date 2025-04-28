#!/bin/bash
# Sync script for BPMS project
echo "Syncing BPMS files to /var/www/html/bpms/"
rsync -av --delete --exclude='.git' --exclude='node_modules' /home/tristan/Documents/Repos/beautyspa/bpms/ /var/www/html/bpms/
echo "Sync complete!"
