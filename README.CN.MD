# DeployLite，项目轻量化部署脚本

> 选择语言：[English](./README.MD)｜中文

![](https://img.shields.io/badge/Script-@PHP-blue.svg?style=flat)

## 环境要求

- 系统: macOS、Linux
- PHP: 7.4+ 且安装 ssh2 扩展（并行部署可选 pcntl 扩展）
- 在命令行（CLI）中运行

## 脚本能做什么？

- 单台或多台云服务器的简易部署
- 部署日志记录、存储构建包、回滚已存储版本
- 自动化流水线部署
- 部署 crontab 定时任务（爬虫项目、站点监控等）
- SSH 密钥或密码认证
- 部署前后自定义钩子脚本
- 多服务器并行部署
- Dry-run 模式预览操作

---

## 配置文件 `./config.json`

```json
{
    "timezone": "PRC",
    "client": {
        "project_path": "./project",
        "package_path": "./package",
        "log_path": "./log"
    },
    "server": {
        "final_path": "/root",
        "parallel": false,
        "crontab_mode": "replace",
        "crontab": [
            {
                "time": "* * */1 * *",
                "command": "echo 'hello'"
            }
        ],
        "group": [
            {
                "host": "192.168.52.10",
                "port": "22",
                "user": "root",
                "pass": "123456",
                "auth_method": "password"
            },
            {
                "host": "192.168.52.3",
                "port": "22",
                "user": "root",
                "auth_method": "key",
                "key_path": "~/.ssh/id_rsa",
                "key_passphrase": ""
            }
        ]
    },
    "package": {
        "save_count": 5
    },
    "hooks": {
        "pre_deploy": [
            "echo '开始部署到 {host}'",
            "./scripts/pre-deploy.sh {host}"
        ],
        "post_deploy": [
            "echo '{host} 部署完成'",
            "curl -X POST https://webhook.example.com/deploy-notify"
        ]
    }
}
```

### 配置字段说明

| 字段 | 描述 |
|------|------|
| `timezone` | 脚本时区，如 `PRC`、`America/New_York` |
| `client.project_path` | 待部署项目目录路径 |
| `client.package_path` | 构建包存储路径 |
| `client.log_path` | 日志存储路径 |
| `server.final_path` | 远程服务器部署路径（必须以 `/` 开头） |
| `server.parallel` | 是否并行部署到多台服务器（默认 `false`） |
| `server.crontab_mode` | `replace`（默认）— 用标记包裹替换托管条目；`append` — 追加到已有 crontab |
| `server.crontab[].time` | Cron 时间表达式 |
| `server.crontab[].command` | 定时执行的命令 |
| `server.group[].host` | 服务器 IP 或域名 |
| `server.group[].port` | SSH 端口（默认 `22`） |
| `server.group[].user` | SSH 用户名 |
| `server.group[].pass` | SSH 密码（`auth_method` 为 `password` 时必填） |
| `server.group[].auth_method` | 认证方式：`password` 或 `key`（默认 `password`） |
| `server.group[].key_path` | SSH 私钥路径（默认 `~/.ssh/id_rsa`） |
| `server.group[].key_passphrase` | SSH 密钥密码（可选） |
| `package.save_count` | 包和日志保留数量 |
| `hooks.pre_deploy` | 每台服务器部署前执行的命令列表 |
| `hooks.post_deploy` | 每台服务器部署后执行的命令列表 |

### 钩子变量

钩子命令中可使用以下占位符：

| 占位符 | 替换为 |
|--------|--------|
| `{host}` | 服务器 IP/域名 |
| `{user}` | SSH 用户名 |
| `{port}` | SSH 端口 |
| `{final_path}` | 远程部署路径 |
| `{project_path}` | 本地项目路径 |
| `{pack_name}` | 当前包名称 |

---

## `.deployignore` 忽略文件

在项目根目录创建 `.deployignore` 文件，打包时将排除匹配的文件。每行一个 glob 模式：

```text
# 忽略版本控制文件
.git
.gitignore

# 忽略 IDE 文件
.idea
.DS_Store

# 忽略依赖目录
node_modules
vendor

# 忽略测试文件
*_test.php
test/

# 忽略日志
*.log
```

---

## CLI 运行命令

```shell
# 赋予执行权限
chmod 755 ./deployLite

# 部署项目（打包、上传、解压、配置 crontab）
./deployLite deploy

# 预览操作，不实际执行
./deployLite deploy --dry-run

# 使用自定义配置文件
./deployLite deploy --config=/path/to/config.json

# 查看已构建的包
./deployLite show

# 回滚到指定版本（例如 20220722-14-1658473168.zip）
./deployLite rollback---20220722-14-1658473168

# 查看当前配置
./deployLite config

# 清理所有日志和构建包
./deployLite clean

# 查看帮助
./deployLite help
```

---

## Crontab 管理

### 替换模式（默认）
托管的 crontab 条目会用 `# DeployLite-managed-{n}-start` / `# DeployLite-managed-{n}-end` 标记包裹。部署时仅替换托管条目，系统已有的非托管 crontab 条目不受影响。

### 追加模式
设置 `"crontab_mode": "append"`，每次部署追加条目而不影响已有 crontab。注意：多次部署可能产生重复条目。

---

## 部署日志示例

```text
2022-07-21 16:46:49 - ------------------------------------------DeployLite script running...
2022-07-21 16:46:49 - init start
2022-07-21 16:46:49 - init end
2022-07-21 16:46:49 - package start
2022-07-21 16:46:49 - zip -r ./package/20220721-16-1658479609.zip ./project
2022-07-21 16:46:49 - package end
2022-07-21 16:46:49 - server-0-192.168.52.10 running task
2022-07-21 16:46:49 - deploy start
2022-07-21 16:46:49 - /path/to/20220721-16-1658479609.zip => ssh2.sftp://.../root/20220721-16-1658479609.zip
2022-07-21 16:46:49 - cd /root && rm -rf project && unzip -q 20220721-16-1658479609.zip && rm -rf 20220721-16-1658479609.zip
2022-07-21 16:46:49 - deploy end
2022-07-21 16:46:49 - crontab start
2022-07-21 16:46:49 - crontab-0: (crontab -u root -l ...) | crontab -u root -
2022-07-21 16:46:49 - crontab end
2022-07-21 16:46:49 - server-0-192.168.52.10 has done
2022-07-21 16:47:09 - server-1-192.168.52.3 running task
...
2022-07-21 16:47:10 - ------------------------------------------DeployLite script end
```

---

## 开源许可

[MIT](LICENSE)

## 作者

[Force](https://www.easybhu.cn)
