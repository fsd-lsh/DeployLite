# DeployLite, Lightweight deployment scripts for projects

> Language: English｜[中文](./README.CN.MD)

![](https://img.shields.io/badge/Script-@PHP-blue.svg?style=flat)

## Requirements

- System: macOS, Linux
- PHP: 7.4+ with ssh2 extension (optional: pcntl for parallel deployment)
- Running at CLI

## What can this script do?

- Simple deployment for one or multiple cloud servers
- Deploy logging, store build packages, and roll back to stored versions
- Automated pipeline deployment workflows
- Deploy crontab tasks (crawler projects, site monitoring, etc.)
- SSH key or password authentication
- Pre/post deploy hooks for custom automation
- Parallel deployment to multiple servers
- Dry-run mode for previewing operations

---

## Configuration `./config.json`

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
                "command": "echo 123"
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
            "echo 'Starting deploy to {host}'",
            "./scripts/pre-deploy.sh {host}"
        ],
        "post_deploy": [
            "echo 'Deploy to {host} complete'",
            "curl -X POST https://webhook.example.com/deploy-notify"
        ]
    }
}
```

### Config Fields

| Field | Description |
|-------|-------------|
| `timezone` | Script timezone (e.g. `PRC`, `America/New_York`) |
| `client.project_path` | Path to the project directory to deploy |
| `client.package_path` | Path to store deployment packages |
| `client.log_path` | Path to store deployment logs |
| `server.final_path` | Remote server deployment path (must start with `/`) |
| `server.parallel` | Enable parallel deployment to multiple servers (default: `false`) |
| `server.crontab_mode` | `replace` (default) — replace managed entries with markers; `append` — add to existing crontab |
| `server.crontab[].time` | Cron time expression |
| `server.crontab[].command` | Cron command to execute |
| `server.group[].host` | Server hostname or IP |
| `server.group[].port` | SSH port (default: `22`) |
| `server.group[].user` | SSH username |
| `server.group[].pass` | SSH password (required for `auth_method: password`) |
| `server.group[].auth_method` | `password` or `key` (default: `password`) |
| `server.group[].key_path` | Path to SSH private key (default: `~/.ssh/id_rsa`) |
| `server.group[].key_passphrase` | SSH key passphrase (optional) |
| `package.save_count` | Number of packages and logs to retain |
| `hooks.pre_deploy` | Commands executed before deployment on each server |
| `hooks.post_deploy` | Commands executed after deployment on each server |

### Hook Variables

The following placeholders are available in hook commands:

| Placeholder | Replaced with |
|-------------|---------------|
| `{host}` | Server hostname/IP |
| `{user}` | SSH username |
| `{port}` | SSH port |
| `{final_path}` | Remote deployment path |
| `{project_path}` | Local project path |
| `{pack_name}` | Current package name |

---

## `.deployignore` File

Create a `.deployignore` file in your project root to exclude files from packaging. Each line is a glob pattern:

```text
# Ignore version control
.git
.gitignore

# Ignore IDE files
.idea
.DS_Store

# Ignore dependencies
node_modules
vendor

# Ignore test files
*_test.php
test/

# Ignore logs
*.log
```

---

## CLI Commands

```shell
# Grant execution permission
chmod 755 ./deployLite

# Deploy project (package, upload, extract, crontab)
./deployLite deploy

# Preview without making changes
./deployLite deploy --dry-run

# Use a custom config file
./deployLite deploy --config=/path/to/config.json

# List built packages
./deployLite show

# Rollback to a specific version (e.g. 20220722-14-1658473168.zip)
./deployLite rollback---20220722-14-1658473168

# View current configuration
./deployLite config

# Clean all logs and packages
./deployLite clean

# Show help
./deployLite help
```

---

## Crontab Management

### Replace Mode (default)
Managed crontab entries are wrapped with `# DeployLite-managed-{n}-start` / `# DeployLite-managed-{n}-end` markers. Only managed entries are replaced during deployment — unmanaged system crontab entries are preserved.

### Append Mode
Set `"crontab_mode": "append"` to add entries without touching existing crontab. Duplicates may occur across multiple deploys.

---

## Log Example

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

## License

[MIT](LICENSE)

## Author

[Force](https://www.easybhu.cn)
