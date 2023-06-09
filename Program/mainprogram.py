import cv2
import numpy as np
from datetime import datetime
import pandas as pd
import csv
import time
#----- dbms mysql -----
import mysql.connector

start_time = time.time()
mydb = mysql.connector.connect(host="[IP]",
                               user="[root or user name]",
                               password="",
                               database="[name_of_database]")

cap1 = cv2.VideoCapture(0) #kedalaman air
cap = cv2.VideoCapture(0) #kecepatan arus

ball_cascade = cv2.CascadeClassifier('file.xml')

# Coordinates of polygon in frame::: [[x1,y1],[x2,y1],[x1,y2],[x2,y2]]
# coord=[[50,50],[600,50],[50,250],[600,250]] #kamera laptop
x1 = 40  # width
x2 = 480
y1 = 150  # height
y2 = 350
coord = [[x1, y1], [x2, y1], [x1, y2], [x2, y2]]

x11 = 230;
y11 = 40
x21 = 480;
y21 = 450

# Distance between two horizontal lines in (meter)
dist = 0.6
all_value=[]

while True:
    ret, img = cap.read()
    _, frame = cap1.read()

    if img is None:
        break

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    balls = ball_cascade.detectMultiScale(gray, 1.8, 2)

    font = cv2.FONT_HERSHEY_SIMPLEX
    # Put current DateTime on each frame
    cv2.putText(frame, str(datetime.now()), (200, 20), font, .5, (255, 255, 255), 2, cv2.LINE_AA)

    cv2.rectangle(frame, (x11, y11), (x21, y21), (0, 0, 255), 1)

    roi = frame[y11:y21, x11:x21]
    hsv = cv2.cvtColor(roi, cv2.COLOR_BGR2HSV)

    # build edge with canny method
    edge = cv2.Canny(hsv, 100, 100, apertureSize=3)

    lines = cv2.HoughLines(edge, 1, np.pi / 180, 140)
    # Draw lines one by one
    img1 = roi.copy()

    for (x, y, w, h) in balls:
        cv2.rectangle(img, (x, y), (x + w, y + h), (225, 0, 0), 2)

    cv2.line(img, (coord[0][0], coord[0][1]), (coord[1][0], coord[1][1]), (0, 0, 255), 2)  # First horizontal line
    cv2.line(img, (coord[0][0], coord[0][1]), (coord[2][0], coord[2][1]), (0, 0, 255), 2)  # Vertical left line
    cv2.line(img, (coord[2][0], coord[2][1]), (coord[3][0], coord[3][1]), (0, 0, 255), 2)  # Second horizontal line
    cv2.line(img, (coord[1][0], coord[1][1]), (coord[3][0], coord[3][1]), (0, 0, 255), 2)  # Vertical right line

    for (x, y, w, h) in balls:
        if (x >= coord[0][0] and y == coord[0][1]):
            cv2.line(img, (coord[0][0], coord[0][1]), (coord[1][0], coord[1][1]), (0, 255, 0), 2)  # Changes line color to green
            tim1 = time.time()  # Initial time
            print("object Entered.")

        if (x >= coord[2][0] and y == coord[2][1]):
            cv2.line(img, (coord[2][0], coord[2][1]), (coord[3][0], coord[3][1]), (0, 0, 255),
                     2)  # Changes line color to green
            tim2 = time.time()  # Final time
            print("object Left.")

            # We know that distance is 0.6m
            ttime = (tim2 - tim1)  # t1
            t2 = ttime / dist  # t2
            k2 = 1 / t2 * 100  # ubah ke cm

            #mulai deteksi tinggi
            for line in lines:
                rho, theta = line[0]
            a = np.cos(theta)
            b = np.sin(theta)
            x0, y0 = a * rho, b * rho
            pt1 = (int(x0 + 1000 * (-b)), int(y0 + 1000 * (a)))  # Calculate the end point of the line
            pt2 = (int(x0 - 1000 * (-b)), int(y0 - 1000 * (a)))  # Calculate the end point of the line
            cv2.line(roi, pt1, pt2, (0, 255, 0), 4)  # Draw a straight line
            # cv2.imshow('HoughLines',img1)
            q = 520 / 8
            y = float(round((y0 - 1000 * (a)) / q, 2))

            #dari hasil pengoperasiana kamera deteksi kecepatan
            print("Speed in (cm/s) is:", k2)
            print(ttime)

            # dari hasil pengoperasiana kamera deteksi kecepatan
            print("Tinggi Permukaan: ", y, "cm")
            value = ("A1", y, k2)
            all_value.append(value)

            write = pd.DataFrame(all_value, columns=['kodearea', 'length', 'kecepatan'])
            write.to_csv('[file.csv]')
            print("data exported to csv")

            time.sleep(3)

            # --------- send to dbms mysql ---------
            with open("file.csv") as csv_file: #use the previous file name
                csvfile = csv.DictReader(csv_file, delimiter=',')
                all_infor = []
                for row in csvfile:
                    infor = (row['kodearea'], row['length'], row['kecepatan'])
                    all_infor.append(infor)

            sqlq = "INSERT INTO table_name (IDpemantau, tinggi_air, kecepatan) VALUES (%s,%s,%s)"
            sql = "SELECT * FROM table_name"
            print(all_infor)
            mycursor = mydb.cursor()
            mycursor.executemany(sqlq, all_infor)

            mydb.commit()

            print(mycursor.rowcount, "was inserted.")
            print("--", time.time() - start_time, "use MySQL", "--")


    cv2.imshow('Kecepatan Arus', img)  # Shows the frame
    cv2.imshow("Ketinggian Air", frame)

    if cv2.waitKey(20) & 0xFF == 27:
        break

cap.release()
cv2.destroyAllWindows()
