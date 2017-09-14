# sql2xls
运行环境linux php
目录 
phpexcel  phpexcel库
templates 存放xls文件生成模板
sql2xls.php 执行脚本

使用说明

chomd +x sql2exl.php
Usage:  ./sql2exl.php  host user password dbname
host : mysql地址 
user : mysql登录用户
password : mysql登录密码
dbname  : 数据库名 

模板说明
第一列为生成xls显示的栏目名称，其中sql栏为特殊栏位不做展示，用于读取需要执行的sql语句
第二列，值为sql执行后结果集的列名，对应xls所需展示的内容。
