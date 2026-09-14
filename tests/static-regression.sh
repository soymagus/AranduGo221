#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
test -s "$root/assets/vendor/jodit/jodit.min.js"
test -s "$root/assets/vendor/jodit/jodit.min.css"
test -s "$root/assets/vendor/jodit/LICENSE.txt"
test -s "$root/assets/images/URLGO_Logo1_comp.png"
for image in Estudio-Contable-Horizonte.png hero-estudio.png valentina-rios.png FAQ.png galeria-planificacion.png galeria-reunion.png galeria-organizacion.png galeria-digital.png; do test -s "$root/uploads/demo-contador/$image"; done
grep -q "version'=>'2.2.1'" "$root/config/version.php"
grep -q '"version": "2.2.1"' "$root/manifest.json"
grep -q 'Rollback a una versión inferior' "$root/assets/dashboard.js"
grep -q 'jodit.min.js' "$root/assets/dashboard.js"
grep -q 'gallerySettings' "$root/src/SiteRepository.php"
grep -q 'URLGO_Logo1_comp.png' "$root/index.php"
grep -q 'Configuración guiada' "$root/assets/dashboard.js"
node --check "$root/assets/dashboard.js"
echo "OK: controles estáticos Arandu Go 2.2.1"
