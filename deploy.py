import paramiko
import zipfile
import os
import sys

LOCAL_PATH = r'c:\Users\USER\Documents\SYSTEMS\WEB\PHP\LARAVEL\lanhs'
REMOTE_BASE = '/home/u618858166/domains/lanhs-ticketing.com'
REMOTE_PUBLIC_HTML = f'{REMOTE_BASE}/public_html'
REMOTE_PP = f'{REMOTE_BASE}/public_html(pp)'
ZIP_NAME = 'deploy_upload.zip'
LOCAL_ZIP = os.path.join(LOCAL_PATH, ZIP_NAME)

EXCLUDE_DIRS = {
    'node_modules', '.git', 'storage/logs',
    '__pycache__', '.idea', '.vscode'
}
EXCLUDE_FILES = {
    '.env', ZIP_NAME, 'deploy.py',
    'db_test.php', '.DS_Store'
}
EXCLUDE_EXTENSIONS = {'.map'}

def should_exclude(rel_path):
    parts = rel_path.replace('\\', '/').split('/')
    # Exclude top-level dirs
    if parts[0] in EXCLUDE_DIRS:
        return True
    # Exclude storage/logs content but keep directory
    if len(parts) >= 2 and parts[0] == 'storage' and parts[1] == 'logs':
        return True
    # Exclude specific files
    if parts[-1] in EXCLUDE_FILES:
        return True
    # Exclude .map files
    _, ext = os.path.splitext(parts[-1])
    if ext in EXCLUDE_EXTENSIONS:
        return True
    return False

print("=== Step 1: Creating zip archive ===")
count = 0
with zipfile.ZipFile(LOCAL_ZIP, 'w', zipfile.ZIP_DEFLATED, compresslevel=6) as zf:
    for root, dirs, files in os.walk(LOCAL_PATH):
        # Prune excluded dirs in-place to avoid descending
        dirs[:] = [
            d for d in dirs
            if not should_exclude(os.path.relpath(os.path.join(root, d), LOCAL_PATH))
        ]
        for file in files:
            abs_path = os.path.join(root, file)
            rel_path = os.path.relpath(abs_path, LOCAL_PATH)
            if not should_exclude(rel_path):
                zf.write(abs_path, rel_path)
                count += 1
                if count % 100 == 0:
                    print(f"  Added {count} files...", flush=True)

zip_size_mb = os.path.getsize(LOCAL_ZIP) / (1024 * 1024)
print(f"  Total: {count} files, zip size: {zip_size_mb:.1f} MB")

print("\n=== Step 2: Connecting to server ===")
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('151.106.117.9', port=65002, username='u618858166', password='DomainnaminniCarts01.')
sftp = ssh.open_sftp()
print("  Connected!")

print(f"\n=== Step 3: Uploading zip ({zip_size_mb:.1f} MB) ===")
remote_zip = f'{REMOTE_PUBLIC_HTML}/{ZIP_NAME}'

uploaded = [0]
total_size = os.path.getsize(LOCAL_ZIP)

def progress(transferred, total):
    pct = (transferred / total) * 100
    mb = transferred / (1024 * 1024)
    if int(pct) % 10 == 0 and (transferred == total or abs(transferred - uploaded[0]) > total * 0.09):
        print(f"  {pct:.0f}% ({mb:.1f} MB)", flush=True)
        uploaded[0] = transferred

sftp.put(LOCAL_ZIP, remote_zip, callback=progress)
print("  Upload complete!")
sftp.close()

print("\n=== Step 4: Unzipping on server ===")
stdin, stdout, stderr = ssh.exec_command(
    f'cd "{REMOTE_PUBLIC_HTML}" && unzip -o "{ZIP_NAME}" && rm "{ZIP_NAME}" && echo "UNZIP_DONE"',
    timeout=300
)
out = stdout.read().decode()
err = stderr.read().decode()
if 'UNZIP_DONE' in out:
    print("  Unzip successful!")
else:
    print("  OUT:", out[-500:])
    print("  ERR:", err[-500:])
    sys.exit(1)

print("\n=== Step 5: Copying production .env from public_html(pp) ===")
stdin, stdout, stderr = ssh.exec_command(
    f'cp "{REMOTE_PP}/.env" "{REMOTE_PUBLIC_HTML}/.env" && echo "ENV_DONE"'
)
out = stdout.read().decode()
if 'ENV_DONE' in out:
    print("  .env copied!")
else:
    print("  ERR:", stderr.read().decode())

print("\n=== Step 6: Setting permissions ===")
cmds = [
    f'chmod -R 755 "{REMOTE_PUBLIC_HTML}/storage"',
    f'chmod -R 755 "{REMOTE_PUBLIC_HTML}/bootstrap/cache"',
]
for cmd in cmds:
    stdin, stdout, stderr = ssh.exec_command(cmd)
    stdout.read()

print("  Permissions set!")

print("\n=== Step 7: Running artisan commands ===")
artisan_cmds = [
    f'php "{REMOTE_PUBLIC_HTML}/artisan" config:cache',
    f'php "{REMOTE_PUBLIC_HTML}/artisan" route:cache',
    f'php "{REMOTE_PUBLIC_HTML}/artisan" view:clear',
    f'php "{REMOTE_PUBLIC_HTML}/artisan" storage:link 2>&1 || true',
]
for cmd in artisan_cmds:
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    label = cmd.split('artisan')[1].strip()
    print(f"  artisan {label}: {out or err or 'ok'}")

ssh.close()

# Cleanup local zip
os.remove(LOCAL_ZIP)
print(f"\n=== DEPLOYMENT COMPLETE ===")
print(f"Site: https://lanhs-ticketing.com")
