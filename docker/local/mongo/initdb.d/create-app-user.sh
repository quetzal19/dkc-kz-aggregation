./etc/mongo/initdb.d/create-app-user.sh
#!/bin/bash
# https://www.stuartellis.name/articles/shell-scripting/#enabling-better-error-handling-with-set
set -Eeuo pipefail

"${mongo[@]}" -u "root" -p "root" --authenticationDatabase "$rootAuthDatabase" "dkc-kz-agg" <<-EOJS
    db.createUser({
        user: $(_js_escape "dkc-user"),
        pwd: $(_js_escape "dkc-password"),
        roles: [ { role: 'readWrite', db: $(_js_escape "dkc-kz-agg") } ]
    })
EOJS