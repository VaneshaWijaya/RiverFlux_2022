import csv
import cv2
import time
import math
import pandas as pd

#----- tes dbms mysql -----
import mysql.connector

start_time = time.time()
mydb = mysql.connector.connect(host="[IP]",
                               user="[root]",
                               password="[pass]",
                               database="[name_of_database]")

#-------------- Kecepatan -------------
cap = cv2.VideoCapture(0)  #Path to footage
car_cascade = cv2.CascadeClassifier('[file.xml]')  #Path to cars.xml

#Coordinates of polygon in frame::: [[x1,y1],[x2,y1],[x1,y2],[x2,y2]]
#coord=[[50,50],[600,50],[50,250],[600,250]] #kamera laptop
x1 = 400 #width
x2 = 1500
y1 = 200 #height
y2 = 700
coord=[[x1,y1],[x2,y1],[x1,y2],[x2,y2]] #video

#Distance between two horizontal lines in (meter)
dist = 0.6

#-------------- Ketinggian ------------
cap0 = cv2.VideoCapture(0)
cap0.set(3,648)
cap0.set(4,480)

while True:
    ret, img = cap.read()
    ret, img0 = cap0.read()
    all_value =[]

    gray = cv2.cvtColor(img,cv2.COLOR_BGR2GRAY)
    cars=car_cascade.detectMultiScale(gray,1.8,2)

    gray0 = cv2.cvtColor(img0,cv2.COLOR_BGR2GRAY)
    edges = cv2.Canny(gray0, 100, 120)
    lines = cv2.HoughLinesP(edges, 1, math.pi / 1, 20, None, 2, 480)

    for (x,y,w,h) in cars:
        cv2.rectangle(img,(x,y),(x+w,y+h),(225,0,0),2)


    cv2.line(img, (coord[0][0],coord[0][1]),(coord[1][0],coord[1][1]),(0,0,255),2)   #First horizontal line
    cv2.line(img, (coord[0][0],coord[0][1]), (coord[2][0],coord[2][1]), (0, 0, 255), 2) #Vertical left line
    cv2.line(img, (coord[2][0],coord[2][1]), (coord[3][0], coord[3][1]), (0, 0, 255), 2) #Second horizontal line
    cv2.line(img, (coord[1][0],coord[1][1]), (coord[3][0], coord[3][1]), (0, 0, 255), 2) #Vertical right line
    for (x, y, w, h) in cars:
        if(x>=coord[0][0] and y==coord[0][1]):
            cv2.line(img, (coord[0][0], coord[0][1]), (coord[1][0], coord[1][1]), (0, 255,0), 2) #Changes line color to green
            tim1= time.time() #Initial time
            print("object Entered.")

        if (x>=coord[2][0] and y==coord[2][1]):
            cv2.line(img, (coord[2][0],coord[2][1]), (coord[3][0], coord[3][1]), (0, 0, 255), 2) #Changes line color to green
            tim2 = time.time() #Final time
            print("object Left.")
            #We know that distance is 3m
            ttime = (tim2-tim1) #t1
            t2 = ttime/dist#t2
            k2 = 1/t2 * 100 #ubah ke cm

            dot1 = (lines[0][0][0], lines[0][0][1])
            dot2 = (lines[0][0][2], lines[0][0][3])
            cv2.line(img, dot1, dot2, (255, 0, 0), 3)
            length = lines[0][0][1] - lines[0][0][3]

            print("Ketinggian : ",length)
            print("Speed in (cm/s) is:", k2)
            print(ttime)

            value=("A1",length,k2)
            all_value.append(value)



            write = pd.DataFrame(all_value, columns=['kodearea','length','kecepatan'])
            write.to_csv('file.csv')
            print("data exported to csv")

            time.sleep(3)

            #--------- send to dbms mysql ---------
            with open("file.csv") as csv_file: #use the previous file name
                csvfile = csv.DictReader(csv_file, delimiter=',')
                all_infor = []
                for row in csvfile:
                    infor = (row['kodearea'], row['length'],row['kecepatan'])
                    all_infor.append(infor)



            sqlq = "INSERT INTO table_name (IDpemantau, tinggi_air, kecepatan) VALUES (%s,%s,%s)"
            sql = "SELECT * FROM table_name"
            print(all_infor)
            mycursor = mydb.cursor()
            mycursor.executemany(sqlq,all_infor)

            mydb.commit()

            print (mycursor.rowcount, "was inserted.")
            print ("--", time.time() - start_time ,"use MySQL","--")



    cv2.imshow("output", img0)
    cv2.imshow('img',img) #Shows the frame


    if cv2.waitKey(20) & 0xFF == ord('q'):
        break



cap.release()
cv2.destroyAllWindows()