#!/bin/bash

# Get the absolute path to the directory where this script is located (the 'src' folder).
SCRIPT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &> /dev/null && pwd)

# Construct the full, absolute path to the cron.php script.
# This is crucial because CRON needs absolute paths to work reliably.
CRON_SCRIPT_PATH="$SCRIPT_DIR/cron.php"

# Find the full path to your system's PHP executable.
PHP_PATH=$(which php)

# Define the schedule and the command for the CRON job.
# "0 * * * *" means "at minute 0 of every hour".
CRON_JOB="0 * * * * $PHP_PATH $CRON_SCRIPT_PATH"

# This command adds the job to your system's crontab.
# It first lists existing jobs, removes any old versions of this specific job to prevent duplicates,
# and then adds the new one.
(crontab -l 2>/dev/null | grep -v -F "$CRON_SCRIPT_PATH" ; echo "$CRON_JOB") | crontab -

echo "CRON job successfully configured to run '$CRON_SCRIPT_PATH' every hour."
echo "You can verify the installation by running the command: crontab -l"
