# DeployLite，项目轻量化部署脚本

> 选择语言：中文｜[English](./README.MD)

![](https://img.shields.io/badge/Script-@PHP-blue.svg?style=flat)

环境需要
- System: MacOS、Linux
- Php: 7.4+ && ssh2 extension
- Running at CLI

脚本能做什么？场景？
- 单台或多台云服务器需要部署简易部署
- 部署日志记录、存储构建包、回滚已经存储版本
- 需要自动化简易流水线部署需求
- 部署crontab任务

待开发功能
- 流水线插件

### 配置文件示例 `./config.json`
```text
{
    "timezone": "PRC",                  //脚本时区
    "client": {                         //部署机配置
        "project_path": "./project",    //待部署项目位置
        "package_path": "./package",    //包存储位置
        "log_path": "./log"             //日志记录位置
    },
    "server": {                         //服务器配置
        "final_path": "/root",          //部署存储位置，需要以/开头
        "crontab": [
            {
                "time": "* * */1 * *",
                "command": "echo 'hello'"
            }
        ],
        "group": [                      //多台linux server部署队列
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
        "save_count": 5                 //包及日志留存数量
    }
}

```

### CLI运行命令，脚本运行依赖`./config.json`文件配置后再运行cli命令
```shell

#首先根据情况给脚本运行权限，再执行所需命令
chmod 777 ./deployLite

#自动化部署项目（打包、ssh部署、记录日志、存储部署包）
./deployLite deploy

#查看已构建的包
./deployLite show

#回滚到指定版本包，例如20220721-14-1658473168.zip包
./deployLite rollback---20220721-14-1658473168

#查看配置
./deployLite config

#清理日志、构建包
./deployLite clean
```

### 部署日志示例
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

### 开源许可
[MIT](LICENSE)

### 作者
[Force](https://www.easybhu.cn)
