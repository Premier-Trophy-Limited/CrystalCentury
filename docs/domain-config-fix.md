# Domain Configuration Fix for crystalcentury.com

## Problem

The domain `crystalcentury.com` is missing critical system configuration files:

- **Missing from `/etc/userdomains`**: The domain is not registered in the system user-domains mapping
- **Missing mail directories**: `/home/crystalcentury/mail/crystalcentury.com` does not exist
- **Missing dovecot auth**: `/etc/dovecot/auth/crystalcentury.com` is not configured

This prevents mail services and domain routing from working correctly.

## Root Cause

In ScalaHosting cPanel/SPanel environments, these files are typically created automatically when a domain is added through the control panel. If they're missing, it usually means:

1. The domain was never properly added to the control panel, OR
2. The control panel configuration is out of sync with actual system configuration

## Solution

### Option 1: Using the Provided Script (Recommended)

The repository includes an automated fix script:

```bash
sudo bash scripts/fix-domain-config.sh
```

**What this script does:**

1. ✓ Adds `crystalcentury.com: crystalcentury` entry to `/etc/userdomains`
2. ✓ Creates mail directory structure at `/home/crystalcentury/mail/crystalcentury.com` with proper permissions
3. ✓ Creates dovecot auth configuration at `/etc/dovecot/auth/crystalcentury.com`
4. ✓ Verifies all changes were applied correctly

**Requirements:**
- SSH access to the server
- Root/sudo privileges
- Bash shell

### Option 2: Manual Configuration

If you prefer to configure manually or the script fails:

#### 1. Add domain to /etc/userdomains

```bash
sudo bash -c 'echo "crystalcentury.com: crystalcentury" >> /etc/userdomains'
```

Verify:
```bash
grep crystalcentury /etc/userdomains
```

#### 2. Create mail directory structure

```bash
sudo mkdir -p /home/crystalcentury/mail/crystalcentury.com/{cur,new,tmp}
sudo chown -R crystalcentury:mail /home/crystalcentury/mail/crystalcentury.com
sudo chmod -R 750 /home/crystalcentury/mail/crystalcentury.com
sudo chmod 700 /home/crystalcentury/mail/crystalcentury.com/{cur,new,tmp}
```

Verify:
```bash
ls -ld /home/crystalcentury/mail/crystalcentury.com
```

#### 3. Create dovecot auth configuration

```bash
sudo mkdir -p /etc/dovecot/auth
```

Create the config file:
```bash
sudo bash -c 'cat > /etc/dovecot/auth/crystalcentury.com << EOF
# Dovecot auth config for crystalcentury.com
driver = passwd
args = /home/crystalcentury/mail/crystalcentury.com
EOF'
```

Verify:
```bash
sudo cat /etc/dovecot/auth/crystalcentury.com
```

## Post-Fix Steps

After applying the fix (script or manual):

1. **Restart dovecot service:**
   ```bash
   sudo systemctl restart dovecot
   ```

2. **Verify services are running:**
   ```bash
   sudo systemctl status dovecot
   sudo systemctl status postfix
   ```

3. **Check mail delivery:**
   ```bash
   sudo tail -f /var/log/mail.log
   ```

4. **Verify domain routing:**
   ```bash
   getent passwd | grep crystalcentury
   ```

## Expected Output After Fix

```bash
$ cat /etc/userdomains | grep crystalcentury
crystalcentury.com: crystalcentury

$ ls -ld /home/crystalcentury/mail/crystalcentury.com
drwx------ 3 crystalcentury mail 4096 Apr 13 12:00 /home/crystalcentury/mail/crystalcentury.com

$ cat /etc/dovecot/auth/crystalcentury.com
# Dovecot auth config for crystalcentury.com
driver = passwd
args = /home/crystalcentury/mail/crystalcentury.com
```

## Troubleshooting

### Script permission denied
Make sure you run with `sudo`:
```bash
sudo bash scripts/fix-domain-config.sh
```

### Mail service won't start
Check dovecot logs:
```bash
sudo journalctl -u dovecot -n 50
```

### Directory permission issues
Verify ownership and permissions:
```bash
ls -ld /home/crystalcentury/mail/crystalcentury.com
```

Should show: `drwx------ crystalcentury mail`

### Domain not appearing in mail client
After restarting services, test with:
```bash
sudo postfix status
sudo dovecot status
```

## References

- ScalaHosting Documentation: Mail Server Configuration
- Dovecot: https://www.dovecot.org/
- Postfix: http://www.postfix.org/
