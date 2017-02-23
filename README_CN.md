WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**本项目用于基于 PHPWind 9 搭建 PT 站前端**

本项目**不提供** Tracker Server，你必须安装 [kinosang/WindTurbine](https://github.com/kinosang/WindTurbine) 或兼容 tracker.

与本项目适配的 PHPWind 主题是 [kinosang/PHPWind-WindPT-Theme](https://github.com/kinosang/PHPWind-WindPT-Theme).

WindTurbine 安装方法见下文.

## 如何使用

* 本项目的扩展必须在 PHPWind 后台手动启用.
* 本项目的计划任务必须在 PHPWind 后台手动添加并启用.
* 若 PHP 没有权限写入 PHPWind 所在目录，你必须在 PHPWind 根目录手动创建 `torrents` 目录并设权限 0755.

```
/
|- cron <dir> // 计划任务文件 [上传文件到 PHPWind/src/service/cron/srv/do/]
|  |- PwCronDoClearPeers.php
|  |- PwCronDoClearTorrents.php
|  =
|- extensions
|  |- torrent <dir> // 扩展 [上传目录到 PHPWind/src/extensions/]
=  =
```

## 安装 WindTurbine

1. 在 [WindTurbine Releases](https://github.com/kinosang/WindTurbine/releases/latest) 下载最新版预编译包（WindTurbine.zip）.
2. 上传到服务器（比如 `~/WindTurbine`）并解压.
3. 复制 `config.sample.xml` 为 `config.xml` 并编辑.
4. 使用 `screen` `nohop` 等工具运行 `WindTurbine`.
5. 可使用 `nginx` 等反代 Tracker.

## 捐赠

[Donate us](https://7in0.me/#donate)

## 更多细节参见 [README.md](README.md)
