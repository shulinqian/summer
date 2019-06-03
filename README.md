# summer

#### 介绍
基于swoole开发的一个轻量级框架

#### 软件架构
软件架构说明:http://www.zacms.com/index.php/archives/340/

#### todo

 - server的端口范围设置 (manage端分配，如果注册server带了端口检查是否占用)
 - server注册 (统一管理server和负载，并给出状态报告)
 - server状态检测和监控 (完成热插拔)
 - config全局配置 (热更新)
 - 请求转发 (http+websocket)
 - auth统一授权 (统一授权，下放登录信息，server可直接获取登录信息)
 - 日志收集 (自动收集日志，看是否用es进行管理日志或者其他方案)
 - 降级限流 (这个没想好怎么做，难道每个server的降级写着manage端还是有单个server自己控制流量)
 - 集中式的事件注册和分发 (基于异步事件，server统一上报，通过注册server监听，分发到监听的server上)
 - 集中式锁（初步设想是注册服务的时候，带上lock关键词，分发的时候，在manage端基于swoole内存操作级别的锁，或者单个server的基于redis的锁）
 - 通用服务插件 (提供一些通用服务，比如短信，邮件等)
 - 支持集群和docker化
 - 中间件

#### 安装教程

#### 使用说明

#### 参与贡献

