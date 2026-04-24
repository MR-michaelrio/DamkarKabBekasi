#!/bin/bash
# Quick photo recovery script

cd /Applications/Dev/damkar-dispatch

echo ""
echo "=========================================="
echo "Photo Recovery Tool"
echo "=========================================="
echo ""

# Run the import script
php import-orphaned-photos.php

echo ""
echo "Recovery complete!"
echo ""
echo "Next steps:"
echo "1. Go to Admin Dashboard"
echo "2. Select a Dispatch"
echo "3. Click Export PDF"
echo "4. Check halaman 3a/3b - photos should appear!"
echo ""
