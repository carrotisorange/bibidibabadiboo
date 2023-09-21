#!/bin/sh
set -e
	if [ $# -lt 2 ]; then
		echo #console only
		echo "Usage: $0 <env (dev:qc:ua:ct:prod:dr)> <job_id>" #console only
		echo #console only
		exit 1
	fi

export DATE=$(date +%Y%m%d%H%M)	
export env=$1
export JobId=$2
export ROOT_DIR=/ap/ecrash/keying
export APP_DIR=$ROOT_DIR/app
export CONF_DIR=$ROOT_DIR/conf
export SECURE_DIR=$CONF_DIR/secure
export NEW_APP_DIR=$ROOT_DIR/app.new
export ARCHIVE_UPLOAD_PATH=$ROOT_DIR/$ARCHIVE_NAME
export APP_SECURE_INI_PATH=$CONF_DIR/secure
export KEYING_LOG_DIR=/ap/ecrash/log/keying
export KEYING_REPORTS_DIR=$APP_DIR/public/images/reports
export KEYING_TEMP_DIR=$APP_DIR/temp
export DEPLOYMENT_FOLDER=/ap/ecrash/etc-scripts/KEYING_DEPLOYMENT

echo "Starting Keying app new CI deployment for Environment:$1, JobId:$2"

mkdir -p $DEPLOYMENT_FOLDER
cd $DEPLOYMENT_FOLDER
	
echo "Downloading assembly$JobId.zip ... "
curl -o assembly$JobId.zip --header PRIVATE-TOKEN:uj-oMCFT5GiwfjE8sXGA https://gitlab.ins.risk.regn.net/api/v4/projects/16245/jobs/$JobId/artifacts ||exit $?
	
echo "Downloading secure.php from master ... "
curl -o secure.php --header PRIVATE-TOKEN:uj-oMCFT5GiwfjE8sXGA https://gitlab.ins.risk.regn.net/api/v4/projects/16245/repository/files/conf%2F$env%2Fsecure%2Fsecure.php/raw?ref=master

echo "Downloading environment.php from master ... "
curl -o environment.php --header PRIVATE-TOKEN:uj-oMCFT5GiwfjE8sXGA https://gitlab.ins.risk.regn.net/api/v4/projects/16245/repository/files/conf%2F$env%2Fenvironment.php/raw?ref=master

echo "Downloading .htaccess from master ... "
curl -o .htaccess --header PRIVATE-TOKEN:uj-oMCFT5GiwfjE8sXGA https://gitlab.ins.risk.regn.net/api/v4/projects/16245/repository/files/conf%2F$env%2F.htaccess/raw?ref=master
	
	
unzip assembly$JobId.zip || exit $?
	
echo "Backup current app release ... "
if [ -d $ROOT_DIR/app ]; then 
  cp -ar $ROOT_DIR/app $ROOT_DIR/app_$DATE; 
else 
  echo $ROOT_DIR/app does not exist; 
fi

echo "Backup current conf release ... "

if [ -d $ROOT_DIR/conf ]; then 
    mv $ROOT_DIR/conf $ROOT_DIR/conf_$DATE; 
    mkdir $CONF_DIR
	mkdir $SECURE_DIR
else 
    echo $ROOT_DIR/conf does not exist; 
fi


echo "Deploying the Latest Keying app Release ... "
        
echo "Removing $APP_DIR..."
rm -rf $APP_DIR 

echo "Creating $NEW_APP_DIR..." 		
mkdir $NEW_APP_DIR 

echo "Extracting deployment archive to $NEW_APP_DIR..." 
tar -xjf $DEPLOYMENT_FOLDER/*.bz2 -C $NEW_APP_DIR        
		
echo "Move $NEW_APP_DIR to $APP_DIR..." 
mv $NEW_APP_DIR $APP_DIR
		
echo "copying secure file $APP_SECURE_INI_PATH" 
cp $DEPLOYMENT_FOLDER/secure.php $APP_SECURE_INI_PATH 

echo "copying environment.php file $CONF_DIR..." 
cp $DEPLOYMENT_FOLDER/environment.php $CONF_DIR 
		
echo "copying .htaccess file $CONF_DIR..." 
cp $DEPLOYMENT_FOLDER/.htaccess $CONF_DIR 
		
echo "create symbolic link for secure.php file"
echo "ln -sf $ROOT_DIR/conf/secure/secure.php $ROOT_DIR/app/application/configs/secure.php"
ln -sf $CONF_DIR/secure/secure.php $ROOT_DIR/app/config/secure.php
        
echo "create symbolic link for environment.php file"
echo "ln -sf $ROOT_DIR/conf/environment.php $ROOT_DIR/app/application/configs/environment.php"
ln -sf $CONF_DIR/environment.php $ROOT_DIR/app/config/environment.php
		
echo "create symbolic link for .htaccess file"
echo "ln -sf $ROOT_DIR/conf/.htaccess $APP_DIR/.htaccess"
ln -sf $CONF_DIR/.htaccess $APP_DIR/.htaccess

echo "Remove temp cache"
echo "rm -rf $ROOT_DIR/app/temp/*"
rm -rf $ROOT_DIR/app/temp/*
        
echo "rm -rf $ROOT_DIR/app/public/images/reports/*"
rm -rf $ROOT_DIR/app/public/images/reports/*

echo "Creating $KEYING_REPORTS_DIR ... "
if [ ! -d $KEYING_REPORTS_DIR ]; then
    mkdir -p $KEYING_REPORTS_DIR
fi

echo "chmod 777 $KEYING_REPORTS_DIR"
chmod 777 $KEYING_REPORTS_DIR
        
echo "Creating $KEYING_TEMP_DIR ... "
if [ ! -d $KEYING_TEMP_DIR ]; then
    mkdir -p $KEYING_TEMP_DIR
fi
        
echo "chmod -R 775 $KEYING_TEMP_DIR"
chmod -R 775 $KEYING_TEMP_DIR
		
echo "Removing $DEPLOYMENT_FOLDER deployment directory "
rm -rf $DEPLOYMENT_FOLDER
echo "Deploying the Latest release completed "