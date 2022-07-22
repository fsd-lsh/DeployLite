# DeployLite, Lightweight deployment scripts for projects

> 选择语言：[中文](./README.CN.MD)｜English

![](https://img.shields.io/badge/Script-@PHP-blue.svg?style=flat)

Environment requires
- System: MacOS、Linux
- Php: 7.4+ && ssh2 extension
- Running at CLI

What can a script do? 
- Simple deployment is required for one or multiple cloud servers
- Deploy logging, store build packages, and roll back stored versions
- Need to automate simple pipeline deployment requirements
- Deploy crontab tasks (such as crawler projects, site monitoring, etc.)

Features to be developed
- Pipeline plug-in

### Configuration Example `./config.json`
```text
{
    "timezone": "America/New_York",     //The script time zone
    "client": {                         //Client Configuration
        "project_path": "./project",    //Project location to deploy
        "package_path": "./package",    //Package path
        "log_path": "./log"             //Log path
    },
    "server": {                         //Server Configuration
        "final_path": "/root",          //Deployment Storage path, It must start with a /
        "crontab": [
            {
                "time": "* * */1 * *",
                "command": "echo 123"
            }
        ],
        "group": [                      //Multiple Linux Server deployment queues
            {
                "host": "192.168.52.10",
                "port": "22",
                "user": "root",
                "pass": "123456"
            },
            {
                "host": "192.168.52.3",
                "port": "22",
                "user": "root",
                "pass": "123456"
            }
        ]
    },
    "package": {
        "save_count": 5                 //Number of retained packages and logs
    }
}

```

### CLI Running Commands，The script runs depending on the configuration of the `./config.json` file before running the CLI command
```shell

#First, grant the permission to run the script based on the situation, and then run the required commands
chmod 777 ./deployLite

#Automated deployment projects (packaging, SSH deployment, logging, storing deployment packages)
./deployLite deploy

#View the built packages
./deployLite show

#Roll back to the specified version package, for example, 20220722-14-1658473168.zip
./deployLite rollback---20220722-14-1658473168

#Check the configuration
./deployLite config

#Clear logs and build packages
./deployLite clean
```

### Log Example
```text
2022-07-21 16:46:49 - ------------------------------------------DeployLite script running... 
2022-07-21 16:46:49 - init start 
2022-07-21 16:46:49 - init end 
2022-07-21 16:46:49 - package start 
2022-07-21 16:46:49 - zip -r ./package/20220721-16-1658479609.zip ./project 
2022-07-21 16:46:49 - package end 
2022-07-21 16:46:49 - server-0-192.168.52.10 running task 
2022-07-21 16:46:49 - deploy start 
2022-07-21 16:46:49 - /Users/force/Desktop/Project/mine/DeployLite/package/20220721-16-1658479609.zip => ssh2.sftp://Resource id #10/root/20220721-16-1658479609.zip 
2022-07-21 16:46:49 - rm -rf project && unzip -q 20220721-16-1658479609.zip && rm -rf 20220721-16-1658479609.zip 
2022-07-21 16:46:49 - deploy end 
2022-07-21 16:46:49 - crontab start 
2022-07-21 16:46:49 - crontab-0: echo '* * */1 * *       echo 123' > /var/spool/cron/root 
2022-07-21 16:46:49 - crontab end 
2022-07-21 16:46:49 - server-0-192.168.52.10 has done 
2022-07-21 16:46:49 - server-1-192.168.52.3 running task 
2022-07-21 16:47:09 - deploy start 
2022-07-21 16:47:09 - /Users/force/Desktop/Project/mine/DeployLite/package/20220721-16-1658479609.zip => ssh2.sftp://Resource id #16/root/20220721-16-1658479609.zip 
2022-07-21 16:47:09 - rm -rf project && unzip -q 20220721-16-1658479609.zip && rm -rf 20220721-16-1658479609.zip 
2022-07-21 16:47:10 - deploy end 
2022-07-21 16:47:10 - crontab start 
2022-07-21 16:47:10 - crontab-0: echo '* * */1 * *       echo 123' > /var/spool/cron/root 
2022-07-21 16:47:10 - crontab end 
2022-07-21 16:47:10 - server-1-192.168.52.3 has done 
2022-07-21 16:47:10 - ------------------------------------------DeployLite script end
```

### License
[MIT](LICENSE)

### Author
[Force](https://www.easybhu.cn)
