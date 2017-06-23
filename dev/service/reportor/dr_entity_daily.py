#!/usr/bin/python
# -*- coding: utf8 -*-
import time
import MySQLdb 
import config 


# entity <===> number flag
entity={1:'rqt',2:'feature',3:'product',4:'invent',12:'idea'}
# init all the data 0
data = {'rqt':0,'feature':0,'product':0,'invent':0,'idea':0}


def dataAnalysis(cursor,table):
 
	today = time.strftime( '%Y-%m-%d', time.localtime() )
	begin = time.strftime( '%Y-%m-%d', time.localtime(time.time()-86400) )

	sql = '''SELECT `type`,count(type) as cont FROM ideapar.entity where `created` 
			between '%s' and '%s' group by `type`;''' %( begin, today )

	try:
		cursor.execute (sql)  
		rows = cursor.fetchall()  
		for row in rows: 
			data[entity[row[0]]]=row[1]
	except:
		print "ERROR:unable to fetch data"

	print begin,data
	
	ISOTIMEFORMAT='%Y-%m-%d %X'
	logtime = time.strftime( ISOTIMEFORMAT, time.localtime() )
	check_sql = ''' SELECT * FROM ideapar.%s WHERE `date`='%s'; '''%(table, begin)

	try:
		cursor.execute (check_sql)  
		today_data = cursor.fetchone()  
		if not today_data:
			sql = '''INSERT INTO `ideapar`.`%s` (`date`, `rqt_count`, `product_count`, `idea_count`, `feature_count`, `invent_count`, `created`) VALUES ('%s', '%d', '%d', '%d', '%d', '%d', '%s')'''%(table,begin,data['rqt'],data['product'],data['idea'],data['feature'],data['invent'],logtime)
		else:
			sql = '''UPDATE `ideapar`.`%s` SET `rqt_count`='%d', `product_count`='%d', `idea_count`='%d', `feature_count`='%d', `invent_count`='%d',`created`='%s'  WHERE `date`='%s';
'''%(table, data['rqt'], data['product'], data['idea'], data['feature'], data['invent'], logtime,begin)

	except:
		print "ERROR:unable to fetch data to update"
	
	try:
		cursor.execute(sql)
		conn.commit()
	except Exception as e:
		print "sql:"
		print sql
		print ""
		print e
		conn.rollback()


def connectMysql(host,user,passwd,db):
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
	try:
	    conn,cursor = connectMysql(host,user,passwd,db)
	    dataAnalysis(cursor,table)
	except Exception as e:
		print e
	finally:
		closeMysql(conn,cursor)
