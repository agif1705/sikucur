# 🚀 Setup Deploy untuk Repository Private

Jika repository GitHub Anda **private**, Anda perlu setup autentikasi terlebih dahulu. Berikut adalah 3 cara yang bisa digunakan:

## 🔐 Opsi 1: SSH Key (RECOMMENDED)

### Setup SSH Key di Server:

```bash
# 1. Generate SSH key baru
ssh-keygen -t ed25519 -C "your_email@example.com"
# Tekan Enter untuk default location (~/.ssh/id_ed25519)
# Opsional: masukkan passphrase untuk keamanan extra

# 2. Copy public key
cat ~/.ssh/id_ed25519.pub
```

### Tambahkan ke GitHub:

1. **Untuk Deploy Key (Recommended untuk server production):**

   - Buka: `https://github.com/agif1705/sikucur/settings/keys`
   - Click "Add deploy key"
   - Paste SSH public key
   - Beri nama: "Production Server Deploy Key"
   - ✅ Centang "Allow write access" jika perlu push
   - Click "Add key"

2. **Atau tambahkan ke Personal SSH Keys:**
   - Buka: `https://github.com/settings/keys`
   - Click "New SSH key"
   - Paste SSH public key
   - Click "Add SSH key"

### Update deploy.sh:

```bash
# Ganti baris REPO_URL di deploy.sh:
REPO_URL="git@github.com:agif1705/sikucur.git"
```

### Test koneksi:

```bash
ssh -T git@github.com
# Harus muncul: "Hi agif1705! You've successfully authenticated..."
```

---

## 🔑 Opsi 2: Personal Access Token

### Buat Personal Access Token:

1. Buka: `https://github.com/settings/tokens`
2. Click "Generate new token (classic)"
3. Beri nama: "Production Server Deploy"
4. Pilih scope: `repo` (Full control of private repositories)
5. Click "Generate token"
6. **COPY TOKEN SEKARANG** (hanya muncul sekali!)

### Update deploy.sh:

```bash
# Ganti baris REPO_URL di deploy.sh:
REPO_URL="https://USERNAME:TOKEN@github.com/agif1705/sikucur.git"

# Contoh:
REPO_URL="https://agif1705:ghp_xxxxxxxxxxxxxxxxxxxx@github.com/agif1705/sikucur.git"
```

⚠️ **PERINGATAN**: Token akan terlihat di process list. Gunakan SSH untuk keamanan lebih baik.

---

## 🛠️ Opsi 3: GitHub CLI

### Install GitHub CLI:

```bash
# Ubuntu/Debian:
curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null
sudo apt update
sudo apt install gh

# CentOS/RHEL:
sudo dnf install gh
```

### Login:

```bash
gh auth login
# Pilih: GitHub.com
# Pilih: HTTPS
# Pilih: Login with a web browser
# Follow instruksi di browser
```

### Update deploy.sh:

```bash
# REPO_URL tetap menggunakan HTTPS
REPO_URL="https://github.com/agif1705/sikucur.git"
```

---

## 🔍 Troubleshooting

### Error: "Permission denied (publickey)"

- SSH key belum ditambahkan ke GitHub
- SSH agent tidak running: `eval "$(ssh-agent -s)" && ssh-add ~/.ssh/id_ed25519`

### Error: "Authentication failed"

- Personal Access Token salah atau expired
- Username salah dalam URL

### Error: "Repository not found"

- Repository name salah
- Tidak ada akses ke repository private
- Branch tidak exist

### Test manual clone:

```bash
# Test SSH:
git clone git@github.com:agif1705/sikucur.git /tmp/test-ssh

# Test HTTPS dengan token:
git clone https://USERNAME:TOKEN@github.com/agif1705/sikucur.git /tmp/test-https

# Hapus test folder:
rm -rf /tmp/test-*
```

---

## 🎯 Rekomendasi

1. **Production Server**: Gunakan **SSH Key dengan Deploy Key**
2. **Development Server**: Bisa gunakan Personal Access Token
3. **Local Development**: Gunakan GitHub CLI atau SSH

## 🔐 Keamanan

- ✅ Jangan commit credential ke repository
- ✅ Gunakan Deploy Key dengan akses minimal yang diperlukan
- ✅ Rotate Personal Access Token secara berkala
- ✅ Monitor akses di GitHub Settings > Security log

---

Setelah setup autentikasi, jalankan deploy seperti biasa:

```bash
./deploy.sh
```
