# 汽车拍卖网站
本项目通过XAMPP完成，将项目文件夹放置在htdoc文件夹内，浏览器访问以下链接即可预览

```localhost/DatabaseProject/index.php```

## 涵盖功能
1. 用户登入登出
2. 管理员账户设置，可查看当前拍卖情况以及所有用户情况
3. 用户可出价，在规定日期前均可叫价
4. 用户可查看当前最高价

## 数据库简介
包含Customer、User、Bids、autos表

其中User表的ID外接Customer表ID，Bid表的auto的ID与自己表内的ID连接，CustomerID与Customer表的ID连接