#!/bin/bash

# Get the directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Run AppleScript to open iTerm2 window and tabs
osascript <<EOF
tell application "iTerm"
    activate
    set newWindow to (create window with default profile)
    tell newWindow
        tell current session of current tab
            write text "cd '$DIR' && php artisan serve"
        end tell
        
        set tab2 to (create tab with default profile)
        tell current session of tab2
            write text "cd '$DIR' && npm run dev"
        end tell
        
        set tab3 to (create tab with default profile)
        tell current session of tab3
            write text "cd '$DIR'"
        end tell
    end tell
end tell
EOF
