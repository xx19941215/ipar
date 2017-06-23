#!/usr/bin/python
# -*- coding: UTF-8 -*-
import time
#设置时间格式
ISOTIMEFORMAT='%Y-%m-%d %X'
logtime = time.strftime( ISOTIMEFORMAT, time.localtime() )
print logtime
