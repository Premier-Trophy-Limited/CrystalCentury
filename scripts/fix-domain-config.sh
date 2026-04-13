#!/bin/bash
#
# fix-domain-config.sh
# Fixes missing crystalcentury.com domain configuration
# Resolves issues with /etc/userdomains, mail directories, and dovecot auth
#
# Usage: sudo bash fix-domain-config.sh
#

set -e

DOMAIN="crystalcentury.com"
USERNAME="crystalcentury"
USERDOMAINS_FILE="/etc/userdomains"
MAIL_BASE="/home/${USERNAME}/mail"
MAIL_DIR="${MAIL_BASE}/${DOMAIN}"
DOVECOT_AUTH_BASE="/etc/dovecot/auth"
DOVECOT_AUTH_FILE="${DOVECOT_AUTH_BASE}/${DOMAIN}"

echo "=========================================="
echo "Fixing domain configuration for ${DOMAIN}"
echo "=========================================="

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "❌ This script must be run as root"
   exit 1
fi

# 1. Fix /etc/userdomains
echo ""
echo "1. Checking /etc/userdomains..."
if ! grep -q "^${DOMAIN}:" "$USERDOMAINS_FILE"; then
    echo "   Adding ${DOMAIN} to /etc/userdomains..."
    echo "${DOMAIN}: ${USERNAME}" >> "$USERDOMAINS_FILE"
    echo "   ✓ Added successfully"
else
    echo "   ✓ Entry already exists"
fi

# 2. Fix mail directory structure
echo ""
echo "2. Checking mail directory structure..."
if [ ! -d "$MAIL_DIR" ]; then
    echo "   Creating mail directories..."
    mkdir -p "${MAIL_DIR}"/{cur,new,tmp}
    chown -R "${USERNAME}:mail" "${MAIL_BASE}/${DOMAIN}"
    chmod -R 750 "${MAIL_DIR}"
    chmod 700 "${MAIL_DIR}"/{cur,new,tmp}
    echo "   ✓ Mail directories created"
else
    echo "   ✓ Mail directory structure exists"
fi

# 3. Fix dovecot auth configuration
echo ""
echo "3. Checking dovecot auth configuration..."
if [ ! -d "$DOVECOT_AUTH_BASE" ]; then
    echo "   Creating dovecot auth directory..."
    mkdir -p "$DOVECOT_AUTH_BASE"
    chmod 755 "$DOVECOT_AUTH_BASE"
fi

if [ ! -f "$DOVECOT_AUTH_FILE" ]; then
    echo "   Creating dovecot auth config for ${DOMAIN}..."
    cat > "$DOVECOT_AUTH_FILE" << EOF
# Dovecot auth config for ${DOMAIN}
driver = passwd
args = ${MAIL_DIR}
EOF
    chown root:root "$DOVECOT_AUTH_FILE"
    chmod 644 "$DOVECOT_AUTH_FILE"
    echo "   ✓ Dovecot auth config created"
else
    echo "   ✓ Dovecot auth config exists"
fi

# 4. Verify configuration
echo ""
echo "=========================================="
echo "Verification:"
echo "=========================================="

echo ""
echo "✓ /etc/userdomains entry:"
grep "^${DOMAIN}:" "$USERDOMAINS_FILE"

echo ""
echo "✓ Mail directory structure:"
ls -ld "$MAIL_DIR"
ls -ld "${MAIL_DIR}"/{cur,new,tmp}

echo ""
echo "✓ Dovecot auth config:"
cat "$DOVECOT_AUTH_FILE"

echo ""
echo "=========================================="
echo "✓ Domain configuration fix completed!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Restart dovecot: systemctl restart dovecot"
echo "2. Verify mail services: systemctl status dovecot postfix"
echo "3. Test domain: mail -E | grep -i $(hostname -d)"
