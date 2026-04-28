#!/usr/bin/env sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$ROOT_DIR"

status=0

echo "Checking direct sql_query/sql_fetch calls outside approved compatibility files..."
if rg -n "sql_(query|fetch)\\(" \
    -g "*.php" \
    -g "!install/**" \
    -g "!plugin/**" \
    -g "!lib/common.sql.lib.php" \
    -g "!lib/common.cert.lib.php" \
    -g "!lib/bootstrap/core.lib.php" \
    -g "!lib/support/base.util.lib.php" \
    .; then
    status=1
else
    echo "OK"
fi

echo "Checking weak token random sources outside approved compatibility/vendor paths..."
if rg -n "\\b(rand|mt_rand|uniqid)\\s*\\(" \
    -g "*.php" \
    -g "!install/**" \
    -g "!plugin/**" \
    -g "!lib/pbkdf2.compat.php" \
    .; then
    status=1
else
    echo "OK"
fi

echo "Checking md5 usage in first-party runtime paths..."
if rg -n "md5\\s*\\(" \
    -g "*.php" \
    -g "!install/**" \
    -g "!plugin/**" \
    -g "!lib/common.session.lib.php" \
    -g "!lib/common.crypto.lib.php" \
    -g "!lib/cache.lib.php" \
    -g "!lib/common.data.lib.php" \
    -g "!lib/support/base.util.lib.php" \
    -g "!lib/domain/member/flow-core.lib.php" \
    -g "!lib/domain/member/validation-account.lib.php" \
    .; then
    status=1
else
    echo "OK"
fi

exit "$status"
