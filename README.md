summer
===============
基于suframe框架的微服务后端服务，通过swoole的tcp server功能提供对外rest接口服务和内部rpc服务，启动会自动上报到注册中心。

# 主要功能

* 接口转发
* 服务注册
* 代理接口连接池
* 定时检测接口
* rpc接口自动同步
* rpc生成ide代码提示

为了给用户提供足够大的开发自由度，服务只提供基本底层功能，具体db，cache，orm等由用户自行选型，后面通过实际项目的磨合，会推荐一些组件或者框架组合，完全按照你喜欢的方式进行开发。

## 创建服务

~~~
composer create-project suframe/summer=@dev
~~~

配置注册中心
app/config/config/php, 修改registerServer的ip或端口(默认可以不修改)

~~~
php app/summer tcp:start
~~~

其他命令：
```
php app/summer list //列出所有命令
php app/summer check //检查tcp服务是否运行中
php app/summer stop //停止tcp服务
php app/summer rpc:sync //同步rpc接口(此命令会更新app/config/.phpstorm.meta.php文件，用与rpc接口提示)
```

## 命名规范

遵循PSR-2命名规范和PSR-4自动加载规范。

## 参与开发

QQ群：904592189


## 版权信息

suframe遵循Apache2开源协议发布，并提供免费使用。

版权所有Copyright © 2019- by qian <330576744@qq.com>

All rights reserved。