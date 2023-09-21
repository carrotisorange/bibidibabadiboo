#!/bin/sh

	if [ $# -lt 1 ]; then
		echo #console only
		echo "Usage: $0 <TimeStamp)>" #console only
        echo 'Example: ./backout-keying.sh 201905151538'
		echo #console only
		exit 1
	fi

TimeStamp=$1	
export ROOT_DIR=/ap/ecrash/keying

echo "Backing out keying CI deployment to $TimeStamp Version "

echo "Backout app directory to app_$TimeStamp version ... "

  if [ -d $ROOT_DIR/app_$TimeStamp ]; then 
      rm -rf $ROOT_DIR/app
      mv $ROOT_DIR/app_$TimeStamp $ROOT_DIR/app; 
  else 
      echo $ROOT_DIR/app_$TimeStamp does not exist; 
  fi

  echo "Backout conf directory to conf_$TimeStamp version ... "

  if [ -d $ROOT_DIR/conf_$TimeStamp ]; then 
      rm -rf $ROOT_DIR/conf
	  mv $ROOT_DIR/conf_$TimeStamp $ROOT_DIR/conf;       
  else 
      echo $ROOT_DIR/conf_$TimeStamp does not exist; 
  fi

