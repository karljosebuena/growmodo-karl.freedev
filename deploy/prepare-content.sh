#!/bin/sh
# Rewrite the exported WXR for a live site.
#
# The export comes from the local Docker install, so its URLs point at
# localhost — including the attachment URLs, which the live importer cannot
# fetch. Every image in the export also ships inside the theme, so this
# repoints attachments at the theme's own assets directory on the live domain,
# making the import self-contained: no manual media upload.
#
# usage: ./prepare-content.sh https://your-site.example
set -e

if [ -z "$1" ]; then
	echo "usage: $0 <live-site-url>   e.g. $0 https://estatein.example" >&2
	exit 1
fi

LIVE=$(printf '%s' "$1" | sed 's#/$##')
SRC="$(dirname "$0")/growmodo-content.xml"
OUT="$(dirname "$0")/growmodo-content-live.xml"
LOCAL="http://localhost:8080"

if [ ! -f "$SRC" ]; then
	echo "error: $SRC not found" >&2
	exit 1
fi

# 1. Point attachment sources at the theme's bundled images.
# 2. Rewrite every remaining localhost URL to the live domain.
sed \
	-e "s#${LOCAL}/wp-content/uploads/[0-9]*/[0-9]*/#${LIVE}/wp-content/themes/growmodo/assets/img/#g" \
	-e "s#${LOCAL}#${LIVE}#g" \
	"$SRC" > "$OUT"

echo "wrote $OUT"
echo "remaining localhost references: $(grep -c 'localhost:8080' "$OUT" || true)"
echo
echo "Next: wp-admin -> Tools -> Import -> WordPress, upload $OUT,"
echo "and tick \"Download and import file attachments\"."
