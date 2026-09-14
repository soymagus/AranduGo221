#!/usr/bin/env sh
set -eu
root="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
check(){ grep -Fq "$2" "$root/$1" || { echo "FALLO: $1 no contiene $2" >&2; exit 1; }; }
check config/version.php "'version'=>'2.2.1'"
check config/version.php "'schema_version'=>7"
check assets/dashboard.js 'n<=6'
check assets/dashboard-210.js 'Guardar y avanzar'
check assets/dashboard-210.js "ensureModule('about','Descripción'"
check dashboardcliente/upload.php "'video/mp4'=>'mp4'"
check dashboardcliente/upload.php "'audio/mpeg'=>'mp3'"
check contact.php 'arandu_math_challenges'
check index.php "'free5','free6'"
check index.php '<video controls'
check index.php '<audio controls'
check partials/site-footer.php 'footerShows'
check assets/site-v101.css '#25d366'
check partials/social-icons.php 'URLGO_Logo1_comp.png'
test -s "$root/assets/images/URLGO_Logo1_comp.png"
echo 'OK: regresión estática Arandu Go 2.2.1'
