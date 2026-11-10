#!/bin/bash
# ==============================================================================
# Script Pembuat File Rilis Siap Upload ke CloudPanel (Mac / Lokal)
# ==============================================================================
set -e

OUTPUT_ZIP="budayariau-release.zip"

echo "📦 [1/3] Menyiapkan paket rilis untuk budayariau.jelatix.com..."

# Hapus zip lama jika ada
rm -f "$OUTPUT_ZIP"

echo "🗜️  [2/3] Mengompresi file proyek ke $OUTPUT_ZIP..."

zip -r -q "$OUTPUT_ZIP" . \
    -x "*.git*" \
    -x "*node_modules*" \
    -x "*tests*" \
    -x "*.gemini*" \
    -x "*storage/logs/*.log" \
    -x "*.DS_Store" \
    -x "*.idea*" \
    -x "*.vscode*" \
    -x "$OUTPUT_ZIP"

echo "✅ [3/3] Selesai!"
echo "------------------------------------------------------------------------------"
echo "File rilis berhasil dibuat: $OUTPUT_ZIP ($(du -h "$OUTPUT_ZIP" | cut -f1))"
echo ""
echo "Cara Menggunakan di CloudPanel:"
echo "1. Buka File Manager di CloudPanel untuk site budayariau.jelatix.com"
echo "2. Upload file $OUTPUT_ZIP ke folder: /home/budayariau/htdocs/budayariau.jelatix.com/"
echo "3. Klik kanan pada file $OUTPUT_ZIP lalu pilih 'Extract'"
echo "4. Buka Terminal / SSH CloudPanel lalu jalankan:"
echo "   ./deploy.sh"
echo "------------------------------------------------------------------------------"

