FROM php:7.3-apache

ARG appName=keying
ARG warName=keying-spi.tar.bz2
ARG artifactRemotePath=/ap/ecrash/$appName/app
ARG confRemotePath=/ap/ecrash/$appName/conf
ARG secureFileName=secure
ARG environmentPhpFileName=environment
ARG htaccessFile=.htaccess
ARG logDirPath=/ap/ecrash/log/$appName
ARG reportsDir=$artifactRemotePath/public/images/reports
ARG tempDir=$artifactRemotePath/temp
ARG sessionDir=/ap/ecrash/session/$appName

RUN mkdir -p $artifactRemotePath
RUN mkdir -p $confRemotePath
RUN mkdir -p $confRemotePath/secure


# Fancy way - probably not needed
#ENV APACHE_DOCUMENT_ROOT /ap/ecrash/keying/app
#RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.con

# un-zipped files (from git action)
COPY www/ /ap/ecrash/keying/app
# compressed files (also from git action)
# COPY artifacts/keying-spi.tar.bz2 /ap/ecrash/keying/app
COPY conf/dev/secure/${secureFileName}.tmpl $confRemotePath/secure/${secureFileName}_temp.php
COPY conf/dev/${environmentPhpFileName}.php $confRemotePath/${environmentPhpFileName}_temp.php
COPY conf/dev/${htaccessFile} $confRemotePath/_temp${htaccessFile}
COPY setenv.sh /usr/local/bin/setenv.sh

RUN sed -i 's/\r$//' /usr/local/bin/setenv.sh
RUN chmod +x /usr/local/bin/setenv.sh

RUN ln -sf $confRemotePath/secure/${secureFileName}.php ${artifactRemotePath}/config/${secureFileName}.php
RUN ln -sf $confRemotePath/${environmentPhpFileName}.php ${artifactRemotePath}/config/${environmentPhpFileName}.php
RUN ln -sf $confRemotePath/${htaccessFile} ${artifactRemotePath}/${htaccessFile}
RUN ln -sf /ap/ecrash/keying/app/public /var/www/html/ecrash

RUN mkdir -p $reportsDir
RUN mkdir -p $tempDir
RUN mkdir -p $logDirPath
RUN mkdir -p $sessionDir

RUN chmod 0755 $artifactRemotePath
RUN chmod 0755 $confRemotePath
RUN chmod 0755 $confRemotePath/secure
RUN chmod 0777 $reportsDir
RUN chmod 0775 $tempDir
RUN chmod 0777 $logDirPath 
RUN chmod 0777 $sessionDir

RUN apt-get update \
 && apt-get install -y git libzip-dev \
 && apt-get install -y git zlib1g-dev \
 && apt-get install -y git libbz2-dev \
 && apt-get install -y git libxml2-dev \
 && apt-get install -y git libedit-dev \
 && apt-get install -y git libtidy-dev \
 && apt-get install -y git libxslt-dev \
 && docker-php-ext-install zip \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install bcmath 
RUN docker-php-ext-install bz2 
RUN docker-php-ext-install calendar 
#RUN docker-php-ext-install Core 
RUN docker-php-ext-install ctype 
#RUN docker-php-ext-install curl 
#RUN docker-php-ext-install date 
RUN docker-php-ext-install dba 
RUN docker-php-ext-install dom 
RUN docker-php-ext-install exif 
RUN docker-php-ext-install fileinfo 
#RUN docker-php-ext-install filter 
#RUN docker-php-ext-install ftp 
#RUN docker-php-ext-install gd 
RUN docker-php-ext-install gettext 
RUN docker-php-ext-install hash 
RUN docker-php-ext-install iconv 
#RUN docker-php-ext-install imap 
RUN docker-php-ext-install intl 
RUN docker-php-ext-install json 
#RUN docker-php-ext-install ldap 
#RUN docker-php-ext-install libxml 
RUN docker-php-ext-install mbstring 
#RUN docker-php-ext-install mcrypt 
RUN docker-php-ext-install pdo_mysql 
RUN docker-php-ext-install mysqli 
#RUN docker-php-ext-install mysqlnd 
#RUN docker-php-ext-install odbc 
#RUN docker-php-ext-install openssl 
RUN docker-php-ext-install pcntl 
#RUN docker-php-ext-install pcre 
#RUN docker-php-ext-install PDO 
#RUN docker-php-ext-install pdo_dblib 
RUN docker-php-ext-install pdo_mysql 
#RUN docker-php-ext-install PDO_ODBC 
#RUN docker-php-ext-install pdo_sqlite 
#RUN docker-php-ext-install Phar 
RUN docker-php-ext-install posix 
#RUN docker-php-ext-install readline 
#RUN docker-php-ext-install Reflection 
RUN docker-php-ext-install session 
RUN docker-php-ext-install shmop 
#RUN docker-php-ext-install SimpleXML 
RUN docker-php-ext-install soap 
RUN docker-php-ext-install sockets 
#RUN docker-php-ext-install SPL 
#RUN docker-php-ext-install sqlite3 
#RUN docker-php-ext-install ssh2 
#RUN docker-php-ext-install standard 
RUN docker-php-ext-install sysvmsg 
RUN docker-php-ext-install sysvsem 
RUN docker-php-ext-install sysvshm 
RUN docker-php-ext-install tidy 
RUN docker-php-ext-install tokenizer 
RUN docker-php-ext-install wddx 
RUN docker-php-ext-install xml 
#RUN docker-php-ext-install xmlreader 
RUN docker-php-ext-install xmlrpc 
RUN docker-php-ext-install xmlwriter 
RUN docker-php-ext-install xsl 
RUN docker-php-ext-install zip 
#RUN docker-php-ext-install zlib
  
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/setenv.sh"]

WORKDIR /ap/ecrash/keying
USER root

# COPY --chown=appecrash:ecrash applicationinsights-agent-3.2.6.jar /ap/ecrash/eCrashServices/appinsights.jar
# COPY --chown=appecrash:ecrash applicationinsights.json /ap/ecrash/eCrashServices/applicationinsights.json


