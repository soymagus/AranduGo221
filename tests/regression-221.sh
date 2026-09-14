#!/usr/bin/env sh
set -eu
root="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
check(){ grep -Fq "$2" "$root/$1" || { echo "FALLO: $1 no contiene $2" >&2; exit 1; }; }
reject(){ if grep -Fq "$2" "$root/$1"; then echo "FALLO: $1 todavía contiene $2" >&2; exit 1; fi; }
check config/version.php "'version'=>'2.2.1'"
check manifest.json '"version": "2.2.1"'
check dashboardcliente/index.php 'dashboard-221.css'
check index.php 'site-221.css'
check index.php 'ui-icon-hours'
check partials/site-footer.php '$legacyContactEnabled'
check partials/site-footer.php "if(\$footerShows('phones'))"
reject dashboardcliente/index.php 'data-path="footer.showContact"'
reject partials/site-footer.php "!empty(\$d['footer']['showContact'])&&\$show"
check assets/dashboard-221.css '[data-content-key="libres"]:not(.open) #freeEditor'
check assets/dashboard-221.css '.ag-admin-gallery-item>.gallery-summary-210'
check assets/dashboard-221.css 'grid-template-columns:repeat(6,minmax(0,1fr))'
check assets/site-221.css '"contact-title contact-title"'
check assets/site-221.css '.contact-method[href*="wa.me"]'
echo 'OK: regresión Arandu Go 2.2.1'
