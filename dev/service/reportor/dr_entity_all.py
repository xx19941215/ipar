#!/usr/bin/python
# -*- coding: utf8 -*-
import time
import MySQLdb  
import calendar
import config

# entity <===> number flag
entity={1:'rqt',2:'feature',3:'product',4:'invent',12:'idea'}
# init all the data 0
data = {'rqt':0,'feature':0,'product':0,'invent':0,'idea':0}

current_year = int(time.strftime( '%Y', time.localtime() ))
current_month = int( time.strftime( '%m', time.localtime() ))
current_day = int( time.strftime( '%d', time.localtime() ))

# get all the day in year,month
def getDate(year,month):
	# start with 2016-03-25
	if year==2016 and month==3:
		return year,month, range(calendar.monthrange(year, month)[1]+1)[25:]
	if year==current_year and month ==current_month:
		return year,month, range(calendar.monthrange(year, month)[1]+1)[1:current_day]
	return year,month, range(calendar.monthrange(year, month)[1]+1)[1:]

# get the begin day and end day
def getBE(year,month,day):

	begin = "%d-%d-%d"%(year,month,day)
	year,month,days = getDate(year,month)
	if days[-1]==day:
		end_month = month + 1
		end_day= 1
		if year==12:
			end_year = year + 1		
		else:
			end_year = year	
	else:
		end_month = month
		end_day = day + 1
		end_year = year
		print end_year,end_month,end_day
	end = "%d-%d-%d"%(end_year,end_month,end_day)
	return begin,end

def fetchData(cursor,begin,end):

	sql = '''SELECT `type`,count(type) as cont FROM ideapar.entity where `created` 
			between '%s' and '%s' group by `type`;''' %( begin,end )

	try:
		cursor.execute (sql)  
		rows = cursor.fetchall()  
		for row in rows: 
			data[entity[row[0]]]=row[1]
	except:
		print "ERROR:unable to fetch data"
	return data
def insertData(cursor,conn,begin):

	ISOTIMEFORMAT='%Y-%m-%d %X'
	logtime = time.strftime( ISOTIMEFORMAT, time.localtime() )
	insert_sql='''INSERT INTO `ideapar`.`dr_entity` 
		(`date`, `rqt_count`, `product_count`, `idea_count`, `feature_count`, `invent_count`, `created`) VALUES 
		('%s', '%d', '%d', '%d', '%d', '%d', '%s')'''%(begin,data['rqt'],data['product'],data['idea'],data['feature'],data['invent'],logtime)
	try:
		cursor.execute(insert_sql)
		conn.commit()
		return True
	except:
		print "ERROR"  
		print insert_sql
		return False

def dataAnalysis(cursor,table,year,month):

	year,month,days = getDate(year,month)

	for day in days:
		
 		check_sql = ''' SELECT * FROM ideapar.%s WHERE `date`='%d-%d-%d'; '''%(table,year,month,day)
 		cursor.execute(check_sql)
 		result = cursor.fetchone()
 		if not result:
 			print '\n%d-%d-%d is lost'%(year,month,day)
 			begin,end = getBE(year,month,day)
 			fetchData(cursor,begin,end)
 			status = insertData(cursor,conn,begin)
 			if status:
 				print '%d-%d-%d is add'%(year,month,day) 
 			else:
 				print "check the log"
 		else:
 			print '%d-%d-%d is ok\t'%(year,month,day),



def connectMysql(host, user, passwd, db):
	conn = MySQLdb.connect (host = host, user = user, passwd = passwd, db = db)  
	# get cursor
	cursor = conn.cursor ()  
	# setting data utf8
	cursor.execute("SET NAMES utf8")
	return conn,cursor

def closeMysql(conn,cursor):
	cursor.close ()  
	conn.close ()  


if __name__=='__main__':

	host, user, passwd, db, table = config.getConfig()
	conn,cursor = connectMysql(host, user, passwd, db)
	try:
		print '%d-%d-%d:'%(current_year,current_month,current_day)
		start_year = 2016
		start_month = 3
		year = current_year
		month = current_month
		months = (year-start_year)*12+month-start_month+1
		for item in xrange(0,months):
			i_year = (item + start_month)/12
			i_month = (item + start_month)%12
			i_month = 12 if i_month==0 else i_month
			dataAnalysis(cursor,table,start_year+i_year,i_month)
		print '-----------------%d-%d-%d----------------------'%(current_year,current_month,current_day)
	finally:
		closeMysql(conn,cursor)
