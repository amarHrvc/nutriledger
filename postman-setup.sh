#!/bin/bash

# NutriLabs Postman Collection Setup Script
# This script helps you set up and run the Postman collection

echo "═══════════════════════════════════════════════════════════"
echo "🚀 NutriLabs API - Postman Collection Setup"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Check if postman CLI is installed
if ! command -v postman &> /dev/null; then
    echo "⚠️  Postman CLI not found. Installing..."
    npm install -g postman
    echo "✅ Postman CLI installed"
fi

echo ""
echo "📋 Available Setups:"
echo ""
echo "1. Import collection to Postman Desktop"
echo "2. Run collection via CLI (requires Postman account)"
echo "3. Display collection info"
echo ""

read -p "Enter choice (1-3): " choice

case $choice in
    1)
        echo ""
        echo "📚 To import into Postman Desktop:"
        echo "1. Open Postman"
        echo "2. Click 'Import' (top-left)"
        echo "3. Select 'postman-nutri-ledger-collection.json'"
        echo "4. Select Environment: 'postman-nutri-ledger-env.json'"
        echo ""
        echo "✅ Collection ready to import!"
        ;;
    2)
        echo ""
        echo "🔑 Postman API Key Setup:"
        read -p "Enter your Postman API Key: " api_key
        
        echo "🏃 Running collection via CLI..."
        postman collection run postman-nutri-ledger-collection.json \
            -e postman-nutri-ledger-env.json \
            --reporters cli,json \
            --reporter-json-export test-results-$(date +%Y%m%d-%H%M%S).json \
            -x "$api_key"
        
        echo ""
        echo "✅ Test run complete! Check test-results-*.json for details"
        ;;
    3)
        echo ""
        echo "📊 Collection Information:"
        echo ""
        jq '.info | {name, description, item_count: (.item | length)}' postman-nutri-ledger-collection.json 2>/dev/null || \
        echo "  Name: NutriLabs API Testing"
        echo "  Requests: 16"
        echo "  Categories: 5"
        echo ""
        echo "📂 Files:"
        echo "  - postman-nutri-ledger-collection.json"
        echo "  - postman-nutri-ledger-env.json"
        echo "  - POSTMAN-COLLECTION-GUIDE.md"
        ;;
    *)
        echo "❌ Invalid choice"
        exit 1
        ;;
esac

echo ""
echo "═══════════════════════════════════════════════════════════"
