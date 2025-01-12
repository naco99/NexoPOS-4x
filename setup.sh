# PHP
sudo apt update
sudo apt install apache2 libapache2-mod-fcgid
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php7.4 php7.4-fpm php7.4-xml php7.4-gd php7.4-zip php7.4-mbstring php7.4-curl php7.4-mysql php7.4-bcmath
sudo a2enmod actions fcgid alias proxy_fcgi
sudo a2enmod rewrite
sudo systemctl restart apache2
# Maria DB
sudo apt update
sudo apt install mariadb-server
sudo mysql_secure_installation
# NVM
sudo apt install curl
curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash
source ~/.profile
nvm install 14

# Apache config
<VirtualHost *:80>
    ServerName res.baramej.io
    ServerAlias res.baramej.io
    DocumentRoot /var/www/html/res.baramej.io
 
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>
 
    <FilesMatch \.php$>
        # 2.4.10+ can proxy to unix socket
        SetHandler "proxy:unix:/var/run/php/php7.4-fpm.sock|fcgi://localhost"
    </FilesMatch>
 
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

GRANT ALL ON *.* TO '*****'@'localhost' IDENTIFIED BY '**********' WITH GRANT OPTION;

# Supervisor
[program:app-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/html/app/worker.log
stopwaitsecs=3600