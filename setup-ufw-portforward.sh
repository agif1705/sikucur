#!/bin/bash

# Ubah sesuai port SSH kamu (default 22)
SSH_PORT=22

# Port lokal yang mau di-forward
LOCAL_PORT=1705

# IP tujuan forwarding dan portnya
DEST_IP="202.155.143.254"
DEST_PORT=8081

echo "Memulai konfigurasi firewall dan port forwarding..."

# Izinkan SSH di UFW
echo "Izinkan SSH port $SSH_PORT/tcp di UFW..."
sudo ufw allow $SSH_PORT/tcp

# Izinkan port lokal 1705
echo "Izinkan port $LOCAL_PORT/tcp di UFW..."
sudo ufw allow $LOCAL_PORT/tcp

# Enable UFW tanpa prompt
echo "Mengaktifkan UFW..."
sudo ufw --force enable

# Tambah aturan iptables untuk NAT (port forwarding)
echo "Menambahkan aturan iptables untuk port forwarding..."
sudo iptables -t nat -A PREROUTING -p tcp --dport $LOCAL_PORT -j DNAT --to-destination $DEST_IP:$DEST_PORT
sudo iptables -t nat -A POSTROUTING -p tcp -d $DEST_IP --dport $DEST_PORT -j MASQUERADE

# Izinkan trafik masuk port LOCAL_PORT
sudo iptables -I INPUT -p tcp --dport $LOCAL_PORT -j ACCEPT

# Izinkan trafik forward ke DEST_IP:DEST_PORT
sudo iptables -I FORWARD -p tcp -d $DEST_IP --dport $DEST_PORT -j ACCEPT

# Simpan aturan iptables agar tetap aktif setelah reboot
echo "Menyimpan aturan iptables..."
sudo apt-get update
sudo apt-get install -y iptables-persistent
sudo netfilter-persistent save

echo "Selesai! Cek status UFW dan iptables kamu."

sudo ufw status verbose
sudo iptables -t nat -L -n -v | grep DNAT
